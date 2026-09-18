<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnggotaProyek;
use App\Models\AktivitasProyek; 
use App\Models\Pengguna;
use App\Models\TimKerja;
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

    // Relasi ke tabel Tim Kerja
    public function timKerja()
    {
        return $this->belongsTo(TimKerja::class, 'id_tim', 'id_tim');
    }

    /**
     * Progress proyek selalu berasal dari rata-rata progress aktivitas.
     * Jika sudah tersimpan di database, gunakan nilainya langsung agar query cepat.
     */
    public function getPersenProgressAttribute($value): float
    {
        if ($value !== null && $value !== '') {
            return min(100, max(0, round((float) $value, 2)));
        }

        $rataRataProgress = $this->relationLoaded('aktivitasProyek')
            ? $this->aktivitasProyek->avg('target')
            : $this->aktivitasProyek()->avg('target');

        return min(100, max(0, round((float) ($rataRataProgress ?? 0), 2)));
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($proyek) {
            $proyek->status_proyek = $proyek->hitungStatusOtomatis();
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('proyek_status_synced_recent');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('proyek_status_synced_recent');
        });
    }

    /**
     * Hitung status proyek secara otomatis (real-time)
     * Berdasarkan Tanggal Mulai, Tanggal Target Selesai, dan Progres.
     * 
     * 1. Jika progres sudah 100%, otomatis Selesai (bisa selesai lebih cepat).
     * 2. Jika hari ini masih sebelum tanggal mulai, otomatis Belum Dimulai.
     * 3. Jika hari ini sudah melewati tanggal target selesai dan progres < 100%, otomatis Terlambat.
     * 4. Jika berada di rentang tanggal berjalan dan progres < 100%, otomatis Sedang Berjalan.
     */
    public function hitungStatusOtomatis(): string
    {
        $today = Carbon::today();
        $mulai = $this->tanggal_mulai ? Carbon::parse($this->tanggal_mulai) : null;
        $selesai = $this->tanggal_target_selesai ? Carbon::parse($this->tanggal_target_selesai) : null;

        $progres = (float) $this->persen_progress;

        // 1. Jika progres sudah 100%, otomatis Selesai (bisa selesai lebih cepat)
        if ($progres >= 100) {
            return 'selesai';
        }

        // 2. Jika hari ini sudah melewati tanggal target selesai dan progres < 100%, otomatis Terlambat
        if ($selesai && $today->gt($selesai)) {
            return 'terlambat';
        }

        // 3. Jika sudah ada progress yang dilaporkan (> 0%) atau sudah memasuki tanggal mulai (today >= mulai), otomatis Sedang Berjalan
        if ($progres > 0 || ($mulai && $today->gte($mulai))) {
            return 'berjalan';
        }

        // 4. Jika hari ini masih sebelum tanggal mulai dan belum ada progress sama sekali, otomatis Belum Dimulai
        return 'belum_dimulai';
    }

    /**
     * Accessor untuk mendapatkan status proyek.
     * Menggunakan nilai dari database jika sudah ada agar hemat query.
     */
    public function getStatusProyekAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        return $this->hitungStatusOtomatis();
    }

    /**
     * Sinkronisasi status dan persentase progress semua proyek di database.
     * Diberi throttle cache 10 menit agar tidak memberatkan server di setiap request halaman.
     */
    public static function sinkronkanSemuaStatus(bool $force = false): void
    {
        if (!$force && \Illuminate\Support\Facades\Cache::has('proyek_status_synced_recent')) {
            return;
        }
        \Illuminate\Support\Facades\Cache::put('proyek_status_synced_recent', true, now()->addMinutes(10));

        AktivitasProyek::sinkronkanSemuaStatus($force);

        $today = Carbon::today();
        $proyeks = static::with('aktivitasProyek')->get();
        foreach ($proyeks as $p) {
            $statusBaru = $p->hitungStatusOtomatis();
            $progresBaru = $p->relationLoaded('aktivitasProyek') && $p->aktivitasProyek->isNotEmpty()
                ? min(100, max(0, round((float) $p->aktivitasProyek->avg('target'), 2)))
                : (float) ($p->getRawOriginal('persen_progress') ?? 0);

            $dirty = [];
            if ($p->getRawOriginal('status_proyek') !== $statusBaru) {
                $dirty['status_proyek'] = $statusBaru;
            }
            if ((float)$p->getRawOriginal('persen_progress') !== (float)$progresBaru) {
                $dirty['persen_progress'] = $progresBaru;
            }
            if ($statusBaru === 'selesai' && !$p->tanggal_selesai_aktual) {
                $dirty['tanggal_selesai_aktual'] = $today->toDateString();
            }

            if (!empty($dirty)) {
                $p->updateQuietly($dirty);
            }
        }
    }
}
