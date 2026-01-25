<?php

use Illuminate\Support\Facades\Route;

// Central domain routes (marketing, registration, etc.)
Route::view('/', 'welcome');

// Registration route (only on central domain)
Route::view('/register', 'livewire.pages.auth.register')
    ->middleware('guest')
    ->name('register');
