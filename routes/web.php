<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TimKerjaController;
use App\Http\Controllers\AnggotaProyekController;
use App\Http\Controllers\KetuaTimController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/portal', function () {
    return view('welcome');
})->name('portal');

// Auth - Register & Login (Dapat diakses langsung tanpa di-redirect oleh middleware guest)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Notifikasi Real-time
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    // Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/manajemenpengguna', [PenggunaController::class, 'index'])->name('manajemenpengguna');
        Route::post('/pengguna/aktivasi', [PenggunaController::class, 'aktivasi'])->name('aktivasi');
        Route::post('/manajemenpengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::post('/manajemenpengguna/update', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::get('/manajementimkerja', [TimKerjaController::class, 'index'])->name('manajementimkerja');
        Route::post('/manajementimkerja', [TimKerjaController::class, 'store'])->name('timkerja.store');
        Route::post('/manajementimkerja/update', [TimKerjaController::class, 'update'])->name('timkerja.update');
    });

    // Direktur
    Route::prefix('direktur')->name('direktur.')->group(function () {
        Route::get('/dashboard', [DirekturController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/dataprogress', [DirekturController::class, 'getChartData'])->name('chart.data');
        Route::get('/dashboard/databebankerja', [DirekturController::class, 'getBebanKerjaData'])->name('chart.bebankerja');
    });

    // Ketua Tim
    Route::prefix('ketuatim')->name('ketuatim.')->group(function () {
        Route::get('/dashboard', [KetuaTimController::class, 'dashboard'])->name('dashboard');
        Route::get('/manajemenproyek', [KetuaTimController::class, 'manajemenProyek'])->name('manajemenproyek');
        Route::post('/manajemenproyek/store', [KetuaTimController::class, 'storeProyek'])->name('manajemenproyek.store');
        Route::delete('/manajemenproyek/{id}', [KetuaTimController::class, 'destroyProyek'])->name('manajemenproyek.destroy');
        Route::put('/proyek/{id}', [KetuaTimController::class, 'updateProyek'])->name('proyek.update');
    });

    // Anggota
    Route::prefix('anggota')->name('anggota.')->group(function () {
        Route::get('/proyekaktivitas', [AnggotaProyekController::class, 'index'])->name('proyekaktivitas');
        Route::get('/proyekaktivitas/{id}/aktivitas', [AnggotaProyekController::class, 'showAktivitas'])->name('proyek.aktivitas');
        Route::post('/proyekaktivitas/{id}/aktivitas', [AnggotaProyekController::class, 'storeAktivitas'])->name('aktivitas.store');
        Route::put('/aktivitas/{id}', [AnggotaProyekController::class, 'updateAktivitas'])->name('aktivitas.update');
        Route::delete('/aktivitas/{id}', [AnggotaProyekController::class, 'destroyAktivitas'])->name('aktivitas.destroy');
        Route::post('/aktivitas/{id}/progress', [AnggotaProyekController::class, 'storeProgressAktivitas'])->name('aktivitas.progress');
        Route::get('/aktivitassaya', [AnggotaProyekController::class, 'aktivitasSaya'])->name('aktivitassaya');
    });
});