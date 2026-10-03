<?php

namespace Database\Seeders;

use App\Models\Pengguna;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * =========================================================================
 * SEEDER: ADMIN AWAL
 * Membuat satu akun Admin (admin@bps.go.id) agar sistem bisa dipakai pertama kali:
 * pengguna lain mendaftar dengan status pending dan hanya Admin yang dapat mengaktifkannya.
 * Aman dijalankan berulang karena memakai updateOrCreate. Ganti kata sandi bawaan
 * setelah login pertama.
 * =========================================================================
 */
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
        if (! $adminRole) {
            return;
        }

        // Membuat akun admin awal.
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
