<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * =========================================================================
 * CONTROLLER: PENGGUNA (ADMIN)
 * Manajemen akun oleh Admin: daftar dan filter, aktivasi akun pending,
 * tambah pengguna tanpa registrasi, dan ubah role/tim/status. Nama, NIP, dan
 * email tidak dapat diubah setelah akun dibuat.
 * =========================================================================
 */
class PenggunaController extends Controller
{
    // =========================================================================
    // KELOMPOK 1: MENAMPILKAN HALAMAN MANAJEMEN PENGGUNA
    // Mengambil dan memfilter data seluruh pengguna untuk ditampilkan ke Admin.
    // =========================================================================

    /** Daftar pengguna dengan pencarian (nama/NIP), tab status, dan paginasi. */
    public function index(Request $request)
    {
        $query = Pengguna::with(['role']);

        if ($request->filled('status') && $request->status != 'semua') {
            $status = ($request->status == 'non-aktif') ? 'nonaktif' : $request->status;
            $query->where('status_akun', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nip', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $users->getCollection()->transform(function ($user) {
            $timAsKetua = DB::table('tim_kerja')
                ->where('id_ketua_tim', $user->id_pengguna)
                ->first();

            if ($timAsKetua) {
                $user->nama_tim = $timAsKetua->nama_tim;
                $user->id_tim_aktif = $timAsKetua->id_tim;
            } else {
                $timAsAnggota = DB::table('anggota_tim')
                    ->join('tim_kerja', 'anggota_tim.id_tim', '=', 'tim_kerja.id_tim')
                    ->where('anggota_tim.id_pengguna', $user->id_pengguna)
                    ->whereNull('anggota_tim.tanggal_keluar')
                    ->orderByDesc('anggota_tim.id_anggota_tim')
                    ->select('tim_kerja.nama_tim', 'tim_kerja.id_tim')
                    ->first();

                $user->nama_tim = $timAsAnggota ? $timAsAnggota->nama_tim : '-';
                $user->id_tim_aktif = $timAsAnggota ? $timAsAnggota->id_tim : '';
            }

            return $user;
        });

        $roles = DB::table('role')->get();
        $tims = DB::table('tim_kerja')->where('status_tim', 'aktif')->get();

        $counts = [
            'semua' => Pengguna::count(),
            'pending' => Pengguna::where('status_akun', 'pending')->count(),
            'aktif' => Pengguna::where('status_akun', 'aktif')->count(),
            'nonaktif' => Pengguna::where('status_akun', 'nonaktif')->count(),
        ];

        return view('admin.manajemenpengguna', compact('users', 'counts', 'roles', 'tims'));
    }

    // =========================================================================
    // KELOMPOK 2: PERSETUJUAN DAN AKTIVASI AKUN
    // Mengatur logika persetujuan (approve) untuk akun yang baru mendaftar (pending).
    // =========================================================================

