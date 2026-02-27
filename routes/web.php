<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DefaultSettingsController;
use App\Http\Controllers\EBookController;

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // Admin Users Management
        Route::resource('admins', AdminController::class)->names([
            'index' => 'admin.admins.index',
            'create' => 'admin.admins.create',
            'store' => 'admin.admins.store',
            'show' => 'admin.admins.show',
            'edit' => 'admin.admins.edit',
            'update' => 'admin.admins.update',
            'destroy' => 'admin.admins.destroy',
        ]);
        
        // Roles & Permissions Management
        Route::resource('roles', RoleController::class)->names([
            'index' => 'admin.roles.index',
            'create' => 'admin.roles.create',
            'store' => 'admin.roles.store',
            'show' => 'admin.roles.show',
            'edit' => 'admin.roles.edit',
            'update' => 'admin.roles.update',
            'destroy' => 'admin.roles.destroy',
        ]);
        
        Route::get('/roles/{role}/permissions-data', [RoleController::class, 'getPermissionsData'])
            ->name('admin.roles.permissions-data');
        Route::post('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->name('admin.roles.update-permissions');

        // Menu Management
        Route::resource('menus', MenuController::class)->only(['index', 'store', 'update', 'destroy'])->names([
            'index' => 'admin.menus.index',
            'store' => 'admin.menus.store',
            'update' => 'admin.menus.update',
            'destroy' => 'admin.menus.destroy',
        ]);

        // Settings > Default Settings
        Route::get('/settings/default-settings', [DefaultSettingsController::class, 'index'])->name('admin.settings.default-settings');
        Route::post('/settings/default-settings', [DefaultSettingsController::class, 'store'])->name('admin.settings.default-settings.store');
        Route::post('/settings/default-settings/{defaultSetting}/set-default', [DefaultSettingsController::class, 'setDefault'])->name('admin.settings.default-settings.set-default');
        Route::delete('/settings/default-settings/{defaultSetting}', [DefaultSettingsController::class, 'destroy'])->name('admin.settings.default-settings.destroy');

        // E-Books Management (UI only for now)
        Route::resource('e-books', EBookController::class)->only(['index', 'create', 'store'])->names([
            'index' => 'admin.ebooks.index',
            'create' => 'admin.ebooks.create',
            'store' => 'admin.ebooks.store',
        ]);
    });
});

Route::get('/', function () {
    return redirect()->route('admin.login');
});
