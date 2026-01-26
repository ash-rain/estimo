<?php

use Illuminate\Support\Facades\Route;

// Central domain routes (marketing, registration, etc.)
Route::view('/', 'welcome');

// Include auth routes for central domain
require __DIR__ . '/auth.php';

// Additional authenticated routes for central domain if needed
Route::middleware(['auth'])->group(function () {
    // Redirect authenticated users to their tenant dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user && $user->tenant_id) {
            $tenant = \App\Models\Tenant::find($user->tenant_id);
            if ($tenant) {
                $domain = $tenant->domains()->first();
                if ($domain) {
                    return redirect('http://' . $domain->domain . '/dashboard');
                }
            }
        }
        return redirect('/');
    })->name('dashboard');
});
