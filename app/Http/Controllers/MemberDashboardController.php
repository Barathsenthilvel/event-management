<?php

namespace App\Http\Controllers;

use App\Models\DonationPayment;
use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\Meeting;
use App\Models\MembershipSubscriptionSetting;
use App\Models\Nomination;
use App\Models\NominationEntry;
use App\Models\NominationPosition;
use App\Models\PaymentTransaction;
use App\Models\Polling;
use App\Models\PollingPosition;
use App\Models\PollingVote;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        $canSeeMembership = $user && $user->profile_completed && $user->is_approved;
        $hasActiveSubscription = $user?->activeSubscription()->exists();
        if ($canSeeMembership && ! $hasActiveSubscription) {
            return redirect()->route('member.subscription.index');
        }

        $latestReceiptTransaction = PaymentTransaction::query()
            ->with('subscriptionPlan')
            ->where('user_id', $user?->id)
            ->where('status', 'successful')
            ->latest('id')
            ->first();

        $memberDonationsTotal = (float) DonationPayment::query()
            ->where('user_id', $user?->id)
            ->where('status', 'successful')
            ->sum('amount');

        $showFullMemberMenu = $canSeeMembership && $hasActiveSubscription;

        $renewalPlans = collect();
        $dashboardNominations = collect();
        $dashboardPolls = collect();
        $nominationSlotQueue = collect();
        $pollingSlotQueue = collect();

        if ($showFullMemberMenu && $user) {
            $renewalPlans = MembershipSubscriptionSetting::query()
                ->where('is_active', true)
                ->where('subscription_type', 'Renewal')
                ->orderBy('payment_type')
                ->get();

            $rawNominations = Nomination::query()
                ->with(['positions' => fn ($q) => $q->orderBy('id')])
                ->where('is_active', true)
                ->where('status', 'active')
                ->latest('id')
                ->limit(30)
                ->get();

            $nominationIds = $rawNominations->pluck('id');
            $entriesByNomination = NominationEntry::query()
                ->where('user_id', $user->id)
                ->whereIn('nomination_id', $nominationIds)
                ->get(['nomination_id', 'position_id'])
                ->groupBy('nomination_id');

            foreach ($rawNominations as $nom) {
                $endDay = ($nom->polling_date_to ?? $nom->polling_date)->toDateString();
                if (Carbon::today()->toDateString() > $endDay) {
                    continue;
                }
                $interestedPositionIds = $entriesByNomination->get($nom->id, collect())
                    ->pluck('position_id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();
                $pendingPositions = $nom->positions->filter(
                    fn ($p) => ! $interestedPositionIds->contains((int) $p->id)
                );
                // Hide only after interest is recorded for every listed role (or user dismissed).
                if ($nom->positions->isNotEmpty() && $pendingPositions->isEmpty()) {
                    continue;
                }
                $dashboardNominations->push([
                    'nomination' => $nom,
                    'pendingPositions' => $pendingPositions,
                    'interestedPositionIds' => $interestedPositionIds,
                ]);
            }

            $rawPolls = Polling::query()
                ->with(['positions' => fn ($q) => $q->with('candidates:id,name')])
                ->where('is_active', true)
                ->where('publish_status', 'published')
                ->where('polling_status', 'live')
                ->latest('id')
                ->limit(30)
                ->get();

            $votesByPolling = PollingVote::query()
                ->where('voter_user_id', $user->id)
                ->whereIn('polling_id', $rawPolls->pluck('id'))
                ->get(['id', 'polling_id', 'position_id', 'candidate_user_id'])
                ->groupBy('polling_id');

            foreach ($rawPolls as $poll) {
                $endDay = ($poll->polling_date_to ?? $poll->polling_date)->toDateString();
                if (Carbon::today()->toDateString() > $endDay) {
                    continue;
                }
                if (! $this->pollingIsWithinSchedule($poll)) {
                    continue;
                }
                $votes = $votesByPolling->get($poll->id, collect());
                $votedPositionIds = $votes->pluck('position_id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();
                $pendingPositions = $poll->positions->filter(
                    fn ($p) => ! $votedPositionIds->contains((int) $p->id)
                );
                if ($pendingPositions->isEmpty()) {
                    continue;
                }
                $dashboardPolls->push([
                    'polling' => $poll,
                    'pollingDashboardVotedIds' => $votedPositionIds,
                    'pollingDashboardVotes' => $votes->keyBy('position_id'),
                ]);
            }

            foreach ($dashboardNominations as $nomRow) {
                foreach ($nomRow['pendingPositions'] as $position) {
                    $nominationSlotQueue->push([
                        'nomination' => $nomRow['nomination'],
                        'position' => $position,
                    ]);
                }
            }

            foreach ($dashboardPolls as $pollRow) {
                $poll = $pollRow['polling'];
                $votedIds = $pollRow['pollingDashboardVotedIds'];
                $votesKeyed = $pollRow['pollingDashboardVotes'];
                foreach ($poll->positions as $ppos) {
                    if ($votedIds->contains((int) $ppos->id)) {
                        continue;
                    }
                    $pollingSlotQueue->push([
                        'polling' => $poll,
                        'position' => $ppos,
                        'pollingDashboardVotedIds' => $votedIds,
                        'pollingDashboardVotes' => $votesKeyed,
                    ]);
                }
            }
        }

        $showNominationDashboard = $dashboardNominations->isNotEmpty();
        $showPollingDashboard = $dashboardPolls->isNotEmpty();

        $upcomingMeetings = collect();
        if ($showFullMemberMenu && $user) {
            // Open meetings (no invites) appear for everyone; once the office adds invites,
            // only listed members see that meeting — same pattern as events.
            $upcomingMeetings = Meeting::query()
                ->with([
                    'schedules' => fn ($q) => $q->orderBy('meeting_date')->orderBy('from_time'),
                    'invites' => fn ($q) => $q->where('user_id', $user->id),
                ])
                ->where('is_active', true)
                ->whereIn('status', ['upcoming', 'live'])
                ->whereHas('schedules', fn ($q) => $q->whereDate('meeting_date', '>=', Carbon::today()->toDateString()))
                ->where(function ($q) use ($user) {
                    $q->whereDoesntHave('invites')
                        ->orWhereHas('invites', fn ($iq) => $iq->where('user_id', $user->id));
                })
                ->get()
                ->sortBy(function (Meeting $m) {
                    $s = $m->schedules->first();
                    if (! $s) {
                        return '9999-12-31 99:99:99';
                    }
                    $from = $s->from_time ?? '00:00:00';

                    return $s->meeting_date->format('Y-m-d').' '.(string) $from;
                })
                ->take(12)
                ->values();
        }

        return view('member.dashboard', [
            'activeSubscription' => $user?->activeSubscription,
            'latestReceiptTransaction' => $latestReceiptTransaction,
            'memberDonationsTotal' => $memberDonationsTotal,
            'renewalPlans' => $renewalPlans,
            'dashboardNominations' => $dashboardNominations,
            'dashboardPolls' => $dashboardPolls,
            'nominationSlotQueue' => $nominationSlotQueue,
            'pollingSlotQueue' => $pollingSlotQueue,
            'showNominationDashboard' => $showNominationDashboard,
            'showPollingDashboard' => $showPollingDashboard,
            'upcomingMeetings' => $upcomingMeetings,
            'transactions' => PaymentTransaction::query()
                ->with('subscriptionPlan')
                ->where('user_id', $user?->id)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    /**
     * Legacy endpoint: closing a card is only hidden in the browser until refresh.
     * Dismissal is not stored; cards reappear on the next visit unless the member
     * completed all required actions (interest / votes), which hides them server-side.
     */
    public function dismissDashboardAnnouncement(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:nomination,polling',
            'entity_id' => 'required|integer|min:1',
            'next' => 'nullable|string',
        ]);

        $table = $data['type'] === 'nomination' ? 'nominations' : 'pollings';
        $request->validate([
            'entity_id' => Rule::exists($table, 'id'),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        $next = $data['next'] ?? route('member.dashboard');
        if (! str_starts_with($next, url('/')) && ! str_starts_with($next, '/')) {
            $next = route('member.dashboard');
        }

        return redirect($next);
    }

    protected function memberPortalUnlocked($user): bool
    {
        return $user
            && $user->profile_completed
            && $user->is_approved
            && $user->activeSubscription()->exists();
    }

    public function nominationsPage()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Nominations are available after you have an active membership plan.'
                );
        }

        $memberNominations = Nomination::query()
            ->with(['positions' => fn ($q) => $q->withCount('entries')])
            ->where('is_active', true)
            ->where('status', 'active')
            ->latest('id')
            ->limit(20)
            ->get();

        $nominationInterestPositionIds = NominationEntry::query()
            ->where('user_id', $user->id)
            ->pluck('position_id');

        return view('member.nominations', [
            'activeSubscription' => $user->activeSubscription,
            'memberNominations' => $memberNominations,
            'nominationInterestPositionIds' => $nominationInterestPositionIds,
        ]);
    }

    public function pollingsPage()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Polling is available after you have an active membership plan.'
                );
        }

        $memberPollings = Polling::query()
            ->with([
                'positions' => fn ($q) => $q->with(['candidates:id,name', 'winner:id,name']),
            ])
            ->where('is_active', true)
            ->where('publish_status', 'published')
            ->where(function ($q) {
                $q->where('polling_status', 'live')
                    ->orWhere(function ($q2) {
                        $q2->where('polling_status', 'ends')
                            ->where('results_visible_to_members', true);
                    });
            })
            ->latest('id')
            ->limit(30)
            ->get();

        $pollingVotedPositionIds = PollingVote::query()
            ->where('voter_user_id', $user->id)
            ->pluck('position_id');

        $memberPollingVotes = PollingVote::query()
            ->where('voter_user_id', $user->id)
            ->whereIn('polling_id', $memberPollings->pluck('id'))
            ->get(['id', 'polling_id', 'position_id', 'candidate_user_id'])
            ->keyBy('position_id');

        $pollingResultStats = [];
        foreach ($memberPollings as $poll) {
            if (! $poll->results_visible_to_members) {
                continue;
            }
            $byPosition = [];
            foreach ($poll->positions as $position) {
                $counts = PollingVote::query()
                    ->where('polling_id', $poll->id)
                    ->where('position_id', $position->id)
                    ->selectRaw('candidate_user_id, COUNT(*) as c')
                    ->groupBy('candidate_user_id')
                    ->pluck('c', 'candidate_user_id');
                $total = (int) $counts->sum();
                $rows = [];
                foreach ($position->candidates as $cand) {
                    $v = (int) ($counts[$cand->id] ?? 0);
                    $rows[] = [
                        'name' => $cand->name,
                        'votes' => $v,
                        'bar_percent' => $total > 0 ? round(($v / $total) * 100) : 0,
                    ];
                }
                $byPosition[$position->id] = [
                    'total' => $total,
                    'candidates' => collect($rows)->sortByDesc('votes')->values()->all(),
                    'winner_name' => $position->winner?->name,
                ];
            }
            $pollingResultStats[$poll->id] = $byPosition;
        }

        return view('member.pollings', [
            'activeSubscription' => $user->activeSubscription,
            'memberPollings' => $memberPollings,
            'pollingVotedPositionIds' => $pollingVotedPositionIds,
            'memberPollingVotes' => $memberPollingVotes,
            'pollingResultStats' => $pollingResultStats,
        ]);
    }

    public function submitNominationInterest(Request $request, Nomination $nomination, NominationPosition $nominationPosition)
    {
        $user = Auth::user();
        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Nominations are available after you have an active membership plan.');
        }

        if ($nominationPosition->nomination_id !== $nomination->id) {
            abort(404);
        }

        if (! $nomination->is_active || $nomination->status !== 'active') {
            return back()->with('nomination_error', 'This nomination is not open for interest.');
        }

        try {
            NominationEntry::firstOrCreate(
                [
                    'nomination_id' => $nomination->id,
                    'position_id' => $nominationPosition->id,
                    'user_id' => $user->id,
                ],
                ['submitted_at' => now()]
            );
        } catch (QueryException $e) {
            return back()->with('nomination_error', 'Could not save your interest. Please try again.');
        }

        return back()->with('nomination_success', 'Your interest for this position has been recorded.');
    }

    public function submitPollingVote(Request $request, Polling $polling)
    {
        $user = Auth::user();
        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Polling is available after you have an active membership plan.');
        }

        $validated = $request->validate([
            'position_id' => 'required|exists:polling_positions,id',
            'candidate_user_id' => 'required|exists:users,id',
        ]);

        $position = PollingPosition::query()
            ->where('id', (int) $validated['position_id'])
            ->where('polling_id', $polling->id)
            ->firstOrFail();

        if (! $position->candidates()->where('users.id', (int) $validated['candidate_user_id'])->exists()) {
            return back()->with('polling_error', 'That candidate is not listed for this position.');
        }

        if (! $polling->is_active || $polling->publish_status !== 'published' || $polling->polling_status !== 'live') {
            return back()->with('polling_error', 'This poll is not open for voting.');
        }

        if (! $this->pollingIsWithinSchedule($polling)) {
            return back()->with('polling_error', 'Voting is only allowed during the scheduled date and time.');
        }

        try {
            PollingVote::updateOrCreate(
                [
                    'polling_id' => $polling->id,
                    'position_id' => $position->id,
                    'voter_user_id' => $user->id,
                ],
                [
                    'candidate_user_id' => (int) $validated['candidate_user_id'],
                    'voted_at' => now(),
                ]
            );
        } catch (QueryException $e) {
            return back()->with('polling_error', 'Could not save your vote. Please try again.');
        }

        return back()
            ->with('polling_success', 'Your vote has been recorded.')
            ->with('polling_success_poll_id', $polling->id)
            ->with('polling_thanks_modal', true)
            ->with('polling_thanks_poll_id', $polling->id);
    }

    private function pollingIsWithinSchedule(Polling $polling): bool
    {
        $fromDate = $polling->polling_date->format('Y-m-d');
        $toDate = ($polling->polling_date_to ?? $polling->polling_date)->format('Y-m-d');
        $start = Carbon::parse($fromDate.' '.$polling->polling_from);
        $end = Carbon::parse($toDate.' '.$polling->polling_to);

        return now()->between($start, $end);
    }

    public function eventsPage()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        $hasActiveSubscription = $user?->activeSubscription()->exists();
        $canSeeMembership = $user && $user->profile_completed && $user->is_approved;
        $showFullMemberMenu = $canSeeMembership && $hasActiveSubscription;

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Your events list is available after you have an active membership plan.'
                );
        }

        $eventCardWith = [
            'dates:id,event_id,event_date,start_time,end_time',
            'creator:id,name',
        ];

        $interestedInvitesWith = [
            'invites' => static function ($query) {
                $query->select('id', 'event_id', 'user_id', 'participation_status', 'invited_at')
                    ->where('participation_status', 'interested')
                    ->orderByDesc('invited_at')
                    ->limit(16)
                    ->with(['user:id,name,first_name,last_name,passport_photo_path']);
            },
        ];

        $myEventInvites = EventInvite::query()
            ->where('user_id', $user->id)
            ->with([
                'event' => function ($q) use ($eventCardWith, $interestedInvitesWith) {
                    $q->select(
                        'id',
                        'title',
                        'status',
                        'template_pdf_path',
                        'description',
                        'venue',
                        'cover_image_path',
                        'seat_mode',
                        'seat_limit',
                        'interested_count',
                        'created_by_admin_id'
                    )->with(array_merge($eventCardWith, $interestedInvitesWith));
                },
            ])
            ->latest('id')
            ->get();

        return view('member.events', [
            'activeSubscription' => $user->activeSubscription,
            'myEventInvites' => $myEventInvites,
        ]);
    }

    public function downloadEventCertificate(Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $invite = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $invite || $invite->participation_status !== 'participated') {
            return redirect()->route('member.events.index')->with(
                'event_interest_error',
                'Your certificate is available only after the admin marks you as attended for this event.'
            );
        }

        $event->loadMissing('dates');

        if (empty($event->template_pdf_path) || ! Storage::disk('public')->exists($event->template_pdf_path)) {
            return redirect()->route('member.events.index')->with(
                'event_interest_error',
                'Certificate file is not available for this event yet. Please contact the office.'
            );
        }

        $memberName = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) ($user->name ?? 'member'));
        $eventTitle = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $event->title);
        $fileName = trim("{$eventTitle}-{$memberName}-certificate.pdf", '-');

        return response()->download(
            Storage::disk('public')->path($event->template_pdf_path),
            $fileName
        );
    }

    public function submitInterest(Request $request, Event $event)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        if (! $event->is_active || $event->status === 'cancelled') {
            return back()->with('event_interest_error', 'This event is not available for interest now.');
        }

        $alreadyInterested = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyInterested) {
            return back()->with('event_interest_error', 'You have already submitted interest for this event.');
        }

        try {
            DB::transaction(function () use ($event, $user) {
                $event->refresh();
                if ($event->seat_mode === 'limited' && $event->seat_limit !== null && $event->interested_count >= $event->seat_limit) {
                    throw new \RuntimeException('Registration closed — this event has reached its seat limit.');
                }

                $alreadyCountedViaPublic = EventInterest::query()
                    ->where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->exists();

                EventInvite::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'participation_status' => 'interested',
                    'invited_at' => now(),
                ]);

                if (! $alreadyCountedViaPublic) {
                    $event->update([
                        'interested_count' => (int) $event->interested_count + 1,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('event_interest_error', $e->getMessage());
        } catch (QueryException $e) {
            return back()->with('event_interest_error', 'You have already submitted interest for this event.');
        }

        return back()->with('event_interest_success', 'Interest submitted successfully.');
    }
}
