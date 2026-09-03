@props(['proyek'])

<div class="bg-white rounded-[24px] shadow-sm border border-gray-200/80 p-6 flex flex-col justify-between hover:shadow-md transition-all">
    <div>
        {{-- Baris Atas: Nama Proyek, Badge Status, & Tanggal Mulai / Selesai --}}
        <div class="flex justify-between items-start gap-3">
            <div class="flex items-center gap-2.5 flex-wrap">
                <h3 class="text-base font-bold text-gray-900">{{ $proyek['nama_proyek'] ?? $proyek->nama_proyek ?? '' }}</h3>
                
                @php
                    $statusProyek = $proyek['status_proyek'] ?? $proyek->status_proyek ?? 'belum_dimulai';
                    
                    $statusBadgeClass = match($statusProyek) {
                        'selesai'   => 'text-emerald-600 border-emerald-300 bg-emerald-50/50',
                        'berjalan'  => 'text-amber-600 border-amber-300 bg-amber-50/50',
                        'terlambat' => 'text-rose-600 border-rose-300 bg-rose-50/50',
                        default     => 'text-orange-600 border-orange-300 bg-orange-50/50', 
                    };
                @endphp

                <span class="px-3 py-0.5 rounded-full text-[10px] font-bold tracking-wide border {{ $statusBadgeClass }}">
                    {{ ucwords(str_replace('_', ' ', $statusProyek)) }}
                </span>
            </div>
            
            {{-- Tanggal Mulai & Tanggal Selesai --}}
            @php
                $tglMulai = $proyek['tanggal_mulai'] ?? $proyek->tanggal_mulai ?? null;
                $tglSelesai = $proyek['tanggal_target_selesai'] ?? $proyek->tanggal_target_selesai ?? null;
            @endphp
            <div class="text-right shrink-0">
                <p class="text-xs font-medium text-gray-400">
                    @if(empty($tglMulai))
                        Mulai: ---
                    @else
                        Mulai: {{ \Carbon\Carbon::parse($tglMulai)->format('M d, Y') }}
                    @endif
                </p>
                <p class="text-xs font-medium text-gray-400 mt-0.5">
                    @if(empty($tglSelesai))
                        Selesai: ---
                    @else
                        Selesai: {{ \Carbon\Carbon::parse($tglSelesai)->format('M d, Y') }}
                    @endif
                </p>
            </div>
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
        <div class="mt-6">
            <div class="flex justify-between items-center text-xs mb-1.5">
                <span class="font-medium text-gray-400">Progress</span>
                <span class="font-bold text-gray-800">{{ number_format($persenProgress, 0) }}%</span>
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $barColor }}" style="width: {{ $persenProgress }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Baris Bawah: Tumpukan Avatar Member & Indikator Status Dinamis --}}
    @php
        $hariIni = \Carbon\Carbon::now();

        $tglMulaiCarbon = !empty($tglMulai) ? \Carbon\Carbon::parse($tglMulai) : null;
        $sisaHariMenujuMulai = $tglMulaiCarbon ? (int) round($hariIni->floatDiffInDays($tglMulaiCarbon, false)) : null;

        $targetSelesai = !empty($tglSelesai) ? \Carbon\Carbon::parse($tglSelesai) : null;
        $sisaHariSelesai = $targetSelesai ? (int) round($hariIni->floatDiffInDays($targetSelesai, false)) : 0;
        
        $tglSelesaiAktual = $proyek['tanggal_selesai_aktual'] ?? $proyek->tanggal_selesai_aktual ?? null;
        $selisihAktual = null;
        if (!empty($tglSelesaiAktual) && $targetSelesai) {
            $tglAktual = \Carbon\Carbon::parse($tglSelesaiAktual);
            $selisihAktual = (int) round($tglAktual->floatDiffInDays($targetSelesai, false));
        }

        $ketua = $proyek->ketuaProyek ?? null;
        $rawMembers = $proyek->anggotaProyek ?? collect();
        $listAnggota = is_iterable($rawMembers) ? collect($rawMembers) : collect();

        $allPeople = collect();
        if ($ketua) {
            $allPeople->push($ketua->nama ?? $ketua->name ?? 'Ketua');
        }

        foreach ($listAnggota as $member) {
            $namaMember = $member->pengguna->nama ?? $member->nama ?? $member->name ?? null;
            if ($namaMember && !$allPeople->contains($namaMember)) {
                $allPeople->push($namaMember);
            }
        }
    @endphp

    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
        {{-- Tumpukan Icon Member --}}
        <div class="flex items-center -space-x-2.5 overflow-hidden py-1">
            @forelse($allPeople->take(5) as $namaOrang)
                @php
                    $words = explode(' ', trim($namaOrang));
                    $initials = count($words) >= 2 
                        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) 
                        : strtoupper(substr($namaOrang, 0, 2));
                @endphp
                <div class="w-8 h-8 rounded-full bg-indigo-50 border-2 border-white flex items-center justify-center text-[11px] font-bold text-[#5C46F5] shadow-xs shrink-0" title="{{ $namaOrang }}">
                    {{ $initials }}
                </div>
            @empty
                <div class="w-8 h-8 rounded-full bg-gray-100 border-2 border-white flex items-center justify-center text-[11px] font-bold text-gray-400 shadow-xs shrink-0" title="Belum ada anggota">
                    -
                </div>
            @endforelse

            @if($allPeople->count() > 5)
                <div class="w-8 h-8 rounded-full bg-gray-200 border-2 border-white flex items-center justify-center text-[10px] font-bold text-gray-600 shadow-xs shrink-0">
                    +{{ $allPeople->count() - 5 }}
                </div>
            @endif
        </div>

        {{-- Kanan: Teks Indikator Waktu Berwarna & Tombol Panah --}}
        <div class="flex items-center gap-3">
            @if($statusProyek == 'belum_dimulai')
                @if(empty($tglMulai))
                    <span class="text-xs font-semibold text-orange-600">
                        Tanggal belum ditetapkan
                    </span>
                @else
                    <span class="text-xs font-semibold text-orange-600">
                        @if($sisaHariMenujuMulai == 0)
                            Dimulai hari ini
                        @elseif($sisaHariMenujuMulai > 0)
                            {{ $sisaHariMenujuMulai }} hari lagi mulai
                        @else
                            Lewat jadwal mulai {{ abs($sisaHariMenujuMulai) }} hari
                        @endif
                    </span>
                @endif
            @elseif($statusProyek == 'selesai' && !empty($tglSelesaiAktual))
                @if($selisihAktual >= 0)
                    <span class="text-xs font-semibold text-emerald-600">
                        Selesai lebih cepat {{ abs($selisihAktual) }} hari
                    </span>
                @else
                    <span class="text-xs font-semibold text-rose-600">
                        Selesai terlambat {{ abs($selisihAktual) }} hari
                    </span>
                @endif
            @elseif($statusProyek == 'selesai')
                <span class="text-xs font-semibold text-emerald-600">
                    Proyek telah selesai
                </span>
            @elseif($statusProyek == 'terlambat' || $sisaHariSelesai < 0)
                <span class="text-xs font-semibold text-rose-600">
                    Terlambat {{ abs($sisaHariSelesai) }} hari
                </span>
            @else
                <span class="text-xs font-semibold text-amber-600">
                    @if($sisaHariSelesai == 0)
                        Tenggat hari ini
                    @else
                        {{ $sisaHariSelesai }} hari lagi selesai
                    @endif
                </span>
            @endif

            {{-- Tombol Panah ke Kanan --}}
            <button type="button" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#5C46F5] hover:text-white text-gray-600 flex items-center justify-center transition-all shadow-xs border border-gray-200 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
</div>