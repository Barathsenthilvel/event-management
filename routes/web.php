<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDesignationController;
use App\Http\Controllers\AdminDonationPaymentController;
use App\Http\Controllers\AdminHomeBlogController;
use App\Http\Controllers\AdminHomeGalleryController;
use App\Http\Controllers\AdminHomeBannerController;
use App\Http\Controllers\AdminJobController;
use App\Http\Controllers\AdminMemberApprovalController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefaultSettingsController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonationPaymentController;
use App\Http\Controllers\EBookController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventInterestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MemberAuthController;
use App\Http\Controllers\MemberDashboardController;
use App\Http\Controllers\MemberEBookController;
use App\Http\Controllers\MemberForgotPasswordController;
use App\Http\Controllers\MemberPasswordController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MemberResetPasswordController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MemberSubscriptionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NominationController;
use App\Http\Controllers\PollingController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Public marketing site
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blogs', [HomeController::class, 'blogs'])->name('blogs.index');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery.index');
Route::get('/events', [HomeController::class, 'events'])->name('events.index');
Route::get('/donations', [HomeController::class, 'donations'])->name('donations.index');
Route::post('/donations/payment/order', [DonationPaymentController::class, 'createOrder'])->name('donations.payment.order');
Route::post('/donations/payment/verify', [DonationPaymentController::class, 'verify'])->name('donations.payment.verify');
Route::redirect('/events-demo', '/events', 301);

