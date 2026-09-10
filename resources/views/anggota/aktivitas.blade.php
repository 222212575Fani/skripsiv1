<x-layoututama title="Aktivitas Saya">
    <div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        tahun: '{{ request('tahun', 'semua') }}',
        bulan: '{{ request('bulan', 'semua') }}',
        filterOpen: false,
        bulanOpen: false,
        fetchAktivitas() {
            let url = `{{ route('anggota.aktivitassaya') }}?search=${encodeURIComponent(this.search)}&status=${this.status}&tahun=${this.tahun}&bulan=${this.bulan}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newWrapper = doc.getElementById('aktivitas-kanban-wrapper').innerHTML;
                
                document.getElementById('aktivitas-kanban-wrapper').innerHTML = newWrapper;
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }
    }" class="flex flex-col gap-6">

        {{-- 1. 2 CARD STATISTIK DI ATAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-2">
            
            {{-- Card 1: Total Proyek Terlibat --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-center transition-all hover:shadow-md">
                <div class="flex flex-col justify-center">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOTAL PROYEK</p>
                    <p class="text-3xl font-extrabold text-[#6E5BC3] mt-1 tracking-tight">{{ $totalProyekTerlibat ?? 0 }}</p>
                    <p class="text-[11px] font-semibold text-[#6E5BC3] mt-1.5 whitespace-nowrap opacity-90">
                        <span>Proyek yang Anda ikuti</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>

            {{-- Card 2: Total Aktivitas Saya --}}
            <div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-center transition-all hover:shadow-md">
                <div class="flex flex-col justify-center">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">TOTAL AKTIVITAS</p>
                    <p class="text-3xl font-extrabold text-[#6E5BC3] mt-1 tracking-tight">{{ $totalAktivitasSaya ?? 0 }}</p>
                    <p class="text-[11px] font-semibold text-[#6E5BC3] mt-1.5 whitespace-nowrap opacity-90">
                        <span>Aktivitas dari semua proyek</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>

        </div>

        {{-- 2. KOTAK PENCARIAN & FILTER BAR --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="flex flex-wrap items-center gap-3 w-full">
                {{-- Input Search --}}
                <div class="relative flex-1 md:w-56 group/search">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-hover/search:text-[#6E5BC3] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    
                    <input type="text" x-model="search" @input.debounce.400ms="fetchAktivitas()" placeholder="Search tasks..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] rounded-2xl text-xs font-normal text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#6E5BC3] transition-all">

                    <template x-if="search">
                        <button @click="search = ''; fetchAktivitas();" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- Filter Bulan & Tahun (Grid 5 Kolom Tahun) --}}
                <div class="relative group/bulan">
                    <button @click="bulanOpen = !bulanOpen; filterOpen = false;" @click.outside="bulanOpen = false" type="button" 
                        class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] text-gray-600 rounded-2xl text-xs font-medium transition-all cursor-pointer min-w-[170px]">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span x-text="
                                (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][bulan - 1]) + 
                                (tahun === 'semua' ? '' : ' ' + tahun)
                            "></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="bulanOpen" x-cloak class="absolute left-0 mt-2 w-80 bg-white border border-gray-100 rounded-[28px] shadow-xl p-4 z-50 space-y-4">
                        
                        <div class="flex flex-col gap-1.5">
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">PILIH TAHUN</div>
                            <div class="grid grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1">
                                <button @click="tahun = 'semua'; fetchAktivitas();" 
                                    :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white' : 'bg-gray-50 text-gray-600 hover:bg-purple-50 hover:text-[#6E5BC3]'"
                                    class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center col-span-5">
                                    Semua Tahun
                                </button>
                                @for($i = date('Y'); $i >= 1990; $i--)
                                    <button @click="tahun = '{{ $i }}'; fetchAktivitas();" 
                                        :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white' : 'bg-gray-50 text-gray-600 hover:bg-purple-50 hover:text-[#6E5BC3]'"
                                        class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-t border-gray-100 pt-3">PILIH BULAN</div>
                        
                        <div>
                            <button @click="bulan = 'semua'; bulanOpen = false; fetchAktivitas();" 
                                :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'"
                                class="w-full py-2 px-4 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center mb-2">
                                Semua Bulan
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                <button @click="bulan = (index + 1).toString(); bulanOpen = false; fetchAktivitas();" 
                                    :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'"
                                    class="py-2.5 px-2 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center"
                                    x-text="namaBulan">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Filter Status Aktivitas --}}
                <div class="relative group/filter ml-auto">
                    <button @click="filterOpen = !filterOpen; bulanOpen = false;" @click.outside="filterOpen = false" type="button" 
                        class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] text-gray-600 rounded-2xl text-xs font-medium transition-all cursor-pointer min-w-[160px]">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover/filter:text-[#6E5BC3] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                            </svg>
                            <span>
                                @if(request('status') == 'belum_dimulai') Belum Dimulai
                                @elseif(request('status') == 'berjalan') Berjalan
                                @elseif(request('status') == 'selesai') Selesai
                                @elseif(request('status') == 'terlambat') Terlambat
                                @else Semua Status
                                @endif
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="filterOpen" x-cloak class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1">
                        <button @click="status = 'semua'; filterOpen = false; fetchAktivitas();" class="w-full text-left px-3 py-2 rounded-xl text-xs hover:bg-purple-50 hover:text-[#6E5BC3]">Semua Status</button>
                        <button @click="status = 'belum_dimulai'; filterOpen = false; fetchAktivitas();" class="w-full text-left px-3 py-2 rounded-xl text-xs hover:bg-purple-50 hover:text-[#6E5BC3]">Belum Dimulai</button>
                        <button @click="status = 'berjalan'; filterOpen = false; fetchAktivitas();" class="w-full text-left px-3 py-2 rounded-xl text-xs hover:bg-purple-50 hover:text-[#6E5BC3]">Berjalan</button>
                        <button @click="status = 'selesai'; filterOpen = false; fetchAktivitas();" class="w-full text-left px-3 py-2 rounded-xl text-xs hover:bg-purple-50 hover:text-[#6E5BC3]">Selesai</button>
                        <button @click="status = 'terlambat'; filterOpen = false; fetchAktivitas();" class="w-full text-left px-3 py-2 rounded-xl text-xs hover:bg-purple-50 hover:text-[#6E5BC3]">Terlambat</button>
                    </div>
                </div>
            </div>

        </div>

        {{-- 3. DAFTAR KARTU PROYEK (Menampilkan Proyek yang Diikuti Pengguna) --}}
        <div id="aktivitas-kanban-wrapper">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start mt-2">
                
                @forelse($proyekTerlibat as $proyek)
                    <div class="bg-white border border-purple-100 rounded-[28px] p-5 flex flex-col gap-4 shadow-xs">
                        
                        {{-- Header Proyek --}}
                        <div class="flex items-center justify-between pb-3 border-b border-purple-100/60 gap-4">
                            <h3 class="text-xs font-normal text-[#6E5BC3] tracking-wide whitespace-nowrap overflow-hidden text-ellipsis" title="{{ $proyek->nama_proyek }}">
                                {{ $proyek->nama_proyek }}
                            </h3>
                            <span class="w-6 h-6 rounded-full bg-purple-50 border border-purple-100 text-[#6E5BC3] text-[11px] font-bold flex items-center justify-center shadow-xs shrink-0">
                                {{ $proyek->aktivitasProyek->count() }}
                            </span>
                        </div>

                        {{-- Daftar Aktivitas Penugasan di Proyek Ini --}}
                        <div class="flex flex-col gap-3">
                            @forelse($proyek->aktivitasProyek as $akt)
                                @php
                                    $statusAktif = $akt->status_aktivitas ?? 'belum_dimulai';
                                    $badgeClass = match($statusAktif) {
                                        'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        'berjalan'  => 'bg-blue-50 text-blue-600 border-blue-100',
                                        default     => 'bg-amber-50 text-amber-600 border-amber-100'
                                    };
                                    $statusLabel = match($statusAktif) {
                                        'selesai'   => 'Selesai',
                                        'terlambat' => 'Terlambat',
                                        'berjalan'  => 'Berjalan',
                                        default     => 'Belum Dimulai'
                                    };
                                    $progressValue = $akt->target ?? 0;
                                    
                                    // Format Tanggal Mulai - Selesai
                                    $tglMulai = $akt->tanggal_mulai ? \Carbon\Carbon::parse($akt->tanggal_mulai)->translatedFormat('d M Y') : null;
                                    $tglSelesai = $akt->tanggal_target_selesai ? \Carbon\Carbon::parse($akt->tanggal_target_selesai)->translatedFormat('d M Y') : null;
                                    
                                    if ($tglMulai && $tglSelesai) {
                                        $rentangTanggal = $tglMulai . ' - ' . $tglSelesai;
                                    } elseif ($tglSelesai) {
                                        $rentangTanggal = $tglSelesai;
                                    } else {
                                        $rentangTanggal = '-';
                                    }
                                @endphp

                                <div class="bg-gray-50/60 rounded-2xl p-4 border border-purple-100 hover:border-[#6E5BC3]/40 transition-all flex flex-col gap-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold uppercase bg-purple-50 text-[#6E5BC3] mb-1.5">
                                                Aktivitas
                                            </span>
                                            <h4 class="text-[11px] font-light text-gray-900 leading-snug break-words">
                                                {{ $akt->nama_aktivitas }}
                                            </h4>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase border shrink-0 {{ $badgeClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    
                                    {{-- Bar Progress --}}
                                    <div class="flex flex-col gap-1.5 pt-1">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="text-gray-500 font-light">Progress</span>
                                            <span class="font-light text-gray-700">{{ $progressValue }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-[#2EBD85] h-2 rounded-full transition-all duration-300" style="width: {{ $progressValue }}%;"></div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-2 border-t border-purple-100/60 mt-1">
                                        <div class="flex items-center gap-1.5 text-[10px] text-gray-400 font-light">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $rentangTanggal }}</span>
                                        </div>
                                        <button type="button" @click="$dispatch('open-modal-lapor-progress', { id: '{{ $akt->id_aktivitas }}', nama: '{{ addslashes($akt->nama_aktivitas) }}', progress: '{{ $progressValue }}' })" 
                                            class="px-2.5 py-1 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all text-[10px] font-semibold cursor-pointer shadow-xs">
                                            Lapor Progress
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="bg-gray-50/50 rounded-2xl p-6 text-center border border-dashed border-purple-100">
                                    <p class="text-[11px] text-gray-400 italic">Tidak ada aktivitas pada proyek ini yang sesuai dengan filter.</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                @empty
                    <div class="col-span-3 py-16 text-center bg-white rounded-[28px] border border-dashed border-purple-100">
                        <p class="text-xs font-bold text-gray-700">Belum ada proyek yang Anda ikuti atau memiliki penugasan aktivitas.</p>
                    </div>
                @endforelse

            </div>
        </div>

    </div>

    {{-- Muat Modal Lapor Progress --}}
    @include('anggota.modals.laporprogress')
</x-layoututama>