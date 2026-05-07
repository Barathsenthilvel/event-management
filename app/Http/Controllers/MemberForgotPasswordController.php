<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class MemberForgotPasswordController extends Controller
{
    public function create()
    {
        return view('member.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim((string) $request->input('email')));

        if (! User::query()->where('email', $email)->exists()) {
            return redirect()
                ->back()
                ->withInput(['email' => $request->input('email')])
                ->with('fp_unknown_email', true);
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return back()->with('status', __($status));
    }
}
