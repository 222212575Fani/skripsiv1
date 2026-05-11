<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan 'anggotaTim.tim' dihapus dari with() jika relasinya belum dibenarkan, 
        // cukup 'role' dan 'anggotaTim' dulu agar tidak error.
        $query = Pengguna::with(['role', 'anggotaTim']); 

        // 1. FILTER STATUS
        if ($request->filled('status') && $request->status != 'semua') {
            $status = ($request->status == 'non-aktif') ? 'nonaktif' : $request->status;
            $query->where('status_akun', $status);
        }

        // 2. PENCARIAN (Hanya Nama dan NIP)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', '%' . $search . '%')
                  ->orWhere('nip', 'LIKE', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $counts = [
            'pending' => Pengguna::where('status_akun', 'pending')->count(),
            'aktif'   => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.manajemenpengguna', compact('users', 'counts'));
    }
}