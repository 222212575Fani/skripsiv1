@props(['proyek'])

@php
    use Carbon\Carbon;

    $aktivitasList = $proyek->aktivitasProyek ?? $proyek->aktivitass ?? collect();
    $totalAkt = $aktivitasList->count();
    
    // Menghitung rata-rata progress proyek dari aktivitas atau mengambil dari properti proyek
    $progressProyek = $proyek->progress ?? ($totalAkt > 0 ? round($aktivitasList->avg('target')) : 0);
    
    $ketuaNama = $proyek->ketuaProyek->nama ?? $proyek->ketua_proyek_nama ?? '-';
    $namaTim = $proyek->timKerja->nama_tim ?? ($proyek->tim->nama_tim ?? 'Tim Kerja');
    $statusProj = $proyek->status_proyek ?? $proyek->status ?? 'belum_dimulai';

    // Format Tanggal Mulai dan Selesai Proyek
    $tglMulai = $proyek->tanggal_mulai ? Carbon::parse($proyek->tanggal_mulai) : null;
    $tglSelesai = ($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) ? Carbon::parse($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) : null;
    
    $formatRentangTanggal = ($tglMulai && $tglSelesai) 
        ? $tglMulai->translatedFormat('d M y') . ' - ' . $tglSelesai->translatedFormat('d M y') 
        : ($tglSelesai ? $tglSelesai->translatedFormat('d M y') : '-');

    $statusProjBadge = match($statusProj) {
        'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
        'berjalan'  => 'bg-blue-50 text-blue-600 border-blue-100',
        default     => 'bg-amber-50 text-amber-600 border-amber-100'
    };
    $statusProjLabel = match($statusProj) {
        'selesai'   => 'Selesai',
        'terlambat' => 'Terlambat',
        'berjalan'  => 'Berjalan',
        default     => 'Belum Dimulai'
    };
@endphp

<div class="bg-white border border-purple-100 hover:border-purple-300 rounded-[28px] p-6 h-full flex flex-col justify-between gap-5 shadow-xs transition-all">
    
    {{-- BAGIAN ATAS: INFO UTAMA PROYEK --}}
    <div class="flex flex-col gap-4">
        
        {{-- INDIKATOR TIM KERJA (CONTAINER TERLUAR) & BADGE STATUS --}}
        <div class="flex items-center justify-between gap-2">
            <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-[#6E5BC3] border border-purple-100 truncate max-w-[180px]">
                {{ $namaTim }}
            </span>
            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase border tracking-wide shrink-0 {{ $statusProjBadge }}">
                {{ $statusProjLabel }}
            </span>
        </div>

        {{-- Judul Proyek --}}
        <div>
            <h3 class="text-sm font-bold text-gray-900 tracking-tight leading-snug">
                {{ $proyek->nama_proyek }}
            </h3>
        </div>

        {{-- Ketua Proyek --}}
        <div class="flex items-center gap-2.5 text-xs text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">{{ $ketuaNama }}</span>
        </div>

        {{-- Rentang Tanggal Proyek --}}
        <div class="flex items-center gap-2.5 text-xs text-gray-500 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>{{ $formatRentangTanggal }}</span>
        </div>

        {{-- Progress Bar Proyek Utama (Warna hitam, tidak di-bold) --}}
        <div class="flex flex-col gap-1.5 pt-1">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-900 font-normal">Progress Proyek</span>
                <span class="font-normal text-gray-900">{{ $progressProyek }}%</span>
            </div>
            <div class="w-full rounded-full h-2 overflow-hidden {{ $progressProyek > 0 ? 'bg-gray-100' : 'bg-white border border-rose-300' }}">
                <div class="bg-rose-500 h-full rounded-full transition-all duration-300" style="width: {{ $progressProyek }}%;"></div>
            </div>
        </div>

    </div>

    {{-- BAGIAN TENGAH: CONTAINER LIST AKTIVITAS --}}
    <div class="flex flex-col gap-3 pt-2">
        <div class="max-h-64 overflow-y-auto pr-1 flex flex-col gap-3 custom-scrollbar">
            @forelse($aktivitasList as $akt)
                @php
                    $statusAkt = $akt->status ?? $akt->status_aktivitas ?? 'belum_dimulai';
                    $targetAkt = $akt->target ?? $akt->progress ?? 0;
                    
                    $badgeAktStyle = match($statusAkt) {
                        'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                        'berjalan'  => 'bg-blue-50 text-blue-600 border-blue-100',
                        default     => 'bg-amber-50 text-amber-600 border-amber-100'
                    };
                    $labelAkt = match($statusAkt) {
                        'selesai'   => 'Selesai',
                        'terlambat' => 'Terlambat',
                        'berjalan'  => 'Berjalan',
                        default     => 'Belum Dimulai'
                    };

                    $tglMulaiAkt = $akt->tanggal_mulai ? Carbon::parse($akt->tanggal_mulai)->translatedFormat('d M Y') : null;
                    $tglSelesaiAkt = ($akt->tenggat_waktu ?? $akt->tanggal_target_selesai) ? Carbon::parse($akt->tenggat_waktu ?? $akt->tanggal_target_selesai)->translatedFormat('d M Y') : null;
                    $rentangAkt = ($tglMulaiAkt && $tglSelesaiAkt) ? "$tglMulaiAkt - $tglSelesaiAkt" : ($tglSelesaiAkt ?? '-');
                @endphp

                <div class="bg-purple-50/40 border border-purple-100/80 rounded-2xl p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-purple-100/60 text-[#6E5BC3]">
                            Aktivitas
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase border {{ $badgeAktStyle }}">
                            {{ $labelAkt }}
                        </span>
                    </div>

                    <h4 class="text-xs font-bold text-gray-800 leading-snug">
                        {{ $akt->nama_aktivitas ?? $akt->nama ?? 'Aktivitas Proyek' }}
                    </h4>

                    {{-- Tulisan Progress Aktivitas (Warna hitam, tidak di-bold) --}}
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between text-[11px] text-gray-900 font-normal">
                            <span>Progress</span>
                            <span class="text-gray-900">{{ $targetAkt }}%</span>
                        </div>
                        <div class="w-full bg-purple-100/60 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#6E5BC3] h-full rounded-full" style="width: {{ $targetAkt }}%;"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400 pt-1 border-t border-purple-100/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $rentangAkt }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center bg-gray-50/50 rounded-2xl border border-dashed border-purple-200">
                    <p class="text-xs text-gray-500 font-normal">Belum ada aktivitas yang terdaftar dalam proyek ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- BAGIAN BAWAH: FOOTER CARD --}}
    <div class="pt-3 border-t border-purple-100/50 flex items-center justify-between">
        <span class="text-[11px] text-gray-400 font-light">Total Aktivitas: {{ $totalAkt }}</span>
    </div>

</div>