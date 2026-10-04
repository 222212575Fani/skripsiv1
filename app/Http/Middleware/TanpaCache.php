<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * =========================================================================
 * MIDDLEWARE: LARANG CACHE HALAMAN
 * Terpasang di setiap request web (lihat bootstrap/app.php). Peramban dilarang
 * menyimpan halaman, sehingga tombol Back setelah logout tidak menampilkan lagi
 * halaman terproteksi dari cache dan selalu meminta ulang ke server.
 * =========================================================================
 */
class TanpaCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Unduhan berkas tidak ikut dilarang di-cache
        if ($response instanceof BinaryFileResponse
            || $response instanceof StreamedResponse) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
