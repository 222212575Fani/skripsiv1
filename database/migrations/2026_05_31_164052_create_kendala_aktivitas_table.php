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
        Schema::create('kendala_aktivitas', function (Blueprint $table) {
            $table->id('id_kendala');
            $table->unsignedBigInteger('id_aktivitas');
            $table->foreign('id_aktivitas')->references('id_aktivitas')->on('aktivitas_proyek');
            $table->unsignedBigInteger('id_pengguna');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna');
            $table->text('kendala_internal')->nullable();
            $table->text('kendala_eksternal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendala_aktivitas');
    }
};