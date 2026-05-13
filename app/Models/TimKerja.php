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
     * Saya sarankan ganti nama menjadi 'ketua' agar lebih singkat 
     * dan sesuai dengan Controller yang kita buat sebelumnya.
     */
    public function ketua()
    {
        // Pastikan Model Pengguna sudah ada, atau gunakan User jika itu nama modelnya
        return $this->belongsTo(Pengguna::class, 'id_ketua_tim', 'id_pengguna');
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