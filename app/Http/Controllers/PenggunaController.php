<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\TimKerja; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    /**
     * Menampilkan daftar pengguna dengan filter dan pencarian
     */
    public function index(Request $request)
    {
        $query = Pengguna::with(['role', 'anggotaTim.tim']);

        // 1. Filter Status (Sinkronisasi 'non-aktif' dari URL ke 'nonaktif' di DB)
        if ($request->filled('status') && $request->status != 'semua') {
            $status = ($request->status == 'non-aktif') ? 'nonaktif' : $request->status;
            $query->where('status_akun', $status);
        }

        // 2. Pencarian Nama atau NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        // 3. Data pendukung untuk Modal (Role & Tim)
        $roles = DB::table('role')->get(); 
        $tims = DB::table('tim_kerja')->where('status_tim', 'aktif')->get(); 

        // 4. Hitung Badge Status untuk Tab Filter
        $counts = [
            'semua'    => Pengguna::count(),
            'pending'  => Pengguna::where('status_akun', 'pending')->count(),
            'aktif'    => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.manajemenpengguna', compact('users', 'counts', 'roles', 'tims'));
    }

    /**
     * Memproses Aktivasi Pengguna (Status Pending -> Aktif)
     */
    public function aktivasi(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_role'     => 'required',
            'id_tim'      => 'nullable|exists:tim_kerja,id_tim'
        ]);

        try {
            DB::beginTransaction();

            $user = Pengguna::findOrFail($request->id_pengguna);
            $user->status_akun = 'aktif';
            $user->id_role = $request->id_role; 
            $user->save();

            // Jika admin langsung menentukan penempatan tim saat aktivasi
            if ($request->filled('id_tim')) {
                DB::table('anggota_tim')->updateOrInsert(
                    ['id_pengguna' => $user->id_pengguna],
                    [
                        'id_tim'        => $request->id_tim,
                        'tanggal_masuk' => now(),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diaktifkan!');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal aktivasi: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan Pengguna Baru yang ditambah manual oleh Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|size:18|unique:pengguna,nip',
            'email' => [
                'required',
                'email',
                'unique:pengguna,email',
                'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/'
            ],
            'password' => 'required|min:8',
        ], [
            'nip.size' => 'NIP harus 18 digit.',
            'email.regex' => 'Email wajib menggunakan domain resmi @bps.go.id'
        ]);

        try {
            Pengguna::create([
                'nama' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Enkripsi password
                'status_akun' => 'aktif', // Otomatis aktif karena diinput Admin
                'id_role' => null, // Role diatur kemudian melalui menu aktivasi/edit
            ]);

            return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambah pengguna: ' . $e->getMessage());
        }
    }
}