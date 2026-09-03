<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnggotaProyek;
use App\Models\Pengguna;
use Carbon\Carbon;

class Proyek extends Model // Sesuaikan dengan nama class asli kamu (Proyek)
{
    use HasFactory;

    protected $table = 'proyek';          // Menyesuaikan nama tabel fisik
    protected $primaryKey = 'id_proyek'; // Menyesuaikan primary key fisik

    protected $guarded = []; 
    
    // Relasi ke tabel Pengguna (sebagai Ketua Proyek)
    public function ketuaProyek()
    {
        return $this->belongsTo(Pengguna::class, 'id_ketua_proyek', 'id_pengguna');
    }

    // Definisikan relasi ke Anggota Proyek
    public function anggotaProyek()
    {
        return $this->hasMany(AnggotaProyek::class, 'id_proyek', 'id_proyek');
    }

    /**
     * Accessor untuk mengubah status secara dinamis berdasarkan tanggal mulai.
     */
    public function getStatusProyekAttribute($value)
    {
        // Jika status di database masih 'belum_dimulai' dan tanggal mulai sudah ada
        if ($value === 'belum_dimulai' && $this->tanggal_mulai) {
            $tanggalMulai = Carbon::parse($this->tanggal_mulai);
            
            // Jika hari ini (atau sudah lewat) >= tanggal mulai proyek, anggap statusnya 'berjalan'
            if (Carbon::today()->greaterThanOrEqualTo($tanggalMulai)) {
                return 'berjalan';
            }
        }

        return $value;
    }
}