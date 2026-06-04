<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'member.subscribed' => \App\Http\Middleware\EnsureMemberHasActiveSubscription::class,
            'gnat.membership.lifecycle' => \App\Http\Middleware\EnsureGnatMembershipLifecycleChecked::class,
        ]);

        // Configure authentication redirect for admin guard
        \Illuminate\Auth\Middleware\Authenticate::redirectUsing(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('member/*')) {
                return route('member.login');
            }

            return route('home');
        });

        // Configure AuthenticationException redirect
        \Illuminate\Auth\AuthenticationException::redirectUsing(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('member/*')) {
                return route('member.login');
            }

            return route('home');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page expired.'], 419);
            }

            return redirect('/')->with('error', 'Your session expired. Please try again.');
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            // "Page Expired" (419) due to CSRF/session timeout
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Page expired.'], 419);
            }

            return redirect('/')->with('error', 'Your session expired. Please try again.');
        });

        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'The uploaded file is too large. Maximum size is 1 MB.'], 413);
            }

            if ($request->is('admin/home-banners') && $request->isMethod('POST')) {
                return redirect()->route('admin.home-banners.create', ['upload_error' => 1]);
            }

            if ($request->is('admin/home-banners/*') && in_array($request->method(), ['PUT', 'PATCH'], true)) {
                $id = $request->route('home_banner');

                if ($id) {
                    return redirect()->route('admin.home-banners.edit', ['home_banner' => $id, 'upload_error' => 1]);
                }
            }

            $referer = $request->headers->get('referer');

            if ($referer) {
                return redirect()->to($referer . (str_contains($referer, '?') ? '&' : '?') . 'upload_error=1');
            }

            return redirect()->route('admin.home-banners.index')->with('error', 'The uploaded file is too large. Maximum size is 1 MB.');
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            // Redirect admin routes to admin login when session expires
            if ($request->is('admin/*')) {
                return redirect()->guest(route('admin.login'));
            }

            if ($request->is('member/*')) {
                return redirect()->guest(route('member.login'));
            }

            // Handle JSON requests
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // For other routes, let Laravel handle with default behavior
            return null;
        });
    })
    ->create();
