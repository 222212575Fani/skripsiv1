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
        Schema::create('tim_kerja', function (Blueprint $table) {
            $table->id('id_tim'); //Atribut yang menyimpan ID pengguna (auto increment)
            $table->string('nama_tim', 100); //Atribut yang menyimpan nama tim kerja
            $table->string('deskripsi_tim', 255); //Atribut yang menyimpan deskripsi dari tim kerja
            $table->foreignId('id_ketua_tim')->constrained('pengguna', 'id_pengguna'); //Atribut yang menyimpan ID ketua tim dengan merujuk pada ID pengguna
            $table->timestamps(); //Atribut yang menyimpan informasi created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tim_kerja');
    }
};
