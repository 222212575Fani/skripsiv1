<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Pengunjung yang belum login dan membuka halaman terproteksi diarahkan ke halaman login
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Pengguna yang sudah login tetapi membuka halaman tamu (mis. /login) diarahkan
        // ke halaman utama sesuai rolenya
        $middleware->redirectUsersTo(function () {
            $role = Auth::user()?->role?->nama_role;

            return match ($role) {
                'Admin' => route('admin.manajemenpengguna'),
                'Direktur' => route('direktur.dashboard'),
                'Ketua Tim' => route('ketuatim.dashboard'),
                'Anggota' => route('anggota.proyekaktivitas'),
                default => route('login'),
            };
        });
        // Logout dikecualikan dari pemeriksaan token CSRF agar tetap bisa dipakai
        // ketika sesi sudah kedaluwarsa
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        // CheckActiveUser berjalan di setiap request web: akun yang dinonaktifkan Admin
        // langsung dikeluarkan pada permintaan berikutnya
        $middleware->web(append: [
            CheckActiveUser::class,
        ]);

        // Nama pendek "role" untuk dipakai di route, mis. ->middleware('role:Admin')
        $middleware->alias([
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Token CSRF kedaluwarsa (halaman dibiarkan terbuka terlalu lama): tampilkan pesan
        // ramah dan arahkan ke login, bukan halaman error 419 bawaan
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        });
    })->create();
