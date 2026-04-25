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
        $this->syncElapsedPollings();

        $q = trim((string) $request->query('q', ''));

        $pollings = Polling::query()
            ->with('creator:id,name')
            ->withCount('votes')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', '%'.$q.'%');
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
                $q->from('nomination_entries')
                    ->select('user_id')
                    ->where('response_status', 'interested')
                    ->distinct();
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
                    $q->from('nomination_entries')
                        ->select('user_id')
                        ->where('response_status', 'interested')
                        ->distinct();
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
            $polling->update($this->buildPayload($request, $validated, false, $polling));
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
        $polling->update(['promote_front' => ! $polling->promote_front]);

        return back()->with('success', 'Promote front updated.');
    }

    public function toggleStatus(Polling $polling)
    {
        $polling->update(['polling_status' => $polling->polling_status === 'live' ? 'ends' : 'live']);

        return back()->with('success', 'Polling status updated.');
    }

    public function stats(Polling $polling)
    {
        $polling->load([
            'positions.candidates:id,name,email,mobile',
            'positions.winner:id,name',
        ]);

        $positionStats = [];
        $votes = PollingVote::query()
            ->with(['voter:id,name,email,mobile'])
            ->where('polling_id', $polling->id)
            ->get(['polling_id', 'position_id', 'candidate_user_id', 'voter_user_id', 'voted_at'])
            ->groupBy('position_id');

        foreach ($polling->positions as $position) {
            $votesForPosition = $votes->get($position->id, collect());
            $counts = PollingVote::query()
                ->where('polling_id', $polling->id)
                ->where('position_id', $position->id)
                ->selectRaw('candidate_user_id, COUNT(*) as c')
                ->groupBy('candidate_user_id')
                ->pluck('c', 'candidate_user_id');

            $totalVotes = (int) $counts->sum();

            $winnerUserId = (int) ($position->winner_user_id ?? 0);

            $candidates = $position->candidates->map(function ($c) use ($counts, $totalVotes, $winnerUserId) {
                $v = (int) ($counts[$c->id] ?? 0);

                return [
                    'id' => (int) $c->id,
                    'name' => $c->name,
                    'votes' => $v,
                    'bar_percent' => $totalVotes > 0 ? round(($v / $totalVotes) * 100) : 0,
                    'is_winner' => $winnerUserId > 0 && $winnerUserId === (int) $c->id,
                ];
            })->sortByDesc('votes')->values()->all();

            $positionStats[] = [
                'position' => $position,
                'total_votes' => $totalVotes,
                'candidates' => $candidates,
                'winner_name' => optional($position->winner)->name,
                'voters' => $votesForPosition
                    ->sortByDesc('voted_at')
                    ->map(function ($vote) use ($position) {
                        $candidateName = optional($position->candidates->firstWhere('id', (int) ($vote->candidate_user_id ?? 0)))->name;
                        return [
                            'candidate_user_id' => (int) ($vote->candidate_user_id ?? 0),
                            'candidate_name' => $candidateName ?: '-',
                            'name' => $vote->voter->name ?? '-',
                            'email' => $vote->voter->email ?? '-',
                            'mobile' => $vote->voter->mobile ?? '-',
                            'voted_at' => optional($vote->voted_at)->format('d M Y h:i A') ?? '-',
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        }

        return view('admin.pollings.stats', compact('polling', 'positionStats'));
    }

    public function saveResults(Request $request, Polling $polling)
    {
        $validated = $request->validate([
            'results_visible_to_members' => 'nullable|boolean',
            'winners' => 'nullable|array',
            'winners.*' => 'nullable|integer|exists:users,id',
        ]);

        $polling->update([
            'results_visible_to_members' => $request->boolean('results_visible_to_members'),
        ]);

        foreach ($validated['winners'] ?? [] as $positionId => $winnerUserId) {
            $positionId = (int) $positionId;
            $position = PollingPosition::query()
                ->where('polling_id', $polling->id)
                ->where('id', $positionId)
                ->first();
            if (! $position) {
                continue;
            }
            $winnerUserId = $winnerUserId ? (int) $winnerUserId : null;
            if ($winnerUserId) {
                $position->load('candidates');
                if (! $position->candidates->contains('id', $winnerUserId)) {
                    continue;
                }
            }
            $position->update(['winner_user_id' => $winnerUserId]);
        }

        return redirect()
            ->route('admin.pollings.stats', $polling)
            ->with('success', 'Results visibility and winners saved.');
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

        $csv = "Position\tCandidate\tVotes By\tVoter Email\tVoter Mobile\tVoted At\n";
        foreach ($rows as $row) {
            $csv .= implode("\t", [
                str_replace("\t", ' ', (string) ($row->position->position ?? '')),
                str_replace("\t", ' ', (string) ($row->candidate->name ?? '')),
                str_replace("\t", ' ', (string) ($row->voter->name ?? '')),
                str_replace("\t", ' ', (string) ($row->voter->email ?? '')),
                str_replace("\t", ' ', (string) ($row->voter->mobile ?? '')),
                str_replace("\t", ' ', optional($row->voted_at)->format('d M Y h:i A') ?? ''),
            ])."\n";
        }

        $fileName = 'polling-'.$polling->id.'-report.xls';
        $tmpPath = 'reports/'.$fileName;
        Storage::disk('local')->put($tmpPath, $csv);

        return response()
            ->download(storage_path('app/'.$tmpPath), $fileName, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ])
            ->deleteFileAfterSend(true);
    }

    private function rules(?int $id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'polling_date' => 'required|date',
            'polling_date_to' => 'nullable|date|after_or_equal:polling_date',
            'polling_from' => 'required|date_format:H:i',
            'polling_to' => 'required|date_format:H:i',
            'promote_front' => 'nullable|boolean',
            'publish_status' => 'required|in:pending,published',
            'polling_status' => 'required|in:live,ends',
            'show_stats' => 'nullable|boolean',
            'results_visible_to_members' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'positions' => 'required|array|min:1',
            'positions.*.position' => 'required|string|max:255',
            'positions.*.candidate_ids' => 'nullable|array',
            'positions.*.candidate_ids.*' => 'integer|exists:users,id',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating, ?Polling $polling = null): array
    {
        $payload = [
            'title' => $validated['title'],
            'polling_date' => $validated['polling_date'],
            'polling_date_to' => $validated['polling_date_to'] ?? null,
            'polling_from' => $validated['polling_from'],
            'polling_to' => $validated['polling_to'],
            // Promote/Show Stats checkboxes removed from form; preserve values on edit.
            'promote_front' => $creating
                ? false
                : (bool) ($polling?->promote_front ?? false),
            'publish_status' => $validated['publish_status'],
            'polling_status' => $validated['polling_status'],
            'show_stats' => $creating
                ? true
                : (bool) ($polling?->show_stats ?? true),
            'results_visible_to_members' => $creating
                ? false
                : (bool) ($polling?->results_visible_to_members ?? false),
            'is_active' => $creating
                ? true
                : (bool) ($polling?->is_active ?? true),
        ];

        if ($creating) {
            $payload['created_by_admin_id'] = Auth::guard('admin')->id();
        }

        return $payload;
    }

    private function extractPositions(Request $request): array
    {
        $positions = [];
        foreach ((array) $request->input('positions', []) as $row) {
            if (! is_array($row) || empty($row['position'])) {
                continue;
            }
            $ids = [];
            if (! empty($row['candidate_ids']) && is_array($row['candidate_ids'])) {
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

    private function syncElapsedPollings(): void
    {
        $livePollings = Polling::query()
            ->where('polling_status', 'live')
            ->where('is_active', true)
            ->get(['id', 'polling_date', 'polling_date_to', 'polling_to']);

        foreach ($livePollings as $polling) {
            if (! $polling->polling_date || ! $polling->polling_to) {
                continue;
            }
            $endDate = ($polling->polling_date_to ?? $polling->polling_date)->format('Y-m-d');
            $end = Carbon::parse($endDate.' '.$polling->polling_to);
            if (now()->greaterThan($end)) {
                $polling->update(['polling_status' => 'ends']);
            }
        }
    }
}
