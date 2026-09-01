<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaProyek extends Model
{
    use HasFactory;

    protected $table = 'anggota_proyek';          // Menyesuaikan nama tabel fisik
    protected $primaryKey = 'id_anggota_proyek'; // Menyesuaikan primary key fisik

    protected $guarded = [];

    // Relasi balik ke Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    // Relasi ke Pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi ke Peran Proyek
    public function peranProyek()
    {
        return $this->belongsTo(PeranProyek::class, 'id_peran_proyek', 'id_peran_proyek');
    }
}