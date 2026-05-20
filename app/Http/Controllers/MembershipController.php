<?php

namespace App\Http\Controllers;

use App\Models\MembershipSubscriptionSetting;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index()
    {
        $settings = MembershipSubscriptionSetting::latest()->get();

        return view('admin.memberships.index', compact('settings'));
    }

    private function validationRules(): array
    {
        return [
            'subscription_type' => 'required|in:New,Renewal',
            'membership_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required_if:subscription_type,New|nullable|numeric|min:0',
            'registration_fee_enabled' => 'nullable|boolean',
            'payment_type' => 'required|in:monthly,bi_monthly,quarterly,half_yearly,yearly',
            'grace_period' => 'nullable|integer|min:0|max:365',
            'discount_based_on_payment' => 'nullable|boolean',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'payment_type.required' => 'Please select a payment type.',
            'payment_type.in' => 'Invalid payment type selected.',
            'grace_period.integer' => 'Grace period must be a whole number of days (0–365).',
            'grace_period.max' => 'Grace period cannot exceed 365 days.',
        ];
    }

    private function prepareMembershipRequest(Request $request): void
    {
        $grace = $request->input('grace_period');
        $grace = ($grace === null || $grace === '') ? 0 : (int) $grace;

        $type = (string) $request->input('subscription_type', '');

        $merge = [
            'grace_period' => $grace,
            'registration_fee_enabled' => $request->boolean('registration_fee_enabled'),
            'discount_based_on_payment' => $request->boolean('discount_based_on_payment'),
        ];

        if ($type === 'Renewal') {
            $merge['registration_fee'] = 0;
            $merge['registration_fee_enabled'] = false;
        } else {
            $reg = $request->input('registration_fee');
            $merge['registration_fee'] = ($reg === null || $reg === '') ? 0 : $reg;
        }

        $request->merge($merge);
    }

    public function store(Request $request)
    {
        $this->prepareMembershipRequest($request);

        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        if (($validated['subscription_type'] ?? null) !== 'New') {
            $validated['registration_fee'] = 0;
            $validated['registration_fee_enabled'] = false;
        } else {
            $validated['registration_fee'] = (float) ($validated['registration_fee'] ?? 0);
        }

        MembershipSubscriptionSetting::create($validated);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership subscription setting created successfully.');
    }

    public function update(Request $request, $id)
    {
        $setting = MembershipSubscriptionSetting::findOrFail($id);

        $this->prepareMembershipRequest($request);

        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        if (($validated['subscription_type'] ?? null) !== 'New') {
            $validated['registration_fee'] = 0;
            $validated['registration_fee_enabled'] = false;
        } else {
            $validated['registration_fee'] = (float) ($validated['registration_fee'] ?? 0);
        }

        $setting->update($validated);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership subscription setting updated successfully.');
    }

    public function destroy($id)
    {
        MembershipSubscriptionSetting::findOrFail($id)->delete();

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership subscription setting deleted.');
    }
}
