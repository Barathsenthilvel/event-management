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
            'subscription_type'         => 'required|in:New,Renewal',
            'membership_fee'            => 'required|numeric|min:0',
            'registration_fee'          => 'required_if:subscription_type,New|nullable|numeric|min:0',
            'registration_fee_enabled'  => 'nullable|boolean',
            'payment_type'              => 'required|in:monthly,bi_monthly,quarterly,half_yearly,yearly',
            'grace_period'              => 'nullable|integer|min:0',
            'discount_based_on_payment' => 'nullable|boolean',
        ];
    }

    private function validationMessages(): array
    {
        return [
            'payment_type.required' => 'Please select a payment type.',
            'payment_type.in'       => 'Invalid payment type selected.',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        $validated['registration_fee_enabled']  = $request->boolean('registration_fee_enabled');
        $validated['discount_based_on_payment'] = $request->boolean('discount_based_on_payment');
        $validated['grace_period']              = $request->input('grace_period', 0);

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

        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        $validated['registration_fee_enabled']  = $request->boolean('registration_fee_enabled');
        $validated['discount_based_on_payment'] = $request->boolean('discount_based_on_payment');
        $validated['grace_period']              = $request->input('grace_period', 0);

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
