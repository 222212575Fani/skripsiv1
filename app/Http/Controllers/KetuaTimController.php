<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use App\Models\Proyek;
use App\Models\AnggotaTim;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KetuaTimController extends Controller
{
    // 1. Method untuk halaman Dashboard Monitoring Ketua Tim
    public function dashboard(Request $request)
    {
        $userId = auth()->id(); // ID Ketua Tim yang sedang login

        // Ambil data tim kerja yang dipimpin
        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        // Ambil status filter dari request (default 'semua')
        $status = $request->get('status', 'semua');

        if (!$timKerja) {
            $totalProyek = $belumDimulai = $berjalan = $selesai = $terlambat = 0;
            $proyekTim = collect()->paginate(6);
            $anggotaTim = collect();
            return view('ketuatim.dashboard', compact('timKerja', 'proyekTim', 'totalProyek', 'belumDimulai', 'berjalan', 'selesai', 'terlambat', 'anggotaTim', 'status'));
        }

        // Ambil semua data proyek tim ini untuk kalkulasi card statistik secara akurat
        $semuaProyekTim = Proyek::where('id_tim', $timKerja->id_tim)->get();

        // Kalkulasi statistik berdasarkan status di database
        $totalProyek  = $semuaProyekTim->count();
        $belumDimulai = $semuaProyekTim->where('status_proyek', 'belum_dimulai')->count();
        $berjalan     = $semuaProyekTim->where('status_proyek', 'berjalan')->count();
        $selesai      = $semuaProyekTim->where('status_proyek', 'selesai')->count();
        $terlambat    = $semuaProyekTim->where('status_proyek', 'terlambat')->count();

        // Query untuk card list proyek di dashboard (memuat relasi ketuaProyek dan anggotaProyek beserta pengguna)
        $query = Proyek::where('id_tim', $timKerja->id_tim)->with(['ketuaProyek', 'anggotaProyek.pengguna']);

        if ($status !== 'semua') {
            $query->where('status_proyek', $status);
        }

        if ($request->filled('search')) {
            $query->where('nama_proyek', 'like', '%' . $request->search . '%');
        }

        // Mengambil data proyek dengan pagination 6 card per halaman
        $proyekTim = $query->latest()->paginate(6)->withQueryString();

        // AMBIL DATA KETUA PROYEK OTOMATIS BERDASARKAN TAHUN BERJALAN
        $tahunBerjalan = now()->year;

        $anggotaTim = Pengguna::whereHas('anggotaTim', function($q) use ($timKerja) {
                $q->where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar');
            })
            ->whereHas('proyekDipimpin', function($q) use ($timKerja, $tahunBerjalan) {
                $q->where('id_tim', $timKerja->id_tim)
                  ->whereYear('created_at', $tahunBerjalan);
            })
            ->withCount(['proyekDipimpin' => function($q) use ($timKerja, $tahunBerjalan) {
                $q->where('id_tim', $timKerja->id_tim)
                  ->whereYear('created_at', $tahunBerjalan);
            }])
            ->get()
            ->map(function ($member) {
                $member->sub_teks = $member->role->nama_role ?? 'Ketua Proyek';
                $member->jumlah_tugas = $member->proyek_dipimpin_count ?? 0;
                return $member;
            });

        return view('ketuatim.dashboard', compact(
            'timKerja', 
            'proyekTim', 
            'totalProyek', 
            'belumDimulai', 
            'berjalan', 
            'selesai', 
            'terlambat',
            'anggotaTim',
            'status'
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
            $anggotaTim = collect();
            return view('ketuatim.manajemenproyek', compact('proyeks', 'counts', 'anggotaTim', 'timKerja'));
        }

        // Ambil daftar anggota tim yang aktif, KECUALI ketua tim yang sedang login
        $anggotaTim = AnggotaTim::where('id_tim', $timKerja->id_tim)
            ->whereNull('tanggal_keluar')
            ->where('id_pengguna', '!=', $userId)
            ->with('pengguna')
            ->get();

        $query = Proyek::where('id_tim', $timKerja->id_tim)->with('ketuaProyek');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_proyek', $request->status);
        }

        // PENCARIAN BERDASARKAN NAMA PROYEK ATAU NAMA KETUA PROYEK
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function($q) use ($keyword) {
                $q->where('nama_proyek', 'like', '%' . $keyword . '%')
                  ->orWhereHas('ketuaProyek', function($subQ) use ($keyword) {
                      $subQ->where('nama', 'like', '%' . $keyword . '%');
                  });
            });
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

        return view('ketuatim.manajemenproyek', compact('proyeks', 'counts', 'anggotaTim', 'timKerja'));
    }
    
    // 3. Method untuk menyimpan data Proyek baru dari modal
    public function storeProyek(Request $request)
    {
        $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'deskripsi'       => 'nullable|string',
            'id_ketua_proyek' => 'required|exists:pengguna,id_pengguna',
            'status'          => 'required|in:belum_dimulai,berjalan,selesai,terlambat',
            'tanggal_mulai'   => 'nullable|date',
            'tenggat_waktu'   => 'nullable|date',
        ], [
            'nama_proyek.required'    => 'Nama proyek wajib diisi.',
            'id_ketua_proyek.required' => 'Pilih salah satu Ketua Proyek dari anggota tim.',
            'status.required'          => 'Status proyek wajib ditentukan.',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

            if (!$timKerja) {
                return redirect()->back()->with('error', 'Gagal: Anda tidak terdaftar sebagai ketua tim aktif.');
            }

            Proyek::create([
                'id_tim'                 => $timKerja->id_tim,
                'nama_proyek'            => $request->nama_proyek,
                'deskripsi_proyek'       => $request->deskripsi,            
                'id_ketua_proyek'        => $request->id_ketua_proyek,
                'status_proyek'          => $request->status,
                'tanggal_mulai'          => $request->tanggal_mulai,
                'tanggal_target_selesai' => $request->tenggat_waktu,          
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Proyek baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah proyek: ' . $e->getMessage());
        }
    }

    // 4. Method untuk menghapus data Proyek
    public function destroy($id)
    {
        try {
            $proyek = Proyek::find($id);

            if (!$proyek) {
                return redirect()->back()->with('error', 'Data proyek tidak ditemukan atau sudah dihapus.');
            }

            $proyek->delete();

            return redirect()->back()->with('success', 'Data proyek berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}