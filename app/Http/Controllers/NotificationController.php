<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * =========================================================================
 * CONTROLLER: NOTIFIKASI (LONCENG)
 * Dua endpoint JSON yang dipanggil komponen lonceng di navbar: cek notifikasi
 * terbaru dan tandai semua sebagai sudah dibaca.
 * =========================================================================
 */
class NotificationController extends Controller
{
    /**
     * Mengecek status notifikasi (jumlah belum dibaca & 15 notifikasi terbaru).
     * Dipanggil lonceng di navbar secara berkala (polling ~30 detik) tanpa memuat ulang halaman.
     * Karena membaca lewat $user->notifications, setiap pengguna hanya melihat miliknya sendiri.
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'unread_count' => 0,
                'has_unread' => false,
                'notifications' => [],
            ]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $notifications = $user->notifications()->latest()->take(15)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'category' => $n->data['category'] ?? 'PROXIS',
                'title' => $n->data['name'] ?? 'Notifikasi',
                'message' => $n->data['message'] ?? '',
                'time' => $n->created_at->locale('id')->diffForHumans(),
                'is_unread' => is_null($n->read_at),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'has_unread' => $unreadCount > 0,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Menandai seluruh notifikasi yang belum dibaca sebagai sudah dibaca
     */
    public function readAll(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return response()->json([
            'status' => 'success',
            'unread_count' => 0,
            'has_unread' => false,
        ]);
    }
}
