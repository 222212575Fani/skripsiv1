<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Tampilan pagination memakai gaya Tailwind
        Paginator::useTailwind();
        // Nama hari dan bulan dalam Bahasa Indonesia (mis. "Senin", "Januari")
        Carbon::setLocale('id');

        // Sapaan waktu dinamis (Pagi, Siang, Sore, Malam) untuk seluruh halaman.
        // View composer "*" membagikan variabel $sapaanWaktu ke SEMUA view, dihitung
        // berdasarkan jam Asia/Jakarta, sehingga tidak perlu dihitung di tiap controller.
        View::composer('*', function ($view) {
            $jam = (int) Carbon::now('Asia/Jakarta')->format('H');
            $sapaanWaktu = match (true) {
                $jam >= 4 && $jam < 11 => 'Selamat Pagi',
                $jam >= 11 && $jam < 15 => 'Selamat Siang',
                $jam >= 15 && $jam < 18 => 'Selamat Sore',
                default => 'Selamat Malam',
            };
            $view->with('sapaanWaktu', $sapaanWaktu);
        });
    }
}
