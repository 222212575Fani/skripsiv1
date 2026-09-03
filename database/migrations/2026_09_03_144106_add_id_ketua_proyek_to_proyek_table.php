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
        Schema::table('proyek', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ketua_proyek')->nullable()->after('deskripsi_proyek');
            $table->foreign('id_ketua_proyek')->references('id_pengguna')->on('pengguna')->onDelete('set null'); // <-- Tambahkan titik koma di sini
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek', function (Blueprint $table) {
            $table->dropForeign(['id_ketua_proyek']);
            $table->dropColumn('id_ketua_proyek');
        });
    }
};