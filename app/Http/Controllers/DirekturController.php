<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\TimKerja;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * =========================================================================
 * CONTROLLER: DIREKTUR
 * Dashboard pemantauan seluruh direktorat: statistik proyek, grafik rerata
 * progress per tim, grafik beban kerja pegawai, dan daftar proyek yang bisa
 * ditelusuri sampai aktivitas dan dokumen pendukungnya. Role ini hanya melihat
 * (tidak mengubah data).
 * =========================================================================
 */
class DirekturController extends Controller
{
    // =========================================================================
    // KELOMPOK 1: DASHBOARD DIREKTUR
    // Menampilkan statistik high-level untuk Direktur (Top Management).
    // =========================================================================

    public function dashboard(Request $request)
    {
        // Pastikan status proyek (mis. "terlambat") sudah sesuai tanggal hari ini
        Proyek::sinkronkanSemuaStatus();
        // Pembanding tenggat memakai awal hari ini (sama dengan perhitungan status di model), sehingga
        // proyek baru dihitung terlambat SETELAH tanggal tenggatnya lewat, bukan pada hari tenggatnya
        $now = Carbon::today();
        $semuaProyekData = Proyek::all();

        $totalProyek = $semuaProyekData->count();
        $belumDimulai = 0;
        $berjalan = 0;
        $selesai = 0;
        $terlambat = 0;

        foreach ($semuaProyekData as $p) {
            $status = strtolower(trim($p->status_proyek ?? $p->status ?? 'belum_dimulai'));
            $tenggat = $p->tenggat_waktu ?? $p->tanggal_target_selesai ? Carbon::parse($p->tenggat_waktu ?? $p->tanggal_target_selesai) : null;

            if ($status !== 'selesai' && $tenggat && $now->greaterThan($tenggat)) {
                $terlambat++;
            } else {
                if ($status === 'belum_dimulai') {
                    $belumDimulai++;
                } elseif ($status === 'berjalan' || $status === 'sedang_berjalan') {
                    $berjalan++;
                } elseif ($status === 'selesai') {
                    $selesai++;
                } else {
                    $belumDimulai++;
                }
            }
        }

        // 1. Keterangan Subtitle Total Proyek
        $totalPersenText = 'Proyek terdaftar';
        $totalTrend = 'chart';

        // 2. Perhitungan Persentase Status terhadap Total Proyek
        $formatPersen = function ($jumlah, $total) {
            if ($total <= 0) {
                return '0%';
            }
            $pct = round(($jumlah / $total) * 100, 1);

            return (floor($pct) == $pct ? (int) $pct : $pct).'%';
        };

        $persenBelumDimulai = $formatPersen($belumDimulai, $totalProyek);
        $persenBerjalan = $formatPersen($berjalan, $totalProyek);
        $persenSelesai = $formatPersen($selesai, $totalProyek);
        $persenTerlambat = $formatPersen($terlambat, $totalProyek);

        $statsDirektorat = [
            'total' => $totalProyek,
            'total_persen_text' => $totalPersenText,
            'total_trend' => $totalTrend,

            'belum_dimulai' => $belumDimulai,
            'belum_dimulai_persen' => $persenBelumDimulai.' dari total',

            'berjalan' => $berjalan,
            'berjalan_persen' => $persenBerjalan.' dari total',

            'selesai' => $selesai,
            'selesai_persen' => $persenSelesai.' dari total',

            'terlambat' => $terlambat,
            'terlambat_persen' => $persenTerlambat.' dari total',
        ];

        // Ambil data default untuk pertama kali load halaman (default tahun berjalan)
        $chartData = $this->hitungRerataProgressTim(date('Y'), 'all');

        $daftarTim = $chartData['daftarTim'];
        $namaTim = $chartData['namaTim'];
        $rerataProgressTim = $chartData['rerataProgressTim'];

        // Ambil data seluruh proyek dari database secara real-time (15 proyek per halaman dengan pagination)
        $semuaProyek = Proyek::with([
            'timKerja',
            'ketuaProyek',
            'anggotaProyek.pengguna',
            'aktivitasProyek.penanggungJawab',
            'aktivitasProyek.dokumenPendukung',
            'aktivitasProyek.kendalaAktivitas',
        ])->latest('id_proyek')->paginate(15)->withQueryString();

        // Ambil data beban kerja anggota untuk pertama kali load halaman
        $bebanKerjaInitial = $this->hitungBebanKerjaAnggota('all', date('Y'), 'all');

        return view('direktur.dashboarddirektur', compact(
            'statsDirektorat',
            'namaTim',
            'rerataProgressTim',
            'daftarTim',
            'semuaProyek',
            'bebanKerjaInitial'
        ));
    }

