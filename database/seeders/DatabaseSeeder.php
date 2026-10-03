<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * =========================================================================
 * SEEDER UTAMA
 * Menjalankan semua seeder dengan urutan yang benar: Role dulu (karena Admin
 * membutuhkan role), lalu akun Admin, lalu Peran Proyek.
 * Perintah: php artisan migrate --seed
 * =========================================================================
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            PeranProyekSeeder::class,
        ]);
    }
}
