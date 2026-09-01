<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AnggotaProyek;

class Proyek extends Model
{
    use HasFactory;

    protected $table = 'proyek';          // Menyesuaikan nama tabel fisik
    protected $primaryKey = 'id_proyek'; // Menyesuaikan primary key fisik

    protected $guarded = []; // Atau tentukan $fillable sesuai kebutuhan
    
    // Definisikan relasi ke Anggota Proyek (untuk mengecek peran Siti/Fani)
    public function anggotaProyek()
    {
        return $this->hasMany(AnggotaProyek::class, 'id_proyek', 'id_proyek');
    }
}