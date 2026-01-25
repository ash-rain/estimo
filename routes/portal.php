<?php

use App\Http\Controllers\ClientPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Client Portal Routes
|--------------------------------------------------------------------------
|
| These are public routes for clients to view and interact with quotes
| without requiring authentication. Access is controlled via secure tokens.
|
*/

Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('quote/{token}', [ClientPortalController::class, 'show'])->name('quote.show');
    Route::post('quote/{token}/accept', [ClientPortalController::class, 'accept'])->name('quote.accept');
    Route::post('quote/{token}/reject', [ClientPortalController::class, 'reject'])->name('quote.reject');
});