    // Method khusus untuk merespons AJAX filter tahun & bulan secara dinamis
    // =========================================================================
    // KELOMPOK 2: DATA GRAFIK (CHART API)
    // Mengembalikan data JSON untuk diproses oleh JavaScript Grafik/Chart.
    // =========================================================================

    public function getChartData(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', 'all');

        $data = $this->hitungRerataProgressTim($tahun, $bulan);

        return response()->json([
            'namaTim' => $data['namaTim'],
            'rerataProgressTim' => $data['rerataProgressTim'],
            'daftarTim' => $data['daftarTim'],
        ]);
    }

    /**
     * Rerata progress setiap tim kerja untuk grafik dashboard Direktur.
     * Rumus: progress tim = rata-rata dari progress semua proyek tim pada periode
     * yang dipilih; progress proyek = rata-rata progress aktivitasnya.
     * Filter tahun/bulan memilih proyek yang rentang tanggalnya beririsan dengan periode.
     * Hasilnya juga dipakai kartu proyek, sehingga Direktur bisa menelusuri
     * dari tim -> proyek -> aktivitas -> dokumen pendukung.
     */
    private function hitungRerataProgressTim($tahun, $bulan)
    {
        $timKerjaList = TimKerja::with([
            'ketuaTim',
            'proyek' => function ($query) use ($tahun, $bulan) {
                if ($tahun !== 'all' && $bulan !== 'all') {
                    // Proyek ikut dihitung bila rentang tanggalnya beririsan dengan bulan yang dipilih
                    // (mulai sebelum akhir bulan DAN target selesai setelah awal bulan)
                    $awalBulan = Carbon::create((int) $tahun, (int) $bulan, 1)->startOfMonth();
                    $akhirBulan = $awalBulan->copy()->endOfMonth();
                    $query->whereDate('tanggal_mulai', '<=', $akhirBulan->toDateString())
                        ->whereDate('tanggal_target_selesai', '>=', $awalBulan->toDateString());
                } elseif ($tahun !== 'all') {
                    $query->whereYear('tanggal_mulai', '<=', $tahun)
                        ->whereYear('tanggal_target_selesai', '>=', $tahun);
                } elseif ($bulan !== 'all') {
                    $query->whereMonth('tanggal_mulai', '<=', $bulan)
                        ->whereMonth('tanggal_target_selesai', '>=', $bulan);
                }
            },
            'proyek.aktivitasProyek',
            'proyek.aktivitasProyek.dokumenPendukung', // Eager loading relasi dokumen pendukung yang benar
            'proyek.ketuaProyek',
        ])->get();

        $namaTim = [];
        $rerataProgressTim = [];
        $daftarTim = [];
        $semuaProyek = collect();

        foreach ($timKerjaList as $tim) {
            $proyeks = $tim->proyek ?? collect();
            $totalProyekTim = $proyeks->count();

            $totalProgress = 0;
            if ($totalProyekTim > 0) {
                foreach ($proyeks as $p) {
                    if (! $p->relationLoaded('timKerja')) {
                        $p->setRelation('timKerja', $tim);
                    }
                    $semuaProyek->push($p);

                    $avgAktivitas = $p->aktivitasProyek->avg('target') ?? 0;
                    $totalProgress += $avgAktivitas;
                }
                $rerataTim = $totalProgress / $totalProyekTim;
            } else {
                $rerataTim = 0;
            }

            $namaTim[] = $tim->nama_tim ?? 'Tim';
            $rerataProgressTim[] = round($rerataTim, 2);

            $tim->proyek_count = $totalProyekTim;
            $tim->proyek_berjalan_count = $proyeks->where('status_proyek', 'berjalan')->count();
            $tim->rerata_progress = round($rerataTim, 2);

            $daftarTim[] = $tim;
        }

        return [
            'namaTim' => $namaTim,
            'rerataProgressTim' => $rerataProgressTim,
            'daftarTim' => $daftarTim,
            'semuaProyek' => $semuaProyek,
        ];
    }

    // Endpoint JSON untuk diagram beban kerja; dipanggil grafik saat filter tim/periode diganti
    public function getBebanKerjaData(Request $request)
    {
        $idTim = $request->input('id_tim', 'all');
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan', 'all');

        $data = $this->hitungBebanKerjaAnggota($idTim, $tahun, $bulan);

        return response()->json($data);
    }

