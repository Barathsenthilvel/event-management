<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $activeSubscription = \App\Models\PaymentTransaction::with('subscriptionPlan')
            ->where('user_id', $user->id)
            ->where('status', 'successful')
            ->latest()
            ->first();

        return view('member.profile.edit', [
            'user' => $user,
            'activeSubscription' => $activeSubscription,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:30'],
            'dob' => ['required', 'date'],
            'gender' => ['required', 'string', 'max:30'],
            'qualification' => ['required', 'string', 'max:120'],
            'blood_group' => ['required', 'string', 'max:30'],
            'rnrm_number_with_date' => ['required', 'string', 'max:120'],
            'college_name' => ['required', 'string', 'max:190'],
            'door_no' => ['required', 'string', 'max:80'],
            'locality_area' => ['required', 'string', 'max:190'],
            'state' => ['required', 'string', 'max:120'],
            'pin_code' => ['required', 'string', 'max:20'],
            'council_state' => ['required', 'string', 'max:120'],
            'currently_working' => ['nullable', 'string', 'max:190'],
            'educational_certificate' => [empty($user?->educational_certificate_path) ? 'required' : 'nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,webp'],
            'aadhar_card' => [empty($user?->aadhar_card_path) ? 'required' : 'nullable', 'file', 'max:5120', 'mimes:pdf,jpg,jpeg,png,webp'],
            'passport_photo' => [empty($user?->passport_photo_path) ? 'required' : 'nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->mobile = $data['mobile'];
        $user->name = trim($data['first_name'] . ' ' . $data['last_name']);

        $user->dob = $data['dob'];
        $user->gender = $data['gender'];
        $user->qualification = $data['qualification'];
        $user->blood_group = $data['blood_group'];
        $user->rnrm_number_with_date = $data['rnrm_number_with_date'];
        $user->college_name = $data['college_name'];
        $user->door_no = $data['door_no'];
        $user->locality_area = $data['locality_area'];
        $user->state = $data['state'];
        $user->pin_code = $data['pin_code'];
        $user->council_state = $data['council_state'];
        $user->currently_working = $data['currently_working'] ?? null;

        $dir = 'member-documents/' . $user->id;

        if ($request->hasFile('educational_certificate')) {
            $user->educational_certificate_path = $request->file('educational_certificate')->store($dir, 'public');
        }
        if ($request->hasFile('aadhar_card')) {
            $user->aadhar_card_path = $request->file('aadhar_card')->store($dir, 'public');
        }
        if ($request->hasFile('passport_photo')) {
            $user->passport_photo_path = $request->file('passport_photo')->store($dir, 'public');
        }

        if (!empty($data['new_password'])) {
            // User model casts password as "hashed", so plain value is stored securely.
            $user->password = $data['new_password'];
        }

        $user->profile_completed = $this->isProfileComplete($user);
        $user->save();

        if ($user->profile_completed && !$user->is_approved) {
            return redirect()
                ->route('member.dashboard')
                ->with('approval_pending_modal', true)
                ->with('success', 'Profile submitted successfully.');
        }

        return redirect()->route('member.dashboard')->with('success', 'Profile updated successfully.');
    }

    private function isProfileComplete($user): bool
    {
        return !empty($user?->first_name)
            && !empty($user?->last_name)
            && !empty($user?->mobile)
            && !empty($user?->dob)
            && !empty($user?->gender)
            && !empty($user?->qualification)
            && !empty($user?->blood_group)
            && !empty($user?->rnrm_number_with_date)
            && !empty($user?->college_name)
            && !empty($user?->door_no)
            && !empty($user?->locality_area)
            && !empty($user?->state)
            && !empty($user?->pin_code)
            && !empty($user?->council_state)
            && !empty($user?->educational_certificate_path)
            && !empty($user?->aadhar_card_path)
            && !empty($user?->passport_photo_path);
    }
}

