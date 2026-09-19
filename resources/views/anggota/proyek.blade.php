<x-layoututama title="Proyek">
    <div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        tahun: '{{ request('tahun', 'semua') }}',
        bulan: '{{ request('bulan', 'semua') }}',
        filterOpen: false,
        bulanOpen: false,
        statusOpen: false,
        fetchProjects() {
            let url = `{{ route('anggota.proyekaktivitas') }}?search=${encodeURIComponent(this.search)}&status=${this.status}&tahun=${this.tahun}&bulan=${this.bulan}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                
                let newDesktop = doc.getElementById('project-results-desktop');
                let desktopWrapper = document.getElementById('project-results-desktop');
                if (newDesktop && desktopWrapper) {
                    desktopWrapper.innerHTML = newDesktop.innerHTML;
                }

                let newMobile = doc.getElementById('project-results-mobile');
                let mobileWrapper = document.getElementById('project-results-mobile');
                if (newMobile && mobileWrapper) {
                    mobileWrapper.innerHTML = newMobile.innerHTML;
                }

                if (window.Alpine) {
                    if (desktopWrapper) window.Alpine.initTree(desktopWrapper);
                    if (mobileWrapper) window.Alpine.initTree(mobileWrapper);
                }
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }
    }" class="flex flex-col gap-6">

        {{-- CONTAINER BANNER UNGU UTAMA --}}
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white">
            <h1 class="text-base font-bold">
                Halo, {{ auth()->user()->nama ?? auth()->user()->name }}! 👋
            </h1>
            <p class="text-xs text-purple-100 mt-1">
                Lihat dan kelola seluruh proyekmu dengan mudah di sini.
            </p>
        </div>

        {{-- 1. CARD STATISTIK (2 BARIS x 3 KOLOM SAAT LAYAR DIPERLEBAR >= xl, 1 KOLOM SAAT LAYAR DIPERKECIL < xl) --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-3 sm:gap-3.5 xl:gap-4">
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $statsProyek['total'] ?? $totalProyek ?? 0 }}" 
                percent="{{ $statsProyek['total_persen_text'] ?? '+0% bulan ini' }}" 
                percentColor="text-teal-600" 
                trend="{{ $statsProyek['total_trend'] ?? 'up' }}" 
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

        @php
            $currentStatus = request('status', 'semua');
            $statuses = [
                'semua' => 'Semua Status',
                'belum_dimulai' => 'Belum Dimulai',
                'berjalan' => 'Sedang Berjalan',
                'selesai' => 'Selesai',
                'terlambat' => 'Terlambat',
            ];
            $counts = [
                'semua' => $totalProyek ?? 0,
                'belum_dimulai' => $proyekBelumDimulai ?? 0,
                'berjalan' => $proyekBerjalan ?? 0,
                'selesai' => $proyekSelesai ?? 0,
                'terlambat' => $proyekTerlambat ?? 0,
            ];
        @endphp

        {{-- ========================================================================= --}}
        {{-- TAMPILAN DESKTOP (HANYA MUNCUL DI LAYAR >= xl)                             --}}
        {{-- SEMUA KOMPONEN (JUDUL, CARI PROYEK, FILTER BULAN, FILTER STATUS, TABEL)    --}}
        {{-- BERADA DI DALAM SATU CONTAINER PUTIH BESAR SEPERTI DI GAMBAR             --}}
        {{-- ========================================================================= --}}
        <div class="hidden xl:flex flex-col gap-6 w-full bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden p-6">
            
            {{-- BARIS ATAS CONTAINER DESKTOP: JUDUL DI KIRI, 3 FILTER DI KANAN SEJAJAR --}}
            <div class="flex justify-between items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Daftar Proyek yang Saya Ketuai</h1>
                </div>

                <div class="flex items-center gap-3">
                    {{-- 1. Cari Proyek Desktop --}}
                    <div class="flex items-center gap-2.5 px-4 py-2 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-56">
                        <span class="text-[#6E5BC3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="search" @input.debounce.400ms="fetchProjects()" placeholder="Cari proyek..." 
                            class="bg-transparent border-none focus:outline-none text-xs font-normal text-gray-800 placeholder:text-[#6E5BC3]/70 w-full p-0 focus:ring-0">
                        <button type="button" x-show="search" @click="search = ''; fetchProjects();" class="text-[#6E5BC3] hover:text-[#524397] transition-colors shrink-0 cursor-pointer" title="Hapus pencarian">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- 2. Filter Bulan & Tahun Desktop --}}
                    <div class="relative" @click.outside="bulanOpen = false">
                        <button @click="bulanOpen = !bulanOpen; statusOpen = false;" type="button" 
                            class="flex items-center justify-between gap-2.5 px-4 py-2 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 text-[#6E5BC3] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate text-gray-700" x-text="
                                (bulan === 'semua' ? 'Semua Bulan' : ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'][parseInt(bulan) - 1]) + 
                                (tahun === 'semua' ? '' : ' ' + tahun)
                            "></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="bulanOpen" x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-purple-100 rounded-3xl shadow-xl p-4 z-50 space-y-3">
                            <div class="flex flex-col gap-1.5">
                                <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                                <div class="grid grid-cols-4 gap-1.5 max-h-32 overflow-y-auto pr-1 custom-scrollbar">
                                    <button type="button" @click="tahun = 'semua'; fetchProjects();" 
                                        :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white font-bold' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                        class="py-1.5 px-2 rounded-xl text-xs transition-all cursor-pointer text-center col-span-2">
                                        Semua Tahun
                                    </button>
                                    @for($i = (int)date('Y'); $i >= 1990; $i--)
                                        <button type="button" @click="tahun = '{{ $i }}'; fetchProjects();" 
                                            :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white font-bold' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                            class="py-1.5 px-1 rounded-xl text-xs transition-all cursor-pointer text-center">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div class="border-t border-purple-100/60 pt-2.5">
                                <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest mb-1.5">PILIH BULAN</div>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <button type="button" @click="bulan = 'semua'; bulanOpen = false; fetchProjects();" 
                                        :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                        class="col-span-3 py-1.5 px-3 rounded-xl text-xs transition-all cursor-pointer text-center">
                                        Semua Bulan
                                    </button>
                                    <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                        <button type="button" @click="bulan = (index + 1).toString(); bulanOpen = false; fetchProjects();" 
                                            :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                            class="py-1.5 px-1 rounded-xl text-xs transition-all cursor-pointer text-center"
                                            x-text="namaBulan">
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Filter Status Dropdown Desktop --}}
                    <div class="relative" @click.outside="statusOpen = false">
                        <button @click="statusOpen = !statusOpen; bulanOpen = false;" type="button" 
                            class="flex items-center justify-between gap-2.5 px-4 py-2 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 text-[#6E5BC3] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span class="truncate text-gray-700" x-text="
                                status === 'belum_dimulai' ? 'Belum Dimulai' :
                                (status === 'berjalan' ? 'Sedang Berjalan' :
                                (status === 'selesai' ? 'Selesai' :
                                (status === 'terlambat' ? 'Terlambat' : 'Semua Status')))
                            "></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="statusOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="statusOpen" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-purple-100 rounded-2xl shadow-xl p-2 z-50 space-y-1">
                            @foreach($statuses as $key => $label)
                                <button type="button" @click="status = '{{ $key }}'; statusOpen = false; fetchProjects();" 
                                    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs transition-all cursor-pointer"
                                    :class="status === '{{ $key }}' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-bold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="status === '{{ $key }}' ? 'bg-[#6E5BC3]' : 'bg-transparent'"></span>
                                        <span>{{ $label }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="status === '{{ $key }}' ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600'">
                                        {{ $counts[$key] ?? 0 }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Reset Filter Button Desktop --}}
                    <div x-show="status !== 'semua' || search !== '' || bulan !== 'semua' || tahun !== 'semua'" x-cloak>
                        <button type="button" 
                            @click="status = 'semua'; search = ''; bulan = 'semua'; tahun = 'semua'; fetchProjects();"
                            class="text-xs text-rose-500 hover:text-rose-700 hover:underline flex items-center gap-1 transition-all cursor-pointer font-medium whitespace-nowrap px-1" 
                            title="Reset semua filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- KOTAK TABEL DATATABLE DESKTOP & PAGINASI (DI-UPDATE SECARA REAKTIF OLEH AJAX) --}}
            <div id="project-results-desktop" class="w-full flex flex-col gap-6">
                
                {{-- KOTAK INNER TABEL DENGAN BORDER UNGU TIPIS --}}
                <div class="bg-white border border-[#DDD6FE] rounded-[22px] overflow-hidden shadow-xs">
                    <div class="w-full overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-900 text-xs font-semibold border-b border-[#DDD6FE] bg-[#F8F7FF]/50">
                                    <th class="py-3.5 px-4 text-center w-[5%]">No</th>
                                    <th class="py-3.5 px-4 w-[24%]">Nama Proyek</th>
                                    <th class="py-3.5 px-4 w-[24%]">Deskripsi</th>
                                    <th class="py-3.5 px-4 w-[16%]">Rentang Waktu</th>
                                    <th class="py-3.5 px-4 w-[13%]">Progress</th>
                                    <th class="py-3.5 px-4 text-center w-[10%]">Status</th>
                                    <th class="py-3.5 pr-6 text-right w-[8%]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-medium divide-y divide-gray-100">
                                @forelse($semuaProyek ?? [] as $index => $p)
                                @php
                                    $statusProjItem = $p->status_proyek ?? 'belum_dimulai';
                                    $statusClass = match($statusProjItem) {
                                        'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        'berjalan' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                        default => 'bg-amber-50 text-amber-600 border-amber-100' 
                                    };
                                    $statusLabelItem = match($statusProjItem) {
                                        'selesai' => 'Selesai',
                                        'berjalan' => 'Berjalan',
                                        'terlambat' => 'Terlambat',
                                        default => 'Belum Dimulai'
                                    };
                                    $aktList = $p->aktivitasProyek ?? collect();
                                    $progVal = $p->progress ?? ($aktList->count() > 0 ? round($aktList->avg('target')) : 0);
                                    $tglMulaiItem = $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d M Y') : '-';
                                    $tglSelesaiItem = ($p->tenggat_waktu ?? $p->tanggal_target_selesai) ? \Carbon\Carbon::parse($p->tenggat_waktu ?? $p->tanggal_target_selesai)->translatedFormat('d M Y') : '-';
                                @endphp

                                <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top border-b border-gray-100 last:border-none">
                                    <td class="py-4 px-4 text-center text-gray-500 font-bold text-xs">
                                        {{ $semuaProyek->firstItem() + $index }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                </svg>
                                            </div>
                                            <span class="font-bold text-gray-900 text-xs wrap-break-word">{{ $p->nama_proyek }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-xs text-gray-600 leading-relaxed whitespace-normal wrap-break-word font-normal text-justify">
                                        {{ $p->deskripsi_proyek ?? '-' }}
                                    </td>
                                    <td class="py-4 px-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                                        {{ $tglMulaiItem }} s/d {{ $tglSelesaiItem }}
                                    </td>
                                    <td class="py-4 px-4 align-middle">
                                        <div class="flex flex-col gap-1 w-28">
                                            <div class="flex items-center justify-between text-[11px]">
                                                <span class="text-gray-500 font-medium">Progress</span>
                                                <span class="font-bold text-gray-800">{{ $progVal }}%</span>
                                            </div>
                                            <div class="w-full bg-purple-100/60 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#6E5BC3] h-full rounded-full transition-all duration-300" style="width: {{ $progVal }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center align-middle">
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border inline-block whitespace-nowrap {{ $statusClass }}">
                                            {{ $statusLabelItem }}
                                        </span>
                                    </td>
                                    <td class="py-4 pr-6 text-right align-middle">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('anggota.proyek.aktivitas', $p->id_proyek) }}" 
                                               class="px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-[#6E5BC3] text-[#6E5BC3] hover:text-white transition-all text-xs font-semibold flex items-center gap-1.5 shadow-xs whitespace-nowrap cursor-pointer"
                                               title="Kelola Aktivitas Proyek">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>Detail</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-4 text-center">
                                        <x-emptystate 
                                            title="Tidak Ada Proyek Ditemukan" 
                                            message="Tidak ada proyek yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                                        />
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINASI TABEL DESKTOP --}}
                @if(isset($semuaProyek))
                <div class="px-2 pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-500">
                    <div>
                        Menampilkan 
                        <span class="font-bold text-gray-700">{{ $semuaProyek->firstItem() ?? 0 }}</span> 
                        sampai 
                        <span class="font-bold text-gray-700">{{ $semuaProyek->lastItem() ?? 0 }}</span> 
                        dari 
                        <span class="font-bold text-gray-700">{{ $semuaProyek->total() }}</span> 
                        data proyek yang saya ketuai
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Tombol Previous (Bulat Ungu) --}}
                        @if ($semuaProyek->onFirstPage())
                            <span class="w-9 h-9 rounded-full bg-purple-100 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-xs font-bold">
                                &lsaquo;
                            </span>
                        @else
                            <a href="{{ $semuaProyek->previousPageUrl() }}" class="w-9 h-9 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold">
                                &lsaquo;
                            </a>
                        @endif

                        {{-- Nomor Halaman --}}
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                            @foreach ($semuaProyek->getUrlRange(1, max(1, $semuaProyek->lastPage())) as $page => $url)
                                @if ($page == $semuaProyek->currentPage())
                                    <span class="w-7 h-7 rounded-full bg-white text-[#6E5BC3] border border-[#6E5BC3] flex items-center justify-center font-bold shadow-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-7 h-7 rounded-full hover:bg-purple-50 hover:text-[#6E5BC3] text-gray-600 flex items-center justify-center transition-all">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Tombol Next (Bulat Ungu) --}}
                        @if ($semuaProyek->hasMorePages())
                            <a href="{{ $semuaProyek->nextPageUrl() }}" class="w-9 h-9 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold">
                                &rsaquo;
                            </a>
                        @else
                            <span class="w-9 h-9 rounded-full bg-purple-100 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-xs font-bold">
                                &rsaquo;
                            </span>
                        @endif
                    </div>
                </div>
                @endif

            </div>

        </div>

        {{-- ========================================================================= --}}
        {{-- TAMPILAN MOBILE / TABLET (HANYA MUNCUL DI LAYAR < xl)                     --}}
        {{-- ========================================================================= --}}
        <div class="xl:hidden bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
            
            {{-- JUDUL HALAMAN MOBILE --}}
            <div>
                <h1 class="text-xl font-bold text-gray-900">Daftar Proyek</h1>
            </div>

            {{-- KONTROL MOBILE (3 SUSUN BERURUTAN KE BAWAH) --}}
            <div class="flex flex-col gap-3 w-full">
                
                {{-- 1. Filter Bulan & Tahun Mobile --}}
                <div class="relative w-full">
                    <button @click="bulanOpen = !bulanOpen; filterOpen = false;" @click.outside="bulanOpen = false" type="button" 
                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 text-[#6E5BC3] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate text-gray-700" x-text="
                                (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][parseInt(bulan) - 1]) + 
                                (tahun === 'semua' ? '' : ' ' + tahun)
                            "></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="bulanOpen" x-cloak class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-3xl shadow-xl p-4 z-50 space-y-3">
                        <div class="flex flex-col gap-1.5">
                            <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-1.5 max-h-32 overflow-y-auto pr-1 custom-scrollbar">
                                <button type="button" @click="tahun = 'semua'; fetchProjects();" 
                                    :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white font-bold' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                    class="py-1.5 px-2 rounded-xl text-xs transition-all cursor-pointer text-center col-span-2">
                                    Semua Tahun
                                </button>
                                @for($i = (int)date('Y'); $i >= 1990; $i--)
                                    <button type="button" @click="tahun = '{{ $i }}'; fetchProjects();" 
                                        :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white font-bold' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                        class="py-1.5 px-1 rounded-xl text-xs transition-all cursor-pointer text-center">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="border-t border-purple-100/60 pt-2.5">
                            <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest mb-1.5">PILIH BULAN</div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1.5">
                                <button type="button" @click="bulan = 'semua'; bulanOpen = false; fetchProjects();" 
                                    :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                    class="col-span-2 sm:col-span-3 md:col-span-4 py-1.5 px-3 rounded-xl text-xs transition-all cursor-pointer text-center">
                                    Semua Bulan
                                </button>
                                <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                    <button type="button" @click="bulan = (index + 1).toString(); bulanOpen = false; fetchProjects();" 
                                        :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                        class="py-2 px-2 rounded-xl text-xs transition-all cursor-pointer text-center"
                                        x-text="namaBulan">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Filter Status Dropdown Mobile --}}
                <div class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" 
                        type="button" 
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-normal text-[#6E5BC3] transition-all cursor-pointer shadow-2xs">
                        <div class="flex items-center gap-2 truncate">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span class="text-gray-700" x-text="
                                status === 'belum_dimulai' ? 'Belum Dimulai' :
                                (status === 'berjalan' ? 'Sedang Berjalan' :
                                (status === 'selesai' ? 'Selesai' :
                                (status === 'terlambat' ? 'Terlambat' : 'Semua Status')))
                            "></span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1">
                        @foreach($statuses as $key => $label)
                            <button type="button" @click="status = '{{ $key }}'; open = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === '{{ $key }}' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-bold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal'">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="status === '{{ $key }}' ? 'bg-[#6E5BC3]' : 'bg-transparent'"></span>
                                    <span>{{ $label }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="status === '{{ $key }}' ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600'">
                                    {{ $counts[$key] ?? 0 }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Kolom Pencarian Proyek Mobile --}}
                <div class="relative w-full">
                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-full">
                        <span class="text-[#6E5BC3] shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="search" @input.debounce.400ms="fetchProjects()" placeholder="Cari nama proyek..." 
                            class="bg-transparent border-none focus:outline-none text-xs font-normal text-gray-800 placeholder:text-[#6E5BC3]/70 w-full p-0 focus:ring-0">
                        <button type="button" x-show="search" @click="search = ''; fetchProjects();" class="text-[#6E5BC3] hover:text-[#524397] transition-colors shrink-0 cursor-pointer" title="Hapus pencarian">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Reset Filter Button Mobile --}}
                <div x-show="status !== 'semua' || search !== '' || bulan !== 'semua' || tahun !== 'semua'" x-cloak class="flex justify-end pt-0.5">
                    <button type="button" 
                        @click="status = 'semua'; search = ''; bulan = 'semua'; tahun = 'semua'; fetchProjects();"
                        class="text-xs text-rose-500 hover:text-rose-700 hover:underline flex items-center gap-1 transition-all cursor-pointer font-medium py-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>

            {{-- PEMBUNGKUS HASIL PROYEK MOBILE --}}
            <div id="project-results-mobile" class="flex flex-col gap-4">
                <div class="grid grid-cols-1 gap-4 w-full">
                    @forelse($semuaProyek ?? [] as $p)
                        @include('anggota.cardproyek', ['proyek' => $p])
                    @empty
                        <div class="col-span-1 w-full">
                            <x-emptystate 
                                title="Tidak Ada Proyek Ditemukan" 
                                message="Tidak ada proyek yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                            />
                        </div>
                    @endforelse
                </div>

                {{-- FOOTER PAGINASI MOBILE BERSIH & CENTERED --}}
                @if(isset($semuaProyek) && $semuaProyek->count() > 0)
                <div class="mt-2 pt-4 border-t border-purple-100/60 flex flex-col items-center gap-3 text-xs font-medium text-gray-500">
                    <div class="text-center text-[#6E5BC3]">
                        Menampilkan 
                        <span class="font-bold">{{ $semuaProyek->firstItem() ?? 0 }}</span> 
                        sampai 
                        <span class="font-bold">{{ $semuaProyek->lastItem() ?? 0 }}</span> 
                        dari 
                        <span class="font-bold">{{ $semuaProyek->total() }}</span> 
                        data proyek yang saya ketuai
                    </div>

                    <div class="flex items-center justify-center gap-2">
                        {{-- Tombol Previous (Bulat Ungu) --}}
                        @if ($semuaProyek->onFirstPage())
                            <span class="w-8 h-8 rounded-full bg-purple-50 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-2xs font-bold text-sm">
                                &lsaquo;
                            </span>
                        @else
                            <a href="{{ $semuaProyek->previousPageUrl() }}" class="w-8 h-8 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold text-sm">
                                &lsaquo;
                            </a>
                        @endif

                        {{-- Nomor Halaman --}}
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                            @foreach ($semuaProyek->getUrlRange(1, max(1, $semuaProyek->lastPage())) as $page => $url)
                                @if ($page == $semuaProyek->currentPage())
                                    <span class="w-7 h-7 rounded-full bg-[#6E5BC3] text-white flex items-center justify-center font-bold shadow-xs">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-7 h-7 rounded-full hover:bg-purple-50 text-gray-600 flex items-center justify-center transition-all">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Tombol Next (Bulat Ungu) --}}
                        @if ($semuaProyek->hasMorePages())
                            <a href="{{ $semuaProyek->nextPageUrl() }}" class="w-8 h-8 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold text-sm">
                                &rsaquo;
                            </a>
                        @else
                            <span class="w-8 h-8 rounded-full bg-purple-50 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-2xs font-bold text-sm">
                                &rsaquo;
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>

    </div>

    {{-- MEMANGGIL KOMPONEN MODAL DETAIL AKTIVITAS --}}
    @include('anggota.detailaktivitas')
</x-layoututama>