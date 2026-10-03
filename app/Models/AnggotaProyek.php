<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * =========================================================================
 * MODEL: ANGGOTA PROYEK (PIVOT)
 * Merepresentasikan relasi *Many-to-Many* antara Pengguna dan Proyek.
 * Menyimpan informasi tambahan berupa 'Peran Proyek' (Ketua / Anggota).
 * =========================================================================
 */
class AnggotaProyek extends Model
{
    use HasFactory;

    protected $table = 'anggota_proyek';         // Menyesuaikan nama tabel fisik

    protected $primaryKey = 'id_anggota_proyek'; // Menyesuaikan primary key fisik

    protected $guarded = [];

    // Relasi balik ke Proyek: proyek tempat orang ini terlibat
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    // Relasi ke Pengguna: orang yang terlibat di proyek tersebut
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi ke Peran Proyek (1 = Ketua Proyek, 2 = Anggota).
    // Catatan: model PeranProyek belum dibuat karena peran dibaca langsung lewat id_peran_proyek
    // pada query; relasi ini baru berfungsi bila model tersebut ditambahkan.
    public function peranProyek()
    {
        return $this->belongsTo(PeranProyek::class, 'id_peran_proyek', 'id_peran_proyek');
    }
}
