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
        Schema::create('anggota_proyek', function (Blueprint $table) {
            $table->id('id_anggota_proyek');
            $table->unsignedBigInteger('id_proyek');
            $table->unsignedBigInteger('id_pengguna');
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