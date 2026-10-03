<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// =========================================================================
// MIGRATION: ANGGOTA PROYEK
// Membangun tabel pivot untuk mencatat pengguna mana yang terlibat di proyek mana,
// serta apa peran spesifik mereka dalam proyek tersebut.
// =========================================================================
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anggota_proyek', function (Blueprint $table) {
            $table->id('id_anggota_proyek');
            $table->unsignedBigInteger('id_proyek');
            $table->unsignedBigInteger('id_pengguna');
            // Peran seseorang DI PROYEK INI (1 = Ketua Proyek, 2 = Anggota). Disimpan di tabel pivot
            // ini, bukan di pengguna, karena orang yang sama bisa berperan berbeda pada proyek berbeda.
            $table->unsignedBigInteger('id_peran_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna');
            $table->foreign('id_peran_proyek')->references('id_peran_proyek')->on('peran_proyek');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_proyek');
    }
};
