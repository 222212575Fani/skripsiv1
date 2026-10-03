<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// =========================================================================
// MIGRATION: PROYEK
// Membangun tabel fisik utama 'proyek'. Menyimpan data jadwal, status, dan
// akumulasi capaian keseluruhan (persen_progress).
// =========================================================================
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proyek', function (Blueprint $table) {
            $table->id('id_proyek');
            $table->string('nama_proyek', 150);
            $table->text('deskripsi_proyek')->nullable();
            $table->unsignedBigInteger('id_tim');
            $table->foreign('id_tim')->references('id_tim')->on('tim_kerja');
            $table->date('tanggal_mulai')->nullable();
            // Target selesai wajib (NOT NULL) agar status "terlambat" selalu bisa ditentukan
            $table->date('tanggal_target_selesai');
            // Tanggal selesai aktual baru terisi ketika proyek benar-benar selesai
            $table->date('tanggal_selesai_aktual')->nullable();
            // status dan persen_progress adalah kolom TURUNAN: dihitung otomatis oleh model Proyek
            // dari tanggal dan rata-rata progress aktivitas, tidak diisi manual oleh pengguna
            $table->enum('status_proyek', ['belum_dimulai', 'berjalan', 'selesai', 'terlambat'])->default('belum_dimulai');
            // DECIMAL(5,2): rentang 0.00 - 100.00 (cukup untuk persen dengan 2 desimal)
            $table->decimal('persen_progress', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyek');
    }
};
