<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\RoleMenuPermission;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
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

            $admin = Auth::guard('admin')->user();
            if ($admin && ! $admin->is_super_admin) {
                $roleIds = $admin->roles()->pluck('roles.id');
                $allowedMenuIds = RoleMenuPermission::query()
                    ->whereIn('role_id', $roleIds)
                    ->where(function ($q) {
                        $q->where('can_view', true)
                            ->orWhere('can_create', true)
                            ->orWhere('can_edit', true)
                            ->orWhere('can_delete', true);
                    })
                    ->pluck('menu_id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $sidebarMenus = $sidebarMenus
                    ->map(function ($menu) use ($allowedMenuIds) {
                        $allowedChildren = $menu->children
                            ->filter(fn ($child) => $allowedMenuIds->contains((int) $child->id))
                            ->values();

                        $menu->setRelation('children', $allowedChildren);
                        $parentAllowed = $allowedMenuIds->contains((int) $menu->id);

                        return $parentAllowed || $allowedChildren->isNotEmpty() ? $menu : null;
                    })
                    ->filter()
                    ->values();
            }

            $view->with('sidebarMenus', $sidebarMenus);
        });
    }
}
