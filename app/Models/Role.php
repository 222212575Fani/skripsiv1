<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    //Nama tabel yang digunakan dalam model ini
    protected $table = 'role';

    //Primary key dalam tabel role
    protected $primaryKey = 'id_role';

    //Timestamps
    public $timestamps = false;

    //Atribut yang boleh diisi ketika create dan update
    protected $fillable = [
        'nama_role',
    ];

    //Relasi untuk setiap satu jenis role mempunyai banyak pengguna
    public function pengguna()
    {
        return $this->hasMany(Pengguna::class, 'id_role', 'id_role');
    }
}
