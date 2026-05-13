<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Models\Pengguna; // Pastikan menggunakan Pengguna sesuai modelmu
use Illuminate\Http\Request;

class TimKerjaController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data dengan relasi ketua, filter, dan pagination
        $timKerja = TimKerja::with('ketua')
            ->when($request->status && $request->status !== 'semua', function ($query) use ($request) {
                return $query->where('status_tim', $request->status);
            })
            ->when($request->search, function ($query) use ($request) {
                return $query->where('nama_tim', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);

        // 2. Hitung jumlah data untuk Badge di Filter Tab
        $counts = [
            'semua'    => TimKerja::count(),
            'aktif'    => TimKerja::where('status_tim', 'aktif')->count(),
            'nonaktif' => TimKerja::where('status_tim', 'nonaktif')->count(),
        ];

        // 3. Ambil data Pengguna untuk pilihan Ketua Tim di Modal
        $users = Pengguna::orderBy('nama', 'asc')->get();

        return view('admin.manajementimkerja', compact('timKerja', 'counts', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tim' => 'required|unique:tim_kerja,nama_tim|max:100',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna|unique:tim_kerja,id_ketua_tim',
            'status_tim' => 'required|in:aktif,nonaktif',
        ]);

        TimKerja::create([
            'nama_tim' => $request->nama_tim,
            'deskripsi_tim' => $request->deskripsi_tim,
            'id_ketua_tim' => $request->id_ketua_tim,
            'status_tim' => $request->status_tim,
        ]);

        return redirect()->back()->with('success', 'Tim Kerja berhasil ditambahkan!');
    }
}