<?php

namespace App\Http\Controllers;

use App\Models\AnggotaTim;
use App\Models\Pengguna;
use App\Models\Proyek;
use App\Models\TimKerja;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * =========================================================================
 * CONTROLLER: KETUA TIM
 * Dashboard tim, serta kelola proyek (tambah, ubah, hapus). Seluruh aksi dibatasi
 * pada proyek milik tim yang dipimpin pengguna yang sedang login.
 * =========================================================================
 */
class KetuaTimController extends Controller
{
    // =========================================================================
    // KELOMPOK 1: DASHBOARD KETUA TIM
    // Menampilkan rangkuman statistik seluruh tim dan proyek.
    // =========================================================================

    /** Dashboard Ketua Tim: statistik dan daftar proyek timnya, plus beban kerja ketua proyek. */
    public function dashboard(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id(); // ID Ketua Tim yang sedang login

        // Ambil data tim kerja yang dipimpin
        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        // Ambil status filter dari request (default 'semua')
        $status = $request->get('status', 'semua');

        if (! $timKerja) {
            $totalProyek = $belumDimulai = $berjalan = $selesai = $terlambat = 0;
            $statsTim = [
                'total' => 0,
                'total_persen_text' => '0% bulan ini',
                'total_trend' => 'neutral',
                'belum_dimulai' => 0,
                'belum_dimulai_persen' => '0% dari total',
                'berjalan' => 0,
                'berjalan_persen' => '0% dari total',
                'selesai' => 0,
                'selesai_persen' => '0% dari total',
                'terlambat' => 0,
                'terlambat_persen' => '0% dari total',
            ];
            $proyekTim = collect()->paginate(10);
            $anggotaTim = collect();

            return view('ketuatim.dashboard', compact('timKerja', 'proyekTim', 'totalProyek', 'belumDimulai', 'berjalan', 'selesai', 'terlambat', 'statsTim', 'anggotaTim', 'status'));
        }

        // Ambil semua data proyek tim ini untuk kalkulasi card statistik secara akurat
        $semuaProyekTim = Proyek::where('id_tim', $timKerja->id_tim)->get();

        // Kalkulasi statistik berdasarkan status di database
        $totalProyek = $semuaProyekTim->count();
        $belumDimulai = $semuaProyekTim->where('status_proyek', 'belum_dimulai')->count();
        $berjalan = $semuaProyekTim->where('status_proyek', 'berjalan')->count();
        $selesai = $semuaProyekTim->where('status_proyek', 'selesai')->count();
        $terlambat = $semuaProyekTim->where('status_proyek', 'terlambat')->count();

        $now = Carbon::now();

        // 1. Perhitungan Pertumbuhan Total Proyek (Bulan ini vs Bulan lalu)
        // 1. Keterangan Subtitle Total Proyek
        $totalPersenText = 'Total proyek tim';
        $totalTrend = 'chart';

        // 2. Perhitungan Persentase Status terhadap Total Proyek
        $formatPersen = function ($jumlah, $total) {
            if ($total <= 0) {
                return '0%';
            }
            $pct = round(($jumlah / $total) * 100, 1);

            return (floor($pct) == $pct ? (int) $pct : $pct).'%';
        };

        $persenBelumDimulai = $formatPersen($belumDimulai, $totalProyek).' dari total';
        $persenBerjalan = $formatPersen($berjalan, $totalProyek).' dari total';
        $persenSelesai = $formatPersen($selesai, $totalProyek).' dari total';
        $persenTerlambat = $formatPersen($terlambat, $totalProyek).' dari total';

        $statsTim = [
            'total' => $totalProyek,
            'total_persen_text' => $totalPersenText,
            'total_trend' => $totalTrend,
            'belum_dimulai' => $belumDimulai,
            'belum_dimulai_persen' => $persenBelumDimulai,
            'berjalan' => $berjalan,
            'berjalan_persen' => $persenBerjalan,
            'selesai' => $selesai,
            'selesai_persen' => $persenSelesai,
            'terlambat' => $terlambat,
            'terlambat_persen' => $persenTerlambat,
        ];

        // Query untuk card list proyek di dashboard (memuat relasi ketuaProyek, anggotaProyek, aktivitasProyek beserta dokumen & kendala)
        $query = Proyek::where('id_tim', $timKerja->id_tim)->with([
            'ketuaProyek',
            'anggotaProyek.pengguna',
            'aktivitasProyek.penanggungJawab',
            'aktivitasProyek.dokumenPendukung',
            'aktivitasProyek.kendalaAktivitas',
        ]);

        if ($status !== 'semua') {
            $query->where('status_proyek', $status);
        }

        if ($request->filled('search')) {
            $query->where('nama_proyek', 'like', '%'.$request->search.'%');
        }

        // Mengambil data proyek dengan pagination 15 card per halaman (3 kolom x 5 baris)
        $proyekTim = $query->latest()->paginate(15)->withQueryString();

        // AMBIL DATA KETUA PROYEK BERDASARKAN FILTER TAHUN & BULAN
        $filterTahun = $request->input('tahun', date('Y'));
        $filterBulan = $request->input('bulan', 'semua');

        $tahunValid = is_numeric($filterTahun) ? (int) $filterTahun : (int) date('Y');

        if ($filterBulan !== 'semua' && is_numeric($filterBulan) && (int) $filterBulan >= 1 && (int) $filterBulan <= 12) {
            $bulanValid = str_pad((string) (int) $filterBulan, 2, '0', STR_PAD_LEFT);
            $startPeriod = Carbon::createFromDate($tahunValid, (int) $bulanValid, 1)->startOfMonth()->toDateString();
            $endPeriod = Carbon::createFromDate($tahunValid, (int) $bulanValid, 1)->endOfMonth()->toDateString();
        } else {
            $startPeriod = Carbon::createFromDate($tahunValid, 1, 1)->startOfYear()->toDateString();
            $endPeriod = Carbon::createFromDate($tahunValid, 12, 31)->endOfYear()->toDateString();
        }

        $proyekFilter = function ($q) use ($timKerja, $startPeriod, $endPeriod) {
            $q->where('id_tim', $timKerja->id_tim)
                ->where(function ($dateQ) use ($startPeriod, $endPeriod) {
                    $dateQ->where(function ($sub) use ($startPeriod, $endPeriod) {
                        $sub->whereNotNull('tanggal_mulai')
                            ->where('tanggal_mulai', '<=', $endPeriod)
                            ->where(function ($targetQ) use ($startPeriod) {
                                $targetQ->where('tanggal_target_selesai', '>=', $startPeriod)
                                    ->orWhereNull('tanggal_target_selesai');
                            });
                    })->orWhere(function ($sub) use ($startPeriod, $endPeriod) {
                        $sub->whereDate('created_at', '<=', $endPeriod)
                            ->whereDate('created_at', '>=', $startPeriod);
                    });
                });
        };

        $anggotaTim = Pengguna::where(function ($pQuery) use ($timKerja) {
            $pQuery->whereHas('anggotaTim', function ($q) use ($timKerja) {
                $q->where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar');
            })->orWhereHas('proyekDipimpin', function ($q) use ($timKerja) {
                $q->where('id_tim', $timKerja->id_tim);
            });
        })
            ->whereHas('proyekDipimpin', $proyekFilter)
            ->withCount(['proyekDipimpin' => $proyekFilter])
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
            'statsTim',
            'anggotaTim',
            'status'
        ));
    }

    // =========================================================================
    // KELOMPOK 2: MENAMPILKAN HALAMAN MANAJEMEN PROYEK
    // Menampilkan daftar proyek khusus untuk tim yang diketuai oleh pengguna.
    // =========================================================================

    /** Halaman Manajemen Proyek: tabel proyek milik tim yang dipimpin, dengan pencarian dan tab status. */
    public function manajemenProyek(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id();

        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        if (! $timKerja) {
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
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_proyek', 'like', '%'.$keyword.'%')
                    ->orWhereHas('ketuaProyek', function ($subQ) use ($keyword) {
                        $subQ->where('nama', 'like', '%'.$keyword.'%');
                    });
            });
        }

        $proyeks = $query->latest()->paginate(10)->withQueryString();

        $baseQuery = Proyek::where('id_tim', $timKerja->id_tim);
        $counts = [
            'semua' => (clone $baseQuery)->count(),
            'belum_dimulai' => (clone $baseQuery)->where('status_proyek', 'belum_dimulai')->count(),
            'berjalan' => (clone $baseQuery)->where('status_proyek', 'berjalan')->count(),
            'selesai' => (clone $baseQuery)->where('status_proyek', 'selesai')->count(),
            'terlambat' => (clone $baseQuery)->where('status_proyek', 'terlambat')->count(),
        ];

        return view('ketuatim.manajemenproyek', compact('proyeks', 'counts', 'anggotaTim', 'timKerja'));
    }

    // =========================================================================
    // KELOMPOK 3: KELOLA PROYEK (TAMBAH, UBAH, HAPUS)
    // =========================================================================

    /**
     * Menyimpan data proyek baru dan menentukan Ketua Proyek beserta anggotanya.
     *
     * Aturan yang diperiksa (sesuai proses bisnis):
     * - Ketua Tim menetapkan Ketua Proyek DAN anggota proyek. Ketua Proyek kemudian hanya dapat
     *   menugaskan aktivitas kepada anggota yang dipilih di sini, jadi minimal satu anggota wajib.
     * - Ketua Proyek tersimpan dengan peran sendiri (peran 1), tidak dihitung sebagai anggota biasa
     *   (peran 2), sehingga anggota yang dipilih harus orang lain selain Ketua Proyek.
     * - Tanggal mulai dan tanggal selesai wajib. Status proyek TIDAK diisi pengguna: model Proyek
     *   menghitungnya otomatis dari tanggal dan progress (selesai, terlambat, berjalan, belum dimulai).
     * - Tim yang sudah dinonaktifkan Admin tidak boleh menerima proyek baru.
     */
    public function storeProyek(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:2000',
            'id_ketua_proyek' => 'required|exists:pengguna,id_pengguna',
            'tanggal_mulai' => 'required|date',
            'tenggat_waktu' => 'required|date|after_or_equal:tanggal_mulai',
            'anggota_proyek' => 'required|array|min:1',
            'anggota_proyek.*' => 'exists:pengguna,id_pengguna',
        ], [
            'anggota_proyek.required' => 'Pilih minimal satu anggota proyek.',
            'anggota_proyek.min' => 'Pilih minimal satu anggota proyek.',
            'nama_proyek.required' => 'Nama proyek wajib diisi.',
            'nama_proyek.max' => 'Nama proyek maksimal 200 karakter.',
            'deskripsi.max' => 'Deskripsi proyek maksimal 2000 karakter.',
            'id_ketua_proyek.required' => 'Pilih salah satu Ketua Proyek dari anggota tim.',
            'id_ketua_proyek.exists' => 'Ketua proyek yang dipilih tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai proyek wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tenggat_waktu.required' => 'Tanggal selesai proyek wajib diisi.',
            'tenggat_waktu.date' => 'Format target tanggal selesai tidak valid.',
            'tenggat_waktu.after_or_equal' => 'Target tanggal selesai tidak boleh mendahului tanggal mulai proyek.',
        ]);

        // Ketua Proyek tidak dihitung sebagai anggota biasa, dan hanya anggota biasa yang bisa
        // dipilih sebagai penanggung jawab aktivitas. Karena itu perlu minimal satu anggota lain.
        $anggotaLain = array_diff($request->anggota_proyek, [$request->id_ketua_proyek]);
        if (empty($anggotaLain)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu anggota proyek selain Ketua Proyek.');
        }

        // Transaksi: proyek, anggota proyek, dan notifikasi tersimpan sekaligus atau batal semua
        try {
            DB::beginTransaction();

            // Proyek selalu dibuat untuk tim yang dipimpin pengguna yang sedang login
            $userId = auth()->id();
            $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

            if (! $timKerja) {
                DB::rollback();

                return redirect()->back()->with('error', 'Gagal: Anda tidak terdaftar sebagai ketua tim aktif.');
            }

            // Tim yang sudah dinonaktifkan Admin tidak boleh menerima proyek baru
            if ($timKerja->status_tim !== 'aktif') {
                DB::rollback();

                return redirect()->back()->withInput()->with('error', 'Gagal: Tim kerja "'.$timKerja->nama_tim.'" sudah dinonaktifkan sehingga tidak dapat menerima proyek baru.');
            }

            // 1. Buat Proyek Baru
            $proyek = Proyek::create([
                'id_tim' => $timKerja->id_tim,
                'nama_proyek' => $request->nama_proyek,
                'deskripsi_proyek' => $request->deskripsi,
                'id_ketua_proyek' => $request->id_ketua_proyek,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_target_selesai' => $request->tenggat_waktu,
            ]);

            // 2. Tambahkan Ketua Proyek ke tabel pivot 'anggota_proyek' (id_peran_proyek = 1)
            if (! DB::table('peran_proyek')->where('id_peran_proyek', 1)->exists()) {
                DB::table('peran_proyek')->insertOrIgnore([
                    ['id_peran_proyek' => 1, 'nama_peran_proyek' => 'Ketua Proyek', 'created_at' => now(), 'updated_at' => now()],
                    ['id_peran_proyek' => 2, 'nama_peran_proyek' => 'Anggota', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            DB::table('anggota_proyek')->insert([
                'id_proyek' => $proyek->id_proyek,
                'id_pengguna' => $request->id_ketua_proyek,
                'id_peran_proyek' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // [TAMBAHAN SESUAI PROSES BISNIS] Tambahkan juga anggota proyek yang dipilih
            if ($request->has('anggota_proyek') && is_array($request->anggota_proyek)) {
                $anggotaData = [];
                foreach ($request->anggota_proyek as $idAnggota) {
                    // Pastikan tidak menduplikasi ketua proyek sebagai anggota biasa
                    if ($idAnggota != $request->id_ketua_proyek) {
                        $anggotaData[] = [
                            'id_proyek' => $proyek->id_proyek,
                            'id_pengguna' => $idAnggota,
                            'id_peran_proyek' => 2, // 2 = Anggota
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (! empty($anggotaData)) {
                    DB::table('anggota_proyek')->insert($anggotaData);
                }
            }

            // 3. KIRIM NOTIFIKASI OTOMATIS KE KETUA PROYEK TERPILIH
            $ketuaProyek = Pengguna::find($request->id_ketua_proyek);
            if ($ketuaProyek && $ketuaProyek->id_pengguna !== $userId) {
                $ketuaProyek->notify(new GeneralNotification(
                    'Penugasan Ketua Proyek',
                    "Anda telah ditunjuk sebagai Ketua Proyek untuk proyek '{$request->nama_proyek}' di bawah {$timKerja->nama_tim}."
                ));
            }

            // 4. KIRIM NOTIFIKASI OTOMATIS KE DIREKTUR TENTANG PROYEK BARU DI TIM KERJA
            $direkturList = Pengguna::whereHas('role', function ($query) {
                $query->where('nama_role', 'Direktur');
            })->get();

            if ($direkturList->isNotEmpty()) {
                $namaKetuaTim = auth()->user()->nama ?? 'Ketua Tim';
                Notification::send($direkturList, new GeneralNotification(
                    'Proyek Baru Ditambahkan',
                    "{$namaKetuaTim} ({$timKerja->nama_tim}) telah menambahkan proyek baru: '{$request->nama_proyek}'."
                ));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Proyek baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->withInput()->with('error', 'Gagal menambah proyek. Silakan coba lagi.');
        }
    }

    /**
     * Menghapus proyek dari sistem.
     * Hanya proyek milik tim yang dipimpin pengguna, dan hanya yang belum punya aktivitas.
     */
    public function destroy($id)
    {
        $proyek = $this->proyekMilikTimSaya($id);

        if (! $proyek) {
            return redirect()->back()->with('error', 'Data proyek tidak ditemukan atau bukan milik tim Anda.');
        }

        // Proyek yang sudah memiliki aktivitas dikunci (restrict) agar riwayat progres,
        // kendala, dan dokumen pendukung tidak ikut hilang
        $jumlahAktivitas = $proyek->aktivitasProyek()->count();
        if ($jumlahAktivitas > 0) {
            return redirect()->back()->with('error', "Proyek \"{$proyek->nama_proyek}\" tidak dapat dihapus karena sudah memiliki {$jumlahAktivitas} aktivitas. Hapus aktivitasnya terlebih dahulu.");
        }

        try {
            DB::beginTransaction();

            // Hapus relasi di anggota_proyek terlebih dahulu agar tidak terjadi foreign key constraint error
            DB::table('anggota_proyek')->where('id_proyek', $proyek->id_proyek)->delete();

            $proyek->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Data proyek berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus proyek. Silakan coba lagi.');
        }
    }

    /**
     * Pagar kepemilikan data: mengambil proyek hanya jika milik tim yang dipimpin
     * Ketua Tim yang sedang login. Mengembalikan null bila bukan miliknya, sehingga
     * Ketua Tim tidak bisa mengubah atau menghapus proyek tim lain dengan menebak ID
     * di URL. Dipakai oleh updateProyek dan destroy.
     */
    private function proyekMilikTimSaya($id): ?Proyek
    {
        $timKerja = TimKerja::where('id_ketua_tim', auth()->id())->first();

        if (! $timKerja) {
            return null;
        }

        return Proyek::where('id_proyek', $id)
            ->where('id_tim', $timKerja->id_tim)
            ->first();
    }

    /**
     * Memperbarui data proyek dan struktur Ketua Proyek.
     */
    public function updateProyek(Request $request, $id)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:2000',
            'id_ketua_proyek' => 'required|exists:pengguna,id_pengguna',
            'tanggal_mulai' => 'required|date',
            'tenggat_waktu' => 'required|date|after_or_equal:tanggal_mulai',
            'anggota_proyek' => 'nullable|array',
            'anggota_proyek.*' => 'exists:pengguna,id_pengguna',
        ], [
            'nama_proyek.required' => 'Nama proyek wajib diisi.',
            'nama_proyek.max' => 'Nama proyek maksimal 200 karakter.',
            'deskripsi.max' => 'Deskripsi proyek maksimal 2000 karakter.',
            'id_ketua_proyek.required' => 'Pilih salah satu Ketua Proyek dari anggota tim.',
            'id_ketua_proyek.exists' => 'Ketua proyek yang dipilih tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai proyek wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tenggat_waktu.required' => 'Tanggal selesai proyek wajib diisi.',
            'tenggat_waktu.date' => 'Format target tanggal selesai tidak valid.',
            'tenggat_waktu.after_or_equal' => 'Target tanggal selesai tidak boleh mendahului tanggal mulai proyek.',
        ]);

        $proyek = $this->proyekMilikTimSaya($id);

        if (! $proyek) {
            return redirect()->back()->with('error', 'Data proyek tidak ditemukan atau bukan milik tim Anda.');
        }

        try {
            DB::beginTransaction();

            $ketuaLama = $proyek->id_ketua_proyek;

            $proyek->update([
                'nama_proyek' => $request->nama_proyek,
                'deskripsi_proyek' => $request->deskripsi,
                'id_ketua_proyek' => $request->id_ketua_proyek,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_target_selesai' => $request->tenggat_waktu,
            ]);

            // Jika Ketua Proyek berubah, update relasi di anggota_proyek:
            // ketua lama dilepas dari peran 1, ketua baru dicatat dan diberi notifikasi.
            if ($ketuaLama != $request->id_ketua_proyek) {
                // Hapus ketua lama dari tabel pivot
                DB::table('anggota_proyek')
                    ->where('id_proyek', $proyek->id_proyek)
                    ->where('id_pengguna', $ketuaLama)
                    ->where('id_peran_proyek', 1)
                    ->delete();

                // Masukkan ketua baru ke tabel pivot
                if (! DB::table('peran_proyek')->where('id_peran_proyek', 1)->exists()) {
                    DB::table('peran_proyek')->insertOrIgnore([
                        ['id_peran_proyek' => 1, 'nama_peran_proyek' => 'Ketua Proyek', 'created_at' => now(), 'updated_at' => now()],
                        ['id_peran_proyek' => 2, 'nama_peran_proyek' => 'Anggota', 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }

                DB::table('anggota_proyek')->insert([
                    'id_proyek' => $proyek->id_proyek,
                    'id_pengguna' => $request->id_ketua_proyek,
                    'id_peran_proyek' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // KIRIM NOTIFIKASI KE KETUA PROYEK BARU
                $ketuaBaru = Pengguna::find($request->id_ketua_proyek);
                if ($ketuaBaru) {
                    $ketuaBaru->notify(new GeneralNotification(
                        'Penugasan Ketua Proyek',
                        "Anda telah ditunjuk sebagai Ketua Proyek untuk proyek '{$request->nama_proyek}'."
                    ));
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data proyek berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui proyek. Silakan coba lagi.');
        }
    }
}
