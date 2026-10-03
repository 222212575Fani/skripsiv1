<?php

use App\Http\Controllers\AnggotaProyekController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\KetuaTimController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TimKerjaController;
use Illuminate\Support\Facades\Route;

// Halaman beranda (landing page) yang dapat dilihat tanpa login
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/portal', function () {
    return view('welcome');
})->name('portal');

// =========================================================================
// KELOMPOK ROUTES: AUTENTIKASI (PUBLIC)
// Rute ini bisa diakses oleh siapa saja tanpa perlu login (Register, Login).
// =========================================================================
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// KELOMPOK ROUTES: PROTECTED (MEMBUTUHKAN LOGIN)
// Semua rute di dalam blok ini WAJIB login. Jika tidak login, akan dilempar kembali.
// =========================================================================
Route::middleware('auth')->group(function () {
    // Notifikasi Real-time
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    // -------------------------------------------------------------------------
    // HAK AKSES: ADMIN
    // Mengatur halaman manajemen data inti (Pengguna dan Tim Kerja)
    // -------------------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
        Route::get('/manajemenpengguna', [PenggunaController::class, 'index'])->name('manajemenpengguna');
        Route::post('/pengguna/aktivasi', [PenggunaController::class, 'aktivasi'])->name('aktivasi');
        Route::post('/manajemenpengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::post('/manajemenpengguna/update', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::get('/manajementimkerja', [TimKerjaController::class, 'index'])->name('manajementimkerja');
        Route::post('/manajementimkerja', [TimKerjaController::class, 'store'])->name('timkerja.store');
        Route::post('/manajementimkerja/update', [TimKerjaController::class, 'update'])->name('timkerja.update');
    });

    // -------------------------------------------------------------------------
    // HAK AKSES: DIREKTUR UTAMA
    // Mengatur halaman Dashboard pemantauan grafik performa dan beban kerja
    // -------------------------------------------------------------------------
    Route::prefix('direktur')->name('direktur.')->middleware('role:Direktur')->group(function () {
        Route::get('/dashboard', [DirekturController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/dataprogress', [DirekturController::class, 'getChartData'])->name('chart.data');
        Route::get('/dashboard/databebankerja', [DirekturController::class, 'getBebanKerjaData'])->name('chart.bebankerja');
    });

    // -------------------------------------------------------------------------
    // HAK AKSES: KETUA TIM
    // Mengatur halaman pembuatan proyek baru di dalam lingkup tim kerjanya
    // -------------------------------------------------------------------------
    Route::prefix('ketuatim')->name('ketuatim.')->middleware('role:Ketua Tim')->group(function () {
        Route::get('/dashboard', [KetuaTimController::class, 'dashboard'])->name('dashboard');
        Route::get('/manajemenproyek', [KetuaTimController::class, 'manajemenProyek'])->name('manajemenproyek');
        Route::post('/manajemenproyek/store', [KetuaTimController::class, 'storeProyek'])->name('manajemenproyek.store');
        Route::delete('/manajemenproyek/{id}', [KetuaTimController::class, 'destroy'])->name('manajemenproyek.destroy');
        Route::put('/proyek/{id}', [KetuaTimController::class, 'updateProyek'])->name('proyek.update');
    });

    // -------------------------------------------------------------------------
    // HAK AKSES: KETUA PROYEK & ANGGOTA
    // Mengatur segala logika detail pekerjaan (Aktivitas) dan pelaporan Progress
    // -------------------------------------------------------------------------
    Route::prefix('anggota')->name('anggota.')->middleware('role:Anggota')->group(function () {
        // Dashboard Anggota/Ketua Proyek (halaman utama setelah login)
        Route::get('/proyekaktivitas', [AnggotaProyekController::class, 'index'])->name('proyekaktivitas');

        // Daftar proyek yang diketuai pengguna
        Route::get('/daftarproyek', [AnggotaProyekController::class, 'daftarProyek'])->name('daftarproyek');

        Route::get('/proyekaktivitas/{id}/aktivitas', [AnggotaProyekController::class, 'showAktivitas'])->name('proyek.aktivitas');
        Route::post('/proyekaktivitas/{id}/aktivitas', [AnggotaProyekController::class, 'storeAktivitas'])->name('aktivitas.store');
        Route::put('/aktivitas/{id}', [AnggotaProyekController::class, 'updateAktivitas'])->name('aktivitas.update');
        Route::delete('/aktivitas/{id}', [AnggotaProyekController::class, 'destroyAktivitas'])->name('aktivitas.destroy');
        Route::post('/aktivitas/{id}/progress', [AnggotaProyekController::class, 'storeProgressAktivitas'])->name('aktivitas.progress');
        Route::get('/aktivitassaya', [AnggotaProyekController::class, 'aktivitasSaya'])->name('aktivitassaya');
    });
});
