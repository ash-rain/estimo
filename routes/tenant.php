<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
    // Guest routes
    Route::get('/', function () {
        return redirect()->route('login');
    });

    require __DIR__.'/auth.php';

    // Authenticated routes
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('dashboard');
        Route::view('/profile', 'profile')->name('profile');
    });
});
