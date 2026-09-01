<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use App\Models\Proyek;
use Carbon\Carbon;

class KetuaTimController extends Controller
{
    // 1. Method untuk halaman Dashboard Monitoring Ketua Tim
    public function dashboard(Request $request)
    {
        $userId = auth()->id(); // ID Ketua Tim yang sedang login

        // Ambil data tim kerja yang dipimpin
        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        if (!$timKerja) {
            $totalProyek = $belumDimulai = $berjalan = $selesai = $terlambat = 0;
            $proyekTim = collect();
            return view('ketuatim.dashboard', compact('timKerja', 'proyekTim', 'totalProyek', 'belumDimulai', 'berjalan', 'selesai', 'terlambat'));
        }

        // Ambil semua data proyek tim ini untuk kalkulasi 6 card statistik secara akurat
        $semuaProyekTim = Proyek::where('id_tim', $timKerja->id_tim)->get();

        // Kalkulasi statistik berdasarkan enum di database
        $totalProyek  = $semuaProyekTim->count();
        $belumDimulai = $semuaProyekTim->where('status_proyek', 'belum_dimulai')->count();
        $berjalan     = $semuaProyekTim->where('status_proyek', 'berjalan')->count();
        $selesai      = $semuaProyekTim->where('status_proyek', 'selesai')->count();
        $terlambat    = $semuaProyekTim->where('status_proyek', 'terlambat')->count();

        // Query untuk card list proyek di bawah (mendukung Filter Status & Search)
        $query = Proyek::where('id_tim', $timKerja->id_tim);

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_proyek', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_proyek', 'like', '%' . $request->search . '%');
        }

        // Mengambil data proyek dengan pagination 6 card per halaman
        $proyekTim = $query->latest()->paginate(6)->withQueryString();

        return view('ketuatim.dashboard', compact(
            'timKerja', 
            'proyekTim', 
            'totalProyek', 
            'belumDimulai', 
            'berjalan', 
            'selesai', 
            'terlambat'
        ));
    }

    // 2. Method untuk halaman Manajemen Proyek Ketua Tim
    public function manajemenProyek(Request $request)
    {
        $userId = auth()->id();

        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        if (!$timKerja) {
            $proyeks = collect()->paginate(10);
            $counts = ['semua' => 0, 'belum_dimulai' => 0, 'berjalan' => 0, 'selesai' => 0, 'terlambat' => 0];
            return view('ketuatim.manajemenproyek', compact('proyeks', 'counts'));
        }

        $query = Proyek::where('id_tim', $timKerja->id_tim);

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_proyek', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_proyek', 'like', '%' . $request->search . '%');
        }

        $proyeks = $query->latest()->paginate(10)->withQueryString();

        $baseQuery = Proyek::where('id_tim', $timKerja->id_tim);
        $counts = [
            'semua'         => (clone $baseQuery)->count(),
            'belum_dimulai' => (clone $baseQuery)->where('status_proyek', 'belum_dimulai')->count(),
            'berjalan'      => (clone $baseQuery)->where('status_proyek', 'berjalan')->count(),
            'selesai'       => (clone $baseQuery)->where('status_proyek', 'selesai')->count(),
            'terlambat'     => (clone $baseQuery)->where('status_proyek', 'terlambat')->count(),
        ];

        return view('ketuatim.manajemenproyek', compact('proyeks', 'counts'));
    }
}