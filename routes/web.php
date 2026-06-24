<?php

use App\Models\User;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

// ==================== JALUR PUBLIK (BISA DIAKSES SIAPAPUN) ====================
Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function () {
    return view('map');
})->name('map');

// API Data Spasial untuk Leaflet (Harus di luar auth agar Pin, Garis, & Area Muncul)
Route::get('/api/points', [ApiController::class, 'index']);
Route::get('/api/routes', [ApiController::class, 'getRoutes']);
Route::get('/api/zones', [ApiController::class, 'getZones']);

// Aksi simpan laporan dari form sidebar
Route::post('/api/points', [ApiController::class, 'store'])->name('points.store');


// ==================== JALUR ADMIN (WAJIB LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard Utama Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Kelola Titik Sampah (Dialihkan ke DashboardController sesuai instruksi sebelumnya)
    Route::put('/points/{id}', [DashboardController::class, 'updateStatus'])->name('points.update');
    Route::delete('/points/{id}', [DashboardController::class, 'destroyPoint'])->name('points.destroy');

    // 3. Aksi Simpan Data Spasial Baru (Tetap di ApiController bawaan lamamu)
    Route::post('/routes/store', [RouteController::class, 'store'])->name('routes.store');
Route::post('/zones/store', [ZoneController::class, 'store'])->name('zones.store');

    // 4. FITUR BARU: Edit Update Jalur Rute (Garis)
    Route::get('/routes/{id}/edit', [RouteController::class, 'edit'])->name('routes.edit');
    Route::put('/routes/{id}', [RouteController::class, 'update'])->name('routes.update');
    Route::delete('/routes/{id}', [RouteController::class, 'destroy'])->name('routes.destroy');

    // 5. FITUR BARU: Edit & Update Wilayah Zona (Area)
    Route::get('/zones/{id}/edit', [ZoneController::class, 'edit'])->name('zones.edit');
    Route::put('/zones/{id}', [ZoneController::class, 'update'])->name('zones.update');
    Route::delete('/zones/{id}', [ZoneController::class, 'destroy'])->name('zones.destroy');
});

// Penyelamat error profile bawaan Breeze
Route::get('/profile', function () {
    return redirect()->route('dashboard');
})->name('profile.edit');

require __DIR__ . '/auth.php';
