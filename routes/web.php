<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/daily-activities', [DailyActivityController::class, 'index'])->name('daily-activities.index');
    Route::post('/daily-activities', [DailyActivityController::class, 'upsert'])->name('daily-activities.upsert');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});
