<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnggotaProyek;
use App\Models\AktivitasProyek; 
use App\Models\Pengguna;
use Carbon\Carbon;

class Proyek extends Model 
{
    use HasFactory;

    protected $table = 'proyek';           // Menyesuaikan nama tabel fisik
    protected $primaryKey = 'id_proyek'; // Menyesuaikan primary key fisik

    protected $guarded = []; 

    protected $casts = [
        'persen_progress' => 'float',
    ];
    
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

    // Relasi ke tabel Aktivitas Proyek (One to Many)
    public function aktivitasProyek()
    {
        return $this->hasMany(AktivitasProyek::class, 'id_proyek', 'id_proyek');
    }

    /**
     * Progress proyek selalu berasal dari rata-rata progress aktivitas.
     * Jika belum ada aktivitas, progress proyek adalah 0%.
     */
    public function getPersenProgressAttribute($value): float
    {
        $rataRataProgress = $this->relationLoaded('aktivitasProyek')
            ? $this->aktivitasProyek->avg('target')
            : $this->aktivitasProyek()->avg('target');

        return min(100, max(0, round((float) ($rataRataProgress ?? 0), 2)));
    }

    /**
     * Accessor untuk menghitung status proyek secara otomatis (real-time)
     * Berdasarkan Tanggal Mulai, Tanggal Selesai (Target), dan Progres.
     */
    public function getStatusProyekAttribute($value)
    {
        $today = Carbon::today();
        $mulai = $this->tanggal_mulai ? Carbon::parse($this->tanggal_mulai) : null;
        $selesai = $this->tanggal_target_selesai ? Carbon::parse($this->tanggal_target_selesai) : null;

        // Jika tanggal belum lengkap, kembalikan nilai asli dari database
        if (!$mulai || !$selesai) {
            return $value;
        }

        $progres = $this->persen_progress;

        // 1. Jika progres sudah 100%, otomatis Selesai
        if ($progres >= 100) {
            return 'selesai';
        }

        // 2. Jika hari ini masih sebelum tanggal mulai, otomatis Belum Dimulai
        if ($today->lt($mulai)) {
            return 'belum_dimulai';
        }

        // 3. Jika hari ini sudah melewati tanggal target selesai dan progres belum 100%, otomatis Terlambat
        if ($today->gt($selesai) && $progres < 100) {
            return 'terlambat';
        }

        // 4. Jika berada di rentang tanggal berjalan dan progres < 100, otomatis Sedang Berjalan
        return 'berjalan';
    }
}
