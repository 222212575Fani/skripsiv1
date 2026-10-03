<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * =========================================================================
 * SEEDER: ROLE
 * Mengisi empat role sistem: Admin, Direktur, Ketua Tim, Anggota. Kode aplikasi
 * mengenali role lewat nama ini (mis. middleware role:Admin), sehingga penulisannya
 * harus sama persis.
 * =========================================================================
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['nama_role' => 'Admin'],
            ['nama_role' => 'Direktur'],
            ['nama_role' => 'Ketua Tim'],
            ['nama_role' => 'Anggota'],
        ];

        foreach ($roles as $role) {
            DB::table('role')->updateOrInsert(
                ['nama_role' => $role['nama_role']],
                $role
            );
        }
    }
}
