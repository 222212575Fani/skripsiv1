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

    // --- GRUP ADMIN ---
    Route::prefix('admin')->name('admin.')->group(function () {
        // Manajemen Pengguna
        Route::get('/manajemenpengguna', [PenggunaController::class, 'index'])->name('manajemenpengguna');
        Route::post('/pengguna/aktivasi', [PenggunaController::class, 'aktivasi'])->name('aktivasi');

        // Manajemen Tim Kerja
        Route::get('/manajementimkerja', [TimKerjaController::class, 'index'])->name('manajementimkerja');
    });

    // --- GRUP DIREKTUR ---
    Route::get('/direktur/dashboard', function () {
        return 'Dashboard Direktur';
    })->name('direktur.dashboard');

    // --- GRUP KETUA TIM ---
    Route::get('/ketuatim/dashboard', function () {
        return 'Dashboard Ketua Tim';
    })->name('ketuatim.dashboard');

    // --- GRUP ANGGOTA ---
    Route::get('/anggota/proyekaktivitas', function () {
        return 'Proyek dan Aktivitas Saya';
    })->name('anggota.proyekaktivitas');
});