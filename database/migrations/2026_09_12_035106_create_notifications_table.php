<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// =========================================================================
// MIGRATION: NOTIFICATIONS
// Membangun tabel sistem bawaan Laravel untuk fitur notifikasi polimorfik (Lonceng).
// =========================================================================
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();           // UUID agar tiap notifikasi punya id unik
            $table->string('type');                  // nama kelas notifikasi (GeneralNotification)
            $table->morphs('notifiable');            // penerima (polimorfik: notifiable_type + notifiable_id)
            $table->text('data');                    // isi notifikasi dalam JSON (judul, pesan, kategori)
            $table->timestamp('read_at')->nullable(); // kosong = belum dibaca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
