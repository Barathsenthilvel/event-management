<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\DefaultSettingsController;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MemberSubscriptionController;
use App\Http\Controllers\AdminMemberApprovalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminSubscriptionController;

// Public marketing site
Route::get('/', [HomeController::class, 'index'])->name('home');

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

        // E-Books Management
        Route::resource('e-books', EBookController::class)->except(['show'])->names([
            'index' => 'admin.ebooks.index',
            'create' => 'admin.ebooks.create',
            'store' => 'admin.ebooks.store',
            'edit' => 'admin.ebooks.edit',
            'update' => 'admin.ebooks.update',
            'destroy' => 'admin.ebooks.destroy',
        ]);
        Route::post('/e-books/{e_book}/toggle-promote', [EBookController::class, 'togglePromote'])->name('admin.ebooks.toggle-promote');
        Route::post('/e-books/{e_book}/toggle-status', [EBookController::class, 'toggleStatus'])->name('admin.ebooks.toggle-status');

        // Membership Subscription Settings
        Route::get('/membershipmodule', [MembershipController::class, 'index'])->name('admin.memberships.index');
        Route::post('/memberships', [MembershipController::class, 'store'])->name('admin.memberships.store');
        Route::put('/memberships/{id}', [MembershipController::class, 'update'])->name('admin.memberships.update');
        Route::delete('/memberships/{id}', [MembershipController::class, 'destroy'])->name('admin.memberships.destroy');

        // Member Approvals
        Route::get('/members/pending-approvals', [AdminMemberApprovalController::class, 'index'])
            ->name('admin.members.pending-approvals.index');
        Route::post('/members/{user}/approve', [AdminMemberApprovalController::class, 'approve'])
            ->name('admin.members.pending-approvals.approve');
        Route::post('/members/{user}/reject', [AdminMemberApprovalController::class, 'reject'])
            ->name('admin.members.pending-approvals.reject');

        // Subscription List (view only)
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])
            ->name('admin.subscriptions.index');

        // Events Management
        Route::get('/events', [EventController::class, 'index'])->name('admin.events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('admin.events.create');
        Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('admin.events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');
        Route::post('/events/{event}/cancel', [EventController::class, 'cancel'])->name('admin.events.cancel');
        Route::post('/events/{event}/toggle-promote', [EventController::class, 'togglePromote'])->name('admin.events.toggle-promote');
        Route::post('/events/{event}/toggle-display', [EventController::class, 'toggleDisplay'])->name('admin.events.toggle-display');
        Route::post('/events/{event}/send-reminder', [EventController::class, 'sendReminder'])->name('admin.events.send-reminder');
        Route::get('/events/{event}/invite', [EventController::class, 'inviteForm'])->name('admin.events.invite');
        Route::post('/events/{event}/invite', [EventController::class, 'inviteStore'])->name('admin.events.invite.store');
    });
});

// Member (User Site) Routes
Route::prefix('member')->name('member.')->group(function () {
    Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('login.store');

    Route::get('/register', [MemberAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [MemberAuthController::class, 'register'])->name('register.store');

    Route::get('/otp', [MemberAuthController::class, 'showOtpForm'])->name('otp');
    Route::post('/otp', [MemberAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/resend-otp', [MemberAuthController::class, 'resendOtp'])->name('otp.resend');

    Route::post('/logout', [MemberAuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [MemberProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
        Route::get('/subscription', [MemberSubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/checkout', [MemberSubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::get('/subscription/checkout', [MemberSubscriptionController::class, 'showCheckout'])->name('subscription.checkout.show');
        Route::post('/subscription/order', [MemberSubscriptionController::class, 'createOrder'])->name('subscription.order');
        Route::post('/subscription/verify', [MemberSubscriptionController::class, 'verifyPayment'])->name('subscription.verify');
        Route::get('/subscription/invoice/{id}', [MemberSubscriptionController::class, 'downloadInvoice'])->name('subscription.invoice');
    });
});

