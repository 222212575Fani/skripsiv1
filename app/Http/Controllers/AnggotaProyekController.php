<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\AktivitasProyek;
use App\Models\ProgressAktivitas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $userId = auth()->id();

        $queryKetua = Proyek::whereHas('anggotaProyek', function($query) use ($userId) {
            $query->where('id_pengguna', $userId)->where('id_peran_proyek', 1);
        });

        $queryAnggota = Proyek::whereHas('anggotaProyek', function($query) use ($userId) {
            $query->where('id_pengguna', $userId)->where('id_peran_proyek', 2);
        });

        $proyekKetuaAll = $queryKetua->get();
        $proyekAnggotaAll = $queryAnggota->get();

        $isKetuaSaja      = $proyekKetuaAll->count() > 0 && $proyekAnggotaAll->count() == 0;
        $isAnggotaSaja    = $proyekKetuaAll->count() == 0 && $proyekAnggotaAll->count() > 0;
        $isPeranGanda     = $proyekKetuaAll->count() > 0 && $proyekAnggotaAll->count() > 0;
        $tidakPunyaProyek = $proyekKetuaAll->count() == 0 && $proyekAnggotaAll->count() == 0;

        $semuaProyekAll = $proyekKetuaAll->merge($proyekAnggotaAll)->unique('id_proyek');
        
        $totalProyek          = $semuaProyekAll->count();
        $proyekBelumDimulai = $semuaProyekAll->where('status_proyek', 'belum_dimulai')->count();
        $proyekBerjalan     = $semuaProyekAll->where('status_proyek', 'berjalan')->count();
        $proyekSelesai      = $semuaProyekAll->where('status_proyek', 'selesai')->count();
        $proyekTerlambat    = $semuaProyekAll->where('status_proyek', 'terlambat')->count();
        
        $jumlahAnggotaProyek = DB::table('anggota_proyek')
            ->whereIn('id_proyek', $semuaProyekAll->pluck('id_proyek'))
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        $applyFilters = function ($query) use ($request) {
            if ($request->filled('search')) {
                $query->where('nama_proyek', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('status') && $request->status !== 'semua') {
                $query->where('status_proyek', $request->status);
            }
        };

        $queryKetuaFiltered = Proyek::with(['ketuaProyek', 'aktivitasProyek.penanggungJawab'])->whereHas('anggotaProyek', function($q) use ($userId) {
            $q->where('id_pengguna', $userId)->where('id_peran_proyek', 1);
        });
        $applyFilters($queryKetuaFiltered);
        $proyekKetua = $queryKetuaFiltered->latest()->paginate(10)->withQueryString();

        $queryAnggotaFiltered = Proyek::with(['ketuaProyek', 'aktivitasProyek.penanggungJawab'])->whereHas('anggotaProyek', function($q) use ($userId) {
            $q->where('id_pengguna', $userId)->where('id_peran_proyek', 2);
        });
        $applyFilters($queryAnggotaFiltered);
        $proyekAnggota = $queryAnggotaFiltered->latest()->paginate(10)->withQueryString();

        $idsGabungan = $semuaProyekAll->pluck('id_proyek');
        $querySemua = Proyek::with(['ketuaProyek', 'aktivitasProyek.penanggungJawab'])->whereIn('id_proyek', $idsGabungan);
        $applyFilters($querySemua);
        $semuaProyek = $querySemua->latest()->paginate(10)->withQueryString();

        return view('anggota.proyek', compact(
            'proyekKetua', 'proyekAnggota', 'semuaProyek',
            'isKetuaSaja', 'isAnggotaSaja', 'isPeranGanda', 'tidakPunyaProyek',
            'totalProyek', 'proyekBelumDimulai', 'proyekBerjalan', 
            'proyekSelesai', 'proyekTerlambat', 'jumlahAnggotaProyek'
        ));
    }

    public function aktivitasSaya(Request $request)
    {
        $userId = auth()->id();

        // 1. Ambil ID proyek di mana user ini terlibat sebagai anggota (peran 2) atau PJ aktivitas
        $proyekIds = DB::table('anggota_proyek')
            ->where('id_pengguna', $userId)
            ->where('id_peran_proyek', 2)
            ->pluck('id_proyek')
            ->merge(
                AktivitasProyek::where('id_penanggung_jawab', $userId)->pluck('id_proyek')
            )
            ->unique();

        // Hitung total keseluruhan proyek yang pengguna terlibat di dalamnya
        $totalProyekTerlibat = $proyekIds->count();

        // 2. Query data proyek beserta aktivitas penugasan user dengan filter pencarian, status, tahun, dan bulan
        $proyekQuery = Proyek::with(['aktivitasProyek' => function($q) use ($userId, $request) {
            $q->where('id_penanggung_jawab', $userId);
            
            if ($request->filled('search')) {
                $q->where('nama_aktivitas', 'like', '%' . $request->search . '%');
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
        }])->whereIn('id_proyek', $proyekIds);

        if ($request->filled('search')) {
            $proyekQuery->where(function($q) use ($request) {
                $q->where('nama_proyek', 'like', '%' . $request->search . '%')
                  ->orWhereHas('aktivitasProyek', function($sub) use ($request) {
                      $sub->where('nama_aktivitas', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('status') && $request->status !== 'semua') {
            $proyekQuery->where('status_proyek', $request->status);
        }

        $proyekTerlibat = $proyekQuery->latest()->get();

        // Hitung total keseluruhan aktivitas (tugas) yang harus dikerjakan user
        $totalAktivitasSaya = AktivitasProyek::where('id_penanggung_jawab', $userId)->count();

        return view('anggota.aktivitas', compact(
            'proyekTerlibat', 
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
            ->with('penanggungJawab')
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
            'nama_aktivitas'         => 'required|string|max:150',
            'deskripsi_aktivitas'    => 'nullable|string',
            'id_penanggung_jawab'    => $this->penanggungJawabRules($proyek),
            'tanggal_mulai'          => ['required', 'date', 'after_or_equal:' . $tanggalMulaiMinimum->toDateString(), 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'tanggal_target_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai', 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'status_aktivitas'       => 'nullable|in:belum_dimulai,berjalan,selesai,terlambat',
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
            'nama_aktivitas' => 'required|string|max:150',
            'deskripsi_aktivitas' => 'nullable|string',
            'id_penanggung_jawab' => $this->penanggungJawabRules($proyek),
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:' . $tanggalMulaiMinimum->toDateString(), 'before_or_equal:' . $proyek->tanggal_target_selesai],
            'tanggal_target_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai', 'before_or_equal:' . $proyek->tanggal_target_selesai],
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
            'progress_minggu_berjalan' => 'required|numeric|min:0|max:100',
            'uraian_progress'          => 'nullable|string|max:2000',
            'kendala_internal'         => 'nullable|string|max:2000',
            'kendala_eksternal'        => 'nullable|string|max:2000',
            'dokumen_pendukung'        => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $filePath = $request->file('dokumen_pendukung')->store('dokumen_progress', 'public');
        }

        ProgressAktivitas::create([
            'id_aktivitas'             => $aktivitas->id_aktivitas,
            'id_pengguna'              => auth()->id(),
            'progress_minggu_berjalan' => $data['progress_minggu_berjalan'],
            'uraian_progress'          => $data['uraian_progress'] ?? null,
            'kendala_internal'         => $data['kendala_internal'] ?? null,
            'kendala_eksternal'        => $data['kendala_eksternal'] ?? null,
            'dokumen_pendukung'        => $filePath,
        ]);

        $aktivitas->update([
            'target'                   => $data['progress_minggu_berjalan'],
            'status_aktivitas'         => $data['progress_minggu_berjalan'] >= 100 ? 'selesai' : 'berjalan',
            'tanggal_selesai_aktual'   => $data['progress_minggu_berjalan'] >= 100 ? now()->toDateString() : null,
            'diperbarui_oleh'          => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Laporan progress, kendala, dan dokumen berhasil dikirim.');
    }
}