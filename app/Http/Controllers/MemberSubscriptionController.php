<?php

namespace App\Http\Controllers;

use App\Models\MembershipSubscriptionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberSubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $plans = MembershipSubscriptionSetting::query()
            ->where('is_active', true)
            ->orderBy('subscription_type')
            ->orderBy('payment_type')
            ->get();

        return view('member.subscription.index', [
            'user' => $user,
            'plans' => $plans,
            'settingsCount' => $plans->count(),
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'membership_setting_id' => ['required', 'integer', 'exists:membership_subscription_settings,id'],
        ]);

        $plan = MembershipSubscriptionSetting::query()
            ->where('id', $data['membership_setting_id'])
            ->where('is_active', true)
            ->first();

        if (!$plan) {
            return redirect()->route('member.subscription.index')
                ->with('error', 'Selected subscription plan is not available.');
        }

        $registrationFee = 0.0;
        if ($plan->subscription_type === 'New' && $plan->registration_fee_enabled) {
            $registrationFee = (float) $plan->registration_fee;
        }

        $payableAmount = (float) $plan->membership_fee + $registrationFee;

        $request->session()->put('member.selected_membership_setting_id', $plan->id);
        $request->session()->put('member.selected_membership_payable_amount', $payableAmount);

        return redirect()->route('member.subscription.checkout.show');
    }

    public function showCheckout(Request $request)
    {
        $id = $request->session()->get('member.selected_membership_setting_id');
        if (!$id) {
            return redirect()->route('member.subscription.index')
                ->with('error', 'Please select a subscription plan first.');
        }

        $plan = MembershipSubscriptionSetting::query()->find($id);
        if (!$plan) {
            $request->session()->forget([
                'member.selected_membership_setting_id',
                'member.selected_membership_payable_amount',
            ]);
            return redirect()->route('member.subscription.index')
                ->with('error', 'Selected subscription plan is no longer available.');
        }

        $payableAmount = (float) $request->session()->get(
            'member.selected_membership_payable_amount',
            (float) $plan->membership_fee
        );

        return view('member.subscription.checkout', [
            'user' => Auth::user(),
            'plan' => $plan,
            'payableAmount' => $payableAmount,
        ]);
    }
}
