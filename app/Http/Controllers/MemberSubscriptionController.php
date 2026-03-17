<?php

namespace App\Http\Controllers;

use App\Models\MembershipSubscriptionSetting;
use Illuminate\Support\Facades\Auth;

class MemberSubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $settings = MembershipSubscriptionSetting::query()
            ->where('is_active', true)
            ->get()
            ->groupBy('subscription_type');

        $rows = collect(['New', 'Renewal'])->map(function (string $type) use ($settings) {
            $typeSettings = $settings->get($type, collect())->keyBy('payment_type');

            $getAmount = function (string $paymentType) use ($typeSettings): ?float {
                $s = $typeSettings->get($paymentType);
                if (!$s) return null;
                return (float) $s->membership_fee;
            };

            $membershipMonthly = $getAmount('monthly');
            $membershipBiMonthly = $getAmount('bi_monthly');
            $membershipQuarterly = $getAmount('quarterly');
            $membershipHalfYearly = $getAmount('half_yearly');
            $membershipYearly = $getAmount('yearly');

            $base = $typeSettings->first();
            $registrationFee = ($type === 'New' && $base && $base->registration_fee_enabled) ? (float) $base->registration_fee : 0.0;
            $subscriptionFee = $membershipMonthly;

            $yearlyTotal = $membershipYearly !== null ? $membershipYearly + $registrationFee : null;

            return [
                'subscription_type' => $type,
                'subscription_fee' => $subscriptionFee,
                'registration_fee' => $type === 'New' ? $registrationFee : null,
                'monthly' => $membershipMonthly,
                'bi_monthly' => $membershipBiMonthly,
                'quarterly' => $membershipQuarterly,
                'half_yearly' => $membershipHalfYearly,
                'yearly' => $membershipYearly,
                'total' => $yearlyTotal,
            ];
        });

        return view('member.subscription.index', [
            'user' => $user,
            'rows' => $rows,
        ]);
    }
}

