<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('member.password.reset', [
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ], false));
        });

        View::composer('admin.layouts.app', function ($view) {
            $sidebarMenus = Menu::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function ($q) {
                    $q->where('is_active', true)->orderBy('order');
                }])
                ->orderBy('order')
                ->get();
            $view->with('sidebarMenus', $sidebarMenus);
        });
    }
}
