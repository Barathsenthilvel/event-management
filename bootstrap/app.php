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
    })->create();
