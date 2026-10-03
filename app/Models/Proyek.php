<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * =========================================================================
 * MODEL: PROYEK
 * Merepresentasikan entitas Proyek yang sedang digarap oleh suatu Tim Kerja.
 * Memiliki kalkulasi otomatis persentase (progress) yang diturunkan
 * dari seluruh Aktivitas di bawahnya.
 * =========================================================================
 */
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

    /**
     * Model event untuk proyek: status selalu dihitung ulang tiap kali disimpan,
     * sehingga pengguna tidak bisa mengisinya secara manual.
     */
    protected static function boot()
    {
        parent::boot();

        // Sebelum disimpan: tentukan status otomatis dari tanggal dan progress.
        static::saving(function ($proyek) {
            $proyek->status_proyek = $proyek->hitungStatusOtomatis();
        });

        // Setelah disimpan/dihapus: hapus penanda cache agar data disinkronkan ulang.
        static::saved(function () {
            Cache::forget('proyek_status_synced_recent');
        });

        static::deleted(function () {
            Cache::forget('proyek_status_synced_recent');
        });
    }

    /**
     * Hitung status proyek secara otomatis dari tanggal mulai, tanggal target
     * selesai, dan progress (rata-rata progress aktivitas). Aturannya sama
     * dengan status aktivitas; yang pertama cocok langsung dipakai:
     * 1. Progress 100%                                 -> Selesai.
     * 2. Lewat tanggal target selesai, progress < 100% -> Terlambat.
     * 3. Progress > 0% atau sudah masuk tanggal mulai  -> Sedang Berjalan.
     * 4. Selain itu                                    -> Belum Dimulai.
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
     * Dipanggil di awal halaman dashboard/daftar agar status yang bergantung pada
     * tanggal (misalnya "terlambat") ikut diperbarui. Dibatasi sekali per 10 menit
     * lewat cache; aktivitas disinkronkan lebih dulu karena progress proyek
     * diturunkan dari aktivitasnya.
     */
    public static function sinkronkanSemuaStatus(bool $force = false): void
    {
        if (! $force && Cache::has('proyek_status_synced_recent')) {
            return;
        }
        Cache::put('proyek_status_synced_recent', true, now()->addMinutes(10));

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
            if ((float) $p->getRawOriginal('persen_progress') !== (float) $progresBaru) {
                $dirty['persen_progress'] = $progresBaru;
            }
            if ($statusBaru === 'selesai' && ! $p->tanggal_selesai_aktual) {
                $dirty['tanggal_selesai_aktual'] = $today->toDateString();
            }

            if (! empty($dirty)) {
                $p->updateQuietly($dirty);
            }
        }
    }
}
