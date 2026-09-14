<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\TimKerja;
use Carbon\Carbon;

class DirekturController extends Controller
{
    public function dashboard(Request $request)
    {
        $now = Carbon::now();
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

        $statsDirektorat = [
            'total'         => $totalProyek,
            'belum_dimulai' => $belumDimulai,
            'berjalan'      => $berjalan,
            'selesai'       => $selesai,
            'terlambat'     => $terlambat,
        ];

        // Ambil data default untuk pertama kali load halaman (tanpa filter)
        $chartData = $this->hitungRerataProgressTim('all', 'all');

        $daftarTim = $chartData['daftarTim'];
        $namaTim = $chartData['namaTim'];
        $rerataProgressTim = $chartData['rerataProgressTim'];
        $semuaProyek = $chartData['semuaProyek'];

        return view('direktur.dashboarddirektur', compact(
            'statsDirektorat', 
            'namaTim', 
            'rerataProgressTim', 
            'daftarTim',
            'semuaProyek'
        ));
    }

    // Method khusus untuk merespons AJAX filter tahun & bulan secara dinamis
    public function getChartData(Request $request)
    {
        $tahun = $request->input('tahun', 'all');
        $bulan = $request->input('bulan', 'all');

        $data = $this->hitungRerataProgressTim($tahun, $bulan);

        return response()->json([
            'namaTim' => $data['namaTim'],
            'rerataProgressTim' => $data['rerataProgressTim'],
            'daftarTim' => $data['daftarTim']
        ]);
    }

    // Logika perhitungan rerata progress tim dengan filter tanggal presisi (strict)
    private function hitungRerataProgressTim($tahun, $bulan)
    {
        $timKerjaList = TimKerja::with(['ketuaTim', 'proyek' => function($query) use ($tahun, $bulan) {
            if ($tahun !== 'all' && $bulan !== 'all') {
                $query->where(function($q) use ($tahun, $bulan) {
                    $q->whereYear('tanggal_mulai', '<=', $tahun)
                      ->whereYear('tanggal_target_selesai', '>=', $tahun)
                      ->whereMonth('tanggal_mulai', '<=', $bulan)
                      ->whereMonth('tanggal_target_selesai', '>=', $bulan);
                })->orWhere(function($q) use ($tahun, $bulan) {
                    $q->whereYear('tanggal_mulai', $tahun)
                      ->whereMonth('tanggal_mulai', $bulan);
                });
            } elseif ($tahun !== 'all') {
                $query->whereYear('tanggal_mulai', '<=', $tahun)
                      ->whereYear('tanggal_target_selesai', '>=', $tahun);
            } elseif ($bulan !== 'all') {
                $query->whereMonth('tanggal_mulai', '<=', $bulan)
                      ->whereMonth('tanggal_target_selesai', '>=', $bulan);
            }
        }, 'proyek.aktivitasProyek', 'proyek.ketuaProyek'])->get();

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
                    if(!$p->relationLoaded('timKerja')) {
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
            'semuaProyek' => $semuaProyek
        ];
    }
}