Route::post('/events/{event}/interest', [EventInterestController::class, 'store'])->name('events.interest');

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

        // Homepage Banner Management
        Route::resource('home-banners', AdminHomeBannerController::class)
            ->except(['show'])
            ->names([
                'index' => 'admin.home-banners.index',
                'create' => 'admin.home-banners.create',
                'store' => 'admin.home-banners.store',
                'edit' => 'admin.home-banners.edit',
                'update' => 'admin.home-banners.update',
                'destroy' => 'admin.home-banners.destroy',
            ]);
        Route::post('/home-banners/{homeBanner}/toggle-status', [AdminHomeBannerController::class, 'toggleStatus'])
            ->name('admin.home-banners.toggle-status');

        // Homepage Blog Management
        Route::resource('home-blogs', AdminHomeBlogController::class)
            ->except(['show'])
            ->parameters([
                'home-blogs' => 'homeBlog',
            ])
            ->names([
                'index' => 'admin.home-blogs.index',
                'create' => 'admin.home-blogs.create',
                'store' => 'admin.home-blogs.store',
                'edit' => 'admin.home-blogs.edit',
                'update' => 'admin.home-blogs.update',
                'destroy' => 'admin.home-blogs.destroy',
            ]);
        Route::post('/home-blogs/{homeBlog}/toggle-status', [AdminHomeBlogController::class, 'toggleStatus'])
            ->name('admin.home-blogs.toggle-status');
        Route::post('/home-blogs/section/update', [AdminHomeBlogController::class, 'updateSection'])
            ->name('admin.home-blogs.section.update');

        // Homepage Gallery Management
        Route::resource('home-galleries', AdminHomeGalleryController::class)
            ->except(['show'])
            ->parameters([
                'home-galleries' => 'homeGallery',
            ])
            ->names([
                'index' => 'admin.home-galleries.index',
                'create' => 'admin.home-galleries.create',
                'store' => 'admin.home-galleries.store',
                'edit' => 'admin.home-galleries.edit',
                'update' => 'admin.home-galleries.update',
                'destroy' => 'admin.home-galleries.destroy',
            ]);
        Route::post('/home-galleries/{homeGallery}/toggle-status', [AdminHomeGalleryController::class, 'toggleStatus'])
            ->name('admin.home-galleries.toggle-status');
        Route::post('/home-galleries/section/update', [AdminHomeGalleryController::class, 'updateSection'])
            ->name('admin.home-galleries.section.update');

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
        Route::get('/members/pending-approvals/{user}', [AdminMemberApprovalController::class, 'show'])
            ->name('admin.members.pending-approvals.show');
        Route::post('/members/{user}/approve', [AdminMemberApprovalController::class, 'approve'])
            ->name('admin.members.pending-approvals.approve');
        Route::post('/members/{user}/reject', [AdminMemberApprovalController::class, 'reject'])
            ->name('admin.members.pending-approvals.reject');

        // Members list (all / active / inactive)
        Route::get('/members', [AdminMemberController::class, 'index'])->name('admin.members.index');
        Route::get('/members/{user}/details', [AdminMemberController::class, 'show'])->name('admin.members.show');
        Route::patch('/members/{user}/designation', [AdminMemberController::class, 'updateDesignation'])
            ->name('admin.members.designation.update');

        // Member designations (admin-defined titles for members)
        Route::get('/designations', [AdminDesignationController::class, 'index'])->name('admin.designations.index');
        Route::post('/designations', [AdminDesignationController::class, 'store'])->name('admin.designations.store');
        Route::get('/designations/{designation}/edit', [AdminDesignationController::class, 'edit'])->name('admin.designations.edit');
        Route::put('/designations/{designation}', [AdminDesignationController::class, 'update'])->name('admin.designations.update');
        Route::delete('/designations/{designation}', [AdminDesignationController::class, 'destroy'])->name('admin.designations.destroy');

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
        Route::post('/events/{event}/invites/{invite}/status', [EventController::class, 'updateInviteStatus'])->name('admin.events.invites.status');
        Route::get('/events/{event}/invites/{invite}/certificate', [EventController::class, 'downloadInviteCertificate'])->name('admin.events.invites.certificate');
        Route::post('/events/{event}/interests/{interest}/attendance', [EventController::class, 'updateInterestAttendance'])->name('admin.events.interests.attendance');
        Route::get('/events/{event}/interests/{interest}/certificate', [EventController::class, 'downloadInterestCertificate'])->name('admin.events.interests.certificate');
        Route::get('/events/{event}/album', [EventController::class, 'album'])->name('admin.events.album');
        Route::post('/events/{event}/album', [EventController::class, 'albumStore'])->name('admin.events.album.store');
        Route::delete('/events/{event}/album/{photo}', [EventController::class, 'albumDestroy'])->name('admin.events.album.destroy');
        Route::get('/events/{event}/invite', [EventController::class, 'inviteForm'])->name('admin.events.invite');
        Route::post('/events/{event}/invite', [EventController::class, 'inviteStore'])->name('admin.events.invite.store');

        // Donations Management
        Route::resource('donations', DonationController::class)->except(['show'])->names([
            'index' => 'admin.donations.index',
            'create' => 'admin.donations.create',
            'store' => 'admin.donations.store',
            'edit' => 'admin.donations.edit',
            'update' => 'admin.donations.update',
            'destroy' => 'admin.donations.destroy',
        ]);
        Route::post('/donations/{donation}/toggle-promote', [DonationController::class, 'togglePromote'])
            ->name('admin.donations.toggle-promote');
        Route::post('/donations/{donation}/toggle-status', [DonationController::class, 'toggleStatus'])
            ->name('admin.donations.toggle-status');
        Route::get('/donations/payments', [AdminDonationPaymentController::class, 'index'])
            ->name('admin.donations.payments.index');

        // Meetings Management
        Route::resource('meetings', MeetingController::class)->except(['show'])->names([
            'index' => 'admin.meetings.index',
            'create' => 'admin.meetings.create',
            'store' => 'admin.meetings.store',
            'edit' => 'admin.meetings.edit',
            'update' => 'admin.meetings.update',
            'destroy' => 'admin.meetings.destroy',
        ]);
        Route::post('/meetings/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('admin.meetings.cancel');
        Route::post('/meetings/{meeting}/send-reminder', [MeetingController::class, 'sendReminder'])->name('admin.meetings.send-reminder');
        Route::post('/meetings/{meeting}/toggle-display', [MeetingController::class, 'toggleDisplay'])->name('admin.meetings.toggle-display');
        Route::get('/meetings/{meeting}/invite', [MeetingController::class, 'inviteForm'])->name('admin.meetings.invite');
        Route::post('/meetings/{meeting}/invite', [MeetingController::class, 'inviteStore'])->name('admin.meetings.invite.store');
        Route::delete('/meetings/{meeting}/invite/{invite}', [MeetingController::class, 'removeInvite'])->name('admin.meetings.invite.remove');

        // Jobs Management
        Route::resource('jobs', AdminJobController::class)->except(['show'])->parameters([
            'jobs' => 'job',
        ])->names([
            'index' => 'admin.jobs.index',
            'create' => 'admin.jobs.create',
            'store' => 'admin.jobs.store',
            'edit' => 'admin.jobs.edit',
            'update' => 'admin.jobs.update',
            'destroy' => 'admin.jobs.destroy',
        ]);
        Route::post('/jobs/{job}/toggle-promote', [AdminJobController::class, 'togglePromote'])->name('admin.jobs.toggle-promote');
        Route::post('/jobs/{job}/toggle-status', [AdminJobController::class, 'toggleStatus'])->name('admin.jobs.toggle-status');
        Route::post('/jobs/{job}/toggle-listing', [AdminJobController::class, 'toggleListing'])->name('admin.jobs.toggle-listing');
        Route::post('/jobs/hospitals', [AdminJobController::class, 'storeHospital'])->name('admin.jobs.hospitals.store');
        Route::get('/jobs/{job}/alert', [AdminJobController::class, 'alertForm'])->name('admin.jobs.alert');
        Route::post('/jobs/{job}/alert', [AdminJobController::class, 'alertStore'])->name('admin.jobs.alert.store');
        Route::get('/jobs/{job}/applications', [AdminJobController::class, 'applications'])->name('admin.jobs.applications');
        Route::get('/jobs/{job}/report', [AdminJobController::class, 'downloadReport'])->name('admin.jobs.report');
        Route::post('/jobs/{job}/applications/{application}/status', [AdminJobController::class, 'updateApplicationStatus'])->name('admin.jobs.applications.status');

        // Nominations Management
        Route::resource('nominations', NominationController::class)->except(['show'])->names([
            'index' => 'admin.nominations.index',
            'create' => 'admin.nominations.create',
            'store' => 'admin.nominations.store',
            'edit' => 'admin.nominations.edit',
            'update' => 'admin.nominations.update',
            'destroy' => 'admin.nominations.destroy',
        ]);
        Route::get('/nominations/{nomination}', [NominationController::class, 'show'])->name('admin.nominations.show');
        Route::post('/nominations/{nomination}/cancel', [NominationController::class, 'cancel'])->name('admin.nominations.cancel');
        Route::post('/nominations/{nomination}/toggle-status', [NominationController::class, 'toggleStatus'])->name('admin.nominations.toggle-status');
        Route::get('/nominations/{nomination}/alert', [NominationController::class, 'alertForm'])->name('admin.nominations.alert');
        Route::post('/nominations/{nomination}/alert', [NominationController::class, 'alertStore'])->name('admin.nominations.alert.store');
        Route::get('/nominations/{nomination}/submissions', [NominationController::class, 'submissions'])->name('admin.nominations.submissions');
        Route::get('/nominations/{nomination}/report', [NominationController::class, 'downloadReport'])->name('admin.nominations.report');

        // Pollings Management
        Route::resource('pollings', PollingController::class)->except(['show'])->names([
            'index' => 'admin.pollings.index',
            'create' => 'admin.pollings.create',
            'store' => 'admin.pollings.store',
            'edit' => 'admin.pollings.edit',
            'update' => 'admin.pollings.update',
            'destroy' => 'admin.pollings.destroy',
        ]);
        Route::post('/pollings/{polling}/toggle-promote', [PollingController::class, 'togglePromote'])->name('admin.pollings.toggle-promote');
        Route::post('/pollings/{polling}/toggle-status', [PollingController::class, 'toggleStatus'])->name('admin.pollings.toggle-status');
        Route::get('/pollings/{polling}/stats', [PollingController::class, 'stats'])->name('admin.pollings.stats');
        Route::get('/pollings/{polling}/report', [PollingController::class, 'downloadReport'])->name('admin.pollings.report');
        Route::post('/pollings/{polling}/results', [PollingController::class, 'saveResults'])->name('admin.pollings.results');
    });
});

