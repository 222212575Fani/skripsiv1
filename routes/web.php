<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman awal diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth - Register
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Auth - Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Auth - Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route sementara setelah login berdasarkan role

// Admin diarahkan ke menu Manajemen Pengguna
Route::get('/admin/manajemenpengguna', function () {
    return 'Halaman Manajemen Pengguna Admin';
})->name('admin.manajemenpengguna');

// Menu Manajemen Tim Kerja untuk Admin
Route::get('/admin/manajementimkerja', function () {
    return 'Halaman Manajemen Tim Kerja Admin';
})->name('admin.manajementimkerja');

// Direktur diarahkan ke Dashboard
Route::get('/direktur/dashboard', function () {
    return 'Dashboard Direktur';
})->name('direktur.dashboard');

// Ketua Tim diarahkan ke Dashboard
Route::get('/ketuatim/dashboard', function () {
    return 'Dashboard Ketua Tim';
})->name('ketuatim.dashboard');

// Anggota diarahkan ke halaman Proyek dan Aktivitas Saya
Route::get('/anggota/proyekaktivitas', function () {
    return 'Proyek dan Aktivitas Saya';
})->name('anggota.proyekaktivitas');