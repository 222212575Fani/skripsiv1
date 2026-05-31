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
        Schema::create('proyek', function (Blueprint $table) {
            $table->id('id_proyek'); 
            $table->string('nama_proyek', 150);
            $table->text('deskripsi_proyek')->nullable();
            $table->unsignedBigInteger('id_tim');
            $table->foreign('id_tim')->references('id_tim')->on('tim_kerja');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_target_selesai');
            $table->date('tanggal_selesai_aktual')->nullable();
            $table->enum('status_proyek', ['belum_dimulai', 'berjalan', 'selesai', 'terlambat'])->default('belum_dimulai');
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