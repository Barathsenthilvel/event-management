<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $profileIncomplete = !$user?->profile_completed
            || empty($user?->first_name)
            || empty($user?->last_name)
            || empty($user?->mobile);

        return view('member.dashboard', [
            'profileIncomplete' => $profileIncomplete,
        ]);
    }
}

