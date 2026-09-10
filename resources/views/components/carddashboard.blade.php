@props(['proyek', 'href' => null])

<div class="bg-white rounded-[24px] border border-gray-200/80 hover:border-[#5C46F5] p-4 flex flex-col justify-between transition-all duration-200 group">
    <div>
        {{-- Baris Atas: Nama Proyek di Kiri, Badge Status di Pojok Kanan Atas --}}
        <div class="flex justify-between items-start gap-3">
            <h3 class="text-xs font-normal text-gray-900 leading-snug">{{ $proyek['nama_proyek'] ?? $proyek->nama_proyek ?? '' }}</h3>
            
            @php
                $statusProyek = $proyek['status_proyek'] ?? $proyek->status_proyek ?? 'belum_dimulai';
                
                $statusBadgeClass = match($statusProyek) {
                    'selesai'   => 'text-emerald-600 border-emerald-300 bg-emerald-50/50',
                    'berjalan'  => 'text-amber-600 border-amber-300 bg-amber-50/50',
                    'terlambat' => 'text-rose-600 border-rose-300 bg-rose-50/50',
                    default     => 'text-orange-600 border-orange-300 bg-orange-50/50', 
                };
            @endphp

            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-normal tracking-wide border {{ $statusBadgeClass }} shrink-0">
                {{ ucwords(str_replace('_', ' ', $statusProyek)) }}
            </span>
        </div>

        {{-- Tanggal Mulai & Selesai (Warna Ungu & Font Normal) --}}
        @php
            $tglMulai = $proyek['tanggal_mulai'] ?? $proyek->tanggal_mulai ?? null;
            $tglSelesai = $proyek['tanggal_target_selesai'] ?? $proyek->tanggal_target_selesai ?? null;
        @endphp
        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-[#5C46F5] font-normal">
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
            $persenProgress = $proyek['persen_progress'] ?? $proyek->persen_progress ?? 0;
            $barColor = match($statusProyek) {
                'selesai'   => 'bg-emerald-500',
                'berjalan'  => 'bg-amber-500',
                'terlambat' => 'bg-rose-500',
                default     => 'bg-orange-500',
            };
        @endphp
        <div class="mt-3">
            <div class="flex justify-between items-center text-[11px] mb-1 font-normal text-gray-400">
                <span>Progress</span>
                <span class="text-gray-700 font-normal">{{ number_format($persenProgress, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
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

        // Ketua proyek selalu ditampilkan, lalu diikuti pengguna yang mendapat penugasan aktivitas.
        $ketua = $proyek->ketuaProyek ?? null;
        $aktivitas = $proyek->aktivitasProyek ?? collect();
        $allPeople = collect([$ketua])
            ->merge(collect($aktivitas)
                ->map(fn ($aktivitas) => $aktivitas->penanggungJawab ?? null)
            )
            ->filter()
            ->unique('id_pengguna')
            ->values();

        $proyekId = $proyek->id_proyek ?? $proyek['id_proyek'] ?? 1;

        // Tentukan URL tujuan: jika parameter $href diisi, gunakan itu. Jika tidak, arahkan ke rute default anggota.
        $targetUrl = $href ?? route('anggota.proyek.aktivitas', $proyekId);
    @endphp

    <div class="flex items-center mt-4 pt-2.5 border-t border-gray-100">
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
                <div class="w-7 h-7 rounded-full bg-indigo-50 border-2 border-white flex items-center justify-center text-[10px] font-normal text-[#5C46F5] shadow-xs shrink-0" title="{{ $namaOrang }}">
                    {{ $initials }}
                </div>
            @empty
                <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-[10px] font-normal text-gray-400 shadow-xs shrink-0" title="Belum ada anggota">
                    -
                </div>
            @endforelse

            @if($allPeople->count() > 8)
                <div class="w-7 h-7 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[9px] font-normal text-gray-600 shadow-xs shrink-0" title="{{ $allPeople->count() - 8 }} penanggung jawab lainnya">
                    +{{ $allPeople->count() - 8 }}
                </div>
            @endif
        </div>

        {{-- Kanan Bawah: Teks Sisa Hari & Tombol Panah Menuju Detail --}}
        <div class="flex items-center gap-2.5 ml-auto">
            <div class="text-xs font-normal text-[#5C46F5]">
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

            <a href="{{ $targetUrl }}" class="w-7 h-7 rounded-full bg-white border border-[#DDD6FE] text-[#5C46F5] flex items-center justify-center shadow-xs shrink-0 group-hover:bg-[#5C46F5] group-hover:text-white group-hover:border-transparent transition-all duration-200 cursor-pointer" title="Lihat Detail">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
