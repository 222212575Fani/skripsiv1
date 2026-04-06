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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna'); //Atribut yang menyimpan ID pengguna (auto increment)
            $table->string('nama', 100); //Atribut yang menyimpan nama lengkap pengguna
            $table->string('nip', 18)->unique(); //Atribut yang menyimpan NIP pengguna, harus unik setiap pengguna
            $table->string('email', 100)-> unique(); //Atribut yang menyimpan email pengguna, harus unik setiap pengguna
            $table->string('password', 255); //Atribut yang menyimpan kata sandi akun pengguna
            $table->string('token_ingat_saya', 100)->nullable(); //Atribut yang menyimpan token untuk fitur ingatkan saya saat login
            $table->unsignedBigInteger('id_role')->nullable();
            $table->foreign('id_role')->references('id_role')->on('role'); //Atribut yang menyimpan role/peran pengguna
            $table->unsignedBigInteger('id_status_akun');
            $table->foreign('id_status_akun')->references('id_status_akun')->on('status_akun'); //Atribut yang menyimpan status akun pengguna sebelum dan setelah aktivasi akun
            $table->timestamp('disetujui_pada')->nullable(); //Atribut yang menyimpan waktu persetujuan aktivasi akun oleh admin
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->foreign('disetujui_oleh')->references('id_pengguna')->on('pengguna'); //Atribut yang menyimpan ID pengguna yang menyetujui aktivasi akun pengguna
            $table->timestamps(); //Atribut yang menyimpan created_at (waktu pengguna registrasi) dan updated_at (waktu setiap kali ada perubahan data pengguna oleh admin mulai dari waktu proses aktivasi akun)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
