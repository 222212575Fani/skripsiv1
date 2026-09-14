<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\AktivitasProyek;
use App\Models\ProgressAktivitas;
use App\Models\Pengguna;
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
        $userId = auth()->id();

        $proyekKetuaAll = Proyek::where('id_ketua_proyek', $userId)->get();
        
        if ($proyekKetuaAll->isEmpty()) {
            return $this->aktivitasSaya($request);
        }

        $queryKetua = Proyek::with(['ketuaProyek', 'aktivitasProyek.penanggungJawab'])
            ->where('id_ketua_proyek', $userId);
        
        $totalProyek         = $proyekKetuaAll->count();
        $proyekBelumDimulai  = $proyekKetuaAll->where('status_proyek', 'belum_dimulai')->count();
        $proyekBerjalan      = $proyekKetuaAll->where('status_proyek', 'berjalan')->count();
        $proyekSelesai       = $proyekKetuaAll->where('status_proyek', 'selesai')->count();
        $proyekTerlambat     = $proyekKetuaAll->where('status_proyek', 'terlambat')->count();
        
        $jumlahAnggotaProyek = DB::table('anggota_proyek')
            ->whereIn('id_proyek', $proyekKetuaAll->pluck('id_proyek'))
            ->distinct('id_pengguna')
            ->count('id_pengguna');

        if ($request->filled('search')) {
            $queryKetua->where('nama_proyek', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status !== 'semua') {
            $queryKetua->where('status_proyek', $request->status);
        }
        if ($request->filled('tahun') && $request->tahun !== 'semua') {
            $queryKetua->where(function($sub) use ($request) {
                $sub->whereYear('tanggal_mulai', $request->tahun)
                    ->orWhereYear('tanggal_target_selesai', $request->tahun);
            });
        }
        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $queryKetua->where(function($sub) use ($request) {
                $sub->whereMonth('tanggal_mulai', $request->bulan)
                    ->orWhereMonth('tanggal_target_selesai', $request->bulan);
            });
        }

        $semuaProyek = $queryKetua->latest()->paginate(10)->withQueryString();

        return view('anggota.proyek', compact(
            'semuaProyek',
            'totalProyek', 'proyekBelumDimulai', 'proyekBerjalan', 
            'proyekSelesai', 'proyekTerlambat', 'jumlahAnggotaProyek'
        ));
    }

    public function aktivitasSaya(Request $request)
    {
        $userId = auth()->id();

        $proyekIds = DB::table('anggota_proyek')
            ->where('id_pengguna', $userId)
            ->where('id_peran_proyek', 2)
            ->pluck('id_proyek')
            ->merge(
                AktivitasProyek::where('id_penanggung_jawab', $userId)->pluck('id_proyek')
            )
            ->unique();

        $totalProyekTerlibat = $proyekIds->count();

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

        $proyekTerlibat = $proyekQuery->latest()->paginate(15)->withQueryString();

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

        // WAJIB ADA: Memuat relasi penanggungJawab dan dokumenPendukung
        $aktivitasProyek = $queryAktivitas
            ->with(['penanggungJawab', 'dokumenPendukung'])
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

        $targetPengguna = Pengguna::find($request->id_penanggung_jawab);
        if ($targetPengguna) {
            $targetPengguna->notify(new GeneralNotification(
                'Tugas Aktivitas Baru',
                "Anda mendapatkan penugasan aktivitas baru: '{$request->nama_aktivitas}' pada proyek {$proyek->nama_proyek}."
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
            'dokumen_pendukung.*'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:5120',
        ]);

        $userId = auth()->id();

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
            'progress_minggu_berjalan' => $data['progress_minggu_berjalan'],
            'uraian_progress'          => $data['uraian_progress'] ?? null,
        ]);

        if ($request->hasFile('dokumen_pendukung')) {
            foreach ($request->file('dokumen_pendukung') as $file) {
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
            'target'                   => $data['progress_minggu_berjalan'],
            'status_aktivitas'         => $data['progress_minggu_berjalan'] >= 100 ? 'selesai' : 'berjalan',
            'tanggal_selesai_aktual'   => $data['progress_minggu_berjalan'] >= 100 ? now()->toDateString() : null,
            'diperbarui_oleh'          => $userId,
        ]);

        return redirect()->back()->with('success', 'Laporan progress, kendala, dan dokumen berhasil dikirim.');
    }
}