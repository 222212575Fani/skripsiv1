<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusAkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('status_akun')->insert([
            ['nama_status_akun' => 'Menunggu Aktivasi'],
            ['nama_status_akun' => 'Aktif'],
        ]);
    }
}
