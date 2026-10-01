<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * =========================================================================
 * MODEL: KENDALA AKTIVITAS
 * Merepresentasikan catatan hambatan (internal maupun eksternal) 
 * yang dilaporkan anggota saat mengerjakan suatu Aktivitas.
 * =========================================================================
 */
class KendalaAktivitas extends Model
{
    use HasFactory;

    protected $table = 'kendala_aktivitas';
    protected $primaryKey = 'id_kendala';
    protected $guarded = [];

    public function aktivitas()
    {
        return $this->belongsTo(AktivitasProyek::class, 'id_aktivitas', 'id_aktivitas');
    }

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}

