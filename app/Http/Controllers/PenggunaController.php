<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengguna::with(['role', 'anggotaTim.tim']);

        // Filter Status
        if ($request->filled('status') && $request->status != 'semua') {
            $status = ($request->status == 'non-aktif') ? 'nonaktif' : $request->status;
            $query->where('status_akun', $status);
        }

        // Pencarian Nama/NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        // Data untuk Dropdown (Sesuai nama tabel di DB kamu)
        $roles = DB::table('role')->get();
        $tims = DB::table('tim_kerja')->get(); 

        $counts = [
            'pending' => Pengguna::where('status_akun', 'pending')->count(),
            'aktif'   => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.manajemenpengguna', compact('users', 'counts', 'roles', 'tims'));
    }

    public function aktivasi(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'id_role' => 'required',
            'id_tim'  => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            $user = Pengguna::findOrFail($request->user_id);
            $user->status_akun = 'aktif';
            $user->id_role = $request->id_role; // Pastikan kolom di model Pengguna namanya id_role
            $user->save();

            // Jika Tim Kerja dipilih
            if ($request->filled('id_tim')) {
                DB::table('anggota_tim')->insert([
                    'id_user'       => $user->id, // Sesuaikan primary key tabel pengguna
                    'id_tim'        => $request->id_tim,
                    'tanggal_masuk' => now(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal aktivasi: ' . $e->getMessage());
        }
    }
}