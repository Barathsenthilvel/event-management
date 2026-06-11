<?php

namespace App\Http\Controllers;

use App\Models\DonationPayment;
use App\Models\Meeting;
use App\Models\MemberSubscription;
use App\Models\Nomination;
use App\Models\PaymentTransaction;
use App\Models\Polling;
use App\Models\User;
use App\Services\GnatMailService;
use App\Services\MembershipLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GnatMailService $gnatMail,
    ) {}

    public function index()
    {
        $this->syncElapsedNominations();
        $this->syncElapsedPollings();
        $this->syncElapsedMeetings();

        $base = User::query();
        $totalMemberCount = (clone $base)->count();
        $activeMemberCount = (clone $base)
            ->where('profile_completed', true)
            ->where('is_approved', true)
            ->whereIn('membership_status', [
                MembershipLifecycleService::STATUS_ACTIVE,
                MembershipLifecycleService::STATUS_GRACE,
            ])
            ->count();

        $pendingApprovalCount = User::query()
            ->where('profile_completed', true)
            ->where('is_approved', false)
            ->count();

        $currentYear = (int) now()->year;
        $chartYears = [$currentYear, $currentYear - 1, $currentYear - 2];

        return view('admin.dashboard', [
            'featuredNomination' => Nomination::query()
                ->whereIn('status', ['active', 'closed'])
                ->latest('id')
                ->first(['id', 'title', 'status', 'is_active']),
            'featuredPolling' => Polling::query()
                ->where('publish_status', 'published')
                ->latest('id')
                ->first(['id', 'title', 'polling_status', 'is_active']),
            'activeMemberCount' => $activeMemberCount,
            'totalMemberCount' => $totalMemberCount,
            'memberGrowthPercent' => $this->computeMemberGrowthPercent(),
            'pendingApprovalCount' => $pendingApprovalCount,
            'upcomingMeeting' => $this->fetchUpcomingMeeting(),
            'donationChartByYear' => $this->buildDonationChartByYear($chartYears),
            'subscriptionChartByYear' => $this->buildSubscriptionChartByYear($chartYears),
            'chartYears' => $chartYears,
            'renewalMembers' => $this->fetchRenewalMembers(),
            'renewalFlaggedCount' => $this->countRenewalFlagged(),
            'recentPayments' => $this->fetchRecentPayments(),
        ]);
    }

    public function sendRenewalReminder(Request $request, MemberSubscription $subscription): JsonResponse|RedirectResponse
    {
        $subscription->loadMissing('user');
        $user = $subscription->user;

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => 'Member not found.'], 404);
            }

            return back()->with('error', 'Member not found.');
        }

        $this->gnatMail->sendRenewalReminder($user, $subscription);
        $subscription->forceFill(['renewal_reminder_sent_at' => now()])->save();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'message' => 'Renewal reminder sent successfully.']);
        }

        return back()->with('success', 'Renewal reminder sent to '.$user->name.'.');
    }

    private function computeMemberGrowthPercent(): ?float
    {
        $recentApproved = User::query()
            ->where('is_approved', true)
            ->where('profile_completed', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $priorApproved = User::query()
            ->where('is_approved', true)
            ->where('profile_completed', true)
            ->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])
            ->count();

        if ($priorApproved === 0) {
            return $recentApproved > 0 ? 100.0 : null;
        }

        return round((($recentApproved - $priorApproved) / $priorApproved) * 100, 1);
    }

    private function fetchUpcomingMeeting(): ?Meeting
    {
        $today = Carbon::today()->toDateString();

        return Meeting::query()
            ->with(['schedules' => fn ($q) => $q->orderBy('meeting_date')->orderBy('from_time')])
            ->where('is_active', true)
            ->whereIn('status', ['upcoming', 'live'])
            ->whereHas('schedules', fn ($q) => $q->whereDate('meeting_date', '>=', $today))
            ->get()
            ->sortBy(function (Meeting $meeting) {
                $schedule = $meeting->schedules->first();
                if (! $schedule?->meeting_date) {
                    return '9999-12-31 23:59:59';
                }

                return $schedule->meeting_date->format('Y-m-d').' '.($schedule->from_time ?? '23:59:59');
            })
            ->first();
    }

    /**
     * @param  list<int>  $years
     * @return array<string, list<float>>
     */
    private function buildDonationChartByYear(array $years): array
    {
        $result = $this->emptyChartYears($years);

        $rows = DonationPayment::query()
            ->where('status', 'successful')
            ->whereYear('created_at', '>=', min($years))
            ->selectRaw('YEAR(created_at) as chart_year, MONTH(created_at) as chart_month, SUM(amount) as total')
            ->groupBy('chart_year', 'chart_month')
            ->get();

        foreach ($rows as $row) {
            $yearKey = (string) $row->chart_year;
            if (isset($result[$yearKey])) {
                $result[$yearKey][(int) $row->chart_month - 1] = (float) $row->total;
            }
        }

        return $result;
    }

    /**
     * @param  list<int>  $years
     * @return array<string, list<float>>
     */
    private function buildSubscriptionChartByYear(array $years): array
    {
        $result = $this->emptyChartYears($years);

        $rows = PaymentTransaction::query()
            ->where('status', 'successful')
            ->where(function ($query) use ($years) {
                $query->whereYear('paid_at', '>=', min($years))
                    ->orWhere(function ($fallback) use ($years) {
                        $fallback->whereNull('paid_at')
                            ->whereYear('created_at', '>=', min($years));
                    });
            })
            ->selectRaw('YEAR(COALESCE(paid_at, created_at)) as chart_year, MONTH(COALESCE(paid_at, created_at)) as chart_month, SUM(amount) as total')
            ->groupBy('chart_year', 'chart_month')
            ->get();

        foreach ($rows as $row) {
            $yearKey = (string) $row->chart_year;
            if (isset($result[$yearKey])) {
                $result[$yearKey][(int) $row->chart_month - 1] = (float) $row->total;
            }
        }

        return $result;
    }

    /**
     * @param  list<int>  $years
     * @return array<string, list<float>>
     */
    private function emptyChartYears(array $years): array
    {
        $result = [];
        foreach ($years as $year) {
            $result[(string) $year] = array_fill(0, 12, 0.0);
        }

        return $result;
    }

    private function countRenewalFlagged(): int
    {
        $reminderDays = max(1, (int) config('gnat_mail.renewal_reminder_days_before', 14));
        $thresholdDate = now()->addDays($reminderDays)->toDateString();

        return MemberSubscription::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $thresholdDate)
            ->count();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{subscription: MemberSubscription, user: User, days_until: int, expiry_label: string, is_expired: bool, is_urgent: bool}>
     */
    private function fetchRenewalMembers()
    {
        $reminderDays = max(1, (int) config('gnat_mail.renewal_reminder_days_before', 14));
        $thresholdDate = now()->addDays($reminderDays)->toDateString();
        $today = now()->toDateString();

        return MemberSubscription::query()
            ->with(['user.designation'])
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $thresholdDate)
            ->orderBy('end_date')
            ->limit(10)
            ->get()
            ->map(function (MemberSubscription $subscription) use ($today) {
                $endDate = $subscription->end_date?->toDateString() ?? $today;
                $daysUntil = (int) Carbon::parse($today)->diffInDays(Carbon::parse($endDate), false);
                $isExpired = $daysUntil < 0;

                return [
                    'subscription' => $subscription,
                    'user' => $subscription->user,
                    'days_until' => $daysUntil,
                    'expiry_label' => $isExpired
                        ? 'Expired'
                        : ($daysUntil === 0 ? 'Today' : 'In '.$daysUntil.' '.str('Day')->plural($daysUntil)),
                    'is_expired' => $isExpired,
                    'is_urgent' => $isExpired || $daysUntil <= 7,
                ];
            })
            ->filter(fn (array $row) => $row['user'] !== null)
            ->values();
    }

    /**
     * @return list<array{type: string, type_label: string, name: string, description: string, amount: float, currency: string, paid_at: \Carbon\Carbon|null}>
     */
    private function fetchRecentPayments(): array
    {
        $subscriptionPayments = PaymentTransaction::query()
            ->with(['user:id,name', 'subscriptionPlan:id,subscription_type,payment_type'])
            ->where('status', 'successful')
            ->orderByDesc(DB::raw('COALESCE(paid_at, created_at)'))
            ->limit(10)
            ->get()
            ->map(function (PaymentTransaction $tx) {
                $planLabel = $tx->subscriptionPlan
                    ? trim(($tx->subscriptionPlan->subscription_type ?? '').' '.($tx->subscriptionPlan->payment_type ?? ''))
                    : 'Membership payment';

                return [
                    'type' => $tx->type === 'new' ? 'new' : 'renewal',
                    'type_label' => $tx->type === 'new' ? 'New' : 'Renewal',
                    'name' => $tx->user?->name ?? 'Member',
                    'description' => $planLabel,
                    'amount' => (float) $tx->amount,
                    'currency' => 'INR',
                    'paid_at' => $tx->paid_at ?? $tx->created_at,
                ];
            });

        $donationPayments = DonationPayment::query()
            ->with(['donation:id,purpose', 'user:id,name'])
            ->where('status', 'successful')
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(function (DonationPayment $payment) {
                return [
                    'type' => 'donation',
                    'type_label' => 'Donation',
                    'name' => $payment->user?->name ?? $payment->donor_name ?? 'Donor',
                    'description' => $payment->donation?->purpose ?? 'Voluntary contribution',
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency ?: 'INR',
                    'paid_at' => $payment->created_at,
                ];
            });

        return $subscriptionPayments
            ->concat($donationPayments)
            ->sortByDesc(fn (array $row) => $row['paid_at']?->timestamp ?? 0)
            ->take(10)
            ->values()
            ->all();
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

            if ($nomination->status === 'active' && ! $nomination->is_active) {
                $nomination->update(['is_active' => true]);
            }
        }
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

    private function syncElapsedMeetings(): void
    {
        $activeMeetings = Meeting::query()
            ->whereIn('status', ['upcoming', 'live'])
            ->with(['schedules:id,meeting_id,meeting_date,from_time,to_time'])
            ->get(['id', 'status', 'is_active']);

        $now = now();
        foreach ($activeMeetings as $meeting) {
            $schedule = $meeting->schedules->sortBy('meeting_date')->first();
            $lastSchedule = $meeting->schedules->sortBy('meeting_date')->last();
            if (! $schedule?->meeting_date || ! $schedule?->from_time || ! $lastSchedule?->meeting_date || ! $lastSchedule?->to_time) {
                continue;
            }

            $start = Carbon::parse($schedule->meeting_date->format('Y-m-d').' '.$schedule->from_time);
            $end = Carbon::parse($lastSchedule->meeting_date->format('Y-m-d').' '.$lastSchedule->to_time);

            if ($now->greaterThan($end)) {
                $meeting->update([
                    'status' => 'completed',
                    'is_active' => false,
                ]);

                continue;
            }

            if ($now->greaterThanOrEqualTo($start) && $meeting->status !== 'live') {
                $meeting->update(['status' => 'live']);

                continue;
            }

            if ($now->lt($start) && $meeting->status !== 'upcoming') {
                $meeting->update(['status' => 'upcoming']);
            }
        }
    }
}
