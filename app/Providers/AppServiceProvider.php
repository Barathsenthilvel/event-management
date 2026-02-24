<?php

namespace App\Providers;

use App\Models\Menu;
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
