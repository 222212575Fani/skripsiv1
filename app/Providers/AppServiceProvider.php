<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        \Carbon\Carbon::setLocale('id');

        // Sapaan waktu dinamis (Pagi, Siang, Sore, Malam) untuk seluruh halaman
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $jam = (int) \Carbon\Carbon::now('Asia/Jakarta')->format('H');
            $sapaanWaktu = match(true) {
                $jam >= 4 && $jam < 11 => 'Selamat Pagi',
                $jam >= 11 && $jam < 15 => 'Selamat Siang',
                $jam >= 15 && $jam < 18 => 'Selamat Sore',
                default => 'Selamat Malam',
            };
            $view->with('sapaanWaktu', $sapaanWaktu);
        });
    }
}
