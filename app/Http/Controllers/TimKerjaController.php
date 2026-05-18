<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use App\Models\Pengguna; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Ditambahkan untuk kebutuhan transaksi database yang aman

class TimKerjaController extends Controller
{
    // Menampilkan halaman manajemen tim kerja beserta fungsionalitas pencarian dan filter tab
    public function index(Request $request)
    {
        // Membuka kueri dasar objek model Tim Kerja beserta pemanggilan relasi ketua
        $query = TimKerja::with('ketua');

        // Menyaring data tim kerja berdasarkan klasifikasi status jika kategori filter dipilih selain 'semua'
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_tim', $request->status);
        }

        // Menyaring data tim kerja berdasarkan kata kunci pencarian nama tim
        if ($request->filled('search')) {
            $query->where('nama_tim', 'like', '%' . $request->search . '%');
        }

        // Mengeksekusi penarikan data terurut dari yang terbaru dengan pembatasan halaman 10 baris
        $timKerja = $query->latest()->paginate(10)->withQueryString();

        // Menghitung ringkasan jumlah akumulatif per status guna mengisi data komponen badge angka tab filter
        $counts = [
            'semua'    => TimKerja::count(),
            'aktif'    => TimKerja::where('status_tim', 'aktif')->count(),
            'nonaktif' => TimKerja::where('status_tim', 'nonaktif')->count(),
        ];

        // Menarik daftar pengguna spesifik yang berstatus aktif dan memiliki hak akses eksklusif sebagai 'Ketua Tim'
        $users = Pengguna::whereHas('role', function($q) {
            // Pastikan string 'Ketua Tim' di bawah ini persis sama ejaannya dengan data di database kamu
            $q->where('nama_role', 'Ketua Tim'); 
        })
        ->where('status_akun', 'aktif')
        ->orderBy('nama', 'asc')
        ->get();

        // Mengirimkan seluruh kompilasi variabel penunjang menuju berkas tampilan antarmuka
        return view('admin.manajementimkerja', compact('timKerja', 'counts', 'users'));
    }

    // Memproses penyimpanan record data tim kerja baru ke dalam basis data
    public function store(Request $request)
    {
        // 1. Validasi Identitas Tim: Memastikan nama tim wajib diisi, maksimal 100 karakter, dan belum pernah digunakan
        // 2. Validasi Penugasan Ketua: Memastikan ID ketua valid dan belum memimpin tim lain (opsi unique)
        // 3. Validasi Status Operasional: Memastikan input status bernilai aktif atau nonaktif
        $request->validate([
            'nama_tim'     => 'required|unique:tim_kerja,nama_tim|max:100',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna|unique:tim_kerja,id_ketua_tim',
            'status_tim'   => 'required|in:aktif,nonaktif',
        ], [
            // Menyediakan luaran pesan kesalahan kustom berbahasa Indonesia resmi
            'nama_tim.required'     => 'Nama tim kerja wajib diisi.',
            'nama_tim.unique'       => 'Nama tim ini sudah digunakan.',
            'id_ketua_tim.required' => 'Ketua tim wajib dipilih.',
            'id_ketua_tim.unique'   => 'Pegawai ini sudah menjabat sebagai ketua di tim lain.',
        ]);

        // Membuka gerbang transaksi database guna menjaga konsistensi integritas penyimpanan
        DB::beginTransaction();
        try {
            // Mendaftarkan entitas tim kerja baru ke tabel tim_kerja
            TimKerja::create([
                'nama_tim'      => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim'  => $request->id_ketua_tim,
                'status_tim'    => $request->status_tim,
            ]);

            // Meresmikan penyimpanan berkas ke dalam database apabila berhasil tereksekusi
            DB::commit();
            return redirect()->back()->with('success', 'Tim Kerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Membatalkan seluruh runtunan modifikasi data jika mendeteksi adanya kegagalan eksekusi
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambah tim kerja: ' . $e->getMessage());
        }
    }

    // Memproses pembaruan record data tim kerja yang sudah ada di basis data
    public function update(Request $request)
    {
        // 1. Validasi Identitas Tim: Memastikan ID wajib diisi dan ada di database
        // 2. Validasi Nama Tim: Wajib diisi, maksimal 100 karakter, dan unique (kecuali untuk ID tim ini sendiri)
        // 3. Validasi Penugasan Ketua: Wajib diisi, dan unique (kecuali jika pegawai tersebut memang ketua di tim ini)
        $request->validate([
            'id_tim'       => 'required|exists:tim_kerja,id_tim',
            'nama_tim'     => 'required|max:100|unique:tim_kerja,nama_tim,' . $request->id_tim . ',id_tim',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna|unique:tim_kerja,id_ketua_tim,' . $request->id_tim . ',id_tim',
            'status_tim'   => 'required|in:aktif,nonaktif',
        ], [
            // Menyediakan luaran pesan kesalahan kustom berbahasa Indonesia resmi
            'nama_tim.required'     => 'Nama tim kerja wajib diisi.',
            'nama_tim.unique'       => 'Nama tim ini sudah digunakan oleh tim lain.',
            'id_ketua_tim.required' => 'Ketua tim wajib dipilih.',
            'id_ketua_tim.unique'   => 'Pegawai ini sudah menjabat sebagai ketua di tim lain.',
        ]);

        // Membuka gerbang transaksi database guna menjaga konsistensi integritas penyimpanan
        DB::beginTransaction();
        try {
            // Mencari data tim kerja spesifik berdasarkan ID
            $timKerja = TimKerja::findOrFail($request->id_tim);
            
            // Memperbarui atribut entitas tim kerja
            $timKerja->update([
                'nama_tim'      => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim'  => $request->id_ketua_tim,
                'status_tim'    => $request->status_tim,
            ]);

            // Meresmikan penyimpanan berkas ke dalam database apabila berhasil tereksekusi
            DB::commit();
            return redirect()->back()->with('success', 'Data Tim Kerja berhasil diperbarui!');
        } catch (\Exception $e) {
            // Membatalkan seluruh runtunan modifikasi data jika mendeteksi adanya kegagalan eksekusi
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tim kerja: ' . $e->getMessage());
        }
    }
}