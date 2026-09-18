<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\KendalaAktivitas;

class AktivitasProyek extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_proyek'; 
    protected $primaryKey = 'id_aktivitas'; 
    protected $guarded = [];

    protected $casts = [
        'target' => 'float',
    ];

    public function setTargetAttribute($value): void
    {
        $this->attributes['target'] = min(100, max(0, (float) $value));
    }

    protected static function booted(): void
    {
        static::saving(function (AktivitasProyek $aktivitas) {
            $aktivitas->target = min(100, max(0, (float) ($aktivitas->target ?? 0)));
            $aktivitas->status_aktivitas = $aktivitas->hitungStatusOtomatis();
            if ($aktivitas->status_aktivitas === 'selesai' && !$aktivitas->tanggal_selesai_aktual) {
                $aktivitas->tanggal_selesai_aktual = Carbon::today()->toDateString();
            }
        });

        static::saved(function (AktivitasProyek $aktivitas) {
            \Illuminate\Support\Facades\Cache::forget('proyek_status_synced_recent');
            \Illuminate\Support\Facades\Cache::forget('aktivitas_status_synced_recent');
            $aktivitas->syncProgressProyek();
        });
        static::deleted(function (AktivitasProyek $aktivitas) {
            \Illuminate\Support\Facades\Cache::forget('proyek_status_synced_recent');
            \Illuminate\Support\Facades\Cache::forget('aktivitas_status_synced_recent');
            $aktivitas->syncProgressProyek();
        });
    }

    /**
     * Hitung status aktivitas secara otomatis (real-time)
     * 1. Jika progres 100%, otomatis Selesai (bisa selesai lebih cepat).
     * 2. Jika hari ini sebelum tanggal mulai, otomatis Belum Dimulai.
     * 3. Jika hari ini lewat tanggal target selesai dan progres < 100%, otomatis Terlambat.
     * 4. Jika berada di rentang tanggal berjalan dan progres < 100%, otomatis Sedang Berjalan.
     */
    public function hitungStatusOtomatis(): string
    {
        $today = Carbon::today();
        $mulai = $this->tanggal_mulai ? Carbon::parse($this->tanggal_mulai) : null;
        $selesai = $this->tanggal_target_selesai ? Carbon::parse($this->tanggal_target_selesai) : null;
        $progres = (float) ($this->target ?? 0);

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
     * Accessor untuk mendapatkan status aktivitas.
     * Menggunakan nilai dari database jika sudah ada agar cepat.
     */
    public function getStatusAktivitasAttribute($value)
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        return $this->hitungStatusOtomatis();
    }

    /**
     * Sinkronkan status semua aktivitas di database.
     * Diberi throttle cache 10 menit agar tidak memberatkan server.
     */
    public static function sinkronkanSemuaStatus(bool $force = false): void
    {
        if (!$force && \Illuminate\Support\Facades\Cache::has('aktivitas_status_synced_recent')) {
            return;
        }
        \Illuminate\Support\Facades\Cache::put('aktivitas_status_synced_recent', true, now()->addMinutes(10));

        $today = Carbon::today();
        $semua = static::all();
        foreach ($semua as $a) {
            $statusBaru = $a->hitungStatusOtomatis();
            $dirty = [];
            if ($a->getRawOriginal('status_aktivitas') !== $statusBaru) {
                $dirty['status_aktivitas'] = $statusBaru;
            }
            if ($statusBaru === 'selesai' && !$a->tanggal_selesai_aktual) {
                $dirty['tanggal_selesai_aktual'] = $today->toDateString();
            }
            if (!empty($dirty)) {
                $a->updateQuietly($dirty);
            }
        }
    }

    public function syncProgressProyek(): void
    {
        $proyek = $this->proyek ?? Proyek::find($this->id_proyek);
        if ($proyek) {
            $rataRataProgress = static::where('id_proyek', $this->id_proyek)->avg('target') ?? 0;
            $progres = min(100, max(0, round((float) $rataRataProgress, 2)));

            $proyek->persen_progress = $progres;
            $statusBaru = $proyek->hitungStatusOtomatis();

            $proyek->updateQuietly([
                'persen_progress'        => $progres,
                'status_proyek'          => $statusBaru,
                'tanggal_selesai_aktual' => $statusBaru === 'selesai' ? ($proyek->tanggal_selesai_aktual ?? now()->toDateString()) : null,
            ]);
        }
    }

    // Relasi balik ke Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'id_proyek', 'id_proyek');
    }

    // Relasi ke Pengguna (Penanggung Jawab)
    public function penanggungJawab()
    {
        return $this->belongsTo(Pengguna::class, 'id_penanggung_jawab', 'id_pengguna');
    }

    public function progressAktivitas()
    {
        return $this->hasMany(ProgressAktivitas::class, 'id_aktivitas', 'id_aktivitas');
    }

    // Relasi ke Dokumen Pendukung
    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukung::class, 'id_aktivitas', 'id_aktivitas');
    }

    // Relasi ke Kendala Aktivitas
    public function kendalaAktivitas()
    {
        return $this->hasMany(KendalaAktivitas::class, 'id_aktivitas', 'id_aktivitas');
    }

    /**
     * Accessor untuk kendala internal (mendukung $akt->kendala_internal dan $akt->kendalaInternal)
     */
    public function getKendalaInternalAttribute(): array
    {
        if ($this->relationLoaded('kendalaAktivitas')) {
            return $this->kendalaAktivitas
                ->pluck('kendala_internal')
                ->filter(fn($v) => !is_null($v) && trim((string)$v) !== '')
                ->values()
                ->all();
        }

        return DB::table('kendala_aktivitas')
            ->where('id_aktivitas', $this->id_aktivitas)
            ->whereNotNull('kendala_internal')
            ->where('kendala_internal', '!=', '')
            ->pluck('kendala_internal')
            ->all();
    }

    /**
     * Accessor untuk kendala eksternal (mendukung $akt->kendala_eksternal dan $akt->kendalaEksternal)
     */
    public function getKendalaEksternalAttribute(): array
    {
        if ($this->relationLoaded('kendalaAktivitas')) {
            return $this->kendalaAktivitas
                ->pluck('kendala_eksternal')
                ->filter(fn($v) => !is_null($v) && trim((string)$v) !== '')
                ->values()
                ->all();
        }

        return DB::table('kendala_aktivitas')
            ->where('id_aktivitas', $this->id_aktivitas)
            ->whereNotNull('kendala_eksternal')
            ->where('kendala_eksternal', '!=', '')
            ->pluck('kendala_eksternal')
            ->all();
    }
}