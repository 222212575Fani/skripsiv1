<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * =========================================================================
 * SEEDER: PERAN PROYEK
 * Mengisi data tetap peran di dalam proyek: 1 = Ketua Proyek, 2 = Anggota.
 * Kode aplikasi memakai angka 1 dan 2 secara langsung, jadi urutan ini jangan diubah.
 * =========================================================================
 */
class PeranProyekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('peran_proyek')->insertOrIgnore([
            [
                'id_peran_proyek' => 1,
                'nama_peran_proyek' => 'Ketua Proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_peran_proyek' => 2,
                'nama_peran_proyek' => 'Anggota',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
