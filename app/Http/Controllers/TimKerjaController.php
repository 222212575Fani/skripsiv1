<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\TimKerja;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * =========================================================================
 * CONTROLLER: TIM KERJA (ADMIN)
 * Admin membuat dan mengubah tim kerja. Aturan penting: satu orang hanya boleh
 * memimpin satu tim, role Ketua Tim terpasang otomatis pada ketua terpilih, dan
 * ketua lama kembali menjadi Anggota saat diganti.
 * =========================================================================
 */
class TimKerjaController extends Controller
{
    // =========================================================================
    // KELOMPOK 1: MENAMPILKAN HALAMAN MANAJEMEN TIM KERJA
    // Mengambil daftar tim kerja dan menampilkannya di Dashboard Admin.
    // =========================================================================

    public function index(Request $request)
    {
        $query = TimKerja::with('ketua');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_tim', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('nama_tim', 'like', '%'.$request->search.'%');
        }

        $timKerja = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'semua' => TimKerja::count(),
            'aktif' => TimKerja::where('status_tim', 'aktif')->count(),
            'nonaktif' => TimKerja::where('status_tim', 'nonaktif')->count(),
        ];

        // Ambil semua pengguna aktif ber-role 'Anggota' atau 'Ketua Tim'
        $users = Pengguna::whereHas('role', function ($q) {
            $q->whereIn('nama_role', ['Anggota', 'Ketua Tim']);
        })
            ->where('status_akun', 'aktif')
            ->with('role')
            ->orderBy('nama', 'asc')
            ->get();

        // Ambil mapping tim yang sedang dipimpin oleh setiap ketua tim (id_ketua_tim => TimKerja)
        $ledTeams = TimKerja::select('id_tim', 'nama_tim', 'id_ketua_tim', 'status_tim')
            ->get()
            ->keyBy('id_ketua_tim');

        return view('admin.manajementimkerja', compact('timKerja', 'counts', 'users', 'ledTeams'));
    }

    // =========================================================================
    // KELOMPOK 2: KELOLA DATA TIM KERJA (TAMBAH DAN EDIT)
    // Berisi logika untuk pembuatan tim baru dan perubahan struktur tim.
    // =========================================================================

    /**
     * Membuat tim kerja baru beserta penunjukan Ketua Tim (jika ada).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tim' => 'required|string|min:3|max:100|unique:tim_kerja,nama_tim',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna',
            'status_tim' => 'required|in:aktif,nonaktif',
            'deskripsi_tim' => 'nullable|string|max:1000',
        ], [
            'nama_tim.required' => 'Nama tim kerja wajib diisi.',
            'nama_tim.min' => 'Nama tim kerja minimal 3 karakter.',
            'nama_tim.max' => 'Nama tim kerja maksimal 100 karakter.',
            'nama_tim.unique' => 'Nama tim ini sudah digunakan. Silakan gunakan nama lain.',
            'id_ketua_tim.required' => 'Ketua tim wajib dipilih.',
            'id_ketua_tim.exists' => 'Pegawai yang dipilih sebagai ketua tim tidak valid.',
            'status_tim.required' => 'Status tim kerja wajib dipilih.',
            'status_tim.in' => 'Status tim kerja yang dipilih tidak valid.',
            'deskripsi_tim.max' => 'Deskripsi tim kerja maksimal 1000 karakter.',
        ]);

        // VALIDASI KETAT: 1 Pegawai HANYA boleh mengetuai 1 tim kerja
        $cekMemimpin = TimKerja::where('id_ketua_tim', $request->id_ketua_tim)->first();
        if ($cekMemimpin) {
            return back()->withInput()->with('error', 'Gagal: Pegawai ini sudah menjabat sebagai Ketua Tim pada "'.$cekMemimpin->nama_tim.'". Satu orang hanya boleh memimpin 1 tim kerja.');
        }

        DB::beginTransaction();
        try {
            $tim = TimKerja::create([
                'nama_tim' => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim' => $request->id_ketua_tim,
                'status_tim' => $request->status_tim,
            ]);

            // OTOMATISASI ROLE: Naikkan role pegawai terpilih menjadi 'Ketua Tim' (id_role = 3)
            $roleKetuaId = DB::table('role')->where('nama_role', 'Ketua Tim')->value('id_role') ?? 3;
            $ketuaUser = Pengguna::find($request->id_ketua_tim);
            if ($ketuaUser && $ketuaUser->id_role != $roleKetuaId) {
                $ketuaUser->id_role = $roleKetuaId;
                $ketuaUser->save();
            }

            // Tambahkan/update relasi anggota_tim agar ketua tercatat di tim tersebut
            DB::table('anggota_tim')->updateOrInsert(
                ['id_pengguna' => $request->id_ketua_tim],
                [
                    'id_tim' => $tim->id_tim,
                    'tanggal_bergabung' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Kirim notifikasi jika status_tim aktif
            if ($ketuaUser && $request->status_tim === 'aktif') {
                $ketuaUser->notify(new GeneralNotification(
                    'Penugasan Ketua Tim Baru',
                    "Anda telah resmi dipilih dan ditugaskan sebagai Ketua Tim untuk {$request->nama_tim}."
                ));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Tim Kerja berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->withInput()->with('error', 'Gagal menambah tim. Silakan coba lagi.');
        }
    }

    /**
     * Mengubah data tim kerja (Nama, Deskripsi, Ketua Tim).
     * Berisi logika mutasi jabatan (mengganti ketua lama menjadi anggota,
     * dan mengangkat anggota menjadi ketua baru).
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_tim' => 'required|exists:tim_kerja,id_tim',
            'nama_tim' => 'required|string|min:3|max:100|unique:tim_kerja,nama_tim,'.$request->id_tim.',id_tim',
            'id_ketua_tim' => 'required|exists:pengguna,id_pengguna',
            'status_tim' => 'required|in:aktif,nonaktif',
            'deskripsi_tim' => 'nullable|string|max:1000',
        ], [
            'id_tim.required' => 'ID tim kerja tidak valid.',
            'id_tim.exists' => 'Tim kerja tidak ditemukan di sistem.',
            'nama_tim.required' => 'Nama tim kerja wajib diisi.',
            'nama_tim.min' => 'Nama tim kerja minimal 3 karakter.',
            'nama_tim.max' => 'Nama tim kerja maksimal 100 karakter.',
            'nama_tim.unique' => 'Nama tim ini sudah digunakan. Silakan gunakan nama lain.',
            'id_ketua_tim.required' => 'Ketua tim wajib dipilih.',
            'id_ketua_tim.exists' => 'Pegawai yang dipilih sebagai ketua tim tidak valid.',
            'status_tim.required' => 'Status tim kerja wajib dipilih.',
            'status_tim.in' => 'Status tim kerja yang dipilih tidak valid.',
            'deskripsi_tim.max' => 'Deskripsi tim kerja maksimal 1000 karakter.',
        ]);

        // VALIDASI KETAT: 1 Pegawai HANYA boleh mengetuai 1 tim kerja
        $cekMemimpin = TimKerja::where('id_ketua_tim', $request->id_ketua_tim)
            ->where('id_tim', '!=', $request->id_tim)
            ->first();
        if ($cekMemimpin) {
            return back()->withInput()->with('error', 'Gagal: Pegawai ini sudah menjabat sebagai Ketua Tim pada "'.$cekMemimpin->nama_tim.'". Satu orang hanya boleh memimpin 1 tim kerja.');
        }

        DB::beginTransaction();
        try {
            $tim = TimKerja::findOrFail($request->id_tim);
            $oldKetuaId = $tim->id_ketua_tim;
            $newKetuaId = $request->id_ketua_tim;

            $tim->update([
                'nama_tim' => $request->nama_tim,
                'deskripsi_tim' => $request->deskripsi_tim,
                'id_ketua_tim' => $newKetuaId,
                'status_tim' => $request->status_tim,
            ]);

            $roleKetuaId = DB::table('role')->where('nama_role', 'Ketua Tim')->value('id_role') ?? 3;
            $roleAnggotaId = DB::table('role')->where('nama_role', 'Anggota')->value('id_role') ?? 4;

            // Jika ada pergantian ketua tim:
            if ($oldKetuaId && $oldKetuaId != $newKetuaId) {
                // 1. Cek apakah mantan ketua masih memimpin tim lain?
                $masihMemimpinLain = TimKerja::where('id_ketua_tim', $oldKetuaId)
                    ->where('id_tim', '!=', $tim->id_tim)
                    ->exists();
                if (! $masihMemimpinLain) {
                    // Kembalikan mantan ketua menjadi role 'Anggota'
                    $oldKetua = Pengguna::find($oldKetuaId);
                    if ($oldKetua && $oldKetua->id_role == $roleKetuaId) {
                        $oldKetua->id_role = $roleAnggotaId;
                        $oldKetua->save();
                    }

                    // Mantan ketua tetap menjadi anggota tim ini. Bila ia belum punya keanggotaan
                    // tim yang aktif (mis. dulu diangkat ketua lewat Tambah Pengguna/Aktivasi),
                    // catat sekarang agar tidak tersisa tanpa tim dan tidak ditolak saat login.
                    $sudahAnggotaAktif = DB::table('anggota_tim')
                        ->where('id_pengguna', $oldKetuaId)
                        ->whereNull('tanggal_keluar')
                        ->exists();
                    if (! $sudahAnggotaAktif) {
                        DB::table('anggota_tim')->insert([
                            'id_tim' => $tim->id_tim,
                            'id_pengguna' => $oldKetuaId,
                            'tanggal_bergabung' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // 2. Naikkan role ketua tim baru menjadi 'Ketua Tim'
                $newKetua = Pengguna::find($newKetuaId);
                if ($newKetua && $newKetua->id_role != $roleKetuaId) {
                    $newKetua->id_role = $roleKetuaId;
                    $newKetua->save();
                }

                // 3. Update relasi anggota_tim untuk ketua baru
                DB::table('anggota_tim')->updateOrInsert(
                    ['id_pengguna' => $newKetuaId],
                    [
                        'id_tim' => $tim->id_tim,
                        'tanggal_bergabung' => now(),
                        'updated_at' => now(),
                    ]
                );

                // 4. Kirim notifikasi ke ketua tim baru
                if ($newKetua && $request->status_tim === 'aktif') {
                    $newKetua->notify(new GeneralNotification(
                        'Penugasan Ketua Tim Baru',
                        "Anda telah ditunjuk sebagai Ketua Tim untuk {$request->nama_tim}."
                    ));
                }
            } else {
                // Pastikan ketua saat ini tetap role 'Ketua Tim'
                $currentKetua = Pengguna::find($newKetuaId);
                if ($currentKetua && $currentKetua->id_role != $roleKetuaId) {
                    $currentKetua->id_role = $roleKetuaId;
                    $currentKetua->save();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data Tim Kerja berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui tim. Silakan coba lagi.');
        }
    }
}
