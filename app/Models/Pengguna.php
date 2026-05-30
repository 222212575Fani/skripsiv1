<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    // Nama tabel yang digunakan dalam model ini
    protected $table = 'pengguna';

    // Primary key dalam tabel pengguna
    protected $primaryKey = 'id_pengguna';

    // Atribut yang boleh diisi ketika create dan update
    // Kolom 'disetujui_oleh' telah dihapus karena sudah tidak ada di database
    protected $fillable = [
        'nama',
        'nip',
        'email',
        'password',
        'token_ingat_saya',
        'id_role',
        'status_akun',
        'disetujui_pada',
    ];

    // Atribut yang disembunyikan saat diubah ke array/JSON
    protected $hidden = [
        'password',
        'token_ingat_saya',
    ];

    // Casting atribut tanggal/waktu
    protected $casts = [
        'disetujui_pada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi untuk setiap pengguna memiliki satu jenis role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // Relasi untuk setiap pengguna yang memimpin satu tim kerja
    public function timDipimpin()
    {
        return $this->hasOne(TimKerja::class, 'id_ketua_tim', 'id_pengguna');
    }

    // Relasi untuk setiap pengguna yang merupakan anggota dari satu tim kerja
    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi untuk mengambil data anggota tim yang masih aktif
    public function anggotaTimAktif()
    {
        return $this->hasOne(AnggotaTim::class, 'id_pengguna', 'id_pengguna')
            ->whereNull('tanggal_keluar');
    } 
}