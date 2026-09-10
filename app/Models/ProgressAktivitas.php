<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressAktivitas extends Model
{
    use HasFactory;

    protected $table = 'progress_aktivitas';
    protected $primaryKey = 'id_progress';
    protected $guarded = [];

    protected $casts = [
        'progress_minggu_berjalan' => 'float',
    ];

    public function aktivitas()
    {
        return $this->belongsTo(AktivitasProyek::class, 'id_aktivitas', 'id_aktivitas');
    }

    public function pelapor()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
