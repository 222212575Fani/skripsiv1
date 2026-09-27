<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\AktivitasProyek;
use App\Models\ProgressAktivitas;
use App\Models\Pengguna;
use App\Models\TimKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Notifications\GeneralNotification;

class AnggotaProyekController extends Controller
{
    protected function dapatKelolaAktivitas(Proyek $proyek): bool
    {
        return $proyek->id_ketua_proyek == auth()->id()
            || DB::table('anggota_proyek')
                ->where('id_proyek', $proyek->id_proyek)
                ->where('id_pengguna', auth()->id())
                ->where('id_peran_proyek', 1)
                ->exists();
    }

    protected function tambahkanAnggotaProyek(Proyek $proyek, int $penggunaId): void
    {
        $sudahTerdaftar = DB::table('anggota_proyek')
            ->where('id_proyek', $proyek->id_proyek)
            ->where('id_pengguna', $penggunaId)
            ->exists();

        if (!$sudahTerdaftar) {
            DB::table('anggota_proyek')->insert([
                'id_proyek' => $proyek->id_proyek,
                'id_pengguna' => $penggunaId,
                'id_peran_proyek' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function penanggungJawabRules(Proyek $proyek): array
    {
        $idKetuaTim = DB::table('tim_kerja')
            ->where('id_tim', $proyek->id_tim)
            ->value('id_ketua_tim');

        return [
            'required',
            Rule::exists('anggota_tim', 'id_pengguna')->where(function ($query) use ($proyek, $idKetuaTim) {
                $query->where('id_tim', $proyek->id_tim)->whereNull('tanggal_keluar');

                if ($idKetuaTim) {
                    $query->where('id_pengguna', '!=', $idKetuaTim);
                }
            }),
        ];
    }

public function index(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id();

        $sapaanWaktu = Carbon::now()->format('H') < 12 ? 'Pagi' : (Carbon::now()->format('H') < 15 ? 'Siang' : (Carbon::now()->format('H') < 18 ? 'Sore' : 'Malam'));

        // 1. QUERY DASAR: Ambil proyek di mana user adalah Ketua ATAU Anggota
        $queryProyek = Proyek::with([
            'ketuaProyek', 
            'anggotaProyek.pengguna', 
            'aktivitasProyek' => function($q) use ($request) {
                $q->with(['penanggungJawab', 'dokumenPendukung', 'kendalaAktivitas'])
                  ->orderBy('id_aktivitas', 'desc');

                if ($request->filled('search')) {
                    $q->where(function($sub) use ($request) {
                        $sub->where('nama_aktivitas', 'like', '%' . $request->search . '%')
                            ->orWhereHas('proyek', function($p) use ($request) {
                                $p->where('nama_proyek', 'like', '%' . $request->search . '%');
                            });
                    });
                }
                if ($request->filled('status') && $request->status !== 'semua') {
                    $q->where('status_aktivitas', $request->status);
                }
                if ($request->filled('tahun') && $request->tahun !== 'semua') {
                    $q->where(function($sub) use ($request) {
                        $sub->whereYear('tanggal_mulai', $request->tahun)
                            ->orWhereYear('tanggal_target_selesai', $request->tahun);
                    });
                }
                if ($request->filled('bulan') && $request->bulan !== 'semua') {
                    $q->where(function($sub) use ($request) {
                        $sub->whereMonth('tanggal_mulai', $request->bulan)
                            ->orWhereMonth('tanggal_target_selesai', $request->bulan);
                    });
                }
            }
        ])->where(function($q) use ($userId) {
            // LOGIKA GABUNGAN: Sebagai Ketua ATAU Sebagai Anggota
            $q->where('id_ketua_proyek', $userId) 
              ->orWhereHas('anggotaProyek', function($sub) use ($userId) {
                  $sub->where('id_pengguna', $userId); 
              });
        });

        // 2. FILTER DROPDOWN (Semua, Proyek yang Diketuai, Aktivitas Saya)
        if ($request->filled('filter_peran') && $request->filter_peran !== 'semua') {
            if ($request->filter_peran === 'ketua') {
                $queryProyek->where('id_ketua_proyek', $userId);
            } elseif ($request->filter_peran === 'anggota') {
                $queryProyek->where('id_ketua_proyek', '!=', $userId)
                            ->whereHas('anggotaProyek', function($sub) use ($userId) {
                                $sub->where('id_pengguna', $userId);
                            });
            }
        }

        // 3. Filter berdasarkan Status dan Search Proyek
        if ($request->filled('search')) {
            $queryProyek->where(function($sub) use ($request) {
                $sub->where('nama_proyek', 'like', '%' . $request->search . '%')
                    ->orWhereHas('aktivitasProyek', function($sq) use ($request) {
                        $sq->where('nama_aktivitas', 'like', '%' . $request->search . '%');
                    });
            });
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $queryProyek->where(function($sub) use ($request) {
                $sub->where('status_proyek', $request->status)
                    ->orWhereHas('aktivitasProyek', function($sq) use ($request) {
                        $sq->where('status_aktivitas', $request->status);
                    });
            });
        }
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $queryProyek->where(function($sub) use ($request) {
                $sub->whereYear('tanggal_mulai', $request->tahun)
                    ->orWhereYear('tanggal_target_selesai', $request->tahun)
                    ->orWhereHas('aktivitasProyek', function($sq) use ($request) {
                        $sq->whereYear('tanggal_mulai', $request->tahun)
                            ->orWhereYear('tanggal_target_selesai', $request->tahun);
                    });
            });
        }
        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $queryProyek->where(function($sub) use ($request) {
                $sub->whereMonth('tanggal_mulai', $request->bulan)
                    ->orWhereMonth('tanggal_target_selesai', $request->bulan)
                    ->orWhereHas('aktivitasProyek', function($sq) use ($request) {
                        $sq->whereMonth('tanggal_mulai', $request->bulan)
                            ->orWhereMonth('tanggal_target_selesai', $request->bulan);
                    });
            });
        }

        $semuaProyek = $queryProyek->latest('id_proyek')->paginate(9)->withQueryString();

        // 4. Hitung Statistik Card Atas (Dihitung dari SELURUH proyek user)
        $proyekSemuaUser = Proyek::where(function($q) use ($userId) {
            $q->where('id_ketua_proyek', $userId)
              ->orWhereHas('anggotaProyek', function($sub) use ($userId) {
                  $sub->where('id_pengguna', $userId);
              });
        })->get();
        
        $totalProyek         = $proyekSemuaUser->count();
        $proyekBelumDimulai  = $proyekSemuaUser->where('status_proyek', 'belum_dimulai')->count();
        $proyekBerjalan      = $proyekSemuaUser->where('status_proyek', 'berjalan')->count();
        $proyekSelesai       = $proyekSemuaUser->where('status_proyek', 'selesai')->count();
        $proyekTerlambat     = $proyekSemuaUser->where('status_proyek', 'terlambat')->count();
        
        $jumlahAnggotaProyek = DB::table('anggota_proyek')
            ->whereIn('id_proyek', $proyekSemuaUser->pluck('id_proyek'))
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        $now = Carbon::now();

        $proyekBulanIni = $proyekSemuaUser->filter(function ($p) use ($now) {
            if (!$p->created_at) return false;
            $created = Carbon::parse($p->created_at);
            return $created->year == $now->year && $created->month == $now->month;
        })->count();

        $bulanLalu = $now->copy()->subMonth();
        $proyekBulanLalu = $proyekSemuaUser->filter(function ($p) use ($bulanLalu) {
            if (!$p->created_at) return false;
            $created = Carbon::parse($p->created_at);
            return $created->year == $bulanLalu->year && $created->month == $bulanLalu->month;
        })->count();

        if ($proyekBulanLalu > 0) {
            $growthTotal = round((($proyekBulanIni - $proyekBulanLalu) / $proyekBulanLalu) * 100, 1);
            $totalPersenText = ($growthTotal >= 0 ? "+{$growthTotal}%" : "{$growthTotal}%") . ' bulan ini';
            $totalTrend = $growthTotal >= 0 ? 'up' : 'down';
        } else {
            if ($proyekBulanIni > 0) {
                $totalPersenText = '+100% bulan ini';
                $totalTrend = 'up';
            } else {
                $totalPersenText = '0% bulan ini';
                $totalTrend = 'neutral';
            }
        }

        $formatPersen = function ($jumlah, $total) {
            if ($total <= 0) return '0%';
            $pct = round(($jumlah / $total) * 100, 1);
            return (floor($pct) == $pct ? (int)$pct : $pct) . '%';
        };

        $persenBelumDimulai = $formatPersen($proyekBelumDimulai, $totalProyek) . ' dari total';
        $persenBerjalan = $formatPersen($proyekBerjalan, $totalProyek) . ' dari total';
        $persenSelesai = $formatPersen($proyekSelesai, $totalProyek) . ' dari total';
        $persenTerlambat = $formatPersen($proyekTerlambat, $totalProyek) . ' dari total';

        $statsProyek = [
            'total'                 => $totalProyek,
            'total_persen_text'     => $totalPersenText,
            'total_trend'           => $totalTrend,
            'belum_dimulai'         => $proyekBelumDimulai,
            'belum_dimulai_persen'  => $persenBelumDimulai,
            'berjalan'              => $proyekBerjalan,
            'berjalan_persen'       => $persenBerjalan,
            'selesai'               => $proyekSelesai,
            'selesai_persen'        => $persenSelesai,
            'terlambat'             => $proyekTerlambat,
            'terlambat_persen'      => $persenTerlambat,
        ];

        return view('anggota.proyek', compact(
            'semuaProyek',
            'totalProyek', 'proyekBelumDimulai', 'proyekBerjalan', 
            'proyekSelesai', 'proyekTerlambat', 'jumlahAnggotaProyek', 'statsProyek',
            'sapaanWaktu'
        ));
    }

    // FUNGSI BARU: Untuk menampilkan tabel daftar proyek khusus yang Anda ketuai
    public function daftarProyek(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id();

        // Query khusus untuk "Daftar proyek yang Anda ketuai"
        $baseQuery = Proyek::where('id_ketua_proyek', $userId)
            ->with(['timKerja', 'ketuaProyek', 'aktivitasProyek']);

        if ($request->filled('search')) {
            $baseQuery->where(function ($q) use ($request) {
                $q->where('nama_proyek', 'like', '%' . $request->search . '%')
                  ->orWhereHas('timKerja', function ($sub) use ($request) {
                      $sub->where('nama_tim', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Hitung badge angka untuk setiap tab status
        $counts = ['semua' => (clone $baseQuery)->count()];
        foreach (['belum_dimulai', 'berjalan', 'selesai', 'terlambat'] as $status) {
            $counts[$status] = (clone $baseQuery)->where('status_proyek', $status)->count();
        }

        // Terapkan filter tab status yang sedang aktif
        $tabelQuery = clone $baseQuery;
        if ($request->filled('status') && $request->status !== 'semua') {
            $tabelQuery->where('status_proyek', $request->status);
        }

        $proyeks = $tabelQuery->latest('id_proyek')->paginate(10)->withQueryString();

        return view('anggota.daftarproyek', compact('proyeks', 'counts'));
    }

    public function aktivitasSaya(Request $request)
    {
        Proyek::sinkronkanSemuaStatus();
        $userId = auth()->id();

        $baseQuery = AktivitasProyek::where('id_penanggung_jawab', $userId)
            ->with(['proyek.ketuaProyek', 'penanggungJawab', 'dokumenPendukung', 'kendalaAktivitas']);

        $applyFilter = function ($q) use ($request) {
            if ($request->filled('search')) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_aktivitas', 'like', '%' . $request->search . '%')
                        ->orWhereHas('proyek', function ($p) use ($request) {
                            $p->where('nama_proyek', 'like', '%' . $request->search . '%');
                        });
                });
            }
            if ($request->filled('tahun') && $request->tahun !== 'semua') {
                $q->where(function ($sub) use ($request) {
                    $sub->whereYear('tanggal_mulai', $request->tahun)
                        ->orWhereYear('tanggal_target_selesai', $request->tahun);
                });
            }
            if ($request->filled('bulan') && $request->bulan !== 'semua') {
                $q->where(function ($sub) use ($request) {
                    $sub->whereMonth('tanggal_mulai', $request->bulan)
                        ->orWhereMonth('tanggal_target_selesai', $request->bulan);
                });
            }
        };
        $applyFilter($baseQuery);

        $counts = ['semua' => (clone $baseQuery)->count()];
        foreach (['belum_dimulai', 'berjalan', 'selesai', 'terlambat'] as $status) {
            $counts[$status] = (clone $baseQuery)->where('status_aktivitas', $status)->count();
        }

        $tabelQuery = clone $baseQuery;
        if ($request->filled('status') && $request->status !== 'semua') {
            $tabelQuery->where('status_aktivitas', $request->status);
        }

        $daftarAktivitas = $tabelQuery->latest('id_aktivitas')->paginate(15)->withQueryString();

        $totalProyekTerlibat = AktivitasProyek::where('id_penanggung_jawab', $userId)
            ->distinct('id_proyek')
            ->count('id_proyek');

        $totalAktivitasSaya = AktivitasProyek::where('id_penanggung_jawab', $userId)->count();

        return view('anggota.aktivitas', compact(
            'daftarAktivitas',
            'counts',
            'totalProyekTerlibat',
            'totalAktivitasSaya'
        ));
    }

    public function showAktivitas($id)
    {
        $proyek = Proyek::with(['ketuaProyek', 'anggotaProyek.pengguna'])->findOrFail($id);
        $userId = auth()->id();
        $isKetuaProyek = $this->dapatKelolaAktivitas($proyek);
        $isAnggotaProyek = DB::table('anggota_proyek')
            ->where('id_proyek', $proyek->id_proyek)
            ->where('id_pengguna', $userId)
            ->exists();

        abort_unless($isKetuaProyek || $isAnggotaProyek, 403);

        $queryAktivitas = $proyek->aktivitasProyek();

        if (!$isKetuaProyek) {
            $queryAktivitas->where('id_penanggung_jawab', $userId);
        }

        if (request()->filled('search')) {
            $queryAktivitas->where('nama_aktivitas', 'like', '%' . request('search') . '%');
        }

        if (request('status') && request('status') !== 'semua') {
            $queryAktivitas->where('status_aktivitas', request('status'));
        }

        $aktivitasProyek = $queryAktivitas
            ->with(['penanggungJawab', 'dokumenPendukung', 'kendalaAktivitas'])
            ->latest('id_aktivitas')
            ->paginate(10)
            ->withQueryString();

        $countsQuery = $proyek->aktivitasProyek();
        if (!$isKetuaProyek) {
            $countsQuery->where('id_penanggung_jawab', $userId);
        }

        $counts = ['semua' => (clone $countsQuery)->count()];
        foreach (['belum_dimulai', 'berjalan', 'selesai', 'terlambat'] as $status) {
            $counts[$status] = (clone $countsQuery)
                ->where('status_aktivitas', $status)
                ->count();
        }

        return view('anggota.detailproyek', compact('proyek', 'aktivitasProyek', 'counts', 'isKetuaProyek'));
    }

    public function storeAktivitas(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);
        abort_unless($this->dapatKelolaAktivitas($proyek), 403);
        $tanggalMulaiMinimum = Carbon::today();

        if ($proyek->tanggal_mulai && Carbon::parse($proyek->tanggal_mulai)->gt($tanggalMulaiMinimum)) {
            $tanggalMulaiMinimum = Carbon::parse($proyek->tanggal_mulai);
        }

        $request->validate([
            'nama_aktivitas'         => 'required|string|min:3|max:150',
            'deskripsi_aktivitas'    => 'nullable|string|max:2000',
            'id_penanggung_jawab'    => $this->penanggungJawabRules($proyek),
            'tanggal_mulai'          => ['required', 'date', 'after_or_equal:' . $tanggalMulaiMinimum->toDateString(), 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'tanggal_target_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai', 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'status_aktivitas'       => 'nullable|in:belum_dimulai,berjalan,selesai,terlambat',
        ], [
            'nama_aktivitas.required'              => 'Nama aktivitas wajib diisi.',
            'nama_aktivitas.min'                   => 'Nama aktivitas minimal 3 karakter.',
            'nama_aktivitas.max'                   => 'Nama aktivitas maksimal 150 karakter.',
            'deskripsi_aktivitas.max'              => 'Deskripsi aktivitas maksimal 2000 karakter.',
            'id_penanggung_jawab.required'         => 'Penanggung jawab aktivitas wajib dipilih.',
            'id_penanggung_jawab.exists'           => 'Penanggung jawab yang dipilih tidak valid.',
            'tanggal_mulai.required'               => 'Tanggal mulai aktivitas wajib ditentukan.',
            'tanggal_mulai.date'                   => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.after_or_equal'         => 'Tanggal mulai aktivitas tidak boleh mendahului hari ini atau tanggal mulai proyek.',
            'tanggal_mulai.before_or_equal'        => 'Tanggal mulai tidak boleh melebihi batas target selesai proyek.',
            'tanggal_target_selesai.required'      => 'Tanggal target selesai wajib ditentukan.',
            'tanggal_target_selesai.date'          => 'Format tanggal target selesai tidak valid.',
            'tanggal_target_selesai.after_or_equal' => 'Tanggal target selesai tidak boleh lebih awal dari tanggal mulai aktivitas.',
            'tanggal_target_selesai.before_or_equal'=> 'Tanggal target selesai tidak boleh melebihi batas target selesai proyek.',
            'status_aktivitas.in'                  => 'Status aktivitas yang dipilih tidak valid.',
        ]);

        AktivitasProyek::create([
            'id_proyek'              => $id,
            'nama_aktivitas'         => $request->nama_aktivitas,
            'deskripsi_aktivitas'    => $request->deskripsi_aktivitas,
            'id_penanggung_jawab'    => $request->id_penanggung_jawab,
            'dibuat_oleh'            => auth()->id(),
            'tanggal_mulai'          => $request->tanggal_mulai,
            'tanggal_target_selesai' => $request->tanggal_target_selesai,
            'target'                 => 0,
            'status_aktivitas'       => $request->input('status_aktivitas', 'belum_dimulai'),
        ]);

        $this->tambahkanAnggotaProyek($proyek, (int) $request->id_penanggung_jawab);

        $namaKetuaProyek = auth()->user()->nama ?? 'Ketua Proyek';

        $timKerja = $proyek->timKerja ?? TimKerja::find($proyek->id_tim);
        $ketuaTim = $timKerja?->ketuaTim ?? ($timKerja?->id_ketua_tim ? Pengguna::find($timKerja->id_ketua_tim) : null);
        if ($ketuaTim && $ketuaTim->id_pengguna !== auth()->id()) {
            $ketuaTim->notify(new GeneralNotification(
                'Aktivitas Proyek Baru Ditambahkan',
                "{$namaKetuaProyek} telah menambahkan aktivitas baru: '{$request->nama_aktivitas}' pada proyek {$proyek->nama_proyek}."
            ));
        }

        $targetPengguna = Pengguna::find($request->id_penanggung_jawab);
        if ($targetPengguna && $targetPengguna->id_pengguna !== auth()->id()) {
            $targetPengguna->notify(new GeneralNotification(
                'Penugasan Aktivitas Baru',
                "Anda telah ditugaskan untuk mengerjakan aktivitas '{$request->nama_aktivitas}' pada proyek {$proyek->nama_proyek}."
            ));
        }

        return redirect()->route('anggota.proyek.aktivitas', $id)
                   ->with('success', 'Aktivitas proyek berhasil ditambahkan.');
    }

    public function updateAktivitas(Request $request, $id)
    {
        $aktivitas = AktivitasProyek::findOrFail($id);
        $proyek = Proyek::findOrFail($aktivitas->id_proyek);
        abort_unless($this->dapatKelolaAktivitas($proyek), 403);
        $tanggalMulaiMinimum = Carbon::today();

        if ($proyek->tanggal_mulai && Carbon::parse($proyek->tanggal_mulai)->gt($tanggalMulaiMinimum)) {
            $tanggalMulaiMinimum = Carbon::parse($proyek->tanggal_mulai);
        }

        $data = $request->validate([
            'nama_aktivitas'         => 'required|string|min:3|max:150',
            'deskripsi_aktivitas'    => 'nullable|string|max:2000',
            'id_penanggung_jawab'    => $this->penanggungJawabRules($proyek),
            'tanggal_mulai'          => ['required', 'date', 'after_or_equal:' . $tanggalMulaiMinimum->toDateString(), 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'tanggal_target_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai', 'before_or_equal:' . $proyek->tanggal_target_selesai],
        ], [
            'nama_aktivitas.required'              => 'Nama aktivitas wajib diisi.',
            'nama_aktivitas.min'                   => 'Nama aktivitas minimal 3 karakter.',
            'nama_aktivitas.max'                   => 'Nama aktivitas maksimal 150 karakter.',
            'deskripsi_aktivitas.max'              => 'Deskripsi aktivitas maksimal 2000 karakter.',
            'id_penanggung_jawab.required'         => 'Penanggung jawab aktivitas wajib dipilih.',
            'id_penanggung_jawab.exists'           => 'Penanggung jawab yang dipilih tidak valid.',
            'tanggal_mulai.required'               => 'Tanggal mulai aktivitas wajib ditentukan.',
            'tanggal_mulai.date'                   => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.after_or_equal'         => 'Tanggal mulai aktivitas tidak boleh mendahului hari ini atau tanggal mulai proyek.',
            'tanggal_mulai.before_or_equal'        => 'Tanggal mulai tidak boleh melebihi batas target selesai proyek.',
            'tanggal_target_selesai.required'      => 'Tanggal target selesai wajib ditentukan.',
            'tanggal_target_selesai.date'          => 'Format tanggal target selesai tidak valid.',
            'tanggal_target_selesai.after_or_equal' => 'Tanggal target selesai tidak boleh lebih awal dari tanggal mulai aktivitas.',
            'tanggal_target_selesai.before_or_equal'=> 'Tanggal target selesai tidak boleh melebihi batas target selesai proyek.',
        ]);

        $data['diperbarui_oleh'] = auth()->id();
        $aktivitas->update($data);
        $this->tambahkanAnggotaProyek($proyek, (int) $data['id_penanggung_jawab']);

        return redirect()->route('anggota.proyek.aktivitas', $aktivitas->id_proyek)
            ->with('success', 'Aktivitas proyek berhasil diperbarui.');
    }

    public function destroyAktivitas($id)
    {
        $aktivitas = AktivitasProyek::findOrFail($id);
        $proyekId = $aktivitas->id_proyek;
        $proyek = Proyek::findOrFail($proyekId);
        abort_unless($this->dapatKelolaAktivitas($proyek), 403);
        $aktivitas->delete();

        return redirect()->route('anggota.proyek.aktivitas', $proyekId)
            ->with('success', 'Aktivitas proyek berhasil dihapus.');
    }

    public function storeProgressAktivitas(Request $request, $id)
    {
        $aktivitas = AktivitasProyek::findOrFail($id);
        abort_unless($aktivitas->id_penanggung_jawab == auth()->id(), 403);

        $data = $request->validate([
            'progress_minggu_berjalan'          => 'required|numeric|min:0|max:100',
            'progress_minggu_berjalan_tambahan' => 'nullable|numeric|min:0|max:100',
            'uraian_progress'                   => 'nullable|string|max:2000',
            'kendala_internal'                  => 'nullable|string|max:2000',
            'kendala_eksternal'                 => 'nullable|string|max:2000',
            'dokumen_pendukung'                 => 'nullable|array',
            'dokumen_pendukung.*'               => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
        ], [
            'progress_minggu_berjalan.required' => 'Nilai persentase progress wajib diisi.',
            'progress_minggu_berjalan.numeric'  => 'Nilai progress harus berupa angka.',
            'progress_minggu_berjalan.min'      => 'Nilai progress minimal adalah 0%.',
            'progress_minggu_berjalan.max'      => 'Nilai progress tidak boleh melebihi 100%.',
            'progress_minggu_berjalan_tambahan.numeric' => 'Nilai tambahan progress harus berupa angka.',
            'progress_minggu_berjalan_tambahan.min'     => 'Nilai tambahan progress minimal adalah 0%.',
            'progress_minggu_berjalan_tambahan.max'     => 'Nilai tambahan progress tidak boleh melebihi 100%.',
            'uraian_progress.max'               => 'Uraian pekerjaan progress maksimal 2000 karakter.',
            'kendala_internal.max'              => 'Catatan kendala internal maksimal 2000 karakter.',
            'kendala_eksternal.max'             => 'Catatan kendala eksternal maksimal 2000 karakter.',
            'dokumen_pendukung.*.mimes'         => 'Format berkas dokumen pendukung harus berupa PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, atau JPEG.',
            'dokumen_pendukung.*.max'           => 'Ukuran setiap berkas dokumen pendukung maksimal 5 MB.',
        ]);

        $userId = auth()->id();
        $progressSebelumnya = (float) ($aktivitas->target ?? 0);
        $tambahan = $request->input('progress_minggu_berjalan_tambahan');

        if ($tambahan !== null && $tambahan !== '') {
            $progressInputan = (float) $tambahan;
            $totalAkhir = min(100, $progressSebelumnya + $progressInputan);
        } else {
            $totalSubmitted = (float) $data['progress_minggu_berjalan'];
            $progressInputan = max(0, $totalSubmitted - $progressSebelumnya);
            $totalAkhir = min(100, $totalSubmitted);
        }

        if (!empty($data['kendala_internal']) || !empty($data['kendala_eksternal'])) {
            DB::table('kendala_aktivitas')->insert([
                'id_aktivitas'      => $aktivitas->id_aktivitas,
                'id_pengguna'       => $userId,
                'kendala_internal'  => $data['kendala_internal'] ?? null,
                'kendala_eksternal' => $data['kendala_eksternal'] ?? null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        ProgressAktivitas::create([
            'id_aktivitas'             => $aktivitas->id_aktivitas,
            'id_pengguna'              => $userId,
            'progress_minggu_berjalan' => $progressInputan,
            'uraian_progress'          => $data['uraian_progress'] ?? null,
        ]);

        $jumlahDokumen = 0;
        if ($request->hasFile('dokumen_pendukung')) {
            $files = $request->file('dokumen_pendukung');
            $jumlahDokumen = count($files);
            foreach ($files as $file) {
                $filePath = $file->store('dokumen_progress', 'public');
                
                DB::table('dokumen_pendukung')->insert([
                    'id_aktivitas'  => $aktivitas->id_aktivitas,
                    'id_pengguna'   => $userId,
                    'nama_dokumen'  => $file->getClientOriginalName(),
                    'file_path'     => $filePath,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        $aktivitas->update([
            'target'                   => $totalAkhir,
            'status_aktivitas'         => $totalAkhir >= 100 ? 'selesai' : 'berjalan',
            'tanggal_selesai_aktual'   => $totalAkhir >= 100 ? now()->toDateString() : null,
            'diperbarui_oleh'          => $userId,
        ]);

        $proyek = $aktivitas->proyek ?? Proyek::find($aktivitas->id_proyek);
        if ($proyek) {
            $proyek->refresh();
            $statusBaru = $proyek->hitungStatusOtomatis();
            $proyek->updateQuietly([
                'persen_progress'        => $proyek->persen_progress,
                'status_proyek'          => $statusBaru,
                'tanggal_selesai_aktual' => $statusBaru === 'selesai' ? ($proyek->tanggal_selesai_aktual ?? now()->toDateString()) : null,
            ]);
        }

        $namaPelapor = auth()->user()->nama ?? 'Anggota';
        $adaDokumen = $jumlahDokumen > 0;
        $adaKendala = !empty($data['kendala_internal']) || !empty($data['kendala_eksternal']);

        $keteranganTambahan = '';
        if ($adaDokumen && $adaKendala) {
            $keteranganTambahan = " serta melampirkan {$jumlahDokumen} dokumen pendukung dan catatan kendala";
        } elseif ($adaDokumen) {
            $keteranganTambahan = " serta melampirkan {$jumlahDokumen} dokumen pendukung";
        } elseif ($adaKendala) {
            $keteranganTambahan = " serta mencantumkan catatan kendala";
        }

        $namaProyek = $proyek?->nama_proyek ?? 'Proyek';
        $pesanNotifikasi = "{$namaPelapor} telah melaporkan progress {$progressInputan}% (Total Capaian: {$totalAkhir}%){$keteranganTambahan} pada aktivitas '{$aktivitas->nama_aktivitas}' (Proyek: {$namaProyek}).";

        $penerimaNotifikasi = collect();

        $ketuaProyek = $proyek?->ketuaProyek ?? ($proyek?->id_ketua_proyek ? Pengguna::find($proyek->id_ketua_proyek) : null);
        if ($ketuaProyek && $ketuaProyek->id_pengguna !== $userId) {
            $penerimaNotifikasi->put($ketuaProyek->id_pengguna, $ketuaProyek);
        }

        $timKerja = $proyek?->timKerja ?? ($proyek?->id_tim ? TimKerja::find($proyek->id_tim) : null);
        $ketuaTim = $timKerja?->ketuaTim ?? ($timKerja?->id_ketua_tim ? Pengguna::find($timKerja->id_ketua_tim) : null);
        if ($ketuaTim && $ketuaTim->id_pengguna !== $userId) {
            $penerimaNotifikasi->put($ketuaTim->id_pengguna, $ketuaTim);
        }

        foreach ($penerimaNotifikasi as $penerima) {
            $penerima->notify(new GeneralNotification(
                'Laporan Progress Aktivitas',
                $pesanNotifikasi
            ));
        }

        return redirect()->back()->with('success', 'Laporan progress, kendala, dan dokumen berhasil dikirim.');
    }
}