@php
    use Carbon\Carbon;

    $aktivitasList = $proyek->aktivitasProyek ?? $proyek->aktivitass ?? collect();
    $totalAkt = $aktivitasList->count();
    
    // Menghitung rata-rata progress proyek dari aktivitas atau mengambil dari properti proyek
    $progressProyek = $proyek->progress ?? ($totalAkt > 0 ? round($aktivitasList->avg('target')) : 0);
    
    $ketuaNama = $proyek->ketuaProyek->nama ?? $proyek->ketua_proyek_nama ?? 'Belum Ditunjuk';
    $namaTim = $proyek->timKerja->nama_tim ?? ($proyek->tim->nama_tim ?? 'Tim Kerja');
    $statusProj = $proyek->status_proyek ?? $proyek->status ?? 'belum_dimulai';

    // Format Tanggal Mulai dan Selesai Proyek
    $tglMulai = $proyek->tanggal_mulai ? Carbon::parse($proyek->tanggal_mulai) : null;
    $tglSelesai = ($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) ? Carbon::parse($proyek->tenggat_waktu ?? $proyek->tanggal_target_selesai) : null;
    
    $formatRentangTanggal = ($tglMulai && $tglSelesai) 
        ? $tglMulai->translatedFormat('d M y') . ' - ' . $tglSelesai->translatedFormat('d M y') 
        : ($tglSelesai ? $tglSelesai->translatedFormat('d M y') : 'Belum diatur');

    $statusConfig = match($statusProj) {
        'selesai'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
        'terlambat' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => 'Terlambat'],
        'berjalan'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200/60', 'dot' => 'bg-blue-500', 'label' => 'Sedang Berjalan'],
        default     => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200/60', 'dot' => 'bg-amber-500', 'label' => 'Belum Dimulai']
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

