<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat laporan progress: setiap hari kerja pukul 08.00 WIB, penanggung jawab aktivitas
// yang sudah 7 hari belum melapor diberi notifikasi lonceng. Memerlukan cron `schedule:run` di server.
Schedule::command('proxis:ingatkan-progress')->weekdays()->dailyAt('08:00')->timezone('Asia/Jakarta');
