<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use App\Models\Proyek;
use App\Models\AnggotaTim;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralNotification;

class KetuaTimController extends Controller
{
    // 1. Method untuk halaman Dashboard Monitoring Ketua Tim
    public function dashboard(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id(); // ID Ketua Tim yang sedang login

        // Ambil data tim kerja yang dipimpin
        $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

        // Ambil status filter dari request (default 'semua')
        $status = $request->get('status', 'semua');

        if (!$timKerja) {
            $totalProyek = $belumDimulai = $berjalan = $selesai = $terlambat = 0;
            $statsTim = [
                'total'                 => 0,
                'total_persen_text'     => '0% bulan ini',
                'total_trend'           => 'neutral',
                'belum_dimulai'         => 0,
                'belum_dimulai_persen'  => '0% dari total',
                'berjalan'              => 0,
                'berjalan_persen'       => '0% dari total',
                'selesai'               => 0,
                'selesai_persen'        => '0% dari total',
                'terlambat'             => 0,
                'terlambat_persen'      => '0% dari total',
            ];
            $proyekTim = collect()->paginate(10);
            $anggotaTim = collect();
            return view('ketuatim.dashboard', compact('timKerja', 'proyekTim', 'totalProyek', 'belumDimulai', 'berjalan', 'selesai', 'terlambat', 'statsTim', 'anggotaTim', 'status'));
        }

        // Ambil semua data proyek tim ini untuk kalkulasi card statistik secara akurat
        $semuaProyekTim = Proyek::where('id_tim', $timKerja->id_tim)->get();

        // Kalkulasi statistik berdasarkan status di database
        $totalProyek  = $semuaProyekTim->count();
        $belumDimulai = $semuaProyekTim->where('status_proyek', 'belum_dimulai')->count();
        $berjalan     = $semuaProyekTim->where('status_proyek', 'berjalan')->count();
        $selesai      = $semuaProyekTim->where('status_proyek', 'selesai')->count();
        $terlambat    = $semuaProyekTim->where('status_proyek', 'terlambat')->count();

        $now = Carbon::now();

        // 1. Perhitungan Pertumbuhan Total Proyek (Bulan ini vs Bulan lalu)
        // 1. Keterangan Subtitle Total Proyek
        $totalPersenText = 'Total proyek tim';
        $totalTrend = 'chart';

        // 2. Perhitungan Persentase Status terhadap Total Proyek
        $formatPersen = function ($jumlah, $total) {
            if ($total <= 0) return '0%';
            $pct = round(($jumlah / $total) * 100, 1);
            return (floor($pct) == $pct ? (int)$pct : $pct) . '%';
        };

        $persenBelumDimulai = $formatPersen($belumDimulai, $totalProyek) . ' dari total';
        $persenBerjalan = $formatPersen($berjalan, $totalProyek) . ' dari total';
        $persenSelesai = $formatPersen($selesai, $totalProyek) . ' dari total';
        $persenTerlambat = $formatPersen($terlambat, $totalProyek) . ' dari total';

        $statsTim = [
            'total'                 => $totalProyek,
            'total_persen_text'     => $totalPersenText,
            'total_trend'           => $totalTrend,
            'belum_dimulai'         => $belumDimulai,
            'belum_dimulai_persen'  => $persenBelumDimulai,
            'berjalan'              => $berjalan,
            'berjalan_persen'       => $persenBerjalan,
            'selesai'               => $selesai,
            'selesai_persen'        => $persenSelesai,
            'terlambat'             => $terlambat,
            'terlambat_persen'      => $persenTerlambat,
        ];

        // Query untuk card list proyek di dashboard (memuat relasi ketuaProyek, anggotaProyek, aktivitasProyek beserta dokumen & kendala)
        $query = Proyek::where('id_tim', $timKerja->id_tim)->with([
            'ketuaProyek', 
            'anggotaProyek.pengguna', 
            'aktivitasProyek.penanggungJawab',
            'aktivitasProyek.dokumenPendukung',
            'aktivitasProyek.kendalaAktivitas'
        ]);

        if ($status !== 'semua') {
            $query->where('status_proyek', $status);
        }

        if ($request->filled('search')) {
            $query->where('nama_proyek', 'like', '%' . $request->search . '%');
        }

        // Mengambil data proyek dengan pagination 15 card per halaman (3 kolom x 5 baris)
        $proyekTim = $query->latest()->paginate(15)->withQueryString();

        // AMBIL DATA KETUA PROYEK BERDASARKAN FILTER TAHUN & BULAN
        $filterTahun = $request->input('tahun', date('Y'));
        $filterBulan = $request->input('bulan', 'semua');

        $tahunValid = is_numeric($filterTahun) ? (int)$filterTahun : (int)date('Y');

        if ($filterBulan !== 'semua' && is_numeric($filterBulan) && (int)$filterBulan >= 1 && (int)$filterBulan <= 12) {
            $bulanValid = str_pad((string)(int)$filterBulan, 2, '0', STR_PAD_LEFT);
            $startPeriod = Carbon::createFromDate($tahunValid, (int)$bulanValid, 1)->startOfMonth()->toDateString();
            $endPeriod = Carbon::createFromDate($tahunValid, (int)$bulanValid, 1)->endOfMonth()->toDateString();
        } else {
            $startPeriod = Carbon::createFromDate($tahunValid, 1, 1)->startOfYear()->toDateString();
            $endPeriod = Carbon::createFromDate($tahunValid, 12, 31)->endOfYear()->toDateString();
        }

        $proyekFilter = function($q) use ($timKerja, $startPeriod, $endPeriod) {
            $q->where('id_tim', $timKerja->id_tim)
              ->where(function($dateQ) use ($startPeriod, $endPeriod) {
                  $dateQ->where(function($sub) use ($startPeriod, $endPeriod) {
                      $sub->whereNotNull('tanggal_mulai')
                          ->where('tanggal_mulai', '<=', $endPeriod)
                          ->where(function($targetQ) use ($startPeriod) {
                              $targetQ->where('tanggal_target_selesai', '>=', $startPeriod)
                                      ->orWhereNull('tanggal_target_selesai');
                          });
                  })->orWhere(function($sub) use ($startPeriod, $endPeriod) {
                      $sub->whereDate('created_at', '<=', $endPeriod)
                          ->whereDate('created_at', '>=', $startPeriod);
                  });
              });
        };

        $anggotaTim = Pengguna::where(function($pQuery) use ($timKerja) {
                $pQuery->whereHas('anggotaTim', function($q) use ($timKerja) {
                    $q->where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar');
                })->orWhereHas('proyekDipimpin', function($q) use ($timKerja) {
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

    // 2. Method untuk halaman Manajemen Proyek Ketua Tim
    public function manajemenProyek(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
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
            'nama_proyek'     => 'required|string|max:200',
            'deskripsi'       => 'nullable|string|max:2000',
            'id_ketua_proyek' => 'required|exists:pengguna,id_pengguna',
            'status'          => 'required|in:belum_dimulai,berjalan,selesai,terlambat',
            'tanggal_mulai'   => 'nullable|date',
            'tenggat_waktu'   => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'nama_proyek.required'         => 'Nama proyek wajib diisi.',
            'nama_proyek.max'              => 'Nama proyek maksimal 200 karakter.',
            'deskripsi.max'                => 'Deskripsi proyek maksimal 2000 karakter.',
            'id_ketua_proyek.required'     => 'Pilih salah satu Ketua Proyek dari anggota tim.',
            'id_ketua_proyek.exists'       => 'Ketua proyek yang dipilih tidak valid.',
            'status.required'              => 'Status proyek wajib ditentukan.',
            'status.in'                    => 'Status proyek tidak valid.',
            'tanggal_mulai.date'           => 'Format tanggal mulai tidak valid.',
            'tenggat_waktu.date'           => 'Format target tanggal selesai tidak valid.',
            'tenggat_waktu.after_or_equal' => 'Target tanggal selesai tidak boleh mendahului tanggal mulai proyek.',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            $timKerja = TimKerja::where('id_ketua_tim', $userId)->first();

            if (!$timKerja) {
                return redirect()->back()->with('error', 'Gagal: Anda tidak terdaftar sebagai ketua tim aktif.');
            }

            // 1. Buat Proyek Baru
            $proyek = Proyek::create([
                'id_tim'                 => $timKerja->id_tim,
                'nama_proyek'            => $request->nama_proyek,
                'deskripsi_proyek'       => $request->deskripsi,            
                'id_ketua_proyek'        => $request->id_ketua_proyek,
                'status_proyek'          => $request->status,
                'tanggal_mulai'          => $request->tanggal_mulai,
                'tanggal_target_selesai' => $request->tenggat_waktu,          
            ]);

            // 2. Tambahkan Ketua Proyek ke tabel pivot 'anggota_proyek' (id_peran_proyek = 1)
            if (!DB::table('peran_proyek')->where('id_peran_proyek', 1)->exists()) {
                DB::table('peran_proyek')->insertOrIgnore([
                    ['id_peran_proyek' => 1, 'nama_peran_proyek' => 'Ketua Proyek', 'created_at' => now(), 'updated_at' => now()],
                    ['id_peran_proyek' => 2, 'nama_peran_proyek' => 'Anggota', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            DB::table('anggota_proyek')->insert([
                'id_proyek'       => $proyek->id_proyek,
                'id_pengguna'     => $request->id_ketua_proyek,
                'id_peran_proyek' => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // 3. KIRIM NOTIFIKASI OTOMATIS KE KETUA PROYEK TERPILIH
            $ketuaProyek = Pengguna::find($request->id_ketua_proyek);
            if ($ketuaProyek && $ketuaProyek->id_pengguna !== $userId) {
                $ketuaProyek->notify(new GeneralNotification(
                    'Penugasan Ketua Proyek',
                    "Anda telah ditunjuk sebagai Ketua Proyek untuk proyek '{$request->nama_proyek}' di bawah {$timKerja->nama_tim}."
                ));
            }

            // 4. KIRIM NOTIFIKASI OTOMATIS KE DIREKTUR TENTANG PROYEK BARU DI TIM KERJA
            $direkturList = Pengguna::whereHas('role', function($query) {
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
            return redirect()->back()->withInput()->with('error', 'Gagal menambah proyek: ' . $e->getMessage());
        }
    }

    // 4. Method untuk menghapus data Proyek
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $proyek = Proyek::find($id);

            if (!$proyek) {
                return redirect()->back()->with('error', 'Data proyek tidak ditemukan atau sudah dihapus.');
            }

            // Hapus relasi di anggota_proyek terlebih dahulu agar tidak terjadi foreign key constraint error
            DB::table('anggota_proyek')->where('id_proyek', $proyek->id_proyek)->delete();
            
            $proyek->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Data proyek berhasil dihapus.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 5. Method untuk mengupdate data Proyek
    public function updateProyek(Request $request, $id)
    {
        $request->validate([
            'nama_proyek'     => 'required|string|max:200',
            'deskripsi'       => 'nullable|string|max:2000',
            'id_ketua_proyek' => 'required|exists:pengguna,id_pengguna',
            'status'          => 'required|in:belum_dimulai,berjalan,selesai,terlambat',
            'tanggal_mulai'   => 'nullable|date',
            'tenggat_waktu'   => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'nama_proyek.required'         => 'Nama proyek wajib diisi.',
            'nama_proyek.max'              => 'Nama proyek maksimal 200 karakter.',
            'deskripsi.max'                => 'Deskripsi proyek maksimal 2000 karakter.',
            'id_ketua_proyek.required'     => 'Pilih salah satu Ketua Proyek dari anggota tim.',
            'id_ketua_proyek.exists'       => 'Ketua proyek yang dipilih tidak valid.',
            'status.required'              => 'Status proyek wajib ditentukan.',
            'status.in'                    => 'Status proyek tidak valid.',
            'tanggal_mulai.date'           => 'Format tanggal mulai tidak valid.',
            'tenggat_waktu.date'           => 'Format target tanggal selesai tidak valid.',
            'tenggat_waktu.after_or_equal' => 'Target tanggal selesai tidak boleh mendahului tanggal mulai proyek.',
        ]);

        try {
            DB::beginTransaction();

            $proyek = Proyek::findOrFail($id);
            $ketuaLama = $proyek->id_ketua_proyek;

            $proyek->update([
                'nama_proyek'            => $request->nama_proyek,
                'deskripsi_proyek'       => $request->deskripsi,            
                'id_ketua_proyek'        => $request->id_ketua_proyek,
                'status_proyek'          => $request->status,
                'tanggal_mulai'          => $request->tanggal_mulai,
                'tanggal_target_selesai' => $request->tenggat_waktu,          
            ]);

            // Jika Ketua Proyek berubah, update relasi di anggota_proyek
            if ($ketuaLama != $request->id_ketua_proyek) {
                // Hapus ketua lama dari tabel pivot
                DB::table('anggota_proyek')
                    ->where('id_proyek', $proyek->id_proyek)
                    ->where('id_pengguna', $ketuaLama)
                    ->where('id_peran_proyek', 1)
                    ->delete();

                // Masukkan ketua baru ke tabel pivot
                if (!DB::table('peran_proyek')->where('id_peran_proyek', 1)->exists()) {
                    DB::table('peran_proyek')->insertOrIgnore([
                        ['id_peran_proyek' => 1, 'nama_peran_proyek' => 'Ketua Proyek', 'created_at' => now(), 'updated_at' => now()],
                        ['id_peran_proyek' => 2, 'nama_peran_proyek' => 'Anggota', 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }

                DB::table('anggota_proyek')->insert([
                    'id_proyek'       => $proyek->id_proyek,
                    'id_pengguna'     => $request->id_ketua_proyek,
                    'id_peran_proyek' => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
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
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui proyek: ' . $e->getMessage());
        }
    }
}