    /**
     * Admin mengaktivasi akun 'pending': menetapkan role dan (untuk non-global) tim kerja.
     * - Admin/Direktur adalah role global, jadi tidak ditempatkan di tim mana pun.
     * - Ketua Tim: tim yang dipilih tidak boleh sudah punya ketua aktif.
     * - Anggota: dicatat di tabel anggota_tim dan Ketua Tim diberi notifikasi.
     * Seluruh perubahan dibungkus transaksi agar tidak ada data setengah tersimpan.
     */
    public function aktivasi(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'id_role' => 'required|exists:role,id_role',
            'id_tim' => 'nullable|exists:tim_kerja,id_tim',
        ], [
            'id_pengguna.required' => 'ID pengguna tidak valid.',
            'id_pengguna.exists' => 'Pengguna tidak ditemukan di sistem.',
            'id_role.required' => 'Silakan tentukan peran (role) untuk pengguna ini.',
            'id_role.exists' => 'Peran yang dipilih tidak ditemukan dalam sistem.',
            'id_tim.exists' => 'Tim kerja yang dipilih tidak valid.',
        ]);

        $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
        $namaRole = $roleData ? strtolower($roleData->nama_role) : '';

        $isRoleKetua = strpos($namaRole, 'ketua') !== false;
        $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

        if (! $isRoleGlobal && $isRoleKetua && $request->filled('id_tim')) {
            $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();

            if ($timTarget && ! empty($timTarget->id_ketua_tim)) {
                $cekKetuaAktif = DB::table('pengguna')
                    ->where('id_pengguna', $timTarget->id_ketua_tim)
                    ->where('status_akun', 'aktif')
                    ->exists();

                if ($cekKetuaAktif) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Tim kerja "'.$timTarget->nama_tim.'" sudah memiliki ketua tim yang aktif.');
                }
            }
        }

        // Mulai transaksi: bila ada langkah yang gagal, semua perubahan dibatalkan (rollback)
        try {
            DB::beginTransaction();

            $user = Pengguna::findOrFail($request->id_pengguna);

            // Langkah 1: ubah status menjadi aktif dan tetapkan role
            $user->status_akun = 'aktif';
            $user->id_role = $request->id_role;
            $user->disetujui_pada = now();
            $user->save();

            $namaTim = null;

            // Langkah 2: penempatan tim sesuai jenis role
            if ($isRoleGlobal) {
                DB::table('anggota_tim')->where('id_pengguna', $user->id_pengguna)->delete();
            } elseif ($request->filled('id_tim')) {
                $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();
                $namaTim = $timTarget ? $timTarget->nama_tim : null;

                if ($isRoleKetua) {
                    DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                        'id_ketua_tim' => $user->id_pengguna,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('anggota_tim')->updateOrInsert(
                        ['id_pengguna' => $user->id_pengguna],
                        [
                            'id_tim' => $request->id_tim,
                            'tanggal_bergabung' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );

                    // Notifikasi ke Ketua Tim bahwa ada anggota baru ditambahkan ke tim kerjanya
                    if ($timTarget && ! empty($timTarget->id_ketua_tim) && $timTarget->id_ketua_tim != $user->id_pengguna) {
                        $ketuaTim = Pengguna::find($timTarget->id_ketua_tim);
                        if ($ketuaTim) {
                            $ketuaTim->notify(new GeneralNotification(
                                'Anggota Tim Baru Ditambahkan',
                                "{$user->nama} telah ditambahkan sebagai anggota ke dalam tim kerja {$namaTim}.",
                                'Tim Kerja'
                            ));
                        }
                    }
                }
            }

            // KIRIM NOTIFIKASI MENGGUNAKAN GENERAL NOTIFICATION (2 parameter)
            $title = 'Aktivasi Akun';
            $message = 'Selamat! Akun Anda telah diaktivasi oleh Admin pada Tim '.($namaTim ?? 'Terkait');
            $user->notify(new GeneralNotification($title, $message));

            DB::commit();

            return redirect()->back()->with('success', 'Akun '.$user->nama.' berhasil diaktivasi dengan peran baru!');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->with('error', 'Gagal mengaktivasi akun. Silakan coba lagi.');
        }
    }

    // =========================================================================
    // KELOMPOK 3: KELOLA DATA PENGGUNA (TAMBAH DAN EDIT)
    // Berisi logika untuk menambah pengguna baru (tanpa registrasi mandiri)
    // dan mengupdate data pengguna yang sudah ada.
    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nip' => 'required|string|size:18|regex:/^[0-9]{18}$/|unique:pengguna,nip',
            'nama_email_baru' => [
                'required',
                'email',
                'max:100',
                'unique:pengguna,email',
                'regex:/^[A-Za-z0-9._%+-]+@bps\.go\.id$/',
            ],
            'kata_sandi_baru' => 'required|string|min:8|max:100',
            'status_akun' => 'required|in:aktif,pending,nonaktif',
            'id_role' => 'required|exists:role,id_role',
            'id_tim' => 'nullable|exists:tim_kerja,id_tim',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'nip.required' => 'NIP wajib diisi.',
            'nip.size' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.regex' => 'NIP hanya boleh berupa angka (18 digit).',
            'nip.unique' => 'NIP ini sudah terdaftar di sistem BPS.',
            'nama_email_baru.required' => 'Alamat email kantor wajib diisi.',
            'nama_email_baru.email' => 'Format email tidak valid.',
            'nama_email_baru.max' => 'Alamat email maksimal 100 karakter.',
            'nama_email_baru.regex' => 'Email wajib menggunakan domain resmi kantor @bps.go.id.',
            'nama_email_baru.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'kata_sandi_baru.required' => 'Kata sandi wajib diisi.',
            'kata_sandi_baru.min' => 'Kata sandi minimal harus 8 karakter.',
            'kata_sandi_baru.max' => 'Kata sandi maksimal 100 karakter.',
            'status_akun.required' => 'Status akun wajib dipilih.',
            'status_akun.in' => 'Status akun yang dipilih tidak valid.',
            'id_role.required' => 'Kamu harus menentukan Peran (Role) terlebih dahulu.',
            'id_role.exists' => 'Peran yang dipilih tidak ditemukan dalam sistem.',
            'id_tim.exists' => 'Tim kerja yang dipilih tidak valid.',
        ]);

        $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
        $namaRole = $roleData ? strtolower($roleData->nama_role) : '';

        $isRoleKetua = strpos($namaRole, 'ketua') !== false;
        $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

        if (! $isRoleGlobal && $isRoleKetua && $request->filled('id_tim')) {
            $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();
            if ($timTarget && ! empty($timTarget->id_ketua_tim)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tim kerja "'.$timTarget->nama_tim.'" sudah memiliki ketua tim.');
            }
        }

        DB::beginTransaction();
        try {
            $disetujuiPada = ($request->status_akun === 'aktif') ? now() : null;

            $pengguna = Pengguna::create([
                'nama' => $request->nama,
                'nip' => $request->nip,
                'email' => $request->nama_email_baru,
                'password' => Hash::make($request->kata_sandi_baru),
                'status_akun' => $request->status_akun,
                'id_role' => $request->id_role,
                'disetujui_pada' => $disetujuiPada,
            ]);

            $namaTim = null;

            if (! $isRoleGlobal && $request->filled('id_tim')) {
                $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();
                $namaTim = $timTarget ? $timTarget->nama_tim : null;

                if ($isRoleKetua) {
                    DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                        'id_ketua_tim' => $pengguna->id_pengguna,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('anggota_tim')->insert([
                        'id_tim' => $request->id_tim,
                        'id_pengguna' => $pengguna->id_pengguna,
                        'tanggal_bergabung' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Notifikasi ke Ketua Tim bahwa ada anggota baru ditambahkan ke tim kerjanya
                    if ($timTarget && ! empty($timTarget->id_ketua_tim) && $timTarget->id_ketua_tim != $pengguna->id_pengguna && $request->status_akun === 'aktif') {
                        $ketuaTim = Pengguna::find($timTarget->id_ketua_tim);
                        if ($ketuaTim) {
                            $ketuaTim->notify(new GeneralNotification(
                                'Anggota Tim Baru Ditambahkan',
                                "{$pengguna->nama} telah ditambahkan sebagai anggota ke dalam tim kerja {$namaTim}.",
                                'Tim Kerja'
                            ));
                        }
                    }
                }
            }

            if ($request->status_akun === 'aktif') {
                $title = 'Aktivasi Akun';
                $message = 'Akun Anda telah ditambahkan dan diaktivasi oleh Admin.';
                $pengguna->notify(new GeneralNotification($title, $message));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->withInput()->with('error', 'Gagal menambah pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Mengupdate data pengguna (Nama, Role, Tim, Status).
     * NIP dan email tidak dapat diubah oleh Admin, sehingga tidak ikut disimpan.
     * Termasuk di dalamnya validasi rumit seperti memindahkan tim
     * dan mengirim notifikasi perubahan.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'nama' => 'required|string|max:100',
            'status_akun' => 'required|in:aktif,pending,nonaktif',
            'id_role' => 'nullable|exists:role,id_role',
            'id_tim' => 'nullable|exists:tim_kerja,id_tim',
        ], [
            'id_pengguna.required' => 'ID pengguna tidak valid.',
            'id_pengguna.exists' => 'Pengguna tidak ditemukan di sistem.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'status_akun.required' => 'Status akun wajib dipilih.',
            'status_akun.in' => 'Status akun yang dipilih tidak valid.',
            'id_role.exists' => 'Peran yang dipilih tidak ditemukan dalam sistem.',
            'id_tim.exists' => 'Tim kerja yang dipilih tidak valid.',
        ]);

        // Tentukan jenis role yang dipilih: "ketua" (Ketua Tim) atau "global" (Admin/Direktur)
        $roleData = DB::table('role')->where('id_role', $request->id_role)->first();
        $namaRole = $roleData ? strtolower($roleData->nama_role) : '';

        $isRoleKetua = strpos($namaRole, 'ketua') !== false;
        $isRoleGlobal = strpos($namaRole, 'admin') !== false || strpos($namaRole, 'direktur') !== false;

        // Satu tim hanya boleh punya satu Ketua Tim
        if (! $isRoleGlobal && $isRoleKetua && $request->filled('id_tim')) {
            $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();
            if ($timTarget && ! empty($timTarget->id_ketua_tim) && $timTarget->id_ketua_tim != $request->id_pengguna) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Tim kerja "'.$timTarget->nama_tim.'" sudah memiliki ketua tim.');
            }
        }

        // Setiap tim kerja wajib selalu punya ketua (kolom id_ketua_tim tidak boleh kosong).
        // Jika pengguna ini masih menjabat Ketua Tim, perubahan yang membuat timnya kehilangan ketua
        // (ganti role, pindah tim, atau dinonaktifkan) ditolak. Pergantian ketua dilakukan
        // lewat menu Manajemen Tim Kerja, yang sekaligus menunjuk ketua penggantinya.
        $timDipimpin = DB::table('tim_kerja')->where('id_ketua_tim', $request->id_pengguna)->first();
        if ($timDipimpin) {
            $tetapKetuaTimYangSama = $isRoleKetua
                && $request->status_akun === 'aktif'
                && (string) $request->id_tim === (string) $timDipimpin->id_tim;

            if (! $tetapKetuaTimYangSama) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Pengguna ini masih menjabat Ketua Tim pada "'.$timDipimpin->nama_tim.'". Ganti ketua tim tersebut terlebih dahulu melalui menu Manajemen Tim Kerja.');
            }
        }

        DB::beginTransaction();
        try {
            $user = Pengguna::findOrFail($request->id_pengguna);
            $statusLama = $user->status_akun;
            $disetujuiPada = $user->disetujui_pada;

            // Tanggal persetujuan dicatat saat akun menjadi aktif, dan dikosongkan bila tidak aktif
            if ($request->status_akun === 'aktif' && $statusLama !== 'aktif') {
                $disetujuiPada = now();
            } elseif ($request->status_akun !== 'aktif') {
                $disetujuiPada = null;
            }

            $user->update([
                'nama' => $request->nama,
                'status_akun' => $request->status_akun,
                'id_role' => $request->id_role,
                'disetujui_pada' => $disetujuiPada,
            ]);

            $namaTim = null;

            // Keanggotaan tim tidak dihapus, hanya diberi tanggal_keluar, agar riwayat
            // tim seseorang tetap tersimpan.
            if ($isRoleGlobal) {
                DB::table('anggota_tim')
                    ->where('id_pengguna', $user->id_pengguna)
                    ->whereNull('tanggal_keluar')
                    ->update(['tanggal_keluar' => now()]);
            } else {
                if ($request->filled('id_tim') && $request->status_akun === 'aktif') {
                    $timTarget = DB::table('tim_kerja')->where('id_tim', $request->id_tim)->first();
                    $namaTim = $timTarget ? $timTarget->nama_tim : null;

                    if ($isRoleKetua) {
                        DB::table('tim_kerja')->where('id_tim', $request->id_tim)->update([
                            'id_ketua_tim' => $user->id_pengguna,
                            'updated_at' => now(),
                        ]);
                    }

                    // Catat tanggal_keluar untuk tim lama yang masih aktif
                    DB::table('anggota_tim')
                        ->where('id_pengguna', $user->id_pengguna)
                        ->where('id_tim', '!=', $request->id_tim)
                        ->whereNull('tanggal_keluar')
                        ->update(['tanggal_keluar' => now()]);

                    // Aktifkan / tambahkan anggota di tim baru
                    $keanggotaanAktif = DB::table('anggota_tim')
                        ->where('id_pengguna', $user->id_pengguna)
                        ->where('id_tim', $request->id_tim)
                        ->whereNull('tanggal_keluar')
                        ->first();

                    if (! $keanggotaanAktif) {
                        DB::table('anggota_tim')->insert([
                            'id_pengguna' => $user->id_pengguna,
                            'id_tim' => $request->id_tim,
                            'tanggal_bergabung' => now(),
                            'tanggal_keluar' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Notifikasi ke Ketua Tim bahwa ada anggota baru ditambahkan ke tim kerjanya
                        if ($timTarget && ! empty($timTarget->id_ketua_tim) && $timTarget->id_ketua_tim != $user->id_pengguna && ! $isRoleKetua) {
                            $ketuaTim = Pengguna::find($timTarget->id_ketua_tim);
                            if ($ketuaTim) {
                                $ketuaTim->notify(new GeneralNotification(
                                    'Anggota Tim Baru Ditambahkan',
                                    "{$user->nama} telah ditambahkan sebagai anggota ke dalam tim kerja {$namaTim}.",
                                    'Tim Kerja'
                                ));
                            }
                        }
                    } elseif ($statusLama !== 'aktif') {
                        // Jika anggota tim yang ada baru diaktifkan kembali
                        if ($timTarget && ! empty($timTarget->id_ketua_tim) && $timTarget->id_ketua_tim != $user->id_pengguna && ! $isRoleKetua) {
                            $ketuaTim = Pengguna::find($timTarget->id_ketua_tim);
                            if ($ketuaTim) {
                                $ketuaTim->notify(new GeneralNotification(
                                    'Anggota Tim Baru Ditambahkan',
                                    "{$user->nama} telah diaktifkan kembali sebagai anggota ke dalam tim kerja {$namaTim}.",
                                    'Tim Kerja'
                                ));
                            }
                        }
                    }
                } else {
                    // Jika akun dinonaktifkan atau tim dikosongkan (pensiun/mutasi keluar)
                    DB::table('anggota_tim')
                        ->where('id_pengguna', $user->id_pengguna)
                        ->whereNull('tanggal_keluar')
                        ->update(['tanggal_keluar' => now()]);
                }
            }

            if ($request->status_akun === 'aktif' && $statusLama !== 'aktif') {
                $title = 'Perubahan Status Akun';
                $message = 'Status akun Anda telah diubah menjadi aktif oleh Admin.';
                $user->notify(new GeneralNotification($title, $message));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->with('error', 'Gagal memperbarui data pengguna. Silakan coba lagi.');
        }
    }
}