    /**
     * Beban kerja = jumlah proyek yang dikerjakan setiap orang pada periode terpilih,
     * dipecah per status proyek. Berguna bagi Direktur untuk melihat pembagian
     * pekerjaan antar pegawai (fitur usulan dari pengujian black-box).
     */
    private function hitungBebanKerjaAnggota($idTim = 'all', $tahun = 'all', $bulan = 'all')
    {
        // Awal hari ini, sama dengan perhitungan status di model (terlambat setelah tenggat lewat)
        $now = Carbon::today();

        // Query proyek dengan filter tim & rentang periode
        $proyekQuery = Proyek::with(['anggotaProyek.pengguna', 'ketuaProyek', 'timKerja']);
        if ($idTim !== 'all') {
            $proyekQuery->where('id_tim', $idTim);
        }

        if ($tahun !== 'all' && $bulan !== 'all') {
            // Proyek ikut dihitung bila rentang tanggalnya beririsan dengan bulan yang dipilih
            $awalBulan = Carbon::create((int) $tahun, (int) $bulan, 1)->startOfMonth();
            $akhirBulan = $awalBulan->copy()->endOfMonth();
            $proyekQuery->whereDate('tanggal_mulai', '<=', $akhirBulan->toDateString())
                ->whereDate('tanggal_target_selesai', '>=', $awalBulan->toDateString());
        } elseif ($tahun !== 'all') {
            $proyekQuery->where(function ($q) use ($tahun) {
                $q->where(function ($sub) use ($tahun) {
                    $sub->whereYear('tanggal_mulai', '<=', $tahun)
                        ->whereYear('tanggal_target_selesai', '>=', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        } elseif ($bulan !== 'all') {
            $proyekQuery->where(function ($q) use ($bulan) {
                $q->where(function ($sub) use ($bulan) {
                    $sub->whereMonth('tanggal_mulai', '<=', $bulan)
                        ->whereMonth('tanggal_target_selesai', '>=', $bulan);
                })->orWhereMonth('tanggal_mulai', $bulan);
            });
        }
        $proyekList = $proyekQuery->get();

        // Query personil dari tim yang dipilih
        // Hanya anggota yang masih aktif di tim (tanggal keluar kosong); yang sudah keluar tidak ikut grafik
        $timQuery = TimKerja::with(['anggotaAktif.pengguna', 'ketuaTim']);
        if ($idTim !== 'all') {
            $timQuery->where('id_tim', $idTim);
        }
        $timList = $timQuery->get();

        $personMap = [];
        foreach ($timList as $tim) {
            if ($tim->ketuaTim) {
                $personMap[$tim->ketuaTim->id_pengguna] = [
                    'id' => $tim->ketuaTim->id_pengguna,
                    'nama' => $tim->ketuaTim->nama,
                    'tim' => $tim->nama_tim,
                    'total' => 0,
                    'berjalan' => 0,
                    'selesai' => 0,
                    'belum_dimulai' => 0,
                    'terlambat' => 0,
                    'proyek_ids' => [],
                ];
            }
            foreach ($tim->anggotaAktif as $at) {
                if ($at->pengguna) {
                    $personMap[$at->pengguna->id_pengguna] = [
                        'id' => $at->pengguna->id_pengguna,
                        'nama' => $at->pengguna->nama,
                        'tim' => $tim->nama_tim,
                        'total' => 0,
                        'berjalan' => 0,
                        'selesai' => 0,
                        'belum_dimulai' => 0,
                        'terlambat' => 0,
                        'proyek_ids' => [],
                    ];
                }
            }
        }

        foreach ($proyekList as $proyek) {
            $status = strtolower(trim($proyek->status_proyek ?? 'belum_dimulai'));
            $tenggat = $proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai ? Carbon::parse($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) : null;

            $effectiveStatus = $status;
            if ($status !== 'selesai' && $tenggat && $now->greaterThan($tenggat)) {
                $effectiveStatus = 'terlambat';
            } elseif ($status === 'sedang_berjalan') {
                $effectiveStatus = 'berjalan';
            }

            $userIdsInProject = [];
            if ($proyek->id_ketua_proyek) {
                $userIdsInProject[] = $proyek->id_ketua_proyek;
                if ($idTim === 'all' && ! isset($personMap[$proyek->id_ketua_proyek]) && $proyek->ketuaProyek) {
                    $personMap[$proyek->id_ketua_proyek] = [
                        'id' => $proyek->id_ketua_proyek,
                        'nama' => $proyek->ketuaProyek->nama,
                        'tim' => $proyek->timKerja->nama_tim ?? 'Tim',
                        'total' => 0,
                        'berjalan' => 0,
                        'selesai' => 0,
                        'belum_dimulai' => 0,
                        'terlambat' => 0,
                        'proyek_ids' => [],
                    ];
                }
            }

            foreach ($proyek->anggotaProyek as $ap) {
                if ($ap->id_pengguna) {
                    $userIdsInProject[] = $ap->id_pengguna;
                    if ($idTim === 'all' && ! isset($personMap[$ap->id_pengguna]) && $ap->pengguna) {
                        $personMap[$ap->id_pengguna] = [
                            'id' => $ap->id_pengguna,
                            'nama' => $ap->pengguna->nama,
                            'tim' => $proyek->timKerja->nama_tim ?? 'Tim',
                            'total' => 0,
                            'berjalan' => 0,
                            'selesai' => 0,
                            'belum_dimulai' => 0,
                            'terlambat' => 0,
                            'proyek_ids' => [],
                        ];
                    }
                }
            }

            $uniqueUsers = array_unique($userIdsInProject);
            foreach ($uniqueUsers as $uid) {
                if (isset($personMap[$uid])) {
                    if (! in_array($proyek->id_proyek, $personMap[$uid]['proyek_ids'])) {
                        $personMap[$uid]['proyek_ids'][] = $proyek->id_proyek;
                        $personMap[$uid]['total']++;
                        if (isset($personMap[$uid][$effectiveStatus])) {
                            $personMap[$uid][$effectiveStatus]++;
                        } else {
                            $personMap[$uid]['belum_dimulai']++;
                        }
                    }
                }
            }
        }

        $personList = array_values($personMap);
        usort($personList, function ($a, $b) {
            if ($b['total'] === $a['total']) {
                return strcmp($a['nama'], $b['nama']);
            }

            return $b['total'] <=> $a['total'];
        });

        $labels = [];
        $dataBerjalan = [];
        $dataSelesai = [];
        $dataBelumDimulai = [];
        $dataTerlambat = [];
        $dataTotal = [];

        $totalWorkload = 0;
        foreach ($personList as $p) {
            $labels[] = $p['nama'];
            $dataBerjalan[] = $p['berjalan'];
            $dataSelesai[] = $p['selesai'];
            $dataBelumDimulai[] = $p['belum_dimulai'];
            $dataTerlambat[] = $p['terlambat'];
            $dataTotal[] = $p['total'];
            $totalWorkload += $p['total'];
        }

        $topPersonText = '-';
        $topPersonFull = '-';
        $topSubtitle = 'Personil dengan penugasan terbanyak';
        if (! empty($personList) && $personList[0]['total'] > 0) {
            $maxTotal = $personList[0]['total'];
            $topPersons = array_filter($personList, fn ($p) => $p['total'] === $maxTotal);
            $topNames = array_column($topPersons, 'nama');
            $topCount = count($topNames);
            $topPersonFull = implode(', ', $topNames);

            if ($topCount === 1) {
                $topPersonText = $topNames[0];
                $topSubtitle = $maxTotal.' proyek';
            } elseif ($topCount === 2) {
                $topPersonText = $topNames[0].', '.$topNames[1];
                $topSubtitle = 'Masing-masing '.$maxTotal.' proyek';
            } else {
                $topPersonText = implode(', ', array_slice($topNames, 0, 2)).' (+'.($topCount - 2).' lainnya)';
                $topSubtitle = $topCount.' personil (masing-masing '.$maxTotal.' proyek)';
            }
        }

        $avgWorkload = count($personList) > 0 ? round($totalWorkload / count($personList), 1) : 0;

        return [
            'labels' => $labels,
            'personList' => $personList,
            'datasets' => [
                'berjalan' => $dataBerjalan,
                'selesai' => $dataSelesai,
                'belum_dimulai' => $dataBelumDimulai,
                'terlambat' => $dataTerlambat,
                'total' => $dataTotal,
            ],
            'summary' => [
                'topPerson' => $topPersonText,
                'topPersonFull' => $topPersonFull,
                'topSubtitle' => $topSubtitle,
                'avgWorkload' => $avgWorkload,
                'totalAnggota' => count($personList),
                'totalProyekAktif' => $proyekList->count(),
            ],
        ];
    }
}
