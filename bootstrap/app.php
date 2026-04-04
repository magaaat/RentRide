<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('customer/*')) {
                return route('customer.login');
            }

            return route('login');
        });

        $middleware->alias([
            'tenant.feature' => \App\Http\Middleware\EnsureTenantHasFeature::class,
            'customer' => \App\Http\Middleware\EnsureUserIsCustomer::class,
            'tenant.domain.active' => \App\Http\Middleware\EnsureTenantDomainIsActive::class,
            'tenant.user' => \App\Http\Middleware\EnsureUserIsTenantUser::class,
            'tenant.admin' => \App\Http\Middleware\EnsureUserIsTenantAdmin::class,
            'tenant.permission' => \App\Http\Middleware\EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
