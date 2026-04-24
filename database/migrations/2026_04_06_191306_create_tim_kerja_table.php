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
            $table->string('nama_tim', 100)->unique(); //Atribut yang menyimpan nama tim kerja
            $table->string('deskripsi_tim', 255)->nullable(); //Atribut yang menyimpan deskripsi dari tim kerja
            $table->unsignedBigInteger('id_ketua_tim')->unique();
            $table->enum('status_tim', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps(); //Atribut yang menyimpan informasi created_at dan updated_at

            $table->foreign('id_ketua_tim')
                ->references('id_pengguna')
                ->on('pengguna')
                ->onDelete('restrict');
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
