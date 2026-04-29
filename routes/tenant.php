<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Tenant login routes (per-domain)
    Route::get('/login', [AuthController::class, 'showTenantLogin'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])->name('tenant.login.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('tenant.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('tenant.password.email');
    Route::get('/reset-password/verify', [AuthController::class, 'showVerifyResetCode'])->name('tenant.password.reset.verify');
    Route::post('/reset-password/verify', [AuthController::class, 'verifyResetCode'])->name('tenant.password.reset.verify.post');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('tenant.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('tenant.password.update');

    // Tenant admin dashboard and related routes use the same controllers,
    // but will automatically use the tenant's database because of the middleware.
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('tenant.admin.dashboard');

});
