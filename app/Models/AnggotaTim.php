<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * =========================================================================
 * MODEL: ANGGOTA TIM (PIVOT)
 * Merepresentasikan riwayat keanggotaan seorang Pengguna di dalam Tim Kerja.
 * Digunakan untuk melacak mutasi masuk/keluarnya anggota tim.
 * =========================================================================
 */
class AnggotaTim extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan pada model ini
    protected $table = 'anggota_tim';

    // Primary key dalam tabel anggota tim
    protected $primaryKey = 'id_anggota_tim';

    // Atribut yang boleh diisi ketika create dan update
    protected $fillable = [
        'id_tim',
        'id_pengguna',
        'tanggal_bergabung',
        'tanggal_keluar',
    ];

    // Casting atribut tanggal/waktu.
    // tanggal_keluar kosong (null) berarti anggota masih AKTIF di tim itu;
    // saat pindah tim atau dinonaktifkan, baris tidak dihapus melainkan diberi tanggal keluar.
    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_keluar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi untuk setiap anggota hanya tergabung ke dalam satu tim kerja
    public function TimKerja()
    {
        return $this->belongsTo(TimKerja::class, 'id_tim', 'id_tim');
    }

    // Relasi untuk setiap keanggotaan tim hanya dimiliki oleh satu pengguna
    public function Pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id_pengguna');
    }
}
