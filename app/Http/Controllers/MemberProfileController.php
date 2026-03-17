<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberProfileController extends Controller
{
    public function edit()
    {
        return view('member.profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'max:30'],
        ]);

        $user = Auth::user();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->mobile = $data['mobile'];
        $user->name = trim($data['first_name'] . ' ' . $data['last_name']);
        $user->profile_completed = true;
        $user->save();

        return redirect()->route('member.dashboard')->with('success', 'Profile updated successfully.');
    }
}

