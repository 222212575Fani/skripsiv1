@props(['proyek'])

@php
    use Carbon\Carbon;

    $aktivitasList = $proyek->aktivitasProyek ?? $proyek->aktivitass ?? collect();
    $totalAkt = $aktivitasList->count();
    
    // Menghitung rata-rata progress proyek
    $progressProyek = $proyek->progress ?? ($totalAkt > 0 ? round($aktivitasList->avg('target')) : 0);
    
    $ketuaNama = $proyek->ketuaProyek->nama ?? $proyek->ketua_proyek_nama ?? '-';
    $statusProj = $proyek->status_proyek ?? $proyek->status ?? 'belum_dimulai';

    // Format Tanggal Mulai dan Selesai Proyek
    $tglMulai = $proyek->tanggal_mulai ? Carbon::parse($proyek->tanggal_mulai) : null;
    $tglSelesai = ($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) ? Carbon::parse($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) : null;
    
    $formatRentangTanggal = ($tglMulai && $tglSelesai) 
        ? $tglMulai->translatedFormat('d M y') . ' - ' . $tglSelesai->translatedFormat('d M y') 
        : ($tglSelesai ? $tglSelesai->translatedFormat('d M y') : '-');

    $statusProjBadge = match($statusProj) {
        'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-200',
        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-200',
        'berjalan'  => 'bg-blue-50 text-blue-600 border-blue-200',
        default     => 'bg-amber-50 text-amber-600 border-amber-200'
    };
    $statusProjLabel = match($statusProj) {
        'selesai'   => 'Selesai',
        'terlambat' => 'Terlambat',
        'berjalan'  => 'Berjalan',
        default     => 'Belum Dimulai'
    };

    // Logika Label & Styling Warna Samping Panah (Tanpa bold untuk warning)
    $sekarang = Carbon::now();
    $badgeKeteranganWaktu = '';
    $badgeWaktuStyle = 'bg-gray-50 border-gray-100 text-gray-500';
    
    if ($statusProj === 'selesai') {
        $badgeKeteranganWaktu = 'Proyek Selesai';
        $badgeWaktuStyle = 'bg-emerald-50 border-emerald-100 text-emerald-600 font-medium';
    } elseif ($statusProj === 'terlambat') {
        $selisihHari = $tglSelesai ? $sekarang->diffInDays($tglSelesai, false) : 0;
        $badgeKeteranganWaktu = 'Terlambat ' . abs(round($selisihHari)) . ' hari';
        $badgeWaktuStyle = 'bg-rose-50 border-rose-100 text-rose-600 font-normal';
    } elseif ($statusProj === 'belum_dimulai') {
        $selisihHari = $tglMulai ? $sekarang->diffInDays($tglMulai, false) : 0;
        if ($selisihHari > 0) {
            $badgeKeteranganWaktu = 'Mulai dalam ' . round($selisihHari) . ' hari';
        } else {
            $badgeKeteranganWaktu = 'Segera Dimulai';
        }
        $badgeWaktuStyle = 'bg-rose-50 border-rose-100 text-rose-600 font-normal';
    } else { // Berjalan
        $sisaHari = $tglSelesai ? $sekarang->diffInDays($tglSelesai, false) : 0;
        if ($sisaHari >= 0) {
            $badgeKeteranganWaktu = round($sisaHari) . ' days left';
            if ($sisaHari <= 3) {
                $badgeWaktuStyle = 'bg-rose-50 border-rose-100 text-rose-600 font-normal';
            } else {
                $badgeWaktuStyle = 'bg-gray-50 border-gray-100 text-gray-500';
            }
        } else {
            $badgeKeteranganWaktu = 'Lewat ' . abs(round($sisaHari)) . ' hari';
            $badgeWaktuStyle = 'bg-rose-50 border-rose-100 text-rose-600 font-normal';
        }
    }
@endphp

<div class="bg-[#FBF9FE] border border-purple-100 hover:border-purple-300 rounded-[28px] p-6 h-full flex flex-col justify-between gap-6 shadow-2xs transition-all">
    
    {{-- BAGIAN ATAS --}}
    <div class="flex flex-col gap-4">
        
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-sm font-medium text-gray-900 tracking-tight flex-1">
                {{ $proyek->nama_proyek }}
            </h3>
            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase border tracking-wide shrink-0 {{ $statusProjBadge }}">
                {{ $statusProjLabel }}
            </span>
        </div>

        {{-- Icon Kalender & Rentang Tanggal --}}
        <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ $formatRentangTanggal }}</span>
        </div>

        {{-- Progress Bar (Background Putih, Border Merah, Isi Merah, Persentase Tidak Bold) --}}
        <div class="flex flex-col gap-1.5 pt-1">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400 font-medium">Progress</span>
                <span class="font-normal text-gray-700">{{ $progressProyek }}%</span>
            </div>
            <div class="w-full bg-white border border-rose-300 rounded-full h-2 overflow-hidden shadow-2xs">
                <div class="bg-rose-500 h-full rounded-full transition-all duration-300" style="width: {{ $progressProyek }}%;"></div>
            </div>
        </div>

    </div>

    {{-- BAGIAN BAWAH --}}
    <div class="flex items-center justify-between pt-2 border-t border-purple-100/50">
        <div class="flex items-center -space-x-2">
            <span class="w-7 h-7 rounded-full bg-purple-100 border border-white text-[#6E5BC3] text-[10px] font-bold flex items-center justify-center shadow-2xs" title="{{ $ketuaNama }}">
                {{ strtoupper(substr($ketuaNama, 0, 2)) }}
            </span>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="px-3 py-1.5 rounded-full border text-[11px] transition-all {{ $badgeWaktuStyle }}">
                {{ $badgeKeteranganWaktu }}
            </span>
            <a href="{{ route('anggota.proyek.aktivitas', $proyek->id_proyek ?? 1) }}" class="w-8 h-8 rounded-full border border-purple-200 hover:border-[#6E5BC3] text-gray-600 hover:text-[#6E5BC3] flex items-center justify-center transition-all bg-white shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

</div>