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
        Schema::create('role', function (Blueprint $table) {
            $table->id('id_role'); //Atribut yang menyimpan ID peran pengguna
            $table->string('nama_role', 30)->unique(); //Atribut yang menyimpan nama peran pengguna, bersifat unik untuk menghindari duplikasi jenis peran yang sama dalam sistem
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
