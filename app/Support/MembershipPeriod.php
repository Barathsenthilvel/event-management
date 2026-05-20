<?php

namespace App\Support;

use App\Models\MemberSubscription;
use App\Models\MembershipSubscriptionSetting;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class MembershipPeriod
{
    /**
     * Normalize legacy / inconsistent payment_type values from settings or history rows.
     */
    public static function normalizePaymentType(mixed $paymentType): ?string
    {
        if ($paymentType === null || $paymentType === '') {
            return null;
        }

        if (is_array($paymentType)) {
            $paymentType = $paymentType[0] ?? null;
        }

        $raw = trim((string) $paymentType);
        if ($raw === '') {
            return null;
        }

        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded[0])) {
                $raw = trim((string) $decoded[0]);
            }
        }

        $normalized = strtolower(str_replace(['-', ' '], '_', $raw));

        return $normalized !== '' ? $normalized : null;
    }

    public static function monthsForPaymentType(mixed $paymentType): int
    {
        return match (self::normalizePaymentType($paymentType)) {
            'monthly' => 1,
            'bi_monthly', 'bimonthly' => 2,
            'quarterly' => 3,
            'half_yearly', 'halfyearly' => 6,
            'yearly' => 12,
            default => 1,
        };
    }

    /** End of the paid billing cycle (before grace days). */
    public static function billingEndDate(CarbonInterface $start, mixed $paymentType): Carbon
    {
        $periodStart = Carbon::parse($start)->startOfDay();
        $months = self::monthsForPaymentType($paymentType);

        return $periodStart->copy()->addMonthsNoOverflow($months)->startOfDay();
    }

    /** Last day of access: billing period + grace days (member may renew during grace). */
    public static function calculateEndDate(CarbonInterface $start, mixed $paymentType, int $graceDays = 0): Carbon
    {
        $end = self::billingEndDate($start, $paymentType);

        if ($graceDays > 0) {
            $end->addDays($graceDays);
        }

        return $end->startOfDay();
    }

    public static function graceDaysForPlan(?MembershipSubscriptionSetting $plan): int
    {
        return max(0, (int) ($plan?->grace_period ?? 0));
    }

    public static function resolvePeriodStart(
        MembershipSubscriptionSetting $plan,
        ?MemberSubscription $currentActive = null,
    ): Carbon {
        $today = Carbon::today();

        if ($plan->subscription_type !== 'Renewal' || $currentActive?->end_date === null) {
            return $today;
        }

        $previousEnd = Carbon::parse($currentActive->end_date)->startOfDay();

        if ($previousEnd->greaterThanOrEqualTo($today)) {
            return $previousEnd->copy()->addDay();
        }

        return $today;
    }

    public static function buildPeriod(
        MembershipSubscriptionSetting $plan,
        ?MemberSubscription $currentActive = null,
    ): array {
        $start = self::resolvePeriodStart($plan, $currentActive);
        $paymentType = self::normalizePaymentType($plan->payment_type);
        $graceDays = self::graceDaysForPlan($plan);
        $end = self::calculateEndDate($start, $paymentType, $graceDays);

        return [
            'start' => $start,
            'end' => $end,
            'payment_type' => $paymentType,
        ];
    }

    public static function formatDate(mixed $date): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        return Carbon::parse($date)->format('j F Y');
    }

    /** Membership is valid through end_date (inclusive, end of that calendar day). */
    public static function isValidThrough(mixed $endDate): bool
    {
        if ($endDate === null || $endDate === '') {
            return true;
        }

        return Carbon::parse($endDate)->endOfDay()->greaterThanOrEqualTo(now());
    }

    public static function expireElapsedForUser(int $userId): void
    {
        MemberSubscription::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', Carbon::today())
            ->update(['status' => 'expired']);
    }

    public static function expectedEndDateForSubscription(MemberSubscription $subscription): ?Carbon
    {
        if ($subscription->start_date === null) {
            return null;
        }

        $subscription->loadMissing('plan');
        $graceDays = (int) ($subscription->plan?->grace_period ?? 0);

        return self::calculateEndDate(
            Carbon::parse($subscription->start_date),
            $subscription->payment_type,
            $graceDays,
        );
    }

    /**
     * Correct subscriptions stored with the old 12-month fallback for short billing cycles.
     */
    public static function repairEndDateIfMisaligned(MemberSubscription $subscription): bool
    {
        $expected = self::expectedEndDateForSubscription($subscription);
        if ($expected === null || $subscription->end_date === null) {
            return false;
        }

        $actual = Carbon::parse($subscription->end_date)->startOfDay();
        if ($actual->equalTo($expected)) {
            return false;
        }

        $monthsStored = max(1, (int) $subscription->start_date->diffInMonths($actual));
        $monthsExpected = self::monthsForPaymentType($subscription->payment_type);

        if ($monthsStored >= 11 && $monthsExpected < 11) {
            $subscription->forceFill(['end_date' => $expected])->save();

            return true;
        }

        return false;
    }
}
