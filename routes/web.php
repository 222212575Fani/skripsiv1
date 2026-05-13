<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TimKerjaController;

// Halaman Awal
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth - Guest (Hanya bisa diakses jika belum login)
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Auth - Terlindungi (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route untuk Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Manajemen Pengguna
        Route::get('/manajemenpengguna', [PenggunaController::class, 'index'])->name('manajemenpengguna');
        Route::post('/pengguna/aktivasi', [PenggunaController::class, 'aktivasi'])->name('aktivasi');
        Route::post('/manajemenpengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        
        // Manajemen Tim Kerja
        Route::get('/manajementimkerja', [TimKerjaController::class, 'index'])->name('manajementimkerja');
        Route::post('/manajementimkerja', [TimKerjaController::class, 'store'])->name('timkerja.store');
    });

    // Route untuk Direktur
    Route::get('/direktur/dashboard', function () {
        return 'Dashboard Direktur';
    })->name('direktur.dashboard');

    // Route untuk ketua tim
    Route::get('/ketuatim/dashboard', function () {
        return 'Dashboard Ketua Tim';
    })->name('ketuatim.dashboard');

    // Route untuk Anggota
    Route::get('/anggota/proyekaktivitas', function () {
        return 'Proyek dan Aktivitas Saya';
    })->name('anggota.proyekaktivitas');
});