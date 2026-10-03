<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// =========================================================================
// MIGRATION: PROGRESS AKTIVITAS
// Membangun tabel pelaporan mingguan (progress_minggu_berjalan) dari setiap aktivitas.
// =========================================================================
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progress_aktivitas', function (Blueprint $table) {
            $table->id('id_progress');
            $table->unsignedBigInteger('id_aktivitas');
            $table->foreign('id_aktivitas')->references('id_aktivitas')->on('aktivitas_proyek');
            $table->unsignedBigInteger('id_pengguna');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna');
            // Kenaikan progress pada laporan ini (bukan total), sehingga riwayat bisa ditelusuri
            $table->decimal('progress_minggu_berjalan', 5, 2);
            // Uraian pekerjaan bersifat opsional
            $table->text('uraian_progress')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_aktivitas');
    }
};