// Member (User Site) Routes
Route::prefix('member')->name('member.')->group(function () {
    Route::get('/donations', function (\Illuminate\Http\Request $request) {
        return redirect()->route('donations.index', $request->query());
    });

    Route::get('/login', [MemberAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MemberAuthController::class, 'login'])->name('login.store');

    Route::get('/register', [MemberAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [MemberAuthController::class, 'register'])->name('register.store');

    Route::get('/otp', [MemberAuthController::class, 'showOtpForm'])->name('otp');
    Route::post('/otp', [MemberAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/resend-otp', [MemberAuthController::class, 'resendOtp'])->name('otp.resend');

    Route::get('/forgot-password', [MemberForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [MemberForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [MemberResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [MemberResetPasswordController::class, 'store'])->name('password.reset.store');

    Route::post('/logout', [MemberAuthController::class, 'logout'])->middleware('auth')->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/announcements/dismiss', [MemberDashboardController::class, 'dismissDashboardAnnouncement'])
            ->name('dashboard.announcements.dismiss');
        Route::get('/jobs', [MemberDashboardController::class, 'jobsPage'])->name('jobs.index');
        Route::get('/events', [MemberDashboardController::class, 'eventsPage'])->name('events.index');
        Route::get('/nominations', [MemberDashboardController::class, 'nominationsPage'])->name('nominations.index');
        Route::get('/pollings', [MemberDashboardController::class, 'pollingsPage'])->name('pollings.index');
        Route::post('/events/{event}/interest', [MemberDashboardController::class, 'submitInterest'])->name('events.interest');
        Route::middleware('member.subscribed')->group(function () {
            Route::get('/events/{event}/certificate', [MemberDashboardController::class, 'downloadEventCertificate'])->name('events.certificate');
        });
        Route::get('/profile', [MemberProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [MemberProfileController::class, 'update'])->name('profile.update');
        Route::get('/password', [MemberPasswordController::class, 'edit'])->name('password.edit');
        Route::post('/password', [MemberPasswordController::class, 'update'])->name('password.update');
        Route::get('/subscription', [MemberSubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('/subscription/checkout', [MemberSubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::get('/subscription/checkout', [MemberSubscriptionController::class, 'showCheckout'])->name('subscription.checkout.show');
        Route::post('/subscription/order', [MemberSubscriptionController::class, 'createOrder'])->name('subscription.order');
        Route::post('/subscription/verify', [MemberSubscriptionController::class, 'verifyPayment'])->name('subscription.verify');
        Route::get('/subscription/invoice/{id}', [MemberSubscriptionController::class, 'downloadInvoice'])->name('subscription.invoice');

        Route::middleware('member.subscribed')->group(function () {
            Route::get('/e-books', [MemberEBookController::class, 'index'])->name('ebooks.index');
            Route::post('/nominations/{nomination}/positions/{nominationPosition}/interest', [MemberDashboardController::class, 'submitNominationInterest'])
                ->name('nominations.interest');
            Route::post('/nominations/{nomination}/positions/{nominationPosition}/not-interested', [MemberDashboardController::class, 'submitNominationNotInterested'])
                ->name('nominations.not-interested');
            Route::post('/pollings/{polling}/vote', [MemberDashboardController::class, 'submitPollingVote'])
                ->name('pollings.vote');
        });
    });
});
