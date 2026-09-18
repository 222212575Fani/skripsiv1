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
        default     => 'bg-amber-50 text-amber-600 border-amber-100'
    };
    $statusProjLabel = match($statusProj) {
        'selesai'   => 'Selesai',
        'terlambat' => 'Terlambat',
        'berjalan'  => 'Berjalan',
        default     => 'Belum Dimulai'
    };

    // Kumpulkan seluruh orang yang terlibat dalam proyek (Ketua, Anggota Proyek, dan Penanggung Jawab Aktivitas)
    $orangTerlibat = collect();

    // 1. Ketua Proyek
    if ($proyek->ketuaProyek) {
        $orangTerlibat->put($proyek->ketuaProyek->id_pengguna, $proyek->ketuaProyek);
    }

    // 2. Anggota Tim / Proyek
    if (method_exists($proyek, 'anggotaProyek')) {
        $anggotaList = $proyek->relationLoaded('anggotaProyek') 
            ? $proyek->anggotaProyek 
            : $proyek->anggotaProyek()->with('pengguna')->get();
        foreach ($anggotaList as $ap) {
            if ($ap->pengguna) {
                $orangTerlibat->put($ap->pengguna->id_pengguna, $ap->pengguna);
            }
        }
    }

    // 3. Penanggung Jawab Aktivitas
    foreach ($aktivitasList as $akt) {
        if ($akt->penanggungJawab) {
            $orangTerlibat->put($akt->penanggungJawab->id_pengguna, $akt->penanggungJawab);
        }
    }

    $semuaOrang = $orangTerlibat->values();
    $totalOrang = $semuaOrang->count();
    $maksTampil = 6;
    $tampilOrang = $semuaOrang->take($maksTampil);
    $sisaOrang = $totalOrang - $maksTampil;
@endphp

