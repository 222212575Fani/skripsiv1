<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TimKerjaController;
use App\Http\Controllers\AnggotaProyekController;
use App\Http\Controllers\KetuaTimController;

// Halaman Awal
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected Routes (Wajib Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/manajemenpengguna', [PenggunaController::class, 'index'])->name('manajemenpengguna');
        Route::post('/pengguna/aktivasi', [PenggunaController::class, 'aktivasi'])->name('aktivasi');
        Route::post('/manajemenpengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::post('/manajemenpengguna/update', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::get('/manajementimkerja', [TimKerjaController::class, 'index'])->name('manajementimkerja');
        Route::post('/manajementimkerja', [TimKerjaController::class, 'store'])->name('timkerja.store');
        Route::post('/manajementimkerja/update', [TimKerjaController::class, 'update'])->name('timkerja.update');
    });

    // Direktur Routes
    Route::prefix('direktur')->name('direktur.')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard', ['role' => 'direktur', 'title' => 'Dashboard Direktur']);
        })->name('dashboard');
    });

    // Ketua Tim Routes
    Route::prefix('ketuatim')->name('ketuatim.')->group(function () {
        Route::get('/dashboard', [KetuaTimController::class, 'dashboard'])->name('dashboard');
        Route::get('/manajemenproyek', [KetuaTimController::class, 'manajemenProyek'])->name('manajemenproyek');
        Route::post('/manajemenproyek/store', [KetuaTimController::class, 'storeProyek'])->name('manajemenproyek.store');
        Route::delete('/manajemenproyek/{id}', [KetuaTimController::class, 'destroyProyek'])->name('manajemenproyek.destroy');
        });


    // Anggota Routes
    Route::prefix('anggota')->name('anggota.')->middleware(['auth'])->group(function () {
    Route::get('/proyek-aktivitas', [AnggotaProyekController::class, 'index'])->name('proyekaktivitas');
    });
});