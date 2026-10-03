<?php

namespace App\Console\Commands;

use App\Models\AktivitasProyek;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * =========================================================================
 * PERINTAH TERJADWAL: PENGINGAT LAPORAN PROGRESS
 * Mengirim notifikasi lonceng kepada penanggung jawab aktivitas yang belum
 * melaporkan progress selama beberapa hari (bawaan 7 hari). Dijalankan setiap
 * hari kerja oleh penjadwal Laravel (lihat routes/console.php).
 *
 * Aturan:
 * - Hanya aktivitas yang sudah dimulai dan belum 100%.
 * - Acuan "terakhir melapor" = laporan progress terbaru; bila belum pernah
 *   ada laporan, dipakai tanggal mulai aktivitas.
 * - Hanya untuk penanggung jawab yang akunnya masih aktif.
 * - Satu aktivitas hanya diingatkan sekali dalam rentang yang sama, agar
 *   lonceng tidak dipenuhi pengingat berulang.
 * Pemakaian manual: php artisan proxis:ingatkan-progress --hari=7
 * =========================================================================
 */
class IngatkanProgress extends Command
{
    protected $signature = 'proxis:ingatkan-progress {--hari=7 : Jumlah hari tanpa laporan sebelum diingatkan}';

    protected $description = 'Kirim pengingat laporan progress kepada penanggung jawab aktivitas yang belum melapor';

    public function handle(): int
    {
        $hari = max(1, (int) $this->option('hari'));
        $batas = Carbon::now()->subDays($hari);
        $hariIni = Carbon::today();
        $terkirim = 0;

        // Aktivitas yang sudah dimulai, belum selesai, dan penanggung jawabnya aktif
        $daftar = AktivitasProyek::with('penanggungJawab', 'proyek')
            ->where('target', '<', 100)
            ->whereNotNull('tanggal_mulai')
            ->whereDate('tanggal_mulai', '<=', $hariIni)
            ->whereHas('penanggungJawab', fn ($q) => $q->where('status_akun', 'aktif'))
            ->get();

        foreach ($daftar as $aktivitas) {
            // Waktu laporan terakhir; bila belum ada, pakai tanggal mulai aktivitas
            $terakhir = DB::table('progress_aktivitas')
                ->where('id_aktivitas', $aktivitas->id_aktivitas)
                ->max('created_at');
            $acuan = $terakhir ? Carbon::parse($terakhir) : Carbon::parse($aktivitas->tanggal_mulai)->startOfDay();

            if ($acuan->gt($batas)) {
                continue; // belum lewat batas hari
            }

            // Cegah pengingat ganda: lewati bila sudah diingatkan dalam rentang yang sama
            $sudahDiingatkan = DB::table('notifications')
                ->where('notifiable_id', $aktivitas->id_penanggung_jawab)
                ->where('created_at', '>=', $batas)
                ->where('data', 'like', '%"id_aktivitas":'.$aktivitas->id_aktivitas.'}%')
                ->exists();
            if ($sudahDiingatkan) {
                continue;
            }

            $lama = (int) floor($acuan->diffInDays(Carbon::now()));
            $namaProyek = $aktivitas->proyek->nama_proyek ?? 'Proyek';

            $aktivitas->penanggungJawab->notify(new GeneralNotification(
                'Pengingat Laporan Progress',
                "Aktivitas '{$aktivitas->nama_aktivitas}' (Proyek: {$namaProyek}) belum dilaporkan progress-nya selama {$lama} hari. Silakan perbarui laporan progress Anda.",
                'Pengingat',
                ['id_aktivitas' => (int) $aktivitas->id_aktivitas]
            ));
            $terkirim++;
        }

        $this->info("Pengingat terkirim: {$terkirim}");

        return self::SUCCESS;
    }
}