<div class="bg-white border border-purple-100/80 hover:border-purple-200 rounded-[28px] p-6 h-full flex flex-col justify-between gap-5 shadow-xs transition-all"
     x-data="{ 
         openAktivitas: false,
         isLarge: window.innerWidth >= 1024 
     }"
     @resize.window.debounce.100ms="isLarge = window.innerWidth >= 1024">
    
    {{-- BAGIAN ATAS CARD (PROYEK) --}}
    <div class="flex flex-col gap-4">
        
        {{-- Baris 1: Judul Proyek --}}
        <div>
            <h3 class="text-sm font-bold text-[#6E5BC3] leading-snug tracking-tight">
                {{ $proyek->nama_proyek }}
            </h3>
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
                <span class="text-gray-900 font-normal">Progress Proyek</span>
                <span class="font-normal text-gray-900">{{ $progressProyek }}%</span>
            </div>
            <div class="w-full bg-purple-100/60 rounded-full h-2 overflow-hidden">
                <div class="bg-[#6E5BC3] h-full rounded-full transition-all duration-300" style="width: {{ $progressProyek }}%;"></div>
            </div>
        </div>

        {{-- Orang yang Terlibat dalam Proyek (Avatar Bulat Profil) --}}
        <div class="flex items-center justify-between pt-1">
            <span class="text-xs text-gray-500 font-normal">Anggota Terlibat</span>
            <div class="flex items-center -space-x-2">
                @forelse($tampilOrang as $orang)
                    @php
                        $namaPerson = $orang->nama ?? 'Pengguna';
                        $inisial = strtoupper(substr($namaPerson, 0, 2));
                    @endphp
                    <span class="w-7 h-7 rounded-full bg-purple-100 border-2 border-white text-[#6E5BC3] text-[10px] font-bold flex items-center justify-center shadow-2xs hover:scale-110 hover:z-20 transition-all cursor-pointer" 
                          title="{{ $namaPerson }}">
                        {{ $inisial }}
                    </span>
                @empty
                    <span class="w-7 h-7 rounded-full bg-purple-100 border-2 border-white text-[#6E5BC3] text-[10px] font-bold flex items-center justify-center shadow-2xs" 
                          title="{{ $ketuaNama }}">
                        {{ strtoupper(substr($ketuaNama, 0, 2)) }}
                    </span>
                @endforelse

                @if($sisaOrang > 0)
                    <span class="w-7 h-7 rounded-full bg-purple-200 border-2 border-white text-[#6E5BC3] text-[9px] font-extrabold flex items-center justify-center shadow-2xs hover:scale-110 hover:z-20 transition-all cursor-pointer" 
                          title="Dan {{ $sisaOrang }} orang lainnya terlibat">
                        +{{ $sisaOrang }}
                    </span>
                @endif
            </div>
        </div>

    </div>

    {{-- BAGIAN TOMBOL DROPDOWN: HANYA MUNCUL SAAT LAYAR KECIL / RESPONSIVE (< lg) --}}
    <div class="flex flex-col gap-3 pt-2 lg:hidden">
        <button type="button" 
            @click="openAktivitas = !openAktivitas"
            class="flex items-center justify-between w-full px-4 py-3 bg-[#EEECFC] hover:bg-[#E5E2F9] rounded-2xl transition-all cursor-pointer group shadow-2xs">
            <div class="flex items-center gap-2.5">
                {{-- Icon 3-layer stack persis di gambar referensi --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l9.75 5.25 9.75-5.25-4.179-2.25m-11.142 0L12 12.75l4.179-2.25m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m-11.142 0L12 7.5l4.179 2.25" />
                </svg>
                <span class="text-xs font-bold text-[#4C3B9B]">
                    Lihat {{ $totalAkt }} Aktivitas
                </span>
            </div>
            
            {{-- Chevron Panah Bawah Berotasi saat Terbuka --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="openAktivitas ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    {{-- CONTAINER LIST AKTIVITAS: LANGSUNG TAMPIL PADA LAYAR BESAR (lg+), DROPDOWN PADA LAYAR KECIL (<lg) --}}
    <div x-show="openAktivitas || isLarge" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="flex flex-col gap-3 pt-2 w-full lg:flex!">
        <div class="max-h-64 overflow-y-auto pr-1 flex flex-col gap-3 custom-scrollbar">
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

                    // Siapkan payload data dalam bentuk array bersih
                    $payloadArray = [
                        'nama' => $akt->nama_aktivitas,
                        'pj' => $akt->penanggungJawab->nama ?? '-',
                        'pm' => $proyek->ketuaProyek->nama ?? $proyek->ketua_proyek_nama ?? '-',
                        'progress' => $progressValue,
                        'status' => $statusAktif,
                        'tglMulai' => $tglMulai,
                        'tglSelesai' => $tglSelesai,
                        'kendalaInternal' => $akt->kendalaInternal ?? [],
                        'kendalaEksternal' => $akt->kendalaEksternal ?? [],
                        'dokumen' => ($akt->dokumenPendukung ?? collect())->map(fn($d) => [
                            'nama' => $d->nama_dokumen,
                            'url' => str_replace('\\', '/', asset('storage/' . $d->file_path))
                        ])->values()
                    ];
                    
                    $jsonPayload = htmlspecialchars(json_encode($payloadArray), ENT_QUOTES, 'UTF-8');
                @endphp

                {{-- Kartu Aktivitas Memanjang (Struktur Bersih, Rapi & Elegan) --}}
                <div x-data="{}" 
                    data-payload="{!! $jsonPayload !!}"
                    @click="$dispatch('open-modal-detail-aktivitas', JSON.parse($el.dataset.payload))"
                    class="w-full bg-[#F8F7FF] hover:bg-[#F2F0FF] border border-purple-100/90 hover:border-purple-300 rounded-2xl p-4 flex flex-col gap-3 transition-all cursor-pointer group shadow-2xs relative z-10">
                    
                    {{-- Baris 1: Kategori 'Aktivitas' di Kiri & Status Badge di Kanan --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-purple-100/70 text-[#6E5BC3] group-hover:bg-[#6E5BC3] group-hover:text-white transition-colors">
                            Aktivitas
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase border tracking-wide shrink-0 {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    {{-- Baris 2: Judul Aktivitas --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#6E5BC3] transition-colors leading-snug wrap-break-word">
                            {{ $akt->nama_aktivitas }}
                        </h4>
                    </div>

                    {{-- Baris 3: Penanggung Jawab Aktivitas (Ikon Orang & Nama Saja, Tanpa 'PJ:') --}}
                    @php
                        $pjNamaTim = $akt->penanggungJawab->nama ?? $akt->pj ?? null;
                    @endphp
                    @if(!empty($pjNamaTim) && $pjNamaTim !== '-')
                    <div class="flex items-center gap-2 text-[11px] text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="font-medium truncate">{{ $pjNamaTim }}</span>
                    </div>
                    @endif

                    {{-- Baris 4: Progress Bar Memanjang Penuh --}}
                    <div class="flex flex-col gap-1.5 pt-0.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-600 font-normal">Progress Aktivitas</span>
                            <span class="font-normal text-gray-900">{{ $progressValue }}%</span>
                        </div>
                        <div class="w-full bg-purple-100/60 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#6E5BC3] h-full rounded-full transition-all duration-300" style="width: {{ $progressValue }}%;"></div>
                        </div>
                    </div>

                    {{-- Baris 5: Tanggal di Kiri & Tombol Lihat Detail di Kanan --}}
                    <div class="flex items-center justify-between pt-2.5 border-t border-purple-100/60 mt-0.5">
                        <div class="flex items-center gap-1.5 text-[11px] text-gray-500 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $rentangTanggal }}</span>
                        </div>

                        <div class="flex items-center gap-1 text-[#6E5BC3] font-bold text-[11px] group-hover:underline whitespace-nowrap">
                            <span>Lihat Detail</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-6 px-4 text-center bg-gray-50/50 rounded-2xl border border-dashed border-purple-200 flex flex-col items-center justify-center gap-2">
                    <p class="text-xs text-gray-500 font-normal">
                        Proyek ini belum memiliki daftar aktivitas yang terdaftar.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- BAGIAN BAWAH: FOOTER CARD (Hanya tampil di layar besar seperti tampilan awal) --}}
    <div class="hidden lg:flex pt-3 border-t border-purple-100/50 items-center justify-between">
        <span class="text-[11px] text-gray-400 font-light">Total Aktivitas: {{ $totalAkt }}</span>
    </div>

</div>