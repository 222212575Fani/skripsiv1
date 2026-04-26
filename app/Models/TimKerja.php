<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    use HasFactory;

    //Nama tabel yang digunakan dalam model ini
    protected $table = 'tim_kerja';

    //Primary key dalam tabel tim kerja
    protected $primaryKey = 'id_tim';

    //Atribut yang boleh diisi ketika create dan update
    protected $fillable = [
        'nama_tim',
        'deskripsi_tim',
        'id_ketua_tim',
        'status_tim',
    ];

    //Casting atribut tanggal/waktu
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relasi untuk setiap tim kerja dipimpin oleh satu pengguna sebagai ketua tim
    public function ketuaTim()
    {
        return $this->belongsTo(Pengguna::class, 'id_ketua_tim', 'id_pengguna');
    }

    //Relasi untuk setiap tim kerja mempunyai banyak anggota tim
    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_tim', 'id_tim');
    }

    //Relasi untuk mengambil data anggota tim yang masih aktif (nilai untuk atribut tanggal_keluar = null)
    public function anggotaAktif()
    {
        return $this->hasMany(AnggotaTim::class, 'id_tim', 'id_tim') ->whereNull('tanggal_keluar');
    }
}
