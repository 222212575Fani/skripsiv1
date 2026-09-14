@props(['proyek'])

@php
    $aktivitasList = $proyek->aktivitasProyek ?? $proyek->aktivitass ?? collect();
    $totalAkt = $aktivitasList->count();
    
    // Menghitung rata-rata progress proyek
    $progressProyek = $proyek->progress ?? ($totalAkt > 0 ? round($aktivitasList->avg('target')) : 0);
    
    $ketuaNama = $proyek->ketuaProyek->nama ?? $proyek->ketua_proyek_nama ?? '-';
    $statusProj = $proyek->status_proyek ?? $proyek->status ?? 'belum_dimulai';

    // Format Tanggal Mulai dan Selesai Proyek
    $tglMulaiProyek = $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d M Y') : null;
    $tglSelesaiProyek = ($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) ? \Carbon\Carbon::parse($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai)->translatedFormat('d M Y') : null;
    $rentangTanggalProyek = ($tglMulaiProyek && $tglSelesaiProyek) ? ($tglMulaiProyek . ' - ' . $tglSelesaiProyek) : ($tglSelesaiProyek ?? '-');

    $statusProjBadge = match($statusProj) {
        'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'terlambat' => 'bg-rose-50 text-rose-600 border-emerald-100',
        'berjalan'  => 'bg-blue-50 text-blue-600 border-emerald-100',
        default     => 'bg-amber-50 text-amber-600 border-emerald-100'
    };
    $statusProjLabel = match($statusProj) {
        'selesai'   => 'Selesai',
        'terlambat' => 'Terlambat',
        'berjalan'  => 'Berjalan',
        default     => 'Belum Dimulai'
    };
@endphp

<div class="bg-white border border-purple-100/80 hover:border-purple-200 rounded-[28px] p-6 h-full flex flex-col justify-between gap-5 shadow-xs transition-all">
    
    {{-- BAGIAN ATAS CARD (PROYEK) --}}
    <div class="flex flex-col gap-4">
        
        {{-- Baris 1: Judul Proyek & Jumlah Akumulasi Aktivitas --}}
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-sm font-bold text-[#6E5BC3] leading-snug tracking-tight">
                {{ $proyek->nama_proyek }}
            </h3>
            <span class="w-7 h-7 rounded-full bg-purple-50 border border-purple-100 text-[#6E5BC3] text-xs font-extrabold flex items-center justify-center shadow-xs shrink-0">
                {{ $totalAkt }}
            </span>
        </div>

        {{-- Baris 2: Nama Manager & Status Proyek --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-gray-700 font-medium">
                <div class="w-7 h-7 rounded-xl bg-purple-50 flex items-center justify-center text-[#6E5BC3] shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-800 font-medium">{{ $ketuaNama }}</span>
            </div>
            
            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border tracking-wide {{ $statusProjBadge }}">
                {{ $statusProjLabel }}
            </span>
        </div>

        {{-- Baris 3: Tanggal Mulai & Selesai Proyek --}}
        <div class="flex items-center gap-2 text-xs text-gray-600 font-medium px-0.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ $rentangTanggalProyek }}</span>
        </div>

        {{-- Baris 4: Progress Proyek Keseluruhan --}}
        <div class="flex flex-col gap-1.5 pt-1">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-400 font-medium">Progress Proyek</span>
                <span class="font-bold text-gray-700">{{ $progressProyek }}%</span>
            </div>
            <div class="w-full bg-gray-100/80 rounded-full h-2.5 overflow-hidden p-0.5 border border-gray-100">
                <div class="bg-[#2EBD85] h-full rounded-full transition-all duration-300 shadow-xs" style="width: {{ $progressProyek }}%;"></div>
            </div>
        </div>

    </div>

    {{-- GARIS PEMBATAS ELEGAN --}}
    <div class="border-t border-purple-100/50"></div>

    {{-- BAGIAN BAWAH CARD (LIST AKTIVITAS DENGAN NUANSA UNGU & PROGRESS HIJAU) --}}
    <div class="flex flex-col gap-3 max-h-[225px] overflow-y-auto pr-1.5 custom-scrollbar flex-1">
        @forelse($aktivitasList as $akt)
            @php
                $statusAktif = $akt->status_aktivitas ?? 'belum_dimulai';
                $badgeClass = match($statusAktif) {
                    'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'terlambat' => 'bg-rose-50 text-rose-600 border-emerald-100',
                    'berjalan'  => 'bg-blue-50 text-blue-600 border-emerald-100',
                    default     => 'bg-amber-50 text-amber-600 border-emerald-100'
                };
                $statusLabel = match($statusAktif) {
                    'selesai'   => 'Selesai',
                    'terlambat' => 'Terlambat',
                    'berjalan'  => 'Berjalan',
                    default     => 'Belum Dimulai'
                };
                $progressValue = $akt->target ?? 0;
                
                $tglMulai = $akt->tanggal_mulai ? \Carbon\Carbon::parse($akt->tanggal_mulai)->translatedFormat('d M Y') : null;
                $tglSelesai = $akt->tanggal_target_selesai ? \Carbon\Carbon::parse($akt->tanggal_target_selesai)->translatedFormat('d M Y') : null;
                $rentangTanggal = ($tglMulai && $tglSelesai) ? ($tglMulai . ' - ' . $tglSelesai) : ($tglSelesai ?? '-');
            @endphp

            {{-- Sub-Card Aktivitas (Bernuansa Ungu Lembut: bg-[#F8F7FF] dengan border ungu halus) --}}
            <div class="bg-[#F8F7FF] rounded-2xl p-4 border border-purple-100/80 hover:border-purple-200 transition-all flex flex-col gap-3 shrink-0">
                
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <span class="inline-block px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-purple-100/60 text-[#6E5BC3] mb-1.5 tracking-wider">
                            Aktivitas
                        </span>
                        <h4 class="text-xs font-semibold text-gray-900 leading-snug break-words">
                            {{ $akt->nama_aktivitas }}
                        </h4>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase border shrink-0 tracking-wide {{ $badgeClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
                
                {{-- Progress Bar Aktivitas (Warna Hijau #2EBD85) --}}
                <div class="flex flex-col gap-1.5 pt-1">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-gray-400 font-medium">Progress</span>
                        <span class="font-semibold text-gray-700">{{ $progressValue }}%</span>
                    </div>
                    <div class="w-full bg-purple-100/50 rounded-full h-2 overflow-hidden p-0.5 border border-purple-100/60">
                        <div class="bg-[#2EBD85] h-full rounded-full transition-all duration-300 shadow-xs" style="width: {{ $progressValue }}%;"></div>
                    </div>
                </div>

                {{-- Tanggal Pelaksanaan --}}
                <div class="flex items-center pt-2.5 border-t border-purple-100/60 mt-0.5">
                    <div class="flex items-center gap-1.5 text-[11px] text-gray-700 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $rentangTanggal }}</span>
                    </div>
                </div>

            </div>
        @empty
            {{-- Desain Empty State untuk Aktivitas --}}
            <div class="py-8 px-4 text-center bg-gradient-to-b from-purple-50/40 to-white rounded-2xl border border-dashed border-purple-200/80 flex flex-col items-center justify-center gap-2 my-auto">
                <div class="w-10 h-10 rounded-full bg-purple-100/60 text-[#6E5BC3] flex items-center justify-center mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-gray-800">Belum Ada Aktivitas</h4>
                <p class="text-[11px] text-gray-400 max-w-[200px] leading-relaxed">
                    Proyek ini belum memiliki daftar aktivitas yang terdaftar.
                </p>
            </div>
        @endforelse
    </div>

</div>