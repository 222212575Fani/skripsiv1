<x-layoututama title="Proyek">
    <div x-data="{ 
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        tahun: '{{ request('tahun', 'semua') }}',
        bulan: '{{ request('bulan', 'semua') }}',
        filterOpen: false,
        bulanOpen: false,
        fetchProjects() {
            let url = `{{ route('anggota.proyekaktivitas') }}?search=${encodeURIComponent(this.search)}&status=${this.status}&tahun=${this.tahun}&bulan=${this.bulan}`;
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newWrapper = doc.getElementById('project-results-wrapper').innerHTML;
                
                document.getElementById('project-results-wrapper').innerHTML = newWrapper;
                window.history.pushState({}, '', url);
            })
            .catch(error => console.error('Error:', error));
        }
    }" class="flex flex-col gap-6">

        {{-- CONTAINER BANNER UNGU UTAMA (SERASI DENGAN DASHBOARD DIREKTUR) --}}
        <div class="bg-gradient-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white">
            <h1 class="text-base font-bold">
                Halo, {{ auth()->user()->nama ?? auth()->user()->name }}! 👋
            </h1>
            <p class="text-xs text-purple-100 mt-1">
                Lihat dan kelola seluruh proyekmu dengan mudah di sini.
            </p>
        </div>

        {{-- 1. CARD STATISTIK --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 px-2">
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $totalProyek ?? 0 }}" 
                subtitle="Total Proyek Anda" 
                color="text-indigo-600" 
                bg="bg-indigo-50"
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
            />

            <x-cardstatistikdashboard 
                title="Belum Dimulai" 
                value="{{ $proyekBelumDimulai ?? 0 }}" 
                subtitle="Menunggu Jadwal Mulai" 
                color="text-amber-600" 
                bg="bg-amber-50"
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Berjalan" 
                value="{{ $proyekBerjalan ?? 0 }}" 
                subtitle="Aktif Dikerjakan" 
                color="text-blue-600" 
                bg="bg-blue-50"
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" 
            />

            <x-cardstatistikdashboard 
                title="Proyek Selesai" 
                value="{{ $proyekSelesai ?? 0 }}" 
                subtitle="Tuntas Dikerjakan" 
                color="text-emerald-600" 
                bg="bg-emerald-50"
                svgPath="M5 13l4 4L19 7" 
            />

            <x-cardstatistikdashboard 
                title="Terlambat" 
                value="{{ $proyekTerlambat ?? 0 }}" 
                subtitle="Melebihi Deadline" 
                color="text-rose-600" 
                bg="bg-rose-50"
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />
        </div>

        {{-- 2. KOTAK UTAMA DENGAN FILTER & PENCARIAN --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-5 flex flex-col gap-3">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        @if(request('status') == 'belum_dimulai') Proyek yang Saya Ketuai (Belum Dimulai)
                        @elseif(request('status') == 'berjalan') Proyek yang Saya Ketuai (Sedang Berjalan)
                        @elseif(request('status') == 'selesai') Proyek yang Saya Ketuai (Selesai)
                        @elseif(request('status') == 'terlambat') Proyek yang Saya Ketuai (Terlambat)
                        @else Daftar Proyek yang Saya Ketuai
                        @endif
                    </h2>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    {{-- Input Search --}}
                    <div class="relative flex-1 md:w-72 group/search">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#6E5BC3] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        
                        <input type="text" x-model="search" @input.debounce.400ms="fetchProjects()" placeholder="Cari proyek..." 
                            class="w-full pl-10 pr-9 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-2xl text-xs font-medium text-[#6E5BC3] placeholder-[#6E5BC3] focus:outline-none focus:border-[#6E5BC3] transition-all">

                        <template x-if="search">
                            <button @click="search = ''; fetchProjects();" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6E5BC3] hover:text-[#5C4AB5]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                    </div>

                    {{-- Filter Bulan & Tahun --}}
                    <div class="relative group/bulan">
                        <button @click="bulanOpen = !bulanOpen; filterOpen = false;" @click.outside="bulanOpen = false" type="button" 
                            class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] text-[#6E5BC3] rounded-2xl text-xs font-medium transition-all cursor-pointer min-w-[170px]">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-text="
                                    (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][bulan - 1]) + 
                                    (tahun === 'semua' ? '' : ' ' + tahun)
                                "></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="bulanOpen" x-cloak class="absolute left-0 mt-2 w-80 bg-white border border-purple-100 rounded-[28px] shadow-xl p-4 z-50 space-y-4">
                            
                            <div class="flex flex-col gap-1.5">
                                <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                                <div class="grid grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1">
                                    <button @click="tahun = 'semua'; fetchProjects();" 
                                        :class="tahun === 'semua' ? 'bg-[#6E5BC3] text-white' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 hover:text-[#6E5BC3]'"
                                        class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center col-span-5">
                                        Semua Tahun
                                    </button>
                                    @for($i = date('Y'); $i >= 1990; $i--)
                                        <button @click="tahun = '{{ $i }}'; fetchProjects();" 
                                            :class="tahun === '{{ $i }}' ? 'bg-[#6E5BC3] text-white' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 hover:text-[#6E5BC3]'"
                                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest border-t border-gray-100 pt-3">PILIH BULAN</div>
                            
                            <div>
                                <button @click="bulan = 'semua'; bulanOpen = false; fetchProjects();" 
                                    :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 hover:text-[#6E5BC3]'"
                                    class="w-full py-2 px-4 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center mb-2">
                                    Semua Bulan
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                                    <button @click="bulan = (index + 1).toString(); bulanOpen = false; fetchProjects();" 
                                        :class="bulan === (index + 1).toString() ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100 hover:text-[#6E5BC3]'"
                                        class="py-2.5 px-2 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center"
                                        x-text="namaBulan">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Filter Status --}}
                    <div class="relative group/filter">
                        <button @click="filterOpen = !filterOpen; bulanOpen = false;" @click.outside="filterOpen = false" type="button" 
                            class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] text-[#6E5BC3] rounded-2xl text-xs font-medium transition-all cursor-pointer min-w-[160px]">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                                </svg>
                                <span>
                                    @if(request('status') == 'belum_dimulai') Belum Dimulai
                                    @elseif(request('status') == 'berjalan') Sedang Berjalan
                                    @elseif(request('status') == 'selesai') Selesai
                                    @elseif(request('status') == 'terlambat') Terlambat
                                    @else Semua Status
                                    @endif
                                </span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="filterOpen" x-cloak 
                            class="absolute right-0 mt-2 w-56 bg-white border border-purple-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1">
                            <button @click="status = 'semua'; filterOpen = false; fetchProjects();" class="w-full text-left px-3 py-2 rounded-xl text-xs text-[#6E5BC3] hover:bg-purple-50 hover:text-[#6E5BC3]">Semua Status</button>
                            <button @click="status = 'belum_dimulai'; filterOpen = false; fetchProjects();" class="w-full text-left px-3 py-2 rounded-xl text-xs text-[#6E5BC3] hover:bg-purple-50 hover:text-[#6E5BC3]">Belum Dimulai</button>
                            <button @click="status = 'berjalan'; filterOpen = false; fetchProjects();" class="w-full text-left px-3 py-2 rounded-xl text-xs text-[#6E5BC3] hover:bg-purple-50 hover:text-[#6E5BC3]">Sedang Berjalan</button>
                            <button @click="status = 'selesai'; filterOpen = false; fetchProjects();" class="w-full text-left px-3 py-2 rounded-xl text-xs text-[#6E5BC3] hover:bg-purple-50 hover:text-[#6E5BC3]">Selesai</button>
                            <button @click="status = 'terlambat'; filterOpen = false; fetchProjects();" class="w-full text-left px-3 py-2 rounded-xl text-xs text-[#6E5BC3] hover:bg-purple-50 hover:text-[#6E5BC3]">Terlambat</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PEMBUNGKUS HASIL PROYEK --}}
            <div id="project-results-wrapper" class="flex flex-col gap-3 mt-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($semuaProyek ?? [] as $p)
                        <x-carddashboard :proyek="$p" />
                    @empty
                        <div class="col-span-2 py-16 text-center text-gray-400 text-xs font-normal bg-gray-50/30 rounded-2xl border border-dashed border-gray-200">
                            Tidak ada data proyek yang ditemukan sesuai filter/pencarian.
                        </div>
                    @endforelse
                </div>

                {{-- FOOTER: PAGINASI BERSIH TANPA KOTAK ABU-ABU --}}
                @if(isset($semuaProyek) && $semuaProyek->count() > 0)
                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-500">
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
                                    <a href="{{ $url }}" class="w-7 h-7 rounded-full hover:bg-purple-50 text-gray-600 flex items-center justify-center transition-all">
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

    </div>
</x-layoututama>