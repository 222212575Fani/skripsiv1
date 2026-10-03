<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * =========================================================================
 * MIDDLEWARE: PEMBATASAN HAK AKSES MENURUT ROLE
 * Dipasang di grup route admin, direktur, ketuatim, dan anggota (routes/web.php).
 * Sisi server inilah yang sebenarnya menolak akses; menu yang disembunyikan di
 * tampilan saja tidak cukup karena URL masih bisa diketik langsung.
 * =========================================================================
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     * Memastikan hanya pengguna dengan role yang diizinkan yang bisa membuka halaman.
     * Contoh pemakaian di route: ->middleware('role:Admin') atau ->middleware('role:Ketua Tim').
     * Jika role tidak sesuai, pengguna dikembalikan ke halaman utama sesuai rolenya.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $namaRole = Auth::user()?->role?->nama_role;

        if (in_array($namaRole, $roles, true)) {
            return $next($request);
        }

        // Permintaan AJAX/JSON (mis. data grafik) cukup ditolak dengan status 403
        if ($request->expectsJson()) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        $halamanUtama = match ($namaRole) {
            'Admin' => route('admin.manajemenpengguna'),
            'Direktur' => route('direktur.dashboard'),
            'Ketua Tim' => route('ketuatim.dashboard'),
            'Anggota' => route('anggota.proyekaktivitas'),
            default => null,
        };

        if (! $halamanUtama) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return redirect($halamanUtama)
            ->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
    }
}
