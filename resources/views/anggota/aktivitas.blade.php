<x-layoututama title="Aktivitas">
    {{-- CSS Kustom untuk Scrollbar Tipis & Ikon Kalender Ungu --}}
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
        .pj-dropdown-scroll {
            scrollbar-width: thin;
            scrollbar-color: #9E8CE3 #F8F7FF;
            scrollbar-gutter: stable;
        }
        .pj-dropdown-scroll::-webkit-scrollbar { width: 6px; }
        .pj-dropdown-scroll::-webkit-scrollbar-track { background: #F8F7FF; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb:hover { background: #6E5BC3; }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(38%) sepia(85%) saturate(1541%) hue-rotate(230deg) brightness(95%) contrast(92%);
            cursor: pointer;
        }
    </style>

    <div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        tahun: '{{ request('tahun', 'semua') }}',
        bulan: '{{ request('bulan', 'semua') }}',
        filterOpen: false,
        bulanOpen: false,
        weekOffset: 0,
        fetchAktivitas(customUrl = null) {
            let url = customUrl || `{{ route('anggota.aktivitassaya') }}?search=${encodeURIComponent(this.search)}&status=${this.status}&tahun=${this.tahun}&bulan=${this.bulan}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newWrapper = doc.getElementById('aktivitas-kanban-wrapper');
                let curWrapper = document.getElementById('aktivitas-kanban-wrapper');
                if (newWrapper && curWrapper) {
                    curWrapper.innerHTML = newWrapper.innerHTML;
                    if (window.Alpine) {
                        window.Alpine.initTree(curWrapper);
                    }
                }
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }
    }" class="flex flex-col gap-4 sm:gap-6 w-full">

        {{-- ================= 1. BAGIAN ATAS: 2 KOLOM (KIRI: CONTAINER UNGU + STATISTIK, KANAN: SIDEBAR KALENDER 7 HARI) ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 items-stretch">
            
            {{-- SISI KIRI: CONTAINER UNGU BESAR --}}
            <div class="lg:col-span-2 bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-2xl sm:rounded-3xl lg:rounded-4xl shadow-sm p-4 sm:p-6 lg:p-8 text-white flex flex-col justify-between gap-5 sm:gap-6">
                
                {{-- Header Banner --}}
                <div class="flex flex-col gap-2">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight">
                        Periksa tugas dan jadwal harianmu
                    </h1>
                    <p class="text-xs sm:text-sm text-purple-100 font-normal leading-relaxed">
                        Halo, {{ $sapaanWaktu }}, <span class="font-bold text-white">{{ auth()->user()->nama ?? auth()->user()->name }}</span>! 👋✨<br> 
                        Pantau dan kelola aktivitas proyekmu dengan mudah di sini. Pastikan untuk selalu memperbarui progress pekerjaan dan melaporkan hasil tugas tepat waktu.
                    </p>
                </div>

                {{-- 2 Card Statistik di Dalam Container Ungu --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    
                    {{-- Sub-Card 1: Total Proyek --}}
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-xl sm:rounded-2xl py-3.5 px-4 sm:py-5 sm:px-6 flex items-center justify-between">
                        <div class="flex flex-col gap-0.5 sm:gap-1">
                            <span class="text-[10px] sm:text-[11px] font-medium text-purple-200 uppercase tracking-wider">Total Proyek</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white">{{ $totalProyekTerlibat ?? 0 }}</h3>
                            <span class="text-[11px] sm:text-xs text-purple-200">Proyek yang Anda ikuti</span>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/15 flex items-center justify-center text-white shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>

                    {{-- Sub-Card 2: Total Aktivitas --}}
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-xl sm:rounded-2xl py-3.5 px-4 sm:py-5 sm:px-6 flex items-center justify-between">
                        <div class="flex flex-col gap-0.5 sm:gap-1">
                            <span class="text-[10px] sm:text-[11px] font-medium text-purple-200 uppercase tracking-wider">Total Aktivitas</span>
                            <h3 class="text-xl sm:text-2xl font-bold text-white">{{ $totalAktivitasSaya ?? 0 }}</h3>
                            <span class="text-[11px] sm:text-xs text-purple-200">Tugas dari semua proyek</span>
                        </div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-white/15 flex items-center justify-center text-white shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>

                </div>

            </div>


            {{-- SISI KANAN: SIDEBAR KALENDER 7 HARI & PENGINGAT --}}
            <div class="bg-white border border-purple-100 rounded-2xl sm:rounded-[28px] p-4 sm:p-5 flex flex-col justify-between shadow-xs"
                 x-data="{
                    currentDate: new Date(),
                    get formattedMonth() {
                        return this.currentDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                    },
                    get weekDays() {
                        let start = new Date(this.currentDate);
                        let day = start.getDay();
                        let diff = start.getDate() - day + (day === 0 ? -6 : 1) + (this.weekOffset * 7);
                        let monday = new Date(start.setDate(diff));
                        
                        const indoDays = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
                        let days = [];
                        for(let i=0; i<7; i++) {
                            let d = new Date(monday);
                            d.setDate(monday.getDate() + i);
                            days.push({
                                name: indoDays[i],
                                number: d.getDate(),
                                isToday: d.toDateString() === new Date().toDateString()
                            });
                        }
                        return days;
                    },
                    weekOffset: 0,
                    prevWeek() { this.weekOffset--; },
                    nextWeek() { this.weekOffset++; }
                 }">
                
                <div class="flex flex-col gap-5">
                    {{-- Bagian Kalender Mini (Prev di Kiri, Bulan di Tengah Satu Baris, Next di Kanan) --}}
                    <div class="flex flex-col gap-3">
                        <div class="grid grid-cols-3 items-center w-full">
                            <div class="flex justify-start">
                                <button @click="prevWeek()" class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                            </div>
                            <div class="text-center">
                                <span class="text-sm font-bold text-gray-900 whitespace-nowrap" x-text="formattedMonth"></span>
                            </div>
                            <div class="flex justify-end">
                                <button @click="nextWeek()" class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center">
                            <template x-for="day in weekDays">
                                <div class="flex flex-col items-center justify-center py-2 rounded-xl text-xs transition-all"
                                     :class="day.isToday ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/40 text-gray-600 hover:bg-purple-100/60'">
                                    <span class="text-[8px] uppercase opacity-80" x-text="day.name"></span>
                                    <span class="font-bold mt-0.5 text-xs" x-text="day.number"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Jadwal & Pesan Pengingat --}}
                    <div class="flex flex-col gap-3 pt-2">
                        <div class="flex items-center justify-between">
                            <h5 class="text-xs font-bold text-gray-900">Pengingat Aktivitas & Tugas</h5>
                        </div>

                        <div class="flex flex-col gap-2.5 max-h-45 overflow-y-auto pr-1">
                            <div class="p-3 bg-amber-50/60 border border-amber-100 rounded-2xl flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wide">Peringatan Tugas</span>
                                </div>
                                <p class="text-[11px] font-medium text-gray-800 leading-snug">
                                    Jangan lupa untuk selalu mengerjakan aktivitas tepat waktu dan rutin melaporkan progress tugasmu!
                                </p>
                            </div>

                            <div class="p-3 bg-purple-50/50 border border-purple-100 rounded-2xl flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-wide">Target Minggu Ini</span>
                                </div>
                                <p class="text-[11px] font-medium text-gray-800 leading-snug">
                                    Pastikan target progress harian diperbarui agar pimpinan dapat memantau capaian kinerja.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        {{-- ================= 2. BAGIAN BAWAH: KOTAK PENCARIAN & FILTER BAR ================= --}}
        <div class="bg-white rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100 p-3.5 sm:p-5 flex flex-col gap-3 w-full">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 w-full">
                {{-- 1. Input Search Memanjang Penuh saat Layar Diperkecil --}}
                <div class="relative w-full lg:flex-1 group/search">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#604EE6] transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    
                    <input type="text" x-model="search" @input.debounce.400ms="fetchAktivitas()" placeholder="Cari aktivitas..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 focus:outline-none focus:border-[#604EE6] focus:ring-2 focus:ring-purple-100 focus:bg-white rounded-full text-xs font-light text-gray-800 placeholder:text-gray-400 placeholder:font-light transition-all shadow-2xs">

                    <template x-if="search">
                        <button @click="search = ''; fetchAktivitas();" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#604EE6] hover:text-[#5C4AB5] cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </template>
                </div>

                {{-- 2. Filter Bulan & Tahun --}}
                <div class="relative w-full lg:w-auto group/bulan" @click.outside="bulanOpen = false">
                    <button @click="bulanOpen = !bulanOpen; filterOpen = false;" type="button" 
                        class="w-full lg:w-auto flex items-center justify-between gap-3 px-4 py-2.5 bg-white border text-[#604EE6] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs lg:min-w-42.5 focus:outline-none"
                        :class="bulanOpen ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate" :class="(bulan === 'semua' && tahun === 'semua') ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="
                                (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][parseInt(bulan) - 1]) + 
                                (tahun === 'semua' ? '' : ' ' + tahun)
                            "></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="bulanOpen" x-cloak class="absolute left-0 right-0 lg:right-auto mt-2 w-full lg:w-80 bg-white border border-purple-100 rounded-[28px] shadow-xl p-4 z-50 space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1 custom-scrollbar">
                                <button type="button" @click="tahun = 'semua'; fetchAktivitas();" 
                                    :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white font-light' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                    class="py-1.5 px-1 rounded-xl text-[11px] font-light transition-all cursor-pointer text-center col-span-4 sm:col-span-5">
                                    Semua Tahun
                                </button>
                                @php
                                    $maxTahun = max((int)date('Y'), 2027);
                                @endphp
                                @for($i = $maxTahun; $i >= 1990; $i--)
                                    <button type="button" @click="tahun = '{{ $i }}'; fetchAktivitas();" 
                                        :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white font-light' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                        class="py-1.5 px-1 rounded-xl text-[11px] font-light transition-all cursor-pointer text-center">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="border-t border-purple-100/60 pt-3">
                            <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest mb-2">PILIH BULAN</div>
                            <div>
                                <button type="button" @click="bulan = 'semua'; bulanOpen = false; fetchAktivitas();" 
                                    :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white font-light shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                    class="w-full py-2 px-4 rounded-2xl text-xs font-light transition-all cursor-pointer text-center mb-2">
                                    Semua Bulan
                                </button>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                    <button type="button" @click="bulan = (index + 1).toString(); bulanOpen = false; fetchAktivitas();" 
                                        :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white font-light shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 font-light'"
                                        class="py-2.5 px-2 rounded-2xl text-xs font-light transition-all cursor-pointer text-center"
                                        x-text="namaBulan">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Filter Status Aktivitas --}}
                <div class="relative w-full lg:w-auto group/filter lg:ml-auto" @click.outside="filterOpen = false">
                    <button @click="filterOpen = !filterOpen; bulanOpen = false;" type="button" 
                        class="w-full lg:w-auto flex items-center justify-between gap-3 px-4 py-2.5 bg-white border text-[#604EE6] rounded-full text-xs font-light transition-all cursor-pointer shadow-2xs lg:min-w-42.5 focus:outline-none"
                        :class="filterOpen ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                            </svg>
                            <span class="truncate" :class="status === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="{
                                'belum_dimulai': 'Belum Dimulai',
                                'berjalan': 'Sedang Berjalan',
                                'selesai': 'Selesai',
                                'terlambat': 'Terlambat'
                            }[status] || 'Semua Status'"></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="filterOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="filterOpen" x-cloak class="absolute left-0 right-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-56 bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1">
                        <button type="button" @click="status = 'semua'; filterOpen = false; fetchAktivitas();" 
                            :class="status === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-light transition-all cursor-pointer">
                            Semua Status
                        </button>
                        <button type="button" @click="status = 'belum_dimulai'; filterOpen = false; fetchAktivitas();" 
                            :class="status === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-light transition-all cursor-pointer">
                            Belum Dimulai
                        </button>
                        <button type="button" @click="status = 'berjalan'; filterOpen = false; fetchAktivitas();" 
                            :class="status === 'berjalan' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-light transition-all cursor-pointer">
                            Sedang Berjalan
                        </button>
                        <button type="button" @click="status = 'selesai'; filterOpen = false; fetchAktivitas();" 
                            :class="status === 'selesai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-light transition-all cursor-pointer">
                            Selesai
                        </button>
                        <button type="button" @click="status = 'terlambat'; filterOpen = false; fetchAktivitas();" 
                            :class="status === 'terlambat' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-light transition-all cursor-pointer">
                            Terlambat
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- ================= 3. BAGIAN BAWAH: KARTU PROYEK AKTIVITAS (3 KOLOM MENYAMPING DENGAN SCROLL INTERNAL TIPIS) ================= --}}
        <div id="aktivitas-kanban-wrapper" class="flex flex-col gap-6 w-full">
            @php
                $totalAktivitasFiltered = $proyekTerlibat->sum(function($proyek) {
                    return $proyek->aktivitasProyek->count();
                });
            @endphp

            @if($totalAktivitasFiltered > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 items-start mt-2">
                    @foreach($proyekTerlibat as $proyek)
                        @if($proyek->aktivitasProyek->count() > 0)
                            @php
                                $totalAktProyek = $proyek->aktivitasProyek->count();
                                $isFiltering = request()->filled('search') || (request()->filled('status') && request()->status !== 'semua') || (request()->filled('bulan') && request()->bulan !== 'semua') || (request()->filled('tahun') && request()->tahun !== 'semua');
                            @endphp
                            <div class="bg-white border border-purple-100 rounded-2xl sm:rounded-[28px] p-4 sm:p-5 flex flex-col gap-3.5 sm:gap-4 shadow-xs"
                                 x-data="{ 
                                     openAktivitas: {{ $isFiltering ? 'true' : 'false' }},
                                     isLarge: window.innerWidth >= 1024
                                 }"
                                 @resize.window.debounce.100ms="isLarge = window.innerWidth >= 1024">
                                
                                {{-- Header Proyek --}}
                                <div class="pb-3 border-b border-purple-100/60">
                                    <h3 class="text-xs font-semibold text-[#6E5BC3] tracking-wide whitespace-nowrap overflow-hidden text-ellipsis" title="{{ $proyek->nama_proyek }}">
                                        {{ $proyek->nama_proyek }}
                                    </h3>
                                </div>

                                {{-- Tombol Dropdown Aktivitas (Hanya tampil saat layar kecil / responsif < lg) --}}
                                <div class="lg:hidden">
                                    <button type="button" 
                                        @click="openAktivitas = !openAktivitas"
                                        class="flex items-center justify-between w-full px-4 py-2.5 bg-[#EEECFC] hover:bg-[#E5E2F9] rounded-2xl transition-all cursor-pointer group shadow-2xs">
                                        <div class="flex items-center gap-2.5">
                                            {{-- Icon 3-layer stack --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l9.75 5.25 9.75-5.25-4.179-2.25m-11.142 0L12 12.75l4.179-2.25m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m-11.142 0L12 7.5l4.179 2.25" />
                                            </svg>
                                            <span class="text-xs font-bold text-[#4C3B9B]" x-text="openAktivitas ? 'Tutup Daftar Aktivitas' : 'Lihat {{ $totalAktProyek }} Aktivitas'">
                                                Lihat {{ $totalAktProyek }} Aktivitas
                                            </span>
                                        </div>
                                        
                                        {{-- Chevron Panah Bawah Berotasi saat Terbuka --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="openAktivitas ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Daftar Aktivitas Penugasan: Langsung tampil pada layar besar (lg+), Dropdown pada layar kecil (< lg) --}}
                                <div x-show="openAktivitas || isLarge"
                                     x-cloak
                                     class="flex flex-col gap-3 max-h-105 overflow-y-auto pr-1.5 custom-scrollbar lg:flex!">
                                    @foreach($proyek->aktivitasProyek as $akt)
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
                                                pm: '{{ addslashes($proyek->ketuaProyek->nama ?? "Belum Ditunjuk") }}',
                                                progress: '{{ $progressValue }}',
                                                status: '{{ $statusLabel }}',
                                                tglMulai: '{{ $tglMulai ?? "Belum diatur" }}',
                                                tglSelesai: '{{ $tglSelesai ?? "Belum diatur" }}',
                                                kendalaInternal: @js($akt->kendala_internal ?? []),
                                                kendalaEksternal: @js($akt->kendala_eksternal ?? []),
                                                riwayatProgress: @js($akt->riwayat_progress ?? []),
                                                dokumen: @js($formattedDocs)
                                            })"
                                            class="bg-gray-50/60 rounded-2xl p-4 border border-purple-100 hover:border-[#6E5BC3]/40 hover:bg-purple-50/20 transition-all flex flex-col gap-3 cursor-pointer group">
                                            
                                            {{-- Baris 1: Label Aktivitas & Status Badge --}}
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-bold uppercase bg-purple-50 text-[#6E5BC3]">
                                                    Aktivitas
                                                </span>
                                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase border shrink-0 {{ $badgeClass }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>

                                            {{-- Baris 2: Judul Aktivitas --}}
                                            <div>
                                                <h4 class="text-[11px] font-normal text-gray-900 leading-snug wrap-break-word">
                                                    {{ $akt->nama_aktivitas }}
                                                </h4>
                                            </div>

                                            {{-- Baris 3: Anggota Terlibat (Penanggung Jawab Aktivitas) --}}
                                            <div class="flex items-center gap-2 text-[11px] text-gray-700">
                                                <span class="w-5 h-5 rounded-full bg-purple-100 border border-purple-200 text-[#6E5BC3] text-[9px] font-bold flex items-center justify-center shrink-0 shadow-2xs" title="Penanggung Jawab: {{ $pjNama }}">
                                                    {{ $pjInisial }}
                                                </span>
                                                <span class="font-normal text-gray-700 truncate" title="{{ $pjNama }}">{{ $pjNama }}</span>
                                            </div>
                                            
                                            {{-- Baris 4: Progress Bar --}}
                                            <div class="flex flex-col gap-1.5 pt-0.5">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="text-gray-500 font-medium">Progress</span>
                                                    <span class="font-normal text-gray-700">{{ $progressValue }}%</span>
                                                </div>
                                                <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                                    <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($progressValue ?? 0))) }}%;"></div>
                                                </div>
                                            </div>

                                            {{-- Baris 5: Tanggal Kalender --}}
                                            <div class="flex items-center gap-1.5 text-[10px] text-gray-400 font-light pt-2 border-t border-purple-100/60 mt-0.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="truncate">{{ $rentangTanggal }}</span>
                                            </div>

                                            {{-- Baris 6: Tombol Aksi (Lihat Detail & Lapor Progress) --}}
                                            <div class="grid grid-cols-2 gap-2 pt-0.5">
                                                <button type="button" 
                                                    @click.stop="$dispatch('open-modal-detail-aktivitas', {
                                                        nama: '{{ addslashes($akt->nama_aktivitas) }}',
                                                        pj: '{{ addslashes($pjNama) }}',
                                                        pm: '{{ addslashes($proyek->ketuaProyek->nama ?? "-") }}',
                                                        progress: '{{ $progressValue }}',
                                                        status: '{{ $statusLabel }}',
                                                        tglMulai: '{{ $tglMulai ?? "-" }}',
                                                        tglSelesai: '{{ $tglSelesai ?? "-" }}',
                                                        kendalaInternal: @js($akt->kendala_internal ?? []),
                                                        kendalaEksternal: @js($akt->kendala_eksternal ?? []),
                                                        riwayatProgress: @js($akt->riwayat_progress ?? []),
                                                        dokumen: @js($formattedDocs)
                                                    })"
                                                    class="w-full py-1.5 px-2 rounded-xl bg-white hover:bg-purple-50 border border-purple-200 text-[#6E5BC3] transition-all text-[10px] font-semibold cursor-pointer shadow-2xs flex items-center justify-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    <span class="truncate">Lihat Detail</span>
                                                </button>
                                                <button type="button" 
                                                    @click.stop="$dispatch('open-modal-lapor-progress', { id: '{{ $akt->id_aktivitas }}', nama: '{{ addslashes($akt->nama_aktivitas) }}', progress: '{{ $progressValue }}' })" 
                                                    class="w-full py-1.5 px-2 rounded-xl bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] transition-all text-[10px] font-semibold cursor-pointer shadow-xs shadow-[#6E5BC3]/20 flex items-center justify-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    <span class="truncate">Lapor Progress</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Footer Card: Total Aktivitas --}}
                                <div class="pt-3 border-t border-purple-100/50 flex items-center justify-between">
                                    <span class="text-[11px] text-gray-400 font-light">Total Aktivitas: {{ $totalAktProyek }}</span>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- DOT PAGINATION DENGAN INDIKATOR PILL & BULAT --}}
                @if ($proyekTerlibat->hasPages())
                    <div class="flex items-center justify-center gap-2 sm:gap-2.5 mt-8 mb-4">
                        @foreach ($proyekTerlibat->getUrlRange(1, $proyekTerlibat->lastPage()) as $page => $url)
                            @if ($page == $proyekTerlibat->currentPage())
                                <span class="h-2.5 w-8 sm:w-10 bg-[#6E5BC3] rounded-full transition-all duration-300 shadow-xs cursor-default" title="Halaman {{ $page }}" aria-current="page"></span>
                            @else
                                <a href="{{ $url }}" 
                                   @click.prevent="fetchAktivitas('{{ $url }}')"
                                   class="h-2.5 w-2.5 bg-[#6E5BC3]/25 hover:bg-[#6E5BC3]/60 rounded-full transition-all duration-300 cursor-pointer" 
                                   title="Ke Halaman {{ $page }}" 
                                   aria-label="Ke Halaman {{ $page }}"></a>
                            @endif
                        @endforeach
                    </div>
                @endif
            @else
                {{-- TAMPILAN EMPTY STATE KETIKA HASIL FILTER KOSONG --}}
                <div class="col-span-1 lg:col-span-3 w-full">
                    <x-emptystate 
                        title="Tidak Ada Aktivitas Ditemukan" 
                        message="Tidak ada aktivitas yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                    />
                </div>
            @endif
        </div>

    </div>

    {{-- Muat Modal Lapor Progress & Modal Detail Aktivitas --}}
    @include('anggota.modals.laporprogress')
    @include('anggota.detailaktivitas')
</x-layoututama>