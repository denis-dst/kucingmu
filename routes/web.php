<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Member Routes
Route::middleware(['auth', 'role:member'])->group(function () {
    Route::post('/cat', [DashboardController::class, 'storeCat'])->name('cat.store');
    Route::get('/cat/{cat}/edit', [DashboardController::class, 'editCat'])->name('cat.edit');
    Route::put('/cat/{cat}', [DashboardController::class, 'updateCat'])->name('cat.update');
    Route::post('/appointment', [DashboardController::class, 'storeAppointment'])->name('appointment.store');
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::post('/checkup/{appointment}', [DashboardController::class, 'storeCheckup'])->name('checkup.store');
});

// Volunteer Routes
Route::middleware(['auth', 'role:volunteer'])->group(function () {
    Route::post('/appointment/{appointment}/checkin', [DashboardController::class, 'checkInAppointment'])->name('appointment.checkin');
    Route::post('/quick-register', [DashboardController::class, 'quickRegister'])->name('quick-register');
    Route::post('/sync-offline', [DashboardController::class, 'syncOffline'])->name('sync-offline');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/export-data', [DashboardController::class, 'exportData'])->name('export-data');
});

// Shared KTAM Download Route
Route::middleware(['auth'])->group(function () {
    Route::get('/cat/{cat}/download-ktam', [DashboardController::class, 'downloadKtam'])->name('ktam.download');
});

// Public Verification Page (No Auth)
Route::get('/verify/{number}', [DashboardController::class, 'verifyKtam'])->name('ktam.verify');

require __DIR__.'/auth.php';
