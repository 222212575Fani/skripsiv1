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
            $table->id('id_anggota_tim');

            $table->unsignedBigInteger('id_tim');
            $table->unsignedBigInteger('id_pengguna');

            $table->date('tanggal_bergabung');
            $table->date('tanggal_keluar')->nullable();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_tim')
                ->references('id_tim')
                ->on('tim_kerja')
                ->onDelete('restrict');

            $table->foreign('id_pengguna')
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
        Schema::dropIfExists('anggota_tim');
    }
};
