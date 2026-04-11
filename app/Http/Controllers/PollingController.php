<?php

namespace App\Http\Controllers;

use App\Models\Polling;
use App\Models\PollingPosition;
use App\Models\PollingVote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PollingController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $pollings = Polling::query()
            ->with('creator:id,name')
            ->withCount('votes')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', '%' . $q . '%');
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.pollings.index', compact('pollings', 'q'));
    }

    public function create()
    {
        $members = User::query()
            ->where('is_approved', true)
            ->whereIn('id', function ($q) {
                $q->from('nomination_entries')->select('user_id')->distinct();
            })
            ->latest('id')
            ->get(['id', 'name', 'email', 'mobile']);
        return view('admin.pollings.create', compact('members'));
    }

    public function store(Request $request)
    {
        $this->mergeNormalizedPollingTimes($request);
        $validated = $request->validate($this->rules());
        $this->assertPollingWindowCoherent($validated);

        DB::transaction(function () use ($request, $validated) {
            $polling = Polling::create($this->buildPayload($request, $validated, true));
            foreach ($this->extractPositions($request) as $position) {
                $candidateIds = $position['candidate_ids'];
                unset($position['candidate_ids']);
                $model = $polling->positions()->create($position);
                if ($candidateIds !== []) {
                    $model->candidates()->sync($candidateIds);
                }
            }
        });

        return redirect()->route('admin.pollings.index')->with('success', 'Polling created successfully.');
    }

    public function edit(Polling $polling)
    {
        $polling->load(['positions.candidates:id,name,email,mobile']);
        $selectedCandidateIds = $polling->positions
            ->flatMap(fn ($position) => $position->candidates->pluck('id'))
            ->unique()
            ->values()
            ->all();

        $members = User::query()
            ->where('is_approved', true)
            ->where(function ($query) use ($selectedCandidateIds) {
                $query->whereIn('id', function ($q) {
                    $q->from('nomination_entries')->select('user_id')->distinct();
                });

                if ($selectedCandidateIds !== []) {
                    $query->orWhereIn('id', $selectedCandidateIds);
                }
            })
            ->latest('id')
            ->get(['id', 'name', 'email', 'mobile']);
        return view('admin.pollings.edit', compact('polling', 'members'));
    }

    public function update(Request $request, Polling $polling)
    {
        $this->mergeNormalizedPollingTimes($request);
        $validated = $request->validate($this->rules($polling->id));
        $this->assertPollingWindowCoherent($validated);

        DB::transaction(function () use ($request, $validated, $polling) {
            $polling->update($this->buildPayload($request, $validated, false));
            $polling->positions()->delete();
            foreach ($this->extractPositions($request) as $position) {
                $candidateIds = $position['candidate_ids'];
                unset($position['candidate_ids']);
                $model = $polling->positions()->create($position);
                if ($candidateIds !== []) {
                    $model->candidates()->sync($candidateIds);
                }
            }
        });

        return redirect()->route('admin.pollings.index')->with('success', 'Polling updated successfully.');
    }

    public function destroy(Polling $polling)
    {
        $polling->delete();
        return back()->with('success', 'Polling deleted.');
    }

    public function togglePromote(Polling $polling)
    {
        $polling->update(['promote_front' => !$polling->promote_front]);
        return back()->with('success', 'Promote front updated.');
    }

    public function toggleStatus(Polling $polling)
    {
        $polling->update(['polling_status' => $polling->polling_status === 'live' ? 'ends' : 'live']);
        return back()->with('success', 'Polling status updated.');
    }

    public function stats(Polling $polling)
    {
        $polling->load(['positions.candidates:id,name']);

        $positionStats = [];
        foreach ($polling->positions as $position) {
            $counts = PollingVote::query()
                ->where('polling_id', $polling->id)
                ->where('position_id', $position->id)
                ->selectRaw('candidate_user_id, COUNT(*) as c')
                ->groupBy('candidate_user_id')
                ->pluck('c', 'candidate_user_id');

            $totalVotes = (int) $counts->sum();

            $candidates = $position->candidates->map(function ($c) use ($counts, $totalVotes) {
                $v = (int) ($counts[$c->id] ?? 0);

                return [
                    'name' => $c->name,
                    'votes' => $v,
                    'bar_percent' => $totalVotes > 0 ? round(($v / $totalVotes) * 100) : 0,
                ];
            })->sortByDesc('votes')->values()->all();

            $positionStats[] = [
                'position' => $position,
                'total_votes' => $totalVotes,
                'candidates' => $candidates,
            ];
        }

        return view('admin.pollings.stats', compact('polling', 'positionStats'));
    }

    public function downloadReport(Polling $polling)
    {
        $rows = PollingVote::query()
            ->with([
                'position:id,position',
                'candidate:id,name,email,mobile',
                'voter:id,name,email,mobile',
            ])
            ->where('polling_id', $polling->id)
            ->get();

        $csv = "Position,Candidate,Votes By,Voted At\n";
        foreach ($rows as $row) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\"\n",
                str_replace('"', '""', (string) ($row->position->position ?? '')),
                str_replace('"', '""', (string) ($row->candidate->name ?? '')),
                str_replace('"', '""', (string) ($row->voter->name ?? '')),
                optional($row->voted_at)->format('d M Y h:i A') ?? ''
            );
        }

        $fileName = 'polling-' . $polling->id . '-report.csv';
        $tmpPath = 'reports/' . $fileName;
        Storage::disk('local')->put($tmpPath, $csv);

        return response()->download(storage_path('app/' . $tmpPath), $fileName)->deleteFileAfterSend(true);
    }

    private function rules(?int $id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'polling_date' => 'required|date',
            'polling_date_to' => 'nullable|date|after_or_equal:polling_date',
            'polling_from' => 'required|date_format:H:i',
            'polling_to' => 'required|date_format:H:i',
            'cover_image' => 'nullable|image|max:5120',
            'banner_image' => 'nullable|image|max:5120',
            'promote_front' => 'nullable|boolean',
            'publish_status' => 'required|in:na,pending,published',
            'polling_status' => 'required|in:live,ends',
            'show_stats' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'positions' => 'required|array|min:1',
            'positions.*.position' => 'required|string|max:255',
            'positions.*.candidate_ids' => 'nullable|array',
            'positions.*.candidate_ids.*' => 'integer|exists:users,id',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'title' => $validated['title'],
            'polling_date' => $validated['polling_date'],
            'polling_date_to' => $validated['polling_date_to'] ?? null,
            'polling_from' => $validated['polling_from'],
            'polling_to' => $validated['polling_to'],
            'promote_front' => $request->boolean('promote_front'),
            'publish_status' => $validated['publish_status'],
            'polling_status' => $validated['polling_status'],
            'show_stats' => $request->boolean('show_stats', true),
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $request->file('cover_image')->store('pollings/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $payload['banner_image_path'] = $request->file('banner_image')->store('pollings/banners', 'public');
        }

        return $payload;
    }

    private function extractPositions(Request $request): array
    {
        $positions = [];
        foreach ((array) $request->input('positions', []) as $row) {
            if (!is_array($row) || empty($row['position'])) {
                continue;
            }
            $ids = [];
            if (!empty($row['candidate_ids']) && is_array($row['candidate_ids'])) {
                $ids = array_values(array_unique(array_map('intval', $row['candidate_ids'])));
            }
            $positions[] = [
                'position' => (string) $row['position'],
                'candidate_ids' => $ids,
            ];
        }
        return $positions;
    }

    private function assertPollingWindowCoherent(array $validated): void
    {
        $fromDate = (string) $validated['polling_date'];
        $toDate = ! empty($validated['polling_date_to']) ? (string) $validated['polling_date_to'] : $fromDate;
        $start = Carbon::parse($fromDate.' '.$validated['polling_from']);
        $end = Carbon::parse($toDate.' '.$validated['polling_to']);
        if ($end->lte($start)) {
            throw ValidationException::withMessages([
                'polling_to' => 'The closing date and time must be after the opening date and time.',
            ]);
        }
    }

    private function mergeNormalizedPollingTimes(Request $request): void
    {
        $request->merge([
            'polling_date_to' => $request->filled('polling_date_to') ? $request->input('polling_date_to') : null,
            'polling_from' => $this->normalizeHiTime($request->input('polling_from')),
            'polling_to' => $this->normalizeHiTime($request->input('polling_to')),
        ]);
    }

    private function normalizeHiTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return is_string($value) ? '' : null;
        }
        $value = trim((string) $value);
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $value, $m)) {
            return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }

        return $value;
    }
}

