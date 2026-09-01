@props(['proyek'])

<div class="bg-white rounded-[24px] shadow-sm border border-gray-200/80 p-6 flex flex-col justify-between hover:shadow-md transition-all">
    <div>
        {{-- Baris Atas: Nama Proyek, Badge Status, & Tanggal Mulai / Selesai --}}
        <div class="flex justify-between items-start gap-3">
            <div class="flex items-center gap-2.5 flex-wrap">
                <h3 class="text-base font-bold text-gray-900">{{ $proyek['nama_proyek'] }}</h3>
                <span class="px-3 py-0.5 rounded-full text-[10px] font-bold tracking-wide border 
                    {{ $proyek['status_proyek'] == 'selesai' ? 'text-emerald-600 border-emerald-300 bg-emerald-50/50' : 
                      ($proyek['status_proyek'] == 'berjalan' ? 'text-amber-600 border-amber-300 bg-amber-50/50' : 
                      ($proyek['status_proyek'] == 'terlambat' ? 'text-rose-600 border-rose-300 bg-rose-50/50' : 'text-pink-600 border-pink-300 bg-pink-50/50')) }}">
                    {{ ucwords(str_replace('_', ' ', $proyek['status_proyek'])) }}
                </span>
            </div>
            
            {{-- Tanggal Mulai & Tanggal Selesai (--- jika belum dimulai) --}}
            <div class="text-right shrink-0">
                <p class="text-xs font-medium text-gray-400">
                    @if(empty($proyek['tanggal_mulai']) || $proyek['status_proyek'] == 'belum_dimulai')
                        Mulai: ---
                    @else
                        Mulai: {{ \Carbon\Carbon::parse($proyek['tanggal_mulai'])->format('M d, Y') }}
                    @endif
                </p>
                <p class="text-xs font-medium text-gray-400 mt-0.5">
                    @if(empty($proyek['tanggal_target_selesai']) || $proyek['status_proyek'] == 'belum_dimulai')
                        Selesai: ---
                    @else
                        Selesai: {{ \Carbon\Carbon::parse($proyek['tanggal_target_selesai'])->format('M d, Y') }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="mt-6">
            <div class="flex justify-between items-center text-xs mb-1.5">
                <span class="font-medium text-gray-400">Progress</span>
                <span class="font-bold text-gray-800">{{ number_format($proyek['persen_progress'], 0) }}%</span>
            </div>
            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all {{ $proyek['color'] }}" style="width: {{ $proyek['persen_progress'] }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Baris Bawah: Indikator Waktu Pengerjaan / Status Selesai Aktual --}}
    @php
        $targetSelesai = !empty($proyek['tanggal_target_selesai']) ? \Carbon\Carbon::parse($proyek['tanggal_target_selesai']) : null;
        $hariIni = \Carbon\Carbon::now();
        $sisaHari = $targetSelesai ? (int) round($hariIni->floatDiffInDays($targetSelesai, false)) : 0;
        
        $selisihAktual = null;
        if (!empty($proyek['tanggal_selesai_aktual']) && $targetSelesai) {
            $tglAktual = \Carbon\Carbon::parse($proyek['tanggal_selesai_aktual']);
            $selisihAktual = (int) round($tglAktual->floatDiffInDays($targetSelesai, false));
        }
    @endphp

    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
        @if($proyek['status_proyek'] == 'belum_dimulai')
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-[#5C46F5] border border-indigo-100">
                Belum mulai
            </span>
        @elseif($proyek['status_proyek'] == 'selesai' && !empty($proyek['tanggal_selesai_aktual']))
            @if($selisihAktual >= 0)
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">
                    Selesai lebih cepat {{ abs($selisihAktual) }} hari
                </span>
            @else
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-200">
                    Selesai terlambat {{ abs($selisihAktual) }} hari
                </span>
            @endif
        @elseif($proyek['status_proyek'] == 'terlambat')
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-200">
                Terlambat {{ abs($sisaHari) }} hari
            </span>
        @else
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-[#5C46F5] border border-indigo-100">
                @if($sisaHari == 0)
                    Tenggat hari ini
                @else
                    {{ $sisaHari }} hari lagi
                @endif
            </span>
        @endif

        <button type="button" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-[#5C46F5] hover:text-white text-gray-600 flex items-center justify-center transition-all shadow-xs border border-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
    </div>
</div>