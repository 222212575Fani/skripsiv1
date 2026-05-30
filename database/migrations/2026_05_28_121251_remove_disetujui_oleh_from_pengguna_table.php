<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropForeign(['disetujui_oleh']);
            $table->dropColumn('disetujui_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->foreign('disetujui_oleh')->references('id_pengguna')->on('pengguna');
        });
    }
};