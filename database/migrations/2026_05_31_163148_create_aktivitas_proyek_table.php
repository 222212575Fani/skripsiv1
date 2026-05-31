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
        Schema::create('aktivitas_proyek', function (Blueprint $table) {
            $table->id('id_aktivitas');
            $table->unsignedBigInteger('id_proyek');
            $table->foreign('id_proyek')->references('id_proyek')->on('proyek');
            $table->string('nama_aktivitas', 150);
            $table->text('deskripsi_aktivitas')->nullable();
            $table->unsignedBigInteger('id_penanggung_jawab');
            $table->foreign('id_penanggung_jawab')->references('id_pengguna')->on('pengguna');
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->foreign('dibuat_oleh')->references('id_pengguna')->on('pengguna');
            $table->unsignedBigInteger('diperbarui_oleh')->nullable();
            $table->foreign('diperbarui_oleh')->references('id_pengguna')->on('pengguna');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_target_selesai');
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->decimal('target', 5, 2);
            $table->enum('status_aktivitas', ['belum_dimulai', 'berjalan', 'selesai', 'terlambat'])->default('belum_dimulai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_proyek');
    }
};