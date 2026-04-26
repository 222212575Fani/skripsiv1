<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    //Nama tabel yang digunakan dalam model ini
    protected $table = 'pengguna';

    //Primary key dalam tabel pengguna
    protected $primaryKey = 'id_pengguna';

    //Atribut yang boleh diisi ketika create dan update
    protected $fillable = [
        'nama',
        'nip',
        'email',
        'password',
        'token_ingat_saya',
        'id_role',
        'status_akun',
        'disetujui_pada',
        'disetujui_oleh',
    ];

    //Atribut yang disembunyikan saat diubah ke array/JSON
    protected $hidden = [
        'password',
        'token_ingat_saya',
    ];

    //Casting atribut tanggal/waktu
    protected $casts = [
        'disetujui_pada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relasi untuk setiap pengguna memiliki satu jenis role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    //Relasi untuk setiap pengguna yang menyetujui akun lain
    public function adminPenyetuju()
    {
        return $this->belongsTo(Pengguna::class, 'disetujui_oleh', 'id_pengguna');
    }

    //Relasi untuk setiap pengguna yang disetujui oleh pengguna lain
    public function penggunaDisetujui()
    {
        return $this->hasMany(Pengguna::class, 'disetujui_oleh', 'id_pengguna');
    }

    //Relasi untuk setiap pengguna yang memimpin satu tim kerja
    public function timDipimpin()
    {
        return $this->hasOne(TimKerja::class, 'id_ketua_tim', 'id_pengguna');
    }

    //Relasi untuk setiap pengguna yang merupakan anggota dari satu tim kerja
    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_pengguna', 'id_pengguna');
    }

    //Relasi untuk mengambil data anggota tim yang masih aktif (nilai untuk atribut tanggal_keluar = null)
    public function anggotaTimAktif()
    {
        return $this->hasOne(AnggotaTim::class, 'id_pengguna', 'id_pengguna')
            ->whereNull('tanggal_keluar');
    } 
}
