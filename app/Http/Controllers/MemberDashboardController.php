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
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
        if ($canSeeMembership && !$hasActiveSubscription) {
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
        $dashboardWinnerPolls = collect();
        $upcomingMeetings = collect();

        if ($showFullMemberMenu && $user) {
            $renewalPlans = MembershipSubscriptionSetting::query()
                ->where('is_active', true)
                ->where('subscription_type', 'Renewal')
                ->orderBy('payment_type')
                ->get();

            $dismissedNominationIds = collect(session('member.dashboard.dismissed_nomination_ids', []))->map(fn ($id) => (int) $id)->filter();
            $dismissedPollingIds = collect(session('member.dashboard.dismissed_polling_ids', []))->map(fn ($id) => (int) $id)->filter();
            $dismissedPollingWinnerIds = collect(session('member.dashboard.dismissed_polling_winner_ids', []))->map(fn ($id) => (int) $id)->filter();
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
                ->get(['nomination_id', 'position_id', 'response_status'])
                ->groupBy('nomination_id');

            foreach ($rawNominations as $nom) {
                if ($dismissedNominationIds->contains($nom->id)) {
                    continue;
                }
                if (! $this->nominationIsWithinSchedule($nom)) {
                    continue;
                }
                $responses = $entriesByNomination->get($nom->id, collect());
                $interested = $responses
                    ->where('response_status', 'interested')
                    ->pluck('position_id')
                    ->values();
                $dismissedPositions = $responses
                    ->where('response_status', 'not_interested')
                    ->pluck('position_id')
                    ->values();

                // Show nomination popup only if the member still has any position unanswered
                $answeredIds = $interested->merge($dismissedPositions)->unique()->values();
                $pendingPositions = $nom->positions->filter(fn ($p) => ! $answeredIds->contains($p->id));
                if ($pendingPositions->isEmpty()) {
                    continue;
                }
                $dashboardNominations->push([
                    'nomination' => $nom,
                    'interestedPositionIds' => $interested,
                    'dismissedPositionIds' => $dismissedPositions,
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
                if ($dismissedPollingIds->contains($poll->id)) {
                    continue;
                }
                $endDay = ($poll->polling_date_to ?? $poll->polling_date)->toDateString();
                if (Carbon::today()->toDateString() > $endDay) {
                    continue;
                }
                if (! $this->pollingIsWithinSchedule($poll)) {
                    continue;
                }
                $votes = $votesByPolling->get($poll->id, collect());
                $votedPositionIds = $votes->pluck('position_id');
                $pendingPositions = $poll->positions->filter(fn ($p) => ! $votedPositionIds->contains($p->id));
                if ($pendingPositions->isEmpty()) {
                    continue;
                }
                $dashboardPolls->push([
                    'polling' => $poll,
                    'pollingDashboardVotedIds' => $votedPositionIds,
                    'pollingDashboardVotes' => $votes->keyBy('position_id'),
                ]);
            }

            $resultPolls = Polling::query()
                ->with(['positions' => fn ($q) => $q->with('winner:id,name')])
                ->where('is_active', true)
                ->where('publish_status', 'published')
                ->where('results_visible_to_members', true)
                ->whereIn('polling_status', ['live', 'ends'])
                ->latest('id')
                ->limit(30)
                ->get();

            foreach ($resultPolls as $poll) {
                if ($dismissedPollingWinnerIds->contains($poll->id)) {
                    continue;
                }
                if (! $this->pollingHasEnded($poll)) {
                    continue;
                }

                $winners = $poll->positions
                    ->filter(fn ($position) => !empty($position->winner_user_id) && $position->winner)
                    ->map(fn ($position) => [
                        'position' => $position->position,
                        'winner_name' => $position->winner->name,
                    ])
                    ->values();

                if ($winners->isEmpty()) {
                    continue;
                }

                $dashboardWinnerPolls->push([
                    'polling' => $poll,
                    'winners' => $winners,
                ]);
            }

            $upcomingMeetings = $this->fetchUpcomingMeetingsForMember($user);
        }

        $showNominationDashboard = $dashboardNominations->isNotEmpty();
        $showPollingDashboard = $dashboardPolls->isNotEmpty();
        $showPollingWinnerDashboard = $dashboardWinnerPolls->isNotEmpty();

        return view('member.dashboard', [
            'activeSubscription' => $user?->activeSubscription,
            'latestReceiptTransaction' => $latestReceiptTransaction,
            'memberDonationsTotal' => $memberDonationsTotal,
            'renewalPlans' => $renewalPlans,
            'dashboardNominations' => $dashboardNominations,
            'dashboardPolls' => $dashboardPolls,
            'dashboardWinnerPolls' => $dashboardWinnerPolls,
            'upcomingMeetings' => $upcomingMeetings,
            'showNominationDashboard' => $showNominationDashboard,
            'showPollingDashboard' => $showPollingDashboard,
            'showPollingWinnerDashboard' => $showPollingWinnerDashboard,
            'transactions' => PaymentTransaction::query()
                ->with('subscriptionPlan')
                ->where('user_id', $user?->id)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }

    public function dismissDashboardAnnouncement(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:nomination,polling,polling_winner',
            'entity_id' => 'required|integer|min:1',
            'next' => 'nullable|string',
        ]);

        $table = $data['type'] === 'nomination' ? 'nominations' : 'pollings';
        $request->validate([
            'entity_id' => Rule::exists($table, 'id'),
        ]);

        $sessionKey = match ($data['type']) {
            'nomination' => 'dismissed_nomination_ids',
            'polling' => 'dismissed_polling_ids',
            default => 'dismissed_polling_winner_ids',
        };

        $ids = collect(session('member.dashboard.'.$sessionKey, []))
            ->map(fn ($id) => (int) $id)
            ->push((int) $data['entity_id'])
            ->unique()
            ->values()
            ->all();

        session()->put('member.dashboard.'.$sessionKey, $ids);

        if ($data['type'] === 'nomination') {
            session()->forget('member.dashboard.dismissed_nomination');
        } elseif ($data['type'] === 'polling') {
            session()->forget('member.dashboard.dismissed_polling');
        }

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
            ->get()
            ->filter(fn (Nomination $nomination) => $this->nominationIsWithinSchedule($nomination))
            ->values();

        $nominationInterestPositionIds = NominationEntry::query()
            ->where('user_id', $user->id)
            ->where('response_status', 'interested')
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
            ->with(['positions' => fn ($q) => $q->with(['candidates:id,name', 'winner:id,name'])])
            ->where('is_active', true)
            ->where('publish_status', 'published')
            ->where(function ($query) {
                $query->where('polling_status', 'live')
                    ->orWhere(function ($sub) {
                        $sub->where('polling_status', 'ends')
                            ->where('results_visible_to_members', true);
                    });
            })
            ->latest('id')
            ->limit(20)
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
        foreach ($memberPollings as $polling) {
            $ended = $polling->polling_status === 'ends' || $this->pollingHasEnded($polling);
            if (! $polling->results_visible_to_members || ! $ended) {
                continue;
            }

            $positionStats = [];
            foreach ($polling->positions as $position) {
                $counts = PollingVote::query()
                    ->where('polling_id', $polling->id)
                    ->where('position_id', $position->id)
                    ->selectRaw('candidate_user_id, COUNT(*) as c')
                    ->groupBy('candidate_user_id')
                    ->pluck('c', 'candidate_user_id');

                $totalVotes = (int) $counts->sum();
                $winnerUserId = (int) ($position->winner_user_id ?? 0);

                $candidates = $position->candidates->map(function ($candidate) use ($counts, $totalVotes, $winnerUserId) {
                    $votes = (int) ($counts[$candidate->id] ?? 0);

                    return [
                        'id' => (int) $candidate->id,
                        'name' => $candidate->name,
                        'votes' => $votes,
                        'bar_percent' => $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0,
                        'is_winner' => $winnerUserId > 0 && $winnerUserId === (int) $candidate->id,
                    ];
                })->sortByDesc('votes')->values()->all();

                $positionStats[$position->id] = [
                    'winner_name' => optional($position->winner)->name,
                    'candidates' => $candidates,
                ];
            }

            $pollingResultStats[$polling->id] = $positionStats;
        }

        return view('member.pollings', [
            'activeSubscription' => $user->activeSubscription,
            'memberPollings' => $memberPollings,
            'pollingVotedPositionIds' => $pollingVotedPositionIds,
            'memberPollingVotes' => $memberPollingVotes,
            'pollingResultStats' => $pollingResultStats,
        ]);
    }

    private function pollingHasEnded(Polling $polling): bool
    {
        if (! $polling->polling_date || ! $polling->polling_to) {
            return false;
        }

        $endDate = ($polling->polling_date_to ?? $polling->polling_date)->format('Y-m-d');
        $end = Carbon::parse($endDate.' '.$polling->polling_to);

        return now()->greaterThan($end);
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
        if (! $this->nominationIsWithinSchedule($nomination)) {
            return back()->with('nomination_error', 'This nomination is closed now.');
        }

        try {
            NominationEntry::updateOrCreate(
                [
                    'nomination_id' => $nomination->id,
                    'position_id' => $nominationPosition->id,
                    'user_id' => $user->id,
                ],
                [
                    'response_status' => 'interested',
                    'submitted_at' => now(),
                ]
            );
        } catch (QueryException $e) {
            return back()->with('nomination_error', 'Could not save your interest. Please try again.');
        }

        return back()
            ->with('nomination_success', 'Your interest for this position has been recorded.')
            ->with('nomination_thanks_modal', true)
            ->with('nomination_thanks_type', 'interested')
            ->with('nomination_thanks_nomination_id', $nomination->id)
            ->with('nomination_thanks_position', $nominationPosition->position);
    }

    public function submitNominationNotInterested(Request $request, Nomination $nomination, NominationPosition $nominationPosition)
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
        if (! $nomination->is_active || $nomination->status !== 'active' || ! $this->nominationIsWithinSchedule($nomination)) {
            return back()->with('nomination_error', 'This nomination is closed now.');
        }

        try {
            NominationEntry::updateOrCreate(
                [
                    'nomination_id' => $nomination->id,
                    'position_id' => $nominationPosition->id,
                    'user_id' => $user->id,
                ],
                [
                    'response_status' => 'not_interested',
                    'submitted_at' => now(),
                ]
            );
        } catch (QueryException $e) {
            return back()->with('nomination_error', 'Could not save your response. Please try again.');
        }

        return back()
            ->with('nomination_success', 'Not interested saved for this position.')
            ->with('nomination_thanks_modal', true)
            ->with('nomination_thanks_type', 'not_interested')
            ->with('nomination_thanks_nomination_id', $nomination->id)
            ->with('nomination_thanks_position', $nominationPosition->position);
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
            ->with('polling_thanks_poll_id', $polling->id)
            ->with('polling_thanks_position_id', $position->id);
    }

    private function pollingIsWithinSchedule(Polling $polling): bool
    {
        $fromDate = $polling->polling_date->format('Y-m-d');
        $toDate = ($polling->polling_date_to ?? $polling->polling_date)->format('Y-m-d');
        $start = Carbon::parse($fromDate.' '.$polling->polling_from);
        $end = Carbon::parse($toDate.' '.$polling->polling_to);

        return now()->between($start, $end);
    }

    private function nominationIsWithinSchedule(Nomination $nomination): bool
    {
        $fromDate = $nomination->polling_date?->format('Y-m-d');
        $toDate = ($nomination->polling_date_to ?? $nomination->polling_date)?->format('Y-m-d');
        if (! $fromDate || ! $toDate || ! $nomination->polling_from || ! $nomination->polling_to) {
            return false;
        }
        $start = Carbon::parse($fromDate.' '.$nomination->polling_from);
        $end = Carbon::parse($toDate.' '.$nomination->polling_to);

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

        $events = Event::query()
            ->with($eventCardWith)
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live', 'completed'])
            ->latest('id')
            ->get();

        $interestedEventIds = EventInvite::query()
            ->where('user_id', $user->id)
            ->pluck('event_id')
            ->all();

        $myEventInvites = EventInvite::query()
            ->where('user_id', $user->id)
            ->with([
                'event' => function ($q) use ($eventCardWith) {
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
                    )->with($eventCardWith);
                },
            ])
            ->latest('id')
            ->get();

        return view('member.events', [
            'activeSubscription' => $user->activeSubscription,
            'events' => $events,
            'interestedEventIds' => $interestedEventIds,
            'myEventInvites' => $myEventInvites,
            'inviteByEventId' => $myEventInvites->keyBy('event_id'),
        ]);
    }

    public function meetingsPage()
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Meetings are available after you have an active membership plan.'
                );
        }

        return view('member.meetings', [
            'activeSubscription' => $user->activeSubscription,
            'upcomingMeetings' => $this->fetchUpcomingMeetingsForMember($user),
        ]);
    }

    private function fetchUpcomingMeetingsForMember($user)
    {
        $today = Carbon::today()->toDateString();

        return Meeting::query()
            ->with([
                'schedules' => fn ($q) => $q->orderBy('meeting_date')->orderBy('from_time'),
                'invites' => fn ($q) => $q->where('user_id', $user->id)->select(['id', 'meeting_id', 'user_id']),
            ])
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->whereHas('schedules', fn ($q) => $q->whereDate('meeting_date', '>=', $today))
            ->where(function ($q) use ($user) {
                $q->whereDoesntHave('invites')
                    ->orWhereHas('invites', fn ($inviteQ) => $inviteQ->where('user_id', $user->id));
            })
            ->get()
            ->sortBy(function ($meeting) {
                $schedule = $meeting->schedules->first();
                if (!$schedule?->meeting_date) {
                    return '9999-12-31 23:59:59';
                }
                return $schedule->meeting_date->format('Y-m-d').' '.($schedule->from_time ?? '23:59:59');
            })
            ->values();
    }

    public function downloadEventCertificate(Event $event)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $invite = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$invite || $invite->participation_status !== 'participated') {
            return redirect()->route('member.events.index')->with(
                'event_interest_error',
                'Your certificate is available only after the admin marks you as attended for this event.'
            );
        }

        if (!in_array($event->status, ['live', 'completed'], true)) {
            return redirect()->route('member.events.index')->with(
                'event_interest_error',
                'Certificate download opens after the event is Live/Completed and the office has uploaded the certificate file.'
            );
        }

        $event->loadMissing('dates');

        if (empty($event->template_pdf_path) || !Storage::disk('public')->exists($event->template_pdf_path)) {
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
        if (!$user) {
            abort(403);
        }

        if (!$event->is_active || $event->status === 'cancelled') {
            return back()
                ->with('event_interest_error', 'This event is not available for interest now.')
                ->with('event_interest_error_modal', true);
        }

        $event->loadMissing('dates');
        $endedReason = $this->eventInterestClosedReason($event);
        if ($endedReason !== null) {
            return back()
                ->with('event_interest_error', $endedReason)
                ->with('event_interest_error_modal', true);
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
                $closedReason = $this->eventInterestClosedReason($event);
                if ($closedReason !== null) {
                    throw new \RuntimeException($closedReason);
                }
                if ($event->seat_mode === 'limited' && $event->seat_limit !== null && $event->interested_count >= $event->seat_limit) {
                    throw new \RuntimeException('Seat limit reached for this event.');
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

                if (!$alreadyCountedViaPublic) {
                    $event->update([
                        'interested_count' => (int) $event->interested_count + 1,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()
                ->with('event_interest_error', $e->getMessage())
                ->with('event_interest_error_modal', true);
        } catch (QueryException $e) {
            return back()->with('event_interest_error', 'You have already submitted interest for this event.');
        }

        return back()
            ->with('event_interest_success', 'Thank you. Your event interest has been recorded.')
            ->with('event_interest_success_modal', true);
    }

    private function eventInterestClosedReason(Event $event): ?string
    {
        if ($event->status === 'completed') {
            return 'This event has ended.';
        }

        $lastDate = $event->dates->sortBy('event_date')->last();
        if (! $lastDate?->event_date) {
            return null;
        }

        $endTime = $lastDate->end_time ?: '23:59';
        $endAt = Carbon::parse($lastDate->event_date->format('Y-m-d').' '.$endTime);
        if (now()->greaterThan($endAt)) {
            return 'This event has ended.';
        }

        return null;
    }
}

