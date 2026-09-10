<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivitasProyek extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_proyek'; // Sesuaikan dengan nama tabel di database
    protected $primaryKey = 'id_aktivitas'; // Sesuaikan primary key jika ada
    protected $guarded = [];

    protected $casts = [
        'target' => 'float',
    ];

    /**
     * Progress setiap aktivitas dibatasi dari 0 sampai 100 persen.
     */
    public function setTargetAttribute($value): void
    {
        $this->attributes['target'] = min(100, max(0, (float) $value));
    }

    protected static function booted(): void
    {
        static::saved(fn (AktivitasProyek $aktivitas) => $aktivitas->syncProgressProyek());
        static::deleted(fn (AktivitasProyek $aktivitas) => $aktivitas->syncProgressProyek());
    }

    /**
     * Progress proyek adalah rata-rata progress seluruh aktivitasnya.
     */
    public function syncProgressProyek(): void
    {
        $rataRataProgress = static::where('id_proyek', $this->id_proyek)->avg('target') ?? 0;

        Proyek::where('id_proyek', $this->id_proyek)->update([
            'persen_progress' => min(100, max(0, round((float) $rataRataProgress, 2))),
        ]);
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
}
