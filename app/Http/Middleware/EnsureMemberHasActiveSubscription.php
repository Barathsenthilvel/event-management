<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberHasActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('member.login');
        }

        if (!$user->profile_completed || !$user->is_approved) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Complete your profile and wait for admin approval before using this area.');
        }

        if (!$user->activeSubscription()->exists()) {
            return redirect()
                ->route('member.dashboard')
                ->with('member_gate_error', 'Choose and pay for a membership plan on your dashboard to unlock this area.');
        }

        return $next($request);
    }
}
