<?php

use Illuminate\Support\Facades\Route;

// Route untuk halaman login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Route untuk halaman registrasi
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Route langsung ke login
Route::get('/', function () {
    return redirect()->route('login');
});