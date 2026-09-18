<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function () {
            $role = \Illuminate\Support\Facades\Auth::user()?->role?->nama_role;
            return match ($role) {
                'Admin' => route('admin.manajemenpengguna'),
                'Direktur' => route('direktur.dashboard'),
                'Ketua Tim' => route('ketuatim.dashboard'),
                'Anggota' => route('anggota.proyekaktivitas'),
                default => route('login'),
            };
        });
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\CheckActiveUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
        });
    })->create();
