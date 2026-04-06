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
        Schema::create('anggota_tim', function (Blueprint $table) {
            $table->id('id_anggota_tim'); //Atribut yang menyimpan ID pengguna dalam tim kerja
            $table->foreignId('id_tim')->constrained('tim_kerja'); //Atribut yang menyimpan ID tim kerja
            $table->foreignId('id_pengguna')->constrained('pengguna'); //Atribut yang menyimpan ID pengguna
            $table->date('tanggal_bergabung'); //Atribut yang menyimpan tanggal seorang pengguna bergabung dalam tim kerja
            $table->date('tanggal_keluar')->nullable(); //Atribut yang menyimpan tanggal seorang pengguna keluar/pindah tim kerja. Boleh null karena belum tentu pindah/keluar keanggotaan tim
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_tim');
    }
};
