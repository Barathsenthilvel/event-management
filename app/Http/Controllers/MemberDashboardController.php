<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profileIncomplete = !$user?->profile_completed
            || empty($user?->first_name)
            || empty($user?->last_name)
            || empty($user?->mobile)
            || empty($user?->dob)
            || empty($user?->gender)
            || empty($user?->qualification)
            || empty($user?->blood_group)
            || empty($user?->rnrm_number_with_date)
            || empty($user?->college_name)
            || empty($user?->door_no)
            || empty($user?->locality_area)
            || empty($user?->state)
            || empty($user?->pin_code)
            || empty($user?->council_state)
            || empty($user?->educational_certificate_path)
            || empty($user?->aadhar_card_path)
            || empty($user?->passport_photo_path);

        return view('member.dashboard', [
            'profileIncomplete' => $profileIncomplete,
            'activeSubscription' => $user?->activeSubscription,
            'transactions' => PaymentTransaction::query()
                ->with('subscriptionPlan')
                ->where('user_id', $user?->id)
                ->latest('id')
                ->limit(10)
                ->get(),
        ]);
    }
}

