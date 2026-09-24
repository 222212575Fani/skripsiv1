<x-layoututama title="Proyek">
    {{-- CSS Kustom untuk Scrollbar Tipis --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2DCF7;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6E5BC3;
        }
    </style>

    <div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        tahun: '{{ request('tahun', 'semua') }}',
        bulan: '{{ request('bulan', 'semua') }}',
        filterOpen: false,
        bulanOpen: false,
        fetchProjects(customUrl = null) {
            let url = customUrl || `{{ route('anggota.proyekaktivitas') }}?search=${encodeURIComponent(this.search)}&status=${this.status}&tahun=${this.tahun}&bulan=${this.bulan}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                
                let newWrapper = doc.getElementById('project-results-wrapper');
                let curWrapper = document.getElementById('project-results-wrapper');
                if (newWrapper && curWrapper) {
                    curWrapper.innerHTML = newWrapper.innerHTML;
                }

                if (window.Alpine) {
                    if (curWrapper) window.Alpine.initTree(curWrapper);
                }
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }
    }" class="flex flex-col gap-4 sm:gap-6 w-full">

        {{-- CONTAINER BANNER UNGU UTAMA --}}
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-2xl sm:rounded-[28px] shadow-sm p-4 sm:p-6 text-white flex flex-col gap-1.5">
            <h1 class="text-sm sm:text-base font-light">
                Halo, {{ $sapaanWaktu ?? 'Selamat Datang' }}, {{ auth()->user()->nama ?? auth()->user()->name }}! 👋
            </h1>
            <p class="text-xs text-purple-100 font-light">
                Lihat dan kelola seluruh proyek serta aktivitas yang Anda pimpin dengan mudah di sini.
            </p>
        </div>

        {{-- 1. CARD STATISTIK (1 KOLOM DI MOBILE, 2 DI TABLET, 3 DI DESKTOP >= xl) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-3.5 xl:gap-4">
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $statsProyek['total'] ?? $totalProyek ?? 0 }}" 
                percent="{{ $statsProyek['total_persen_text'] ?? 'Total proyek dipimpin' }}" 
                percentColor="text-indigo-600" 
                trend="chart" 
                color="text-indigo-600" 
                bg="bg-indigo-50" 
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Belum Dimulai" 
                value="{{ $statsProyek['belum_dimulai'] ?? $proyekBelumDimulai ?? 0 }}" 
                percent="{{ $statsProyek['belum_dimulai_persen'] ?? '0% dari total' }}" 
                percentColor="text-amber-600" 
                trend="chart" 
                color="text-amber-600" 
                bg="bg-amber-50" 
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Sedang Berjalan" 
                value="{{ $statsProyek['berjalan'] ?? $proyekBerjalan ?? 0 }}" 
                percent="{{ $statsProyek['berjalan_persen'] ?? '0% dari total' }}" 
                percentColor="text-blue-600" 
                trend="chart" 
                color="text-blue-600" 
                bg="bg-blue-50" 
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Selesai" 
                value="{{ $statsProyek['selesai'] ?? $proyekSelesai ?? 0 }}" 
                percent="{{ $statsProyek['selesai_persen'] ?? '0% dari total' }}" 
                percentColor="text-emerald-600" 
                trend="check" 
                color="text-emerald-600" 
                bg="bg-emerald-50" 
                svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Terlambat" 
                value="{{ $statsProyek['terlambat'] ?? $proyekTerlambat ?? 0 }}" 
                percent="{{ $statsProyek['terlambat_persen'] ?? '0% dari total' }}" 
                percentColor="{{ ($statsProyek['terlambat'] ?? $proyekTerlambat ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}" 
                trend="{{ ($statsProyek['terlambat'] ?? $proyekTerlambat ?? 0) > 0 ? 'alert' : 'check' }}" 
                color="text-rose-600" 
                bg="bg-rose-50" 
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />

            <x-cardstatistikdashboard 
                title="Jumlah Anggota Terlibat" 
                value="{{ $jumlahAnggotaProyek ?? 0 }}" 
                percent="{{ $jumlahAnggotaProyek ?? 0 }} orang" 
                percentColor="text-indigo-600" 
                trend="chart" 
                color="text-indigo-600" 
                bg="bg-indigo-50" 
                svgPath="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" 
            />
        </div>

        {{-- ================= 2. BAGIAN PENCARIAN & FILTER BAR (SESUAI DESAIN KARTU ANGGOTA) ================= --}}
        <div class="bg-white rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100 p-3.5 sm:p-5 flex flex-col gap-3 w-full">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 w-full">
                {{-- 1. Input Search Memanjang Penuh saat Layar Diperkecil --}}
                <div class="relative w-full lg:flex-1 group/search">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#604EE6] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    
                    <input type="text" x-model="search" @input.debounce.400ms="fetchProjects()" placeholder="Cari aktivitas atau proyek..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 focus:outline-none focus:border-[#604EE6] focus:ring-2 focus:ring-purple-100 focus:bg-white rounded-full text-xs font-light text-gray-800 placeholder:text-gray-400 placeholder:font-light transition-all shadow-2xs">

                    <template x-if="search">
                        <button @click="search = ''; fetchProjects();" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#604EE6] hover:text-[#5C4AB5] cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- 2. Filter Bulan & Tahun --}}
                <div class="relative w-full lg:w-auto group/bulan" @click.outside="bulanOpen = false">
                    <button @click="bulanOpen = !bulanOpen; filterOpen = false;" type="button" 
                        class="w-full lg:w-auto flex items-center justify-between gap-3 px-4 py-2.5 bg-white border text-[#604EE6] rounded-full text-xs font-light transition-all cursor-pointer shadow-2xs lg:min-w-42.5 focus:outline-none"
                        :class="bulanOpen ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate" :class="(bulan === 'semua' && tahun === 'semua') ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="
                                (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][parseInt(bulan) - 1]) + 
                                (tahun === 'semua' ? '' : ' ' + tahun)
                            "></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="bulanOpen" x-cloak class="absolute left-0 right-0 lg:right-auto mt-2 w-full lg:w-80 bg-white border border-purple-100 rounded-[28px] shadow-xl p-4 z-50 space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="text-[10px] font-normal text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1 custom-scrollbar">
                                <button type="button" @click="tahun = 'semua'; fetchProjects();" 
                                    :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white font-normal' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                    class="py-1.5 px-1 rounded-xl text-[11px] transition-all cursor-pointer text-center col-span-4 sm:col-span-5">
                                    Semua Tahun
                                </button>
                                @php
                                    $maxTahun = max((int)date('Y'), 2027);
                                @endphp
                                @for($i = $maxTahun; $i >= 1990; $i--)
                                    <button type="button" @click="tahun = '{{ $i }}'; fetchProjects();" 
                                        :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white font-normal' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                        class="py-1.5 px-1 rounded-xl text-[11px] transition-all cursor-pointer text-center">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="border-t border-purple-100/60 pt-3">
                            <div class="text-[10px] font-normal text-[#6E5BC3] uppercase tracking-widest mb-2">PILIH BULAN</div>
                            <div>
                                <button type="button" @click="bulan = 'semua'; bulanOpen = false; fetchProjects();" 
                                    :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white font-normal shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                    class="w-full py-2 px-4 rounded-2xl text-xs font-light transition-all cursor-pointer text-center mb-2">
                                    Semua Bulan
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                    <button type="button" @click="bulan = (index + 1).toString(); bulanOpen = false; fetchProjects();" 
                                        :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white font-normal shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                        class="py-2.5 px-2 rounded-2xl text-xs font-light transition-all cursor-pointer text-center"
                                        x-text="namaBulan">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Filter Status Proyek / Aktivitas --}}
                <div class="relative w-full lg:w-auto group/filter lg:ml-auto" @click.outside="filterOpen = false">
                    <button @click="filterOpen = !filterOpen; bulanOpen = false;" type="button" 
                        class="w-full lg:w-auto flex items-center justify-between gap-3 px-4 py-2.5 bg-white border text-[#604EE6] rounded-full text-xs font-light transition-all cursor-pointer shadow-2xs lg:min-w-40 focus:outline-none"
                        :class="filterOpen ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                            </svg>
                            <span class="truncate" :class="status === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="{
                                'belum_dimulai': 'Belum Dimulai',
                                'berjalan': 'Sedang Berjalan',
                                'selesai': 'Selesai',
                                'terlambat': 'Terlambat'
                            }[status] || 'Semua Status'"></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="filterOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="filterOpen" x-cloak class="absolute left-0 right-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-56 bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1">
                        <button type="button" @click="status = 'semua'; filterOpen = false; fetchProjects();" 
                            :class="status === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-normal' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer">
                            Semua Status
                        </button>
                        <button type="button" @click="status = 'belum_dimulai'; filterOpen = false; fetchProjects();" 
                            :class="status === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-normal' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer">
                            Belum Dimulai
                        </button>
                        <button type="button" @click="status = 'berjalan'; filterOpen = false; fetchProjects();" 
                            :class="status === 'berjalan' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-normal' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer">
                            Sedang Berjalan
                        </button>
                        <button type="button" @click="status = 'selesai'; filterOpen = false; fetchProjects();" 
                            :class="status === 'selesai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-normal' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer">
                            Selesai
                        </button>
                        <button type="button" @click="status = 'terlambat'; filterOpen = false; fetchProjects();" 
                            :class="status === 'terlambat' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-normal' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer">
                            Terlambat
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= 3. BAGIAN KARTU PROYEK (KOLOM CARD DENGAN MENU KELOLA & SUB-CARD AKTIVITAS) ================= --}}
        <div id="project-results-wrapper" class="flex flex-col gap-6 w-full">
            @php
                $isFiltering = request()->filled('search') || (request()->filled('status') && request()->status !== 'semua') || (request()->filled('bulan') && request()->bulan !== 'semua') || (request()->filled('tahun') && request()->tahun !== 'semua');
            @endphp

            @if(isset($semuaProyek) && $semuaProyek->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 items-start mt-2">
                    @foreach($semuaProyek as $proyek)
                        @php
                            $aktivitasList = $proyek->aktivitasProyek ?? collect();
                            $totalAktProyek = $aktivitasList->count();
                        @endphp
                        
                        <div class="bg-white border border-purple-100 rounded-2xl sm:rounded-[28px] p-4 sm:p-5 flex flex-col gap-3.5 sm:gap-4 shadow-xs"
                             x-data="{ 
                                 openAktivitas: {{ $isFiltering ? 'true' : 'false' }},
                                 isLarge: window.innerWidth >= 1024
                             }"
                             @resize.window.debounce.100ms="isLarge = window.innerWidth >= 1024">
                            
                            {{-- Header Proyek: Nama Proyek di Kiri, Menu Kelola di Kanan --}}
                            <div class="pb-3 border-b border-purple-100/60 flex items-center justify-between gap-3">
                                <h3 class="text-xs font-light text-[#6E5BC3] tracking-wide whitespace-nowrap overflow-hidden text-ellipsis flex-1" title="{{ $proyek->nama_proyek }}">
                                    {{ $proyek->nama_proyek }}
                                </h3>

                                {{-- Menu / Tombol Kelola Proyek --}}
                                <a href="{{ route('anggota.proyek.aktivitas', $proyek->id_proyek) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-purple-50 hover:bg-[#6E5BC3] text-[#6E5BC3] hover:text-white transition-all text-[11px] font-light border border-purple-200/70 shadow-2xs shrink-0 cursor-pointer"
                                   title="Kelola Aktivitas Proyek">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Kelola</span>
                                </a>
                            </div>

                            {{-- Tombol Dropdown Aktivitas (Hanya tampil saat layar kecil / responsif < lg) --}}
                            <div class="lg:hidden">
                                <button type="button" 
                                    @click="openAktivitas = !openAktivitas"
                                    class="flex items-center justify-between w-full px-4 py-2.5 bg-[#EEECFC] hover:bg-[#E5E2F9] rounded-2xl transition-all cursor-pointer group shadow-2xs">
                                    <div class="flex items-center gap-2.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l9.75 5.25 9.75-5.25-4.179-2.25m-11.142 0L12 12.75l4.179-2.25m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m-11.142 0L12 7.5l4.179 2.25" />
                                        </svg>
                                        <span class="text-xs font-light text-[#4C3B9B]" x-text="openAktivitas ? 'Tutup Daftar Aktivitas' : 'Lihat {{ $totalAktProyek }} Aktivitas'">
                                            Lihat {{ $totalAktProyek }} Aktivitas
                                        </span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="openAktivitas ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Daftar Aktivitas: Langsung tampil pada layar besar (lg+), Dropdown pada layar kecil (< lg) --}}
                            <div x-show="openAktivitas || isLarge"
                                 x-cloak
                                 class="flex flex-col gap-3 max-h-105 overflow-y-auto pr-1.5 custom-scrollbar lg:flex!">
                                @forelse($aktivitasList as $akt)
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
                                        
                                        $tglMulai = $akt->tanggal_mulai ? \Carbon\Carbon::parse($akt->tanggal_mulai)->translatedFormat('d M Y') : null;
                                        $tglSelesai = $akt->tanggal_target_selesai ? \Carbon\Carbon::parse($akt->tanggal_target_selesai)->translatedFormat('d M Y') : null;
                                        $rentangTanggal = ($tglMulai && $tglSelesai) ? ($tglMulai . ' - ' . $tglSelesai) : ($tglSelesai ?? 'Belum diatur');

                                        $pjNama = $akt->penanggungJawab->nama ?? 'Belum Ditunjuk';
                                        $pjInisial = strtoupper(substr($pjNama, 0, 2));

                                        $formattedDocs = [];
                                        foreach($akt->dokumenPendukung ?? [] as $docItem) {
                                            $filePath = $docItem->file_path ?? $docItem->path ?? $docItem->url ?? '';
                                            $urlDoc = filter_var($filePath, FILTER_VALIDATE_URL) ? $filePath : asset('storage/' . $filePath);
                                            $formattedDocs[] = [
                                                'nama_dokumen' => $docItem->nama_dokumen ?? basename($filePath),
                                                'url' => $urlDoc
                                            ];
                                        }
                                    @endphp

                                    {{-- KARTU AKTIVITAS YANG BISA DIKLIK --}}
                                    <div @click="$dispatch('open-modal-detail-aktivitas', {
                                            nama: '{{ addslashes($akt->nama_aktivitas) }}',
                                            pj: '{{ addslashes($pjNama) }}',
                                            pm: '{{ addslashes($proyek->ketuaProyek->nama ?? 'Belum Ditunjuk') }}',
                                            progress: '{{ $progressValue }}',
                                            status: '{{ $statusLabel }}',
                                            tglMulai: '{{ $tglMulai ?? 'Belum diatur' }}',
                                            tglSelesai: '{{ $tglSelesai ?? 'Belum diatur' }}',
                                            kendalaInternal: @js($akt->kendala_internal ?? []),
                                            kendalaEksternal: @js($akt->kendala_eksternal ?? []),
                                            riwayatProgress: @js($akt->riwayat_progress ?? []),
                                            dokumen: @js($formattedDocs)
                                        })"
                                        class="bg-gray-50/60 rounded-2xl p-4 border border-purple-100 hover:border-[#6E5BC3]/40 hover:bg-purple-50/20 transition-all flex flex-col gap-3 cursor-pointer group">
                                        
                                        {{-- Header Sub-Card: Badge Aktivitas & Status --}}
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[9px] font-normal tracking-wider px-2 py-0.5 rounded-full bg-purple-100/70 text-[#6E5BC3] uppercase">
                                                Aktivitas
                                            </span>
                                            <span class="text-[9px] font-light px-2 py-0.5 rounded-full border uppercase tracking-wider {{ $badgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>

                                        {{-- Nama Aktivitas --}}
                                        <h4 class="text-xs font-light text-gray-800 line-clamp-2 leading-relaxed">
                                            {{ $akt->nama_aktivitas }}
                                        </h4>

                                        {{-- Info Penanggung Jawab --}}
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-purple-100 text-[#6E5BC3] font-light text-[10px] flex items-center justify-center shrink-0 border border-purple-200">
                                                {{ $pjInisial }}
                                            </div>
                                            <span class="text-[11px] text-gray-600 font-light truncate">
                                                {{ $pjNama }}
                                            </span>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="flex flex-col gap-1.5 pt-1">
                                            <div class="flex items-center justify-between text-[11px] font-light">
                                                <span class="text-gray-500">Progress</span>
                                                <span class="text-gray-700">{{ $progressValue }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200/80 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#6E5BC3] h-1.5 rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($progressValue))) }}%"></div>
                                            </div>
                                        </div>

                                        {{-- Rentang Tanggal --}}
                                        <div class="flex items-center gap-1.5 text-[11px] text-gray-500 font-light pt-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="truncate">{{ $rentangTanggal }}</span>
                                        </div>

                                        {{-- Tombol Aksi: Lihat Detail Aktivitas Saja (Ketua Proyek Tidak Melapor Progress) --}}
                                        <div class="pt-2 border-t border-purple-100/40">
                                            <button type="button" 
                                                @click.stop="$dispatch('open-modal-detail-aktivitas', {
                                                    nama: '{{ addslashes($akt->nama_aktivitas) }}',
                                                    pj: '{{ addslashes($pjNama) }}',
                                                    pm: '{{ addslashes($proyek->ketuaProyek->nama ?? 'Belum Ditunjuk') }}',
                                                    progress: '{{ $progressValue }}',
                                                    status: '{{ $statusLabel }}',
                                                    tglMulai: '{{ $tglMulai ?? 'Belum diatur' }}',
                                                    tglSelesai: '{{ $tglSelesai ?? 'Belum diatur' }}',
                                                    kendalaInternal: @js($akt->kendala_internal ?? []),
                                                    kendalaEksternal: @js($akt->kendala_eksternal ?? []),
                                                    riwayatProgress: @js($akt->riwayat_progress ?? []),
                                                    dokumen: @js($formattedDocs)
                                                })"
                                                class="w-full py-1.5 px-3 rounded-xl bg-purple-50/60 hover:bg-[#6E5BC3] text-[#6E5BC3] hover:text-white border border-purple-100 transition-all text-[11px] font-light cursor-pointer shadow-2xs flex items-center justify-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Lihat Detail</span>
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="py-8 px-4 text-center rounded-2xl bg-purple-50/30 border border-dashed border-purple-200/70 flex flex-col items-center justify-center gap-2">
                                        <div class="w-9 h-9 rounded-full bg-white border border-purple-100 flex items-center justify-center text-[#6E5BC3] shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500 font-light">Belum ada aktivitas di proyek ini.</p>
                                        <a href="{{ route('anggota.proyek.aktivitas', $proyek->id_proyek) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#604EE6] text-white text-[11px] font-light hover:bg-[#503ED8] transition-all shadow-xs">
                                            <span>+ Tambah Aktivitas</span>
                                        </a>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Footer Card: Total Aktivitas --}}
                            <div class="pt-3 border-t border-purple-100/50 flex items-center justify-between">
                                <span class="text-[11px] text-gray-400 font-light">Total Aktivitas: {{ $totalAktProyek }}</span>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- PAGINASI DENGAN INDIKATOR PILL & BULAT --}}
                @if ($semuaProyek->hasPages())
                    <div class="flex items-center justify-center gap-2 sm:gap-2.5 mt-8 mb-4">
                        @foreach ($semuaProyek->getUrlRange(1, $semuaProyek->lastPage()) as $page => $url)
                            @if ($page == $semuaProyek->currentPage())
                                <span class="h-2.5 w-8 sm:w-10 bg-[#6E5BC3] rounded-full transition-all duration-300 shadow-xs cursor-default" title="Halaman {{ $page }}" aria-current="page"></span>
                            @else
                                <a href="{{ $url }}" 
                                   @click.prevent="fetchProjects('{{ $url }}')"
                                   class="h-2.5 w-2.5 bg-[#6E5BC3]/25 hover:bg-[#6E5BC3]/60 rounded-full transition-all duration-300 cursor-pointer" 
                                   title="Ke Halaman {{ $page }}" 
                                   aria-label="Ke Halaman {{ $page }}"></a>
                            @endif
                        @endforeach
                    </div>
                @endif
            @else
                {{-- TAMPILAN EMPTY STATE KETIKA HASIL FILTER KOSONG --}}
                <div class="col-span-1 lg:col-span-3 w-full py-8">
                    <x-emptystate 
                        title="Tidak Ada Proyek Ditemukan" 
                        message="Tidak ada proyek atau aktivitas yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                    />
                </div>
            @endif
        </div>

    </div>

    {{-- Muat Modal Detail Aktivitas --}}
    @include('anggota.detailaktivitas')
</x-layoututama>