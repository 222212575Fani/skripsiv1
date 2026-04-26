<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil role Admin dari tabel role
        $adminRole = Role::where('nama_role', 'Admin')->first();

        // Jika role Admin belum ada, seeder dihentikan
        if (!$adminRole) {
            return;
        }

        // Membuat akun admin awal.
        // updateOrCreate dipakai supaya aman jika seeder dijalankan berkali-kali.
        Pengguna::updateOrCreate(
            [
                'email' => 'admin@bps.go.id',
            ],
            [
                'nama' => 'Admin Sistem',
                'nip' => '199001010000000001',
                'password' => Hash::make('password123'),
                'id_role' => $adminRole->id_role,
                'status_akun' => 'aktif',
                'disetujui_pada' => now(),
                'disetujui_oleh' => null,
            ]
        );
    }
}