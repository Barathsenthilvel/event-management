<?php

namespace App\Http\Controllers;

use App\Models\AdminJob;
use App\Models\AdminJobApplication;
use App\Models\DonationPayment;
use App\Models\Event;
use App\Models\EventInterest;
use App\Models\EventInvite;
use App\Models\Hospital;
use App\Models\Meeting;
use App\Models\MeetingInvite;
use App\Models\MemberJobRequest;
use App\Models\MemberSavedJob;
use App\Models\MembershipSubscriptionSetting;
use App\Models\Nomination;
use App\Models\NominationEntry;
use App\Models\NominationPosition;
use App\Models\PaymentTransaction;
use App\Models\Polling;
use App\Models\PollingPosition;
use App\Models\PollingVote;
use App\Support\EventInterestErrorFlash;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $this->syncElapsedPollings();
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
                ->with(['positions' => fn ($q) => $q->with('candidates')])
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
                ->with(['positions' => fn ($q) => $q->with('winner')])
                ->where('publish_status', 'published')
                ->where('results_visible_to_members', true)
                ->where('polling_status', 'ends')
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
                    ->filter(fn ($position) => ! empty($position->winner_user_id) && $position->winner)
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

        $memberPollings = collect();
        $pollingVotedPositionIds = collect();
        $memberPollingVotes = collect();
        $pollingResultStats = [];
        if ($showFullMemberMenu && $user) {
            $portalPolling = $this->memberPollingsPortalData($user);
            $memberPollings = $portalPolling['memberPollings'];
            $pollingVotedPositionIds = $portalPolling['pollingVotedPositionIds'];
            $memberPollingVotes = $portalPolling['memberPollingVotes'];
            $pollingResultStats = $portalPolling['pollingResultStats'];
        }

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
            'memberPollings' => $memberPollings,
            'pollingVotedPositionIds' => $pollingVotedPositionIds,
            'memberPollingVotes' => $memberPollingVotes,
            'pollingResultStats' => $pollingResultStats,
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

    public function jobsPage(Request $request)
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Jobs are available after you have an active membership plan.'
                );
        }

        $q = trim((string) $request->query('q', ''));
        $tab = (string) $request->query('tab', 'search');
        if (! in_array($tab, ['search', 'applied', 'saved', 'need-job'], true)) {
            $tab = 'search';
        }
        $sort = (string) $request->query('sort', 'recent');
        if (! in_array($sort, ['recent', 'a-z', 'z-a'], true)) {
            $sort = 'recent';
        }

        $savedJobIds = MemberSavedJob::query()
            ->where('user_id', $user->id)
            ->pluck('job_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $appliedJobIds = AdminJobApplication::query()
            ->where('user_id', $user->id)
            ->pluck('job_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $jobsQuery = AdminJob::query()
            ->where('is_active', true)
            ->when($tab === 'search', fn ($query) => $query->where('listing_status', 'listed'))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('hospital', 'like', '%'.$q.'%')
                        ->orWhere('title', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhere('key_skills', 'like', '%'.$q.'%');
                });
            })
            ->when($tab === 'applied', function ($query) use ($user) {
                $query->whereHas('applications', fn ($appQ) => $appQ->where('user_id', $user->id));
            })
            ->when($tab === 'saved', function ($query) use ($user) {
                $query->whereHas('savedByMembers', fn ($saveQ) => $saveQ->where('user_id', $user->id));
            })
            ->orderByDesc('promote_front');

        if ($sort === 'a-z') {
            $jobsQuery->orderBy('title');
        } elseif ($sort === 'z-a') {
            $jobsQuery->orderByDesc('title');
        } else {
            $jobsQuery->latest('id');
        }

        $jobs = $tab === 'need-job'
            ? collect()
            : $jobsQuery->paginate(10)->withQueryString();

        $latestNeedJobRequest = MemberJobRequest::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        $hospitalLogos = Hospital::query()
            ->whereNotNull('logo_path')
            ->pluck('logo_path', 'name');

        return view('member.jobs', [
            'activeSubscription' => $user->activeSubscription,
            'jobs' => $jobs,
            'q' => $q,
            'tab' => $tab,
            'sort' => $sort,
            'appliedJobIds' => $appliedJobIds,
            'savedJobIds' => $savedJobIds,
            'appliedCount' => count($appliedJobIds),
            'savedCount' => count($savedJobIds),
            'latestNeedJobRequest' => $latestNeedJobRequest,
            'profileResumeAvailable' => (bool) ($user->educational_certificate_path),
            'hospitalLogos' => $hospitalLogos,
        ]);
    }

    public function applyJob(Request $request, AdminJob $job)
    {
        $user = Auth::user();
        $user?->refresh();
        $user?->loadMissing('designation');

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with(
                    'member_gate_error',
                    'Jobs are available after you have an active membership plan.'
                );
        }

        if (! $job->is_active || $job->listing_status !== 'listed') {
            return back()->with('job_apply_error', 'This job is not accepting applications right now.');
        }

        $validated = $request->validate([
            'resume_choice' => 'required|in:profile,upload',
            'resume' => 'required_if:resume_choice,upload|nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validated['resume_choice'] === 'profile') {
            if (empty($user->educational_certificate_path)) {
                return back()->with(
                    'job_apply_error',
                    'You do not have a profile resume on file. Upload a PDF below or add your educational certificate in your profile first.'
                );
            }
            $resumePath = $user->educational_certificate_path;
        } else {
            if (! $request->hasFile('resume')) {
                return back()->with('job_apply_error', 'Please choose a PDF resume to upload.');
            }
            $resumePath = $request->file('resume')->store('job-applications/resumes', 'public');
        }

        $existing = AdminJobApplication::query()
            ->where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('job_apply_error', 'You have already applied for this job.');
        }

        try {
            AdminJobApplication::query()->create([
                'job_id' => $job->id,
                'user_id' => $user->id,
                'submitted_at' => now(),
                'application_status' => 'pending',
                'resume_path' => $resumePath,
            ]);
        } catch (QueryException $e) {
            return back()->with('job_apply_error', 'Unable to submit your application right now. Please try again.');
        }

        return back()->with('job_apply_success', 'Application submitted successfully.');
    }

    public function toggleSavedJob(AdminJob $job)
    {
        $user = Auth::user();
        $user?->refresh();

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Jobs are available after you have an active membership plan.');
        }

        if (! $job->is_active || $job->listing_status !== 'listed') {
            return back()->with('job_apply_error', 'This job is not available right now.');
        }

        $existing = MemberSavedJob::query()
            ->where('job_id', $job->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('job_apply_success', 'Job removed from saved list.');
        }

        MemberSavedJob::query()->create([
            'job_id' => $job->id,
            'user_id' => $user->id,
        ]);

        return back()->with('job_apply_success', 'Job saved successfully.');
    }

    public function storeNeedJob(Request $request)
    {
        $user = Auth::user();
        $user?->refresh();

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Jobs are available after you have an active membership plan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:40',
            'email' => 'required|email|max:255',
            'qualification' => 'nullable|string|max:255',
            'position_looking_for' => 'nullable|string|max:255',
            'experience' => 'nullable|string|max:255',
            'details' => 'nullable|string|max:3000',
            'resume' => 'nullable|file|mimes:pdf|max:5120',
            'use_profile_resume' => 'nullable|boolean',
        ]);

        $resumePath = null;
        if ($request->boolean('use_profile_resume') && $user->educational_certificate_path) {
            $resumePath = $user->educational_certificate_path;
        } elseif ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('member-job-requests/resumes', 'public');
        }

        MemberJobRequest::query()->create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'mobile' => $validated['mobile'] ?? null,
            'email' => $validated['email'],
            'qualification' => $validated['qualification'] ?? null,
            'position_looking_for' => $validated['position_looking_for'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'details' => $validated['details'] ?? null,
            'resume_path' => $resumePath,
            'status' => 'new',
        ]);

        return redirect()
            ->route('member.jobs.index', ['tab' => 'need-job'])
            ->with('job_apply_success', 'Need Job form submitted successfully.');
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
        $this->syncElapsedPollings();
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

        $portal = $this->memberPollingsPortalData($user);

        return view('member.pollings', [
            'activeSubscription' => $user->activeSubscription,
            'memberPollings' => $portal['memberPollings'],
            'pollingVotedPositionIds' => $portal['pollingVotedPositionIds'],
            'memberPollingVotes' => $portal['memberPollingVotes'],
            'pollingResultStats' => $portal['pollingResultStats'],
        ]);
    }

    /**
     * Published pollings list, the member’s votes, and aggregated result stats (when the office has enabled results for members).
     *
     * @return array{
     *     memberPollings: \Illuminate\Support\Collection<int, Polling>,
     *     pollingVotedPositionIds: \Illuminate\Support\Collection<int, int>,
     *     memberPollingVotes: \Illuminate\Support\Collection<int, PollingVote>,
     *     pollingResultStats: array<int, array<int, array{winner_name: ?string, candidates: list<array<string, mixed>}>>
     * }
     */
    private function memberPollingsPortalData($user): array
    {
        $memberPollings = Polling::query()
            ->with(['positions' => fn ($q) => $q->with(['candidates', 'winner'])])
            ->where('publish_status', 'published')
            ->where(function ($query) {
                $query->where(function ($live) {
                    $live->where('is_active', true)
                        ->where('polling_status', 'live');
                })->orWhere(function ($ended) {
                    $ended->where('polling_status', 'ends')
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
            if (! $polling->results_visible_to_members) {
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

        return [
            'memberPollings' => $memberPollings,
            'pollingVotedPositionIds' => $pollingVotedPositionIds,
            'memberPollingVotes' => $memberPollingVotes,
            'pollingResultStats' => $pollingResultStats,
        ];
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

    private function syncElapsedPollings(): void
    {
        $pollings = Polling::query()
            ->where('publish_status', 'published')
            ->whereIn('polling_status', ['live', 'ends'])
            ->get(['id', 'polling_date', 'polling_date_to', 'polling_from', 'polling_to', 'polling_status', 'is_active']);

        $now = now();
        foreach ($pollings as $polling) {
            if (! $polling->polling_date || ! $polling->polling_from || ! $polling->polling_to) {
                continue;
            }
            $startDate = $polling->polling_date->format('Y-m-d');
            $endDate = ($polling->polling_date_to ?? $polling->polling_date)->format('Y-m-d');
            $start = Carbon::parse($startDate.' '.$polling->polling_from);
            $end = Carbon::parse($endDate.' '.$polling->polling_to);

            if ($now->greaterThan($end)) {
                $polling->update([
                    'polling_status' => 'ends',
                    'is_active' => false,
                ]);

                continue;
            }

            if ($now->greaterThanOrEqualTo($start) && $polling->polling_status !== 'live') {
                $polling->update([
                    'polling_status' => 'live',
                    'is_active' => true,
                ]);

                continue;
            }

            if (! $polling->is_active) {
                $polling->update(['is_active' => true]);
            }
        }
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

    public function respondToMeetingInvite(Request $request, Meeting $meeting)
    {
        $user = Auth::user();
        $user?->refresh();

        if (! $this->memberPortalUnlocked($user)) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Meetings are available after you have an active membership plan.');
        }

        $validated = $request->validate([
            'response' => 'required|in:interested,not_interested',
        ]);

        $invite = MeetingInvite::query()
            ->where('meeting_id', $meeting->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $invite) {
            return back()->with('event_interest_error', 'You are not invited for this meeting.');
        }

        $nextStatus = $validated['response'] === 'interested' ? 'interested' : 'not_participated';
        if (($invite->participation_status ?? null) === $nextStatus) {
            return back();
        }

        $invite->update([
            'participation_status' => $nextStatus,
            'attended_at' => null,
        ]);

        if ($validated['response'] === 'interested') {
            return back()
                ->with('event_interest_success', 'Thank you for letting us know. We have recorded your interest in this meeting.')
                ->with('event_interest_success_modal', true)
                ->with('event_interest_success_title', 'Thank you!')
                ->with('event_interest_success_variant', 'success');
        }

        return back()
            ->with('event_interest_success', 'Thank you. We have noted that you are not interested in this meeting.')
            ->with('event_interest_success_modal', true)
            ->with('event_interest_success_title', 'Response recorded')
            ->with('event_interest_success_variant', 'neutral');
    }

    private function fetchUpcomingMeetingsForMember($user)
    {
        $today = Carbon::today()->toDateString();

        return Meeting::query()
            ->with([
                'schedules' => fn ($q) => $q->orderBy('meeting_date')->orderBy('from_time'),
                'invites' => fn ($q) => $q->where('user_id', $user->id)->select(['id', 'meeting_id', 'user_id', 'participation_status']),
            ])
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->whereHas('schedules', fn ($q) => $q->whereDate('meeting_date', '>=', $today))
            ->whereHas('invites', fn ($inviteQ) => $inviteQ->where('user_id', $user->id))
            ->get()
            ->sortBy(function ($meeting) {
                $schedule = $meeting->schedules->first();
                if (! $schedule?->meeting_date) {
                    return '9999-12-31 23:59:59';
                }

                return $schedule->meeting_date->format('Y-m-d').' '.($schedule->from_time ?? '23:59:59');
            })
            ->values();
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

        if (! in_array($event->status, ['live', 'completed'], true)) {
            return redirect()->route('member.events.index')->with(
                'event_interest_error',
                'Certificate download opens after the event is Live/Completed and the office has uploaded the certificate file.'
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
            return $this->interestSubmitErrorResponse($request, [
                'event_interest_error_title' => 'Event registration unavailable',
                'event_interest_error' => 'This event is not available for interest now.',
            ]);
        }

        $event->loadMissing('dates');
        if ($event->isPastRegistrationDeadline()) {
            return $this->interestSubmitErrorResponse($request, EventInterestErrorFlash::eventEnded());
        }

        $alreadyInterested = EventInvite::query()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyInterested) {
            return $this->interestSubmitErrorResponse($request, [
                'event_interest_error_title' => 'Event registration unavailable',
                'event_interest_error' => 'You have already submitted interest for this event.',
            ]);
        }

        try {
            DB::transaction(function () use ($event, $user) {
                $event->refresh();
                if ($event->isPastRegistrationDeadline()) {
                    throw new \RuntimeException(EventInterestErrorFlash::ERR_ENDED);
                }
                if ($event->isAtSeatLimit()) {
                    throw new \RuntimeException(EventInterestErrorFlash::ERR_SEAT_LIMIT);
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
            return $this->interestSubmitErrorResponse($request, EventInterestErrorFlash::fromException($e));
        } catch (QueryException $e) {
            return $this->interestSubmitErrorResponse($request, [
                'event_interest_error_title' => 'Event registration unavailable',
                'event_interest_error' => 'You have already submitted interest for this event.',
            ]);
        }

        return $this->interestSubmitSuccessResponse($request);
    }

    /**
     * @param  array{event_interest_error_title?: string, event_interest_error: string}  $flash
     */
    private function interestSubmitErrorResponse(Request $request, array $flash): RedirectResponse|JsonResponse
    {
        $title = $flash['event_interest_error_title'] ?? 'Event registration unavailable';
        $body = $flash['event_interest_error'] ?? 'Unable to complete request.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'event_interest_error_title' => $title,
                'event_interest_error_html' => Str::markdown($body),
            ], 422);
        }

        return back()
            ->with('event_interest_error_title', $title)
            ->with('event_interest_error', $body)
            ->with('event_interest_error_modal', true);
    }

    private function interestSubmitSuccessResponse(Request $request): RedirectResponse|JsonResponse
    {
        $message = 'Thank you. Your event interest has been recorded.';

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
            ]);
        }

        return back()
            ->with('event_interest_success', $message)
            ->with('event_interest_success_modal', true);
    }
}
