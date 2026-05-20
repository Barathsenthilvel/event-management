<?php

namespace App\Services;

use App\Models\MemberSubscription;
use App\Models\User;
use App\Support\MembershipPeriod;
use Carbon\Carbon;

class MembershipLifecycleService
{
    public const STATUS_NONE = 'none';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_GRACE = 'grace';

    public const STATUS_INACTIVE = 'inactive';

    public const INACTIVE_GRACE_EXPIRED = 'grace_expired';

    public const INACTIVE_ADMIN = 'admin_manual';

    public const INACTIVE_LONG_LAPSE = 'subscription_lapsed';

    /** @return list<string> */
    public static function inactiveTypeOptions(): array
    {
        return [
            self::INACTIVE_GRACE_EXPIRED => 'Grace period ended (automatic)',
            self::INACTIVE_ADMIN => 'Marked inactive by admin',
            self::INACTIVE_LONG_LAPSE => 'No renewal (extended lapse)',
        ];
    }

    /**
     * Paid period ended but grace days still apply.
     */
    /** Block renewal only during the paid billing period (not during grace). */
    public static function renewalBlockedDuringPaidPeriod(?MemberSubscription $subscription): bool
    {
        return self::subscriptionPhase($subscription) === self::STATUS_ACTIVE;
    }

    public static function subscriptionPhase(?MemberSubscription $subscription): string
    {
        if ($subscription === null || $subscription->status !== 'active') {
            return 'none';
        }

        if ($subscription->start_date === null || $subscription->end_date === null) {
            return MembershipPeriod::isValidThrough($subscription->end_date) ? self::STATUS_ACTIVE : 'none';
        }

        $today = Carbon::today();
        $billingEnd = MembershipPeriod::billingEndDate(
            Carbon::parse($subscription->start_date),
            $subscription->payment_type,
        );
        $accessEnd = Carbon::parse($subscription->end_date)->startOfDay();

        if ($today->greaterThan($accessEnd)) {
            return 'none';
        }

        if ($today->greaterThan($billingEnd)) {
            return self::STATUS_GRACE;
        }

        return self::STATUS_ACTIVE;
    }

    public function syncUser(User $user): void
    {
        MembershipPeriod::expireElapsedForUser((int) $user->id);
        $user->refresh();

        $subscription = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', Carbon::today());
            })
            ->latest('id')
            ->first();

        if ($subscription) {
            $phase = self::subscriptionPhase($subscription);
            $status = $phase === self::STATUS_GRACE ? self::STATUS_GRACE : self::STATUS_ACTIVE;

            if ($user->membership_status !== $status || $user->membership_inactive_type !== null) {
                $user->forceFill([
                    'membership_status' => $status,
                    'membership_inactive_type' => null,
                ])->save();
            }

            return;
        }

        $lastSub = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if ($lastSub && $lastSub->end_date && Carbon::parse($lastSub->end_date)->startOfDay()->lessThan(Carbon::today())) {
            if ($user->membership_status !== self::STATUS_INACTIVE) {
                $user->forceFill([
                    'membership_status' => self::STATUS_INACTIVE,
                    'membership_inactive_type' => $user->membership_inactive_type ?: self::INACTIVE_GRACE_EXPIRED,
                ])->save();
            }

            return;
        }

        if (! $user->profile_completed || ! $user->is_approved) {
            return;
        }

        if ($user->membership_status !== self::STATUS_NONE && $user->membership_status !== self::STATUS_INACTIVE) {
            $user->forceFill([
                'membership_status' => self::STATUS_NONE,
                'membership_inactive_type' => null,
            ])->save();
        }
    }

    public function markInactiveByAdmin(User $user, string $inactiveType): void
    {
        MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $user->forceFill([
            'membership_status' => self::STATUS_INACTIVE,
            'membership_inactive_type' => $inactiveType,
        ])->save();
    }

    public function markActiveByAdmin(User $user): void
    {
        $user->forceFill([
            'membership_status' => $user->activeSubscription()->exists() ? self::STATUS_ACTIVE : self::STATUS_NONE,
            'membership_inactive_type' => null,
        ])->save();
    }
}
