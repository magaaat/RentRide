<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerPortalController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdminPlanController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminExtensionController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantReportController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TenantUserProfileController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;

Route::get('/', function () {
    $plans = SubscriptionPlan::where('show_on_landing', true)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    $mostSubscribedPlan = Tenant::where('status', 'approved')
        ->selectRaw('subscription_plan, COUNT(*) as cnt')
        ->groupBy('subscription_plan')
        ->orderByDesc('cnt')
        ->value('subscription_plan');

    $premiumPlanKeys = SubscriptionPlan::query()
        ->where('feature_tier', 'premium')
        ->pluck('key');

    $featuredTenants = Tenant::where('status', 'approved')
        ->where('is_domain_active', true)
        ->whereIn('subscription_plan', $premiumPlanKeys)
        ->where('is_featured', true)
        ->orderBy('company_name')
        ->limit(8)
        ->get();

    return view('landing', compact('plans', 'mostSubscribedPlan', 'featuredTenants'));
});

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('password.email');
Route::get('/reset-password/verify', [AuthController::class, 'showVerifyResetCode'])->name('password.reset.verify');
Route::post('/reset-password/verify', [AuthController::class, 'verifyResetCode'])->name('password.reset.verify.post');
Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Super admin login (separate page from landing)
Route::get('/superadmin/login', [AuthController::class, 'showSuperAdminLogin'])->name('superadmin.login');

// Customer login / register (central app — no Super Admin approval)
Route::get('/customer/login', [AuthController::class, 'showCustomerLogin'])->name('customer.login');
Route::post('/customer/login', [AuthController::class, 'customerLogin'])->name('customer.login.post');

// Tenant registration
Route::get('/register-tenant', [AuthController::class, 'showRegisterTenant'])->name('tenant.register');
Route::post('/register-tenant', [AuthController::class, 'registerTenant'])->name('tenant.register.post');

// Tenant plan extension request (guest, shown when domain disabled)
Route::post('/tenant/extend-request', [AuthController::class, 'requestPlanExtension'])->name('tenant.extend.request');

// Customer registration
Route::get('/register-customer', [AuthController::class, 'showRegisterCustomer'])->name('customer.register');
Route::post('/register-customer', [AuthController::class, 'registerCustomer'])->name('customer.register.post');