<div class="bg-white border border-purple-100 hover:border-purple-300 rounded-2xl sm:rounded-[28px] p-4 sm:p-6 h-full flex flex-col justify-between gap-4 sm:gap-5 shadow-xs transition-all"
     x-data="{ openAktivitas: false }">
    
    {{-- BAGIAN ATAS: INFO UTAMA PROYEK --}}
    <div class="flex flex-col gap-4">
        
        {{-- INDIKATOR TIM KERJA & BADGE STATUS --}}
        <div class="flex items-center justify-between gap-2">
            <span class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-[#6E5BC3] border border-purple-100 truncate max-w-55" title="{{ $namaTim }}">
                {{ $namaTim }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold border shrink-0 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                <span>{{ $statusConfig['label'] }}</span>
            </span>
        </div>

        {{-- Judul Proyek (Tinggi seragam 2 baris agar seluruh elemen di bawahnya lurus sejajar) --}}
        <div class="min-h-[2.625rem] flex items-start">
            <h3 class="text-sm font-bold text-gray-900 tracking-tight leading-snug line-clamp-2" title="{{ $proyek->nama_proyek }}">
                {{ $proyek->nama_proyek }}
            </h3>
        </div>

        {{-- Ketua Proyek --}}
        <div class="flex items-center gap-2.5 text-xs text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            @if($proyek->ketuaProyek)
                <span class="font-medium truncate">{{ $proyek->ketuaProyek->nama }}</span>
            @else
                <span class="inline-flex items-center gap-1.5 text-amber-600 font-normal text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                    <span>Belum Ditunjuk</span>
                </span>
            @endif
        </div>

        {{-- Rentang Tanggal Proyek --}}
        <div class="flex items-center gap-2.5 text-xs text-gray-500 font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            @if($tglMulai && $tglSelesai)
                <span class="truncate">{{ $formatRentangTanggal }}</span>
            @else
                <span class="inline-flex items-center gap-1.5 text-amber-600 font-normal text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                    <span>Belum diatur</span>
                </span>
            @endif
        </div>

        {{-- Progress Bar Proyek Utama --}}
        <div class="flex flex-col gap-1.5 pt-1">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-medium">Progress Proyek</span>
                <span class="font-normal text-gray-700">{{ $progressProyek }}%</span>
            </div>
            <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($progressProyek ?? 0))) }}%;"></div>
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
                <span class="text-xs font-bold text-[#4C3B9B]" x-text="openAktivitas ? 'Tutup Daftar Aktivitas' : 'Lihat {{ $totalAkt }} Aktivitas'">
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
    <div :class="openAktivitas ? 'flex' : 'hidden lg:flex'"
         class="flex-col gap-3 pt-2 w-full">
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
                    $rentangAkt = ($tglMulaiAkt && $tglSelesaiAkt) ? "$tglMulaiAkt - $tglSelesaiAkt" : ($tglSelesaiAkt ?? 'Belum diatur');

                    // Mengambil data dokumen dari relasi dokumenPendukung milik model AktivitasProyek
                    $listDokumen = $akt->dokumenPendukung ?? [];
                    $formattedDocs = [];
                    foreach($listDokumen as $doc) {
                        $filePath = $doc->file_path ?? $doc->path ?? $doc->url ?? $doc->nama_file ?? '';
                        $urlDoc = filter_var($filePath, FILTER_VALIDATE_URL) ? $filePath : asset('storage/' . $filePath);
                        $namaDoc = $doc->nama_dokumen ?? $doc->nama ?? basename($filePath);

                        $formattedDocs[] = [
                            'nama_dokumen' => $namaDoc,
                            'url' => $urlDoc
                        ];
                    }

                    // Menyiapkan data kendala internal dan eksternal
                    $kendalaInt = is_array($akt->kendala_internal) ? $akt->kendala_internal : (!empty($akt->kendala_internal) ? [$akt->kendala_internal] : []);
                    $kendalaEks = is_array($akt->kendala_eksternal) ? $akt->kendala_eksternal : (!empty($akt->kendala_eksternal) ? [$akt->kendala_eksternal] : []);
                @endphp

                {{-- Kartu Aktivitas Memanjang (Struktur Bersih, Rapi & Elegan) --}}
                <div @click="$dispatch('open-modal-detail-aktivitas', {
                        nama: @js($akt->nama_aktivitas ?? $akt->nama ?? 'Aktivitas Proyek'),
                        pj: @js($akt->penanggungJawab->nama ?? $akt->pj ?? 'Belum Ditunjuk'),
                        pm: @js($ketuaNama),
                        progress: @js($targetAkt),
                        status: @js($statusAkt),
                        tglMulai: @js($tglMulaiAkt ?? 'Belum diatur'),
                        tglSelesai: @js($tglSelesaiAkt ?? 'Belum diatur'),
                        kendalaInternal: @js($kendalaInt),
                        kendalaEksternal: @js($kendalaEks),
                        riwayatProgress: @js($akt->riwayat_progress ?? []),
                        dokumen: @js($formattedDocs)
                    })"
                    class="w-full bg-[#F8F7FF] hover:bg-[#F2F0FF] border border-purple-100/90 hover:border-purple-300 rounded-2xl p-4 flex flex-col gap-3 transition-all cursor-pointer group shadow-2xs relative z-10">
                    
                    {{-- Baris 1: Kategori 'Aktivitas' di Kiri & Status Badge di Kanan --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-purple-100/70 text-[#6E5BC3] group-hover:bg-[#6E5BC3] group-hover:text-white transition-colors">
                            Aktivitas
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase border tracking-wide shrink-0 {{ $badgeAktStyle }}">
                            {{ $labelAkt }}
                        </span>
                    </div>

                    {{-- Baris 2: Judul Aktivitas --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#6E5BC3] transition-colors leading-snug wrap-break-word">
                            {{ $akt->nama_aktivitas ?? $akt->nama ?? 'Aktivitas Proyek' }}
                        </h4>
                    </div>

                    {{-- Baris 3: Penanggung Jawab Aktivitas (Ikon Orang & Nama Saja, Tanpa 'PJ:') --}}
                    @php
                        $pjAktNama = $akt->penanggungJawab->nama ?? $akt->pj ?? null;
                    @endphp
                    <div class="flex items-center gap-2 text-[11px] text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        @if(!empty($pjAktNama) && $pjAktNama !== '-' && $pjAktNama !== 'Belum Ditunjuk')
                            <span class="font-medium truncate">{{ $pjAktNama }}</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-amber-600 font-normal text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Belum Ditunjuk</span>
                            </span>
                        @endif
                    </div>

                    {{-- Baris 4: Progress Bar Memanjang Penuh --}}
                    <div class="flex flex-col gap-1.5 pt-0.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-500 font-medium">Progress Aktivitas</span>
                            <span class="font-normal text-gray-700">{{ $targetAkt }}%</span>
                        </div>
                        <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                            <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($targetAkt ?? 0))) }}%;"></div>
                        </div>
                    </div>

                    {{-- Baris 5: Tanggal di Kiri & Tombol Lihat Detail di Kanan --}}
                    <div class="flex items-center justify-between pt-2.5 border-t border-purple-100/60 mt-0.5">
                        <div class="flex items-center gap-1.5 text-[11px] text-gray-500 font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            @if(!empty($tglMulaiAkt) && !empty($tglSelesaiAkt) && $rentangAkt !== 'Belum diatur')
                                <span>{{ $rentangAkt }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-normal text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span>Belum diatur</span>
                                </span>
                            @endif
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
                <div class="col-span-full p-5 text-center bg-gray-50/50 rounded-2xl border border-dashed border-purple-200">
                    <p class="text-xs text-gray-500 font-normal">Belum ada aktivitas yang terdaftar dalam proyek ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- BAGIAN BAWAH: FOOTER CARD (Hanya tampil di layar besar seperti tampilan awal) --}}
    <div class="hidden lg:flex pt-3 border-t border-purple-100/50 items-center justify-between">
        <span class="text-[11px] text-gray-400 font-light">Total Aktivitas: {{ $totalAkt }}</span>
    </div>

</div>