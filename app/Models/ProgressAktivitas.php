<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * =========================================================================
 * MODEL: PROGRESS AKTIVITAS
 * Merepresentasikan riwayat pelaporan persentase capaian (%) dari anggota
 * beserta uraian laporannya setiap kali mengupdate status tugas.
 * =========================================================================
 */
class ProgressAktivitas extends Model
{
    use HasFactory;

    protected $table = 'progress_aktivitas';

    protected $primaryKey = 'id_progress';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    // progress_minggu_berjalan menyimpan KENAIKAN progress pada satu laporan (bukan total),
    // sehingga seluruh riwayat bisa ditelusuri perubahan demi perubahan.
    protected $casts = [
        'progress_minggu_berjalan' => 'float',
    ];

    // Aktivitas yang dilaporkan
    public function aktivitas()
    {
        return $this->belongsTo(AktivitasProyek::class, 'id_aktivitas', 'id_aktivitas');
    }

    // Anggota yang mengirim laporan
    public function pelapor()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
