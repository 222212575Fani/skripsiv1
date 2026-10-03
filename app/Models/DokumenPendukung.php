<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * =========================================================================
 * MODEL: DOKUMEN PENDUKUNG
 * Merepresentasikan entitas file (berkas) yang diunggah anggota
 * sebagai bukti fisik penyelesaian suatu Aktivitas/Progress.
 * =========================================================================
 */
class DokumenPendukung extends Model
{
    use HasFactory;

    protected $table = 'dokumen_pendukung';

    protected $primaryKey = 'id_dokumen';

    protected $guarded = [];

    // Kolom file_path menyimpan jalur berkas di disk "public" (folder dokumen_progress);
    // basis data hanya menyimpan nama dan jalurnya, bukan isi berkasnya.

    // Relasi balik ke AktivitasProyek: aktivitas yang dibuktikan oleh dokumen ini
    public function aktivitasProyek()
    {
        return $this->belongsTo(AktivitasProyek::class, 'id_aktivitas', 'id_aktivitas');
    }
}
