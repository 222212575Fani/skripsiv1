<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * =========================================================================
 * MODEL: PENGGUNA (USER)
 * Ini adalah entitas utama (Core Entity) untuk Autentikasi sistem.
 * Terhubung ke banyak entitas lain: Role, TimKerja, Proyek, dan Aktivitas.
 * Dilengkapi trait Notifiable untuk menerima notifikasi sistem (Lonceng).
 * =========================================================================
 */
class Pengguna extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $primaryKey = 'id_pengguna';

    // Kolom yang boleh diisi massal (create/update). Kolom seperti disetujui_oleh
    // sengaja tidak dimasukkan.
    protected $fillable = [
        'nama',
        'nip',
        'email',
        'password',
        'remember_token',
        'id_role',
        'status_akun',
        'disetujui_pada',
    ];

    // Disembunyikan saat model diubah ke array/JSON agar kata sandi tidak ikut terkirim
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'disetujui_pada' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Role akun (Admin / Direktur / Ketua Tim / Anggota). Dasar pembatasan hak akses.
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    // Tim yang dipimpin pengguna ini sebagai Ketua Tim (maksimal satu)
    public function timDipimpin()
    {
        return $this->hasOne(TimKerja::class, 'id_ketua_tim', 'id_pengguna');
    }

    // Seluruh riwayat keanggotaan tim (termasuk yang sudah keluar)
    public function anggotaTim()
    {
        return $this->hasMany(AnggotaTim::class, 'id_pengguna', 'id_pengguna');
    }

    // Keanggotaan tim yang masih berjalan (tanggal_keluar kosong)
    public function anggotaTimAktif()
    {
        return $this->hasOne(AnggotaTim::class, 'id_pengguna', 'id_pengguna')
            ->whereNull('tanggal_keluar');
    }

    /**
     * Proyek-proyek yang dipimpin pengguna ini sebagai Ketua Proyek.
     * Ketua Proyek adalah peran per proyek (bukan role akun): seorang Anggota
     * dapat menjadi Ketua Proyek pada satu proyek dan anggota biasa di proyek lain.
     */
    public function proyekDipimpin()
    {
        return $this->hasMany(Proyek::class, 'id_ketua_proyek', 'id_pengguna');
    }
}
