<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\StrayCatSurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $events = \App\Models\Event::where('status', 'active')->orderBy('date', 'asc')->get();
    $activityAlbums = \App\Models\ActivityAlbum::where('is_active', true)
        ->orderBy('order', 'asc')
        ->orderBy('activity_date', 'desc')
        ->orderBy('id', 'desc')
        ->get();
    return view('welcome', compact('events', 'activityAlbums'));
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
    Route::post('/appointment', [DashboardController::class, 'storeAppointment'])->name('appointment.store');
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::post('/checkup/{appointment}', [DashboardController::class, 'storeCheckup'])->name('checkup.store');
});

use App\Http\Controllers\PtmaCatCensusController;

// Volunteer Routes
Route::middleware(['auth', 'role:volunteer'])->group(function () {
    Route::get('/surveillance-kucing', [StrayCatSurveyController::class, 'index'])->name('volunteer.surveillance.index');
    Route::post('/surveillance-kucing', [StrayCatSurveyController::class, 'store'])->name('volunteer.surveillance.store');
    Route::get('/surveillance-kucing/{survey}/pdf', [StrayCatSurveyController::class, 'pdf'])->name('volunteer.surveillance.pdf');
    Route::post('/appointment/{appointment}/checkin', [DashboardController::class, 'checkInAppointment'])->name('appointment.checkin');
    Route::post('/quick-register', [DashboardController::class, 'quickRegister'])->name('quick-register');
    Route::post('/sync-offline', [DashboardController::class, 'syncOffline'])->name('volunteer.sync-offline');
    Route::post('/sync-offline-legacy', [DashboardController::class, 'syncOffline'])->name('sync-offline');

    // Sensus Kucing PTMA
    Route::get('/sensus-kucing/scan', [PtmaCatCensusController::class, 'scan'])->name('volunteer.census.scan');
    Route::post('/sensus-kucing/match', [PtmaCatCensusController::class, 'match'])->name('volunteer.census.match');
    Route::get('/sensus-kucing/missing-embeddings', [PtmaCatCensusController::class, 'getMissingEmbeddings'])->name('volunteer.census.missing-embeddings');
    Route::post('/sensus-kucing/sync-embeddings', [PtmaCatCensusController::class, 'syncEmbeddings'])->name('volunteer.census.sync-embeddings');
    Route::get('/sensus-kucing/next-id', [PtmaCatCensusController::class, 'nextId'])->name('volunteer.census.next-id');
    Route::get('/sensus-kucing/export-csv', [PtmaCatCensusController::class, 'exportCsv'])->name('volunteer.census.export');
    Route::resource('/sensus-kucing', PtmaCatCensusController::class, [
        'names' => 'volunteer.census',
        'parameters' => [
            'sensus-kucing' => 'census'
        ]
    ]);
});

use App\Http\Controllers\MasterWilayahController;
use App\Http\Controllers\ActivityAlbumController;

// Admin & Superadmin Routes
Route::middleware(['auth', 'role:admin,superadmin'])->group(function () {
    Route::get('/export-data', [DashboardController::class, 'exportData'])->name('export-data');
    Route::post('/admin/cats/{cat}/verify-ktam', [DashboardController::class, 'verifyAndIssueKtam'])->name('admin.verify-ktam');
    Route::get('/settings', [AppSettingController::class, 'index'])->name('admin.settings');
    Route::put('/settings', [AppSettingController::class, 'update'])->name('admin.settings.update');
    Route::resource('/events', EventController::class, ['names' => 'admin.events']);

    // Superadmin Master Wilayah Management
    Route::post('/superadmin/wilayah/seed-default', [MasterWilayahController::class, 'seedDefault'])->name('superadmin.wilayah.seed-default');
    Route::post('/superadmin/wilayah/{wilayah}/toggle-status', [MasterWilayahController::class, 'toggleStatus'])->name('superadmin.wilayah.toggle-status');
    Route::resource('/superadmin/wilayah', MasterWilayahController::class, ['names' => 'superadmin.wilayah']);

    // Superadmin Album Foto Kegiatan
    Route::post('/superadmin/albums/seed-default', [ActivityAlbumController::class, 'seedDefault'])->name('superadmin.albums.seed-default');
    Route::post('/superadmin/albums/{album}/toggle-status', [ActivityAlbumController::class, 'toggleStatus'])->name('superadmin.albums.toggle-status');
    Route::resource('/superadmin/albums', ActivityAlbumController::class, ['names' => 'superadmin.albums']);
});

// Shared Cat Management, KTAM Download & Preview & Photo Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cat/{cat}/edit', [DashboardController::class, 'editCat'])->name('cat.edit');
    Route::put('/cat/{cat}', [DashboardController::class, 'updateCat'])->name('cat.update');
    Route::delete('/cat/{cat}', [DashboardController::class, 'destroyCat'])->name('cat.destroy');
    Route::post('/cat/{cat}/toggle-status', [DashboardController::class, 'toggleCatStatus'])->name('cat.toggle-status');
    Route::get('/cat/{cat}/download-ktam', [DashboardController::class, 'downloadKtam'])->name('ktam.download');
    Route::get('/cat/{cat}/preview-ktam', [DashboardController::class, 'previewKtam'])->name('ktam.preview');
    Route::post('/cat/{cat}/photos', [DashboardController::class, 'uploadCatPhoto'])->name('cat.photos.store');
    Route::post('/photos/{photo}/set-primary', [DashboardController::class, 'setPrimaryPhoto'])->name('photos.set-primary');
    Route::delete('/photos/{photo}', [DashboardController::class, 'deletePhoto'])->name('photos.destroy');
});

// Public Verification Page (No Auth)
Route::get('/verify/{number}', [DashboardController::class, 'verifyKtam'])->name('ktam.verify');

// Language Switcher Route
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

require __DIR__.'/auth.php';
