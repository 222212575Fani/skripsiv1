<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Models\Pengguna; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimKerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = TimKerja::with('ketua');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_tim', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_tim', 'like', '%' . $request->search . '%');
        }

        $timKerja = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'semua'    => TimKerja::count(),
            'aktif'    => TimKerja::where('status_tim', 'aktif')->count(),
            'nonaktif' => TimKerja::where('status_tim', 'nonaktif')->count(),
        ];

        $users = Pengguna::whereHas('role', function($q) {
            $q->where('nama_role', 'Ketua Tim'); 
        })
        ->where('status_akun', 'aktif')
        ->orderBy('nama', 'asc')
        ->get();

        return view('admin.manajementimkerja', compact('timKerja', 'counts', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tim'     => 'required|unique:tim_kerja,nama_tim|max:100',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna',
            'status_tim'   => 'required|in:aktif,nonaktif',
        ], [
            'nama_tim.required'     => 'Nama tim kerja wajib diisi.',
            'nama_tim.unique'       => 'Nama tim ini sudah digunakan.',
            'id_ketua_tim.required' => 'Ketua tim wajib dipilih.',
        ]);

        // Logika bisnis: Cek apakah sudah jadi ketua di tim lain yang aktif
        $cek = TimKerja::where('id_ketua_tim', $request->id_ketua_tim)
                       ->where('status_tim', 'aktif')
                       ->exists();

        if ($cek) {
            return back()->withInput()->with('error', 'Pegawai ini sudah menjabat sebagai Ketua Tim Aktif di tim lain.');
        }

        DB::beginTransaction();
        try {
            TimKerja::create([
                'nama_tim'      => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim'  => $request->id_ketua_tim,
                'status_tim'    => $request->status_tim,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tim Kerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah tim: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id_tim'       => 'required|exists:tim_kerja,id_tim',
            'nama_tim'     => 'required|max:100|unique:tim_kerja,nama_tim,' . $request->id_tim . ',id_tim',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna',
            'status_tim'   => 'required|in:aktif,nonaktif',
        ]);

        // Logika bisnis: Cek, tapi abaikan tim yang sedang diedit (id_tim != $request->id_tim)
        $cek = TimKerja::where('id_ketua_tim', $request->id_ketua_tim)
                       ->where('status_tim', 'aktif')
                       ->where('id_tim', '!=', $request->id_tim)
                       ->exists();

        if ($cek) {
            return back()->withInput()->with('error', 'Pegawai ini sudah menjabat sebagai Ketua Tim Aktif di tim lain.');
        }

        DB::beginTransaction();
        try {
            $tim = TimKerja::findOrFail($request->id_tim);
            $tim->update([
                'nama_tim'      => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim'  => $request->id_ketua_tim,
                'status_tim'    => $request->status_tim,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Tim Kerja berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tim: ' . $e->getMessage());
        }
    }
}