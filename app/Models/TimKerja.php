<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    use HasFactory;

    protected $table = 'tim_kerja';
    protected $primaryKey = 'id_tim';

    protected $fillable = [
        'nama_tim',
        'deskripsi_tim',
        'id_ketua_tim',
        'status_tim',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Ketua Tim
     */
    public function ketua()
    {
        return $this->belongsTo(Pengguna::class, 'id_ketua_tim', 'id_pengguna');
    }

    /**
     * Alias relasi untuk mendukung pemanggilan ketuaTim di Controller
     */
    public function ketuaTim()
    {
        return $this->belongsTo(Pengguna::class, 'id_ketua_tim', 'id_pengguna');
    }

    /**
     * Relasi ke Proyek (Menghubungkan tim kerja dengan daftar proyeknya)
     */
    public function proyek()
    {
        return $this->hasMany(Proyek::class, 'id_tim', 'id_tim');
    }

    /**
     * Relasi untuk semua anggota tim
     */
    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_tim', 'id_tim');
    }

    /**
     * Relasi untuk anggota yang masih aktif
     */
    public function anggotaAktif()
    {
        return $this->hasMany(AnggotaTim::class, 'id_tim', 'id_tim')
                    ->whereNull('tanggal_keluar');
    }
}