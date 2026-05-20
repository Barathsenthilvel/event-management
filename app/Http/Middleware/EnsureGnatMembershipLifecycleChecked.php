<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\GnatMailService;
use App\Services\MembershipLifecycleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * On each member portal request: renewal reminder, natural expiry (m07 + admin),
 * and inactive notice — all normal request-driven mail (no cron).
 */
class EnsureGnatMembershipLifecycleChecked
{
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = Auth::user();

        if ($authUser instanceof User) {
            app(MembershipLifecycleService::class)->syncUser($authUser);
            app(GnatMailService::class)->runMembershipLifecycleForUser($authUser);
        }

        return $next($request);
    }
}
