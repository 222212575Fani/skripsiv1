<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';

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

    protected $hidden = [
        'password',
        'token_ingat_saya',
    ];

    protected $casts = [
        'disetujui_pada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function timDipimpin()
    {
        return $this->hasOne(TimKerja::class, 'id_ketua_tim', 'id_pengguna');
    }

    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_pengguna', 'id_pengguna');
    }

    public function anggotaTimAktif()
    {
        return $this->hasOne(AnggotaTim::class, 'id_pengguna', 'id_pengguna')
            ->whereNull('tanggal_keluar');
    } 

    /**
     * TAMBAHKAN RELASI INI:
     * Relasi untuk mengambil proyek-proyek yang dipimpin oleh pengguna ini sebagai Ketua Proyek
     */
    public function proyekDipimpin()
    {
        return $this->hasMany(Proyek::class, 'id_ketua_proyek', 'id_pengguna');
    }
}