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
        Schema::create('status_akun', function (Blueprint $table) {
            $table->id('id_status_akun'); //Atribut yang menyimpan ID jenis status akun pengguna
            $table->string('nama_status_akun', 30)->unique(); //Atribut yang menyimpan nama status akun pengguna, bersifat unik untuk menghindari duplikasi jenis status akun
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_akun');
    }
};
