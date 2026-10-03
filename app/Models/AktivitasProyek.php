<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * =========================================================================
 * MODEL: AKTIVITAS PROYEK
 * Merepresentasikan entitas tugas/aktivitas turunan dari suatu Proyek.
 * Mengatur relasi ke Proyek, Penanggung Jawab, Dokumen, dan Kendala.
 * =========================================================================
 */
class AktivitasProyek extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_proyek';

    protected $primaryKey = 'id_aktivitas';

    protected $guarded = [];

    protected $casts = [
        'target' => 'float',
    ];

    /**
     * Mutator: setiap nilai progress (kolom "target") dijepit ke rentang 0-100,
     * sehingga data di database tidak mungkin keluar dari batas walau
     * validasi di controller terlewati.
     */
    public function setTargetAttribute($value): void
    {
        $this->attributes['target'] = min(100, max(0, (float) $value));
    }

    /**
     * Model event: bagian ini yang membuat status dan progress bersifat otomatis.
     * Dijalankan setiap aktivitas disimpan atau dihapus, dari controller mana pun.
     */
    protected static function booted(): void
    {
        // SEBELUM disimpan: pastikan progress valid, hitung status otomatis dari
        // tanggal dan progress, lalu isi tanggal selesai aktual saat mencapai 100%.
        static::saving(function (AktivitasProyek $aktivitas) {
            $aktivitas->target = min(100, max(0, (float) ($aktivitas->target ?? 0)));
            $aktivitas->status_aktivitas = $aktivitas->hitungStatusOtomatis();
            if ($aktivitas->status_aktivitas === 'selesai' && ! $aktivitas->tanggal_selesai_aktual) {
                $aktivitas->tanggal_selesai_aktual = Carbon::today()->toDateString();
            }
        });

        // SESUDAH disimpan: hapus penanda cache sinkronisasi (agar halaman berikutnya
        // memakai data terbaru) lalu hitung ulang progress proyek induknya.
        static::saved(function (AktivitasProyek $aktivitas) {
            Cache::forget('proyek_status_synced_recent');
            Cache::forget('aktivitas_status_synced_recent');
            $aktivitas->syncProgressProyek();
        });

        // SESUDAH dihapus: progress proyek dihitung ulang tanpa aktivitas tersebut.
        static::deleted(function (AktivitasProyek $aktivitas) {
            Cache::forget('proyek_status_synced_recent');
            Cache::forget('aktivitas_status_synced_recent');
            $aktivitas->syncProgressProyek();
        });
    }

    /**
     * Hitung status aktivitas secara otomatis (tidak pernah diisi manual).
     * Urutan pemeriksaan (yang pertama cocok langsung dipakai):
     * 1. Progress 100%                                 -> Selesai (boleh lebih cepat dari target).
     * 2. Lewat tanggal target selesai, progress < 100% -> Terlambat.
     * 3. Progress > 0% atau sudah masuk tanggal mulai  -> Sedang Berjalan.
     * 4. Selain itu                                    -> Belum Dimulai.
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
     * Status bergantung pada tanggal hari ini, jadi aktivitas yang tidak disentuh
     * pun bisa berubah menjadi "terlambat" tanpa ada yang menyimpan datanya.
     * Metode ini dipanggil di awal halaman daftar/dashboard; dibatasi (throttle)
     * sekali per 10 menit lewat cache agar tidak memberatkan server.
     */
    public static function sinkronkanSemuaStatus(bool $force = false): void
    {
        if (! $force && Cache::has('aktivitas_status_synced_recent')) {
            return;
        }
        Cache::put('aktivitas_status_synced_recent', true, now()->addMinutes(10));

        $today = Carbon::today();
        $semua = static::all();
        foreach ($semua as $a) {
            $statusBaru = $a->hitungStatusOtomatis();
            $dirty = [];
            if ($a->getRawOriginal('status_aktivitas') !== $statusBaru) {
                $dirty['status_aktivitas'] = $statusBaru;
            }
            if ($statusBaru === 'selesai' && ! $a->tanggal_selesai_aktual) {
                $dirty['tanggal_selesai_aktual'] = $today->toDateString();
            }
            // updateQuietly: simpan tanpa memicu model event, supaya tidak terjadi
            // perulangan (event saved -> sinkron -> simpan -> event saved ...).
            if (! empty($dirty)) {
                $a->updateQuietly($dirty);
            }
        }
    }

    /**
     * Dorong perubahan aktivitas ke proyek induknya:
     * progress proyek = rata-rata progress semua aktivitasnya, lalu status
     * proyek dihitung ulang (proyek selesai bila rata-rata mencapai 100%).
     */
    public function syncProgressProyek(): void
    {
        $proyek = $this->proyek ?? Proyek::find($this->id_proyek);
        if ($proyek) {
            $rataRataProgress = static::where('id_proyek', $this->id_proyek)->avg('target') ?? 0;
            $progres = min(100, max(0, round((float) $rataRataProgress, 2)));

            $proyek->persen_progress = $progres;
            $statusBaru = $proyek->hitungStatusOtomatis();

            $proyek->updateQuietly([
                'persen_progress' => $progres,
                'status_proyek' => $statusBaru,
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
                ->filter(fn ($v) => ! is_null($v) && trim((string) $v) !== '')
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
                ->filter(fn ($v) => ! is_null($v) && trim((string) $v) !== '')
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

    /**
     * Accessor untuk riwayat pelaporan progress beserta tanggal pelaporan, pelapor, dan uraian
     */
    public function getRiwayatProgressAttribute(): array
    {
        if ($this->relationLoaded('progressAktivitas')) {
            $list = $this->progressAktivitas;
        } else {
            $list = $this->progressAktivitas()->with('pelapor')->orderByDesc('created_at')->get();
        }

        return $list->sortByDesc('created_at')->map(function ($item) {
            $carbon = $item->created_at ? Carbon::parse($item->created_at)->locale('id') : null;

            return [
                'id' => $item->id_progress ?? null,
                'progress' => (float) ($item->progress_minggu_berjalan ?? 0),
                'uraian' => $item->uraian_progress ?? '',
                'pelapor' => $item->pelapor->nama ?? 'Anggota Tim',
                'tanggal' => $carbon ? $carbon->translatedFormat('d M Y, H:i') : '-',
                'tanggal_lengkap' => $carbon ? $carbon->translatedFormat('l, d F Y - H:i').' WIB' : '-',
                'waktu_lalu' => $carbon ? $carbon->diffForHumans() : '',
            ];
        })->values()->all();
    }
}
