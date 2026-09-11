@props(['proyek', 'href' => null])

<div class="bg-white border border-purple-100 rounded-[28px] p-5 flex flex-col justify-between transition-all duration-200 group shadow-xs hover:border-[#6E5BC3]/40">
    <div>
        {{-- Baris Atas: Nama Proyek di Kiri, Badge Status di Pojok Kanan Atas --}}
        <div class="flex justify-between items-start gap-3">
            <h3 class="text-xs font-normal text-gray-900 leading-snug line-clamp-2" title="{{ $proyek['nama_proyek'] ?? $proyek->nama_proyek ?? '' }}">
                {{ $proyek['nama_proyek'] ?? $proyek->nama_proyek ?? '' }}
            </h3>
            
            @php
                $statusProyek = $proyek['status_proyek'] ?? $proyek->status_proyek ?? 'belum_dimulai';
                
                $statusBadgeClass = match($statusProyek) {
                    'selesai'   => 'text-emerald-600 border-emerald-200 bg-emerald-50/50',
                    'berjalan'  => 'text-amber-600 border-amber-200 bg-amber-50/50',
                    'terlambat' => 'text-rose-600 border-rose-200 bg-rose-50/50',
                    default     => 'text-orange-600 border-orange-200 bg-orange-50/50', 
                };
            @endphp

            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold tracking-wide border {{ $statusBadgeClass }} shrink-0 uppercase">
                {{ str_replace('_', ' ', $statusProyek) }}
            </span>
        </div>

        {{-- Tanggal Mulai & Selesai (Warna Ungu & Font Normal) --}}
        @php
            $tglMulai = $proyek['tanggal_mulai'] ?? $proyek->tanggal_mulai ?? null;
            $tglSelesai = $proyek['tanggal_target_selesai'] ?? $proyek->tanggal_target_selesai ?? null;
        @endphp
        <div class="flex items-center gap-1.5 mt-2 text-[11px] text-[#6E5BC3] font-normal">
            <span>
                @if(empty($tglMulai)) Mulai: --- @else {{ \Carbon\Carbon::parse($tglMulai)->locale('id')->translatedFormat('d M Y') }} @endif
            </span>
            <span>-</span>
            <span>
                @if(empty($tglSelesai)) Selesai: --- @else {{ \Carbon\Carbon::parse($tglSelesai)->locale('id')->translatedFormat('d M Y') }} @endif
            </span>
        </div>

        {{-- Progress Bar --}}
        @php
            $persenProgress = $proyek['persen_progress'] ?? $proyek->persen_progress ?? null;
            if ($persenProgress === null && isset($proyek->aktivitasProyek)) {
                $persenProgress = $proyek->aktivitasProyek->avg('target') ?? 0;
            }
            $persenProgress = round($persenProgress ?? 0);

            $barColor = match($statusProyek) {
                'selesai'   => 'bg-emerald-500',
                'berjalan'  => 'bg-amber-500',
                'terlambat' => 'bg-rose-500',
                default     => 'bg-orange-500',
            };
        @endphp
        <div class="mt-3.5">
            <div class="flex justify-between items-center text-[11px] mb-1 font-normal text-gray-400">
                <span>Progress</span>
                <span class="text-gray-700 font-normal">{{ number_format($persenProgress, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $barColor }}" style="width: {{ $persenProgress }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Baris Bawah: Tumpukan Avatar Member, Teks Sisa Hari, & Tombol Panah --}}
    @php
        $hariIni = \Carbon\Carbon::now();
        $targetSelesai = !empty($tglSelesai) ? \Carbon\Carbon::parse($tglSelesai) : null;
        $sisaHariSelesai = $targetSelesai ? (int) round($hariIni->floatDiffInDays($targetSelesai, false)) : 0;
        
        $tglMulaiCarbon = !empty($tglMulai) ? \Carbon\Carbon::parse($tglMulai) : null;
        $sisaHariMenujuMulai = $tglMulaiCarbon ? (int) round($hariIni->floatDiffInDays($tglMulaiCarbon, false)) : null;

        $tglSelesaiAktual = $proyek['tanggal_selesai_aktual'] ?? $proyek->tanggal_selesai_aktual ?? null;
        $selisihAktual = null;
        if (!empty($tglSelesaiAktual) && $targetSelesai) {
            $tglAktual = \Carbon\Carbon::parse($tglSelesaiAktual);
            $selisihAktual = (int) round($tglAktual->floatDiffInDays($targetSelesai, false));
        }

        $ketua = $proyek->ketuaProyek ?? null;
        $aktivitas = $proyek->aktivitasProyek ?? collect();
        $allPeople = collect([$ketua])
            ->merge(collect($aktivitas)
                ->map(fn ($akt) => $akt->penanggungJawab ?? null)
            )
            ->filter()
            ->unique('id_pengguna')
            ->values();

        $proyekId = $proyek->id_proyek ?? $proyek['id_proyek'] ?? 1;
        $targetUrl = $href ?? route('anggota.proyek.aktivitas', $proyekId);
    @endphp

    <div class="flex items-center mt-5 pt-3 border-t border-purple-100/60">
        {{-- Kiri Bawah: Tumpukan Icon Member --}}
        <div class="flex items-center -space-x-2 py-1">
            @forelse($allPeople->take(8) as $pengguna)
                @php
                    $namaOrang = $pengguna->nama ?? $pengguna->name ?? 'Pengguna';
                    $words = explode(' ', trim($namaOrang));
                    $initials = count($words) >= 2 
                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) 
                        : strtoupper(substr($namaOrang, 0, 2));
                @endphp
                <div class="w-6 h-6 rounded-full bg-purple-50 border border-purple-100 text-[#6E5BC3] text-[9px] font-bold flex items-center justify-center shrink-0 shadow-xs" title="{{ $namaOrang }}">
                    {{ $initials }}
                </div>
            @empty
                <div class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[9px] font-bold text-gray-400 shrink-0 shadow-xs" title="Belum ada anggota">
                    -
                </div>
            @endforelse

            @if($allPeople->count() > 8)
                <div class="w-6 h-6 rounded-full bg-gray-200 border border-white flex items-center justify-center text-[9px] font-bold text-gray-600 shrink-0 shadow-xs" title="{{ $allPeople->count() - 8 }} lainnya">
                    +{{ $allPeople->count() - 8 }}
                </div>
            @endif
        </div>

        {{-- Kanan Bawah: Teks Sisa Hari & Tombol Panah Menuju Detail --}}
        <div class="flex items-center gap-2.5 ml-auto">
            <div class="text-[11px] font-light text-[#6E5BC3]">
                @if($statusProyek == 'belum_dimulai')
                    @if(empty($tglMulai))
                        <span>Tanggal belum ditetapkan</span>
                    @else
                        <span>
                            @if($sisaHariMenujuMulai == 0) Dimulai hari ini
                            @elseif($sisaHariMenujuMulai > 0) {{ $sisaHariMenujuMulai }} hari lagi mulai
                            @else Lewat jadwal mulai {{ abs($sisaHariMenujuMulai) }} hari
                            @endif
                        </span>
                    @endif
                @elseif($statusProyek == 'selesai' && !empty($tglSelesaiAktual))
                    @if($selisihAktual >= 0)
                        <span>Selesai lebih cepat {{ abs($selisihAktual) }} hari</span>
                    @else
                        <span>Selesai terlambat {{ abs($selisihAktual) }} hari</span>
                    @endif
                @elseif($statusProyek == 'selesai')
                    <span>Proyek telah selesai</span>
                @elseif($statusProyek == 'terlambat' || $sisaHariSelesai < 0)
                    <span>Terlambat {{ abs($sisaHariSelesai) }} hari</span>
                @else
                    <span>
                        @if($sisaHariSelesai == 0) Tenggat hari ini
                        @else {{ $sisaHariSelesai }} hari lagi selesai
                        @endif
                    </span>
                @endif
            </div>

            <a href="{{ $targetUrl }}" class="w-7 h-7 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center shadow-xs shrink-0 transition-all duration-200 cursor-pointer" title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>