Route::middleware(['auth', 'tenant.domain.active'])->group(function () {
    // Super admin routes (role-checked inside controller)
    Route::get('/superadmin/dashboard', [DashboardController::class, 'superAdmin'])->name('superadmin.dashboard');
    Route::get('/superadmin/about', [SupportController::class, 'superAdminAbout'])->name('superadmin.about');
    Route::post('/superadmin/about/tenants/{tenant}/messages', [SupportController::class, 'storeSuperAdminChatMessage'])->name('superadmin.about.tenants.messages.store');
    Route::post('/superadmin/about/messages/{inquiry}/reply', [SupportController::class, 'storeSuperAdminReply'])->name('superadmin.about.messages.reply');
    Route::get('/superadmin/support', fn () => redirect()->route('superadmin.about'))->name('superadmin.support');
    Route::get('/superadmin/tenants', [SuperAdminController::class, 'tenantsIndex'])->name('superadmin.tenants.index');
    Route::get('/superadmin/tenants/create', [SuperAdminController::class, 'createTenant'])->name('superadmin.tenants.create');
    Route::post('/superadmin/tenants', [SuperAdminController::class, 'storeTenant'])->name('superadmin.tenants.store');
    Route::get('/superadmin/tenants/{tenant}', [SuperAdminController::class, 'showTenant'])->name('superadmin.tenants.show');
    Route::get('/superadmin/tenants/{tenant}/edit', [SuperAdminController::class, 'editTenant'])->name('superadmin.tenants.edit');
    Route::put('/superadmin/tenants/{tenant}', [SuperAdminController::class, 'updateTenant'])->name('superadmin.tenants.update');
    Route::delete('/superadmin/tenants/{tenant}', [SuperAdminController::class, 'destroyTenant'])->name('superadmin.tenants.destroy');
    Route::get('/superadmin/plans', [SuperAdminPlanController::class, 'index'])->name('superadmin.plans.index');
    Route::get('/superadmin/plans/create', [SuperAdminPlanController::class, 'create'])->name('superadmin.plans.create');
    Route::post('/superadmin/plans', [SuperAdminPlanController::class, 'store'])->name('superadmin.plans.store');
    Route::get('/superadmin/plans/{plan}/edit', [SuperAdminPlanController::class, 'edit'])->name('superadmin.plans.edit');
    Route::put('/superadmin/plans/{plan}', [SuperAdminPlanController::class, 'update'])->name('superadmin.plans.update');
    Route::delete('/superadmin/plans/{plan}', [SuperAdminPlanController::class, 'destroy'])->name('superadmin.plans.destroy');
    Route::get('/superadmin/profile', [SuperAdminController::class, 'profile'])->name('superadmin.profile');
    Route::put('/superadmin/profile', [SuperAdminController::class, 'updateProfile'])->name('superadmin.profile.update');

    // Plan extension requests
    Route::get('/superadmin/extensions', [SuperAdminExtensionController::class, 'index'])->name('superadmin.extensions.index');
    Route::post('/superadmin/extensions/{extension}/approve', [SuperAdminExtensionController::class, 'approve'])->name('superadmin.extensions.approve');
    Route::post('/superadmin/extensions/{extension}/reject', [SuperAdminExtensionController::class, 'reject'])->name('superadmin.extensions.reject');

    // Rental tenant users (Admin + Staff)
    Route::middleware('tenant.user')->group(function () {
        Route::get('/admin/my-profile', [TenantUserProfileController::class, 'edit'])->name('tenant.staff-profile');
        Route::put('/admin/my-profile', [TenantUserProfileController::class, 'update'])->name('tenant.staff-profile.update');

        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/updated-module', [DashboardController::class, 'updatedModule'])->name('admin.updated-module');
        Route::get('/admin/about', [SupportController::class, 'tenantAbout'])->name('admin.about');
        Route::post('/admin/about/messages', [SupportController::class, 'storeTenantInquiry'])->name('admin.about.messages.store');
        Route::post('/admin/about/download-update', [SupportController::class, 'downloadTenantUpdate'])->name('admin.about.update.download');
        Route::get('/admin/support', fn () => redirect()->route('admin.about'))->name('admin.support');

        Route::get('/admin/reports', [TenantReportController::class, 'index'])
            ->middleware(['tenant.permission:reports.view', 'tenant.feature:sales_dashboard'])
            ->name('tenant.reports');
        Route::get('/admin/analytics', [TenantReportController::class, 'analytics'])
            ->middleware(['tenant.permission:reports.view', 'tenant.feature:advanced_analytics'])
            ->name('tenant.analytics');

        Route::get('vehicles/maintenance', [MaintenanceController::class, 'index'])
            ->middleware(['tenant.permission:maintenance.manage', 'tenant.feature:maintenance_tracking'])
            ->name('vehicles.maintenance');

        Route::resource('vehicles', VehicleController::class)
            ->except(['show'])
            ->middleware('tenant.permission:vehicles.manage');
        Route::resource('customers', CustomerController::class)
            ->except(['edit', 'update'])
            ->middleware('tenant.permission:customers.manage');

        Route::get('bookings/calendar', [BookingCalendarController::class, 'index'])
            ->middleware(['tenant.permission:bookings.manage', 'tenant.feature:booking_calendar'])
            ->name('bookings.calendar');
        Route::get('bookings', [BookingController::class, 'index'])->middleware('tenant.permission:bookings.manage')->name('bookings.index');
        Route::get('bookings/create', [BookingController::class, 'create'])->middleware('tenant.permission:bookings.manage')->name('bookings.create');
        Route::post('bookings', [BookingController::class, 'store'])->middleware('tenant.permission:bookings.manage')->name('bookings.store');
        Route::post('bookings/{booking}/status', [BookingController::class, 'updateStatus'])->middleware('tenant.permission:bookings.manage')->name('bookings.updateStatus');

        Route::middleware(['tenant.permission:payments.manage', 'tenant.feature:payment_tracking'])->group(function () {
            Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('payments/{booking}/create', [PaymentController::class, 'create'])->name('payments.create');
            Route::post('payments/{booking}', [PaymentController::class, 'store'])->name('payments.store');
        });
    });

    // Tenant owner/admin only
    Route::middleware('tenant.admin')->group(function () {
        Route::get('/admin/profile', [TenantController::class, 'profile'])->name('admin.profile');
        Route::put('/admin/profile', [TenantController::class, 'updateProfile'])->name('admin.profile.update');

        Route::get('/admin/staff', [StaffController::class, 'index'])->name('admin.staff.index');
        Route::get('/admin/staff/create', [StaffController::class, 'create'])->name('admin.staff.create');
        Route::post('/admin/staff', [StaffController::class, 'store'])->name('admin.staff.store');
        Route::post('/admin/staff/role-permissions', [StaffController::class, 'updateRolePermissions'])->name('admin.staff.role-permissions.update');
        Route::get('/admin/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
        Route::put('/admin/staff/{staff}', [StaffController::class, 'update'])->name('admin.staff.update');
        Route::delete('/admin/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');

        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/upgrade', [SubscriptionController::class, 'upgradeForm'])->name('subscriptions.upgrade');
        Route::post('subscriptions/upgrade', [SubscriptionController::class, 'upgrade'])->name('subscriptions.upgrade.post');
    });

    // Customer portal (browse tenants, vehicles, book — central database)
    Route::middleware('customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [CustomerPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AuthController::class, 'showCustomerProfile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateCustomerProfile'])->name('profile.update');
        Route::get('/companies', [CustomerPortalController::class, 'tenantsIndex'])->name('tenants.index');
        Route::get('/search', [CustomerPortalController::class, 'search'])->name('search');
        Route::get('/companies/{tenant}/vehicles', [CustomerPortalController::class, 'vehicles'])->name('tenants.vehicles');
        Route::get('/companies/{tenant}/vehicles/{vehicle}', [CustomerPortalController::class, 'vehicleShow'])->name('vehicles.show');
        Route::post('/companies/{tenant}/vehicles/{vehicle}/book', [CustomerPortalController::class, 'storeBooking'])->name('vehicles.book');
    });
});
