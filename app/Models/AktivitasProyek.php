<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        static::saved(fn (AktivitasProyek $aktivitas) => $aktivitas->syncProgressProyek());
        static::deleted(fn (AktivitasProyek $aktivitas) => $aktivitas->syncProgressProyek());
    }

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

    // Relasi ke Dokumen Pendukung
    public function dokumenPendukung()
    {
        return $this->hasMany(DokumenPendukung::class, 'id_aktivitas', 'id_aktivitas');
    }
}