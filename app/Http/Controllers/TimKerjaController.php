<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use Illuminate\Support\Facades\DB;

class TimKerjaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data dari database dengan pagination
        // Pastikan nama modelnya benar (TimKerja)
        $timKerja = TimKerja::with('ketua') // Eager loading relasi ketua agar tidak error
            ->when($request->status && $request->status !== 'semua', function($query) use ($request) {
                return $query->where('status_tim', $request->status);
            })
            ->when($request->search, function($query) use ($request) {
                return $query->where('nama_tim', 'like', '%' . $request->search . '%');
            })
            ->paginate(10);

        // 2. Hitung jumlah untuk Badge (agar tidak error juga)
        $counts = [
            'semua' => TimKerja::count(),
            'aktif' => TimKerja::where('status_tim', 'aktif')->count(),
            'nonaktif' => TimKerja::where('status_tim', 'nonaktif')->count(),
        ];

        // 3. Kirim ke view (Pastikan variabel $timKerja dan $counts terkirim)
        return view('admin.manajementimkerja', compact('timKerja', 'counts'));
    }
}