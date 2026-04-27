<?php

namespace App\Http\Controllers;

use App\Models\Nomination;
use App\Models\NominationAlert;
use App\Models\NominationEntry;
use App\Models\NominationPosition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class NominationController extends Controller
{
    public function index(Request $request)
    {
        $this->syncElapsedNominations();

        $q = trim((string) $request->query('q', ''));
        $tab = $request->query('tab', 'nominations');
        $response = $request->query('response', 'all');

        $interestCount = NominationEntry::query()->where('response_status', 'interested')->count();
        $notInterestCount = NominationEntry::query()->where('response_status', 'not_interested')->count();
        $responseCount = $interestCount + $notInterestCount;

        if ($tab === 'interests') {
            $interestEntries = NominationEntry::query()
                ->with([
                    'user:id,name,email,mobile',
                    'position:id,nomination_id,position',
                    'nomination:id,title',
                ])
                ->when(in_array($response, ['interested', 'not_interested'], true), function ($query) use ($response) {
                    $query->where('response_status', $response);
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->whereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', '%' . $q . '%')
                                ->orWhere('email', 'like', '%' . $q . '%')
                                ->orWhere('mobile', 'like', '%' . $q . '%');
                        })->orWhereHas('nomination', function ($n) use ($q) {
                            $n->where('title', 'like', '%' . $q . '%');
                        })->orWhereHas('position', function ($p) use ($q) {
                            $p->where('position', 'like', '%' . $q . '%');
                        });
                    });
                })
                ->latest('id')
                ->paginate(20)
                ->withQueryString();

            return view('admin.nominations.index', [
                'tab' => 'interests',
                'q' => $q,
                'response' => $response,
                'interestCount' => $interestCount,
                'notInterestCount' => $notInterestCount,
                'responseCount' => $responseCount,
                'interestEntries' => $interestEntries,
                'nominations' => null,
            ]);
        }

        $nominations = Nomination::query()
            ->with(['creator:id,name', 'positions:id,nomination_id,position'])
            ->withCount('entries')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('title', 'like', '%' . $q . '%');
            })
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.nominations.index', [
            'tab' => 'nominations',
            'q' => $q,
            'response' => $response,
            'interestCount' => $interestCount,
            'notInterestCount' => $notInterestCount,
            'responseCount' => $responseCount,
            'nominations' => $nominations,
            'interestEntries' => null,
        ]);
    }

    public function show(Nomination $nomination)
    {
        $this->syncElapsedNominations();
        $nomination->load(['positions', 'creator:id,name']);

        return view('admin.nominations.show', compact('nomination'));
    }

    public function create()
    {
        return view('admin.nominations.create');
    }

    public function store(Request $request)
    {
        $this->mergeNormalizedPollingTimes($request);
        $validated = $request->validate($this->rules());
        $this->assertNominationPollingWindowCoherent($validated);

        DB::transaction(function () use ($request, $validated) {
            $nomination = Nomination::create($this->buildPayload($request, $validated, true));
            foreach ($this->extractPositions($request) as $position) {
                $nomination->positions()->create($position);
            }
        });

        return redirect()->route('admin.nominations.index')->with('success', 'Nomination created successfully.');
    }

    public function edit(Nomination $nomination)
    {
        $this->syncElapsedNominations();
        $nomination->load('positions');

        return view('admin.nominations.edit', compact('nomination'));
    }

    public function update(Request $request, Nomination $nomination)
    {
        $this->mergeNormalizedPollingTimes($request);
        $validated = $request->validate($this->rules($nomination->id));
        $this->assertNominationPollingWindowCoherent($validated);

        DB::transaction(function () use ($request, $validated, $nomination) {
            $nomination->update($this->buildPayload($request, $validated, false));
            $this->syncPositionsOnUpdate($nomination, $request);
        });

        return redirect()->route('admin.nominations.index')->with('success', 'Nomination updated successfully.');
    }

    public function destroy(Nomination $nomination)
    {
        $nomination->delete();
        return back()->with('success', 'Nomination deleted.');
    }

    public function cancel(Nomination $nomination)
    {
        $nomination->update(['status' => 'cancelled', 'is_active' => false]);
        return back()->with('success', 'Nomination cancelled.');
    }

    public function toggleStatus(Nomination $nomination)
    {
        $nomination->update(['is_active' => !$nomination->is_active]);
        return back()->with('success', 'Display status updated.');
    }

    public function alertForm(Nomination $nomination, Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $members = User::query()
            ->where('is_approved', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%')
                        ->orWhere('mobile', 'like', '%' . $q . '%');
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $alertedIds = NominationAlert::query()->where('nomination_id', $nomination->id)->pluck('user_id')->all();

        return view('admin.nominations.alert', compact('nomination', 'members', 'alertedIds', 'q'));
    }

    public function alertStore(Nomination $nomination, Request $request)
    {
        $validated = $request->validate([
            'target' => 'required|in:all,specific,leaders',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'notify_whatsapp' => 'nullable|boolean',
            'notify_sms' => 'nullable|boolean',
            'notify_email' => 'nullable|boolean',
        ]);

        $notifyWhatsApp = $request->boolean('notify_whatsapp');
        $notifySms = $request->boolean('notify_sms');
        $notifyEmail = $request->boolean('notify_email');

        if (!$notifyWhatsApp && !$notifySms && !$notifyEmail) {
            return back()->withErrors(['notify_channel' => 'Select at least one notification channel.'])->withInput();
        }

        $target = $validated['target'];
        if ($target === 'all') {
            $memberIds = User::query()->where('is_approved', true)->pluck('id')->all();
        } elseif ($target === 'leaders') {
            $memberIds = User::query()
                ->where('is_approved', true)
                ->whereNotNull('currently_working')
                ->where('currently_working', '!=', '')
                ->pluck('id')
                ->all();
        } else {
            $memberIds = array_values(array_unique($validated['member_ids'] ?? []));
        }

        if (empty($memberIds)) {
            return back()->withErrors(['member_ids' => 'Please select at least one member.'])->withInput();
        }

        DB::transaction(function () use ($memberIds, $nomination, $notifyWhatsApp, $notifySms, $notifyEmail) {
            foreach ($memberIds as $userId) {
                NominationAlert::updateOrCreate(
                    ['nomination_id' => $nomination->id, 'user_id' => $userId],
                    [
                        'notify_whatsapp' => $notifyWhatsApp,
                        'notify_sms' => $notifySms,
                        'notify_email' => $notifyEmail,
                        'alert_sent_at' => now(),
                    ]
                );
            }
        });

        return redirect()->route('admin.nominations.submissions', $nomination->id)->with('success', 'Nomination alert sent. Members register interest from their dashboard; submissions appear here as they respond.');
    }

    public function submissions(Nomination $nomination, Request $request)
    {
        $this->syncElapsedNominations();
        $q = trim((string) $request->query('q', ''));
        $response = $request->query('response', 'all');
        $positionId = (int) $request->query('position_id', 0);

        $entries = NominationEntry::query()
            ->with([
                'user:id,name,email,mobile',
                'position:id,nomination_id,position',
            ])
            ->where('nomination_id', $nomination->id)
            ->when(in_array($response, ['interested', 'not_interested'], true), function ($query) use ($response) {
                $query->where('response_status', $response);
            })
            ->when($positionId > 0, function ($query) use ($positionId) {
                $query->where('position_id', $positionId);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('user', function ($userQ) use ($q) {
                        $userQ->where('name', 'like', '%' . $q . '%')
                            ->orWhere('email', 'like', '%' . $q . '%')
                            ->orWhere('mobile', 'like', '%' . $q . '%');
                    })->orWhereHas('position', function ($posQ) use ($q) {
                        $posQ->where('position', 'like', '%' . $q . '%');
                    });
                });
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $positions = NominationPosition::query()
            ->where('nomination_id', $nomination->id)
            ->withCount([
                'entries as interested_entries_count' => fn ($q) => $q->where('response_status', 'interested'),
                'entries as not_interested_entries_count' => fn ($q) => $q->where('response_status', 'not_interested'),
            ])
            ->get();

        return view('admin.nominations.submissions', compact('nomination', 'entries', 'positions', 'q', 'response', 'positionId'));
    }

    public function downloadReport(Nomination $nomination)
    {
        $rows = NominationEntry::query()
            ->with(['user:id,name,email,mobile', 'position:id,position'])
            ->where('nomination_id', $nomination->id)
            ->get();

        $csv = "Position\tMember Name\tEmail\tMobile\tResponse\tSubmitted On\n";
        foreach ($rows as $row) {
            $csv .= implode("\t", [
                str_replace("\t", ' ', (string) ($row->position->position ?? '')),
                str_replace("\t", ' ', (string) ($row->user->name ?? '')),
                str_replace("\t", ' ', (string) ($row->user->email ?? '')),
                str_replace("\t", ' ', (string) ($row->user->mobile ?? '')),
                str_replace("\t", ' ', (string) str_replace('_', ' ', (string) $row->response_status)),
                str_replace("\t", ' ', optional($row->submitted_at)->format('d M Y h:i A') ?? ''),
            ])."\n";
        }

        $fileName = 'nomination-' . $nomination->id . '-report.xls';
        $tmpPath = 'reports/' . $fileName;
        Storage::disk('local')->put($tmpPath, $csv);

        return response()
            ->download(storage_path('app/' . $tmpPath), $fileName, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            ])
            ->deleteFileAfterSend(true);
    }

    private function rules(?int $id = null): array
    {
        return [
            'title' => 'required|string|max:255',
            'terms' => 'nullable|string',
            'polling_date' => 'required|date',
            'polling_date_to' => 'nullable|date|after_or_equal:polling_date',
            'polling_from' => 'required|date_format:H:i',
            'polling_to' => 'required|date_format:H:i',
            'status' => 'required|in:draft,active,closed,cancelled',
            'is_active' => 'nullable|boolean',
            'positions' => 'required|array|min:1',
            'positions.*.id' => 'nullable|integer|exists:nomination_positions,id',
            'positions.*.position' => 'required|string|max:255',
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $creating): array
    {
        $payload = [
            'title' => $validated['title'],
            'terms' => $validated['terms'] ?? null,
            'polling_date' => $validated['polling_date'],
            'polling_date_to' => $validated['polling_date_to'] ?? null,
            'polling_from' => $validated['polling_from'],
            'polling_to' => $validated['polling_to'],
            'status' => $validated['status'],
            'is_active' => $request->boolean('is_active', true),
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
            if (!is_array($row) || empty($row['position'])) {
                continue;
            }
            $positions[] = [
                'id' => isset($row['id']) && $row['id'] !== '' ? (int) $row['id'] : null,
                'position' => (string) $row['position'],
                'member_user_id' => null,
            ];
        }
        return $positions;
    }

    private function syncPositionsOnUpdate(Nomination $nomination, Request $request): void
    {
        $submittedRows = $this->extractPositions($request);
        $existing = $nomination->positions()->get()->keyBy('id');
        $keptIds = [];

        foreach ($submittedRows as $row) {
            $positionId = (int) ($row['id'] ?? 0);
            if ($positionId > 0 && $existing->has($positionId)) {
                $position = $existing->get($positionId);
                $position->update([
                    'position' => $row['position'],
                ]);
                $keptIds[] = $positionId;
                continue;
            }

            $created = $nomination->positions()->create([
                'position' => $row['position'],
                'member_user_id' => null,
            ]);
            $keptIds[] = (int) $created->id;
        }

        // Delete only positions removed from form that have no member responses.
        $existing->each(function (NominationPosition $position) use ($keptIds) {
            if (in_array((int) $position->id, $keptIds, true)) {
                return;
            }
            if (!$position->entries()->exists()) {
                $position->delete();
            }
        });
    }

    private function assertNominationPollingWindowCoherent(array $validated): void
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

    /**
     * HTML time inputs may send H:i:s; validation expects H:i.
     */
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
        if (preg_match('/^\d{1,2}:\d{2}\s?(AM|PM)$/i', $value)) {
            try {
                return Carbon::createFromFormat('g:i A', strtoupper(str_replace('.', '', $value)))->format('H:i');
            } catch (\Throwable $e) {
                return $value;
            }
        }

        return $value;
    }

    private function syncElapsedNominations(): void
    {
        $nominations = Nomination::query()
            ->whereIn('status', ['draft', 'active'])
            ->get(['id', 'polling_date', 'polling_date_to', 'polling_from', 'polling_to', 'status', 'is_active']);

        $now = now();
        foreach ($nominations as $nomination) {
            if (! $nomination->polling_date || ! $nomination->polling_from || ! $nomination->polling_to) {
                continue;
            }
            $startDate = $nomination->polling_date->format('Y-m-d');
            $endDate = ($nomination->polling_date_to ?? $nomination->polling_date)->format('Y-m-d');
            $start = Carbon::parse($startDate.' '.$nomination->polling_from);
            $end = Carbon::parse($endDate.' '.$nomination->polling_to);

            if ($now->greaterThan($end)) {
                $nomination->update([
                    'status' => 'closed',
                    'is_active' => false,
                ]);
                continue;
            }

            if ($now->greaterThanOrEqualTo($start) && $nomination->status !== 'active') {
                $nomination->update(['status' => 'active']);
                continue;
            }

            if ($now->lt($start) && $nomination->status !== 'draft') {
                $nomination->update(['status' => 'draft']);
            }
        }
    }
}

