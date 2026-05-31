<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // Menghapus kolom token_ingat_saya
            $table->dropColumn('token_ingat_saya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            // Menambahkan kembali jika sewaktu-waktu melakukan rollback
            // Asumsi tipe datanya string, sesuaikan jika sebelumnya pakai tipe lain
            $table->string('token_ingat_saya')->nullable();
        });
    }
};