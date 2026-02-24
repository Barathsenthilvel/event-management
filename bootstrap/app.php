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
            return route('admin.login'); // Default to admin login
        });

        // Configure AuthenticationException redirect
        \Illuminate\Auth\AuthenticationException::redirectUsing(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            return route('admin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            // Redirect admin routes to admin login when session expires
            if ($request->is('admin/*')) {
                return redirect()->guest(route('admin.login'));
            }

            // Handle JSON requests
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // For other routes, let Laravel handle with default behavior
            return null;
        });
    })->create();
