<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * =========================================================================
 * NOTIFIKASI: GENERAL NOTIFICATION
 * Satu kelas notifikasi umum untuk seluruh jenis pemberitahuan di sistem
 * (penugasan, aktivasi, laporan progress, dll). Hanya memakai kanal "database",
 * jadi pesan disimpan di tabel `notifications` dan ditampilkan lewat lonceng.
 * Pemakaian: $pengguna->notify(new GeneralNotification($judul, $pesan));
 * =========================================================================
 */
class GeneralNotification extends Notification
{
    use Queueable;

    protected $title;

    protected $message;

    protected $category;

    // Data tambahan opsional (mis. id_aktivitas untuk mencegah pengingat ganda)
    protected $extra;

    // Konstruktor menerima Judul, Pesan, Kategori opsional, dan data tambahan opsional
    public function __construct($title, $message, $category = 'PROXIS', array $extra = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->category = $category;
        $this->extra = $extra;
    }

    // Kanal pengiriman: hanya database (tidak ada email atau SMS)
    public function via($notifiable)
    {
        return ['database'];
    }

    // Isi yang disimpan di kolom `data` tabel notifications (dibaca NotificationController)
    public function toArray($notifiable)
    {
        return array_merge([
            'name' => $this->title,
            'message' => $this->message,
            'category' => $this->category ?? 'PROXIS',
        ], $this->extra);
    }
}
