<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\TimKerja;
use App\Models\AktivitasProyek;

class DirekturController extends Controller
{
    public function dashboard()
    {
        // 1. Statistik Global Proyek dalam Direktorat SIS
        $statsDirektorat = [
            'total'         => Proyek::count(),
            'belum_dimulai' => Proyek::where('status_proyek', 'belum_dimulai')->count(),
            'berjalan'      => Proyek::where('status_proyek', 'berjalan')->count(),
            'selesai'       => Proyek::where('status_proyek', 'selesai')->count(),
            'terlambat'     => Proyek::where('status_proyek', 'terlambat')->count(),
        ];

        // 2. Ambil data Tim Kerja beserta proyek dan aktivitasnya untuk Grafik & Card
        $timKerjaList = TimKerja::with(['ketuaTim', 'proyek.aktivitasProyek'])->get();

        $namaTim = [];
        $rerataProgressTim = [];
        $daftarTim = [];

        foreach ($timKerjaList as $tim) {
            $proyeks = $tim->proyek;
            $totalProyek = $proyeks->count();
            
            // Hitung rerata progress proyek di dalam tim kerja tersebut
            $totalProgress = 0;
            if ($totalProyek > 0) {
                foreach ($proyeks as $p) {
                    $avgAktivitas = $p->aktivitasProyek->avg('target') ?? 0;
                    $totalProgress += $avgAktivitas;
                }
                $rerataTim = $totalProgress / $totalProyek;
            } else {
                $rerataTim = 0;
            }

            // Masukkan data untuk grafik batang
            $namaTim[] = $tim->nama_tim;
            $rerataProgressTim[] = round($rerataTim, 2);

            // Tambahkan properti tambahan untuk kebutuhan card masing-masing tim
            $tim->proyek_count = $totalProyek;
            $tim->proyek_berjalan_count = $proyeks->where('status_proyek', 'berjalan')->count();
            $tim->rerata_progress = $rerataTim;
            
            $daftarTim[] = $tim;
        }

        return view('direktur.dashboarddirektur', compact(
            'statsDirektorat', 
            'namaTim', 
            'rerataProgressTim', 
            'daftarTim'
        ));
    }
}