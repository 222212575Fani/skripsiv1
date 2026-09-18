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

        {{-- CONTAINER BANNER UNGU UTAMA --}}
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white">
            <h1 class="text-base font-bold">
                Halo, {{ auth()->user()->nama ?? auth()->user()->name }}! 👋
            </h1>
            <p class="text-xs text-purple-100 mt-1">
                Lihat dan kelola seluruh proyekmu dengan mudah di sini.
            </p>
        </div>

        {{-- 1. CARD STATISTIK (MEMANJANG DAN BERJEJER KE BAWAH SATU PER SATU) --}}
        <div class="grid grid-cols-1 gap-3 sm:gap-3.5 xl:gap-4">
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
        </div>

        {{-- 2. KOTAK UTAMA DENGAN FILTER & PENCARIAN --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-6 flex flex-col gap-5">
            
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900" x-text="
                        status === 'belum_dimulai' ? 'Proyek yang Saya Ketuai (Belum Dimulai)' :
                        (status === 'berjalan' ? 'Proyek yang Saya Ketuai (Sedang Berjalan)' :
                        (status === 'selesai' ? 'Proyek yang Saya Ketuai (Selesai)' :
                        (status === 'terlambat' ? 'Proyek yang Saya Ketuai (Terlambat)' : 'Daftar Proyek yang Saya Ketuai')))
                    ">
                        @if(request('status') == 'belum_dimulai') Proyek yang Saya Ketuai (Belum Dimulai)
                        @elseif(request('status') == 'berjalan') Proyek yang Saya Ketuai (Sedang Berjalan)
                        @elseif(request('status') == 'selesai') Proyek yang Saya Ketuai (Selesai)
                        @elseif(request('status') == 'terlambat') Proyek yang Saya Ketuai (Terlambat)
                        @else Daftar Proyek yang Saya Ketuai
                        @endif
                    </h2>
                </div>

                {{-- FILTER BULAN, FILTER STATUS, DAN PENCARIAN PROYEK BERJEJER 3 KE BAWAH --}}
                <div class="flex flex-col gap-2.5 w-full">
                    {{-- 1. Filter Bulan & Tahun --}}
                    <div class="relative w-full">
                        <button @click="bulanOpen = !bulanOpen; filterOpen = false;" @click.outside="bulanOpen = false" type="button" 
                            class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 text-[#6E5BC3] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate" x-text="
                                    (bulan === 'semua' ? 'Semua Bulan' : ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][parseInt(bulan) - 1]) + 
                                    (tahun === 'semua' ? '' : ' ' + tahun)
                                "></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="bulanOpen" x-cloak class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-3xl shadow-xl p-4 z-50 space-y-3.5">
                            {{-- PILIH TAHUN --}}
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
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-1.5">
                                    <button type="button" @click="bulan = 'semua'; bulanOpen = false; fetchProjects();" 
                                        :class="bulan === 'semua' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                        class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-6 py-1.5 px-3 rounded-xl text-xs transition-all cursor-pointer text-center">
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

                    {{-- 2. Filter Status --}}
                    <div class="relative w-full">
                        <button @click="filterOpen = !filterOpen; bulanOpen = false;" @click.outside="filterOpen = false" type="button" 
                            class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 text-[#6E5BC3] rounded-full text-xs font-normal transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                                </svg>
                                <span class="truncate" x-text="
                                    status === 'belum_dimulai' ? 'Belum Dimulai' :
                                    (status === 'berjalan' ? 'Sedang Berjalan' :
                                    (status === 'selesai' ? 'Selesai' :
                                    (status === 'terlambat' ? 'Terlambat' : 'Semua Status')))
                                "></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="filterOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="filterOpen" x-cloak 
                            class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1">
                            <button type="button" @click="status = 'semua'; filterOpen = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                <span>Semua Status</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px]" :class="status === 'semua' ? 'bg-indigo-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $totalProyek ?? 0 }}</span>
                            </button>
                            <button type="button" @click="status = 'belum_dimulai'; filterOpen = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                <span>Belum Dimulai</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px]" :class="status === 'belum_dimulai' ? 'bg-indigo-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $proyekBelumDimulai ?? 0 }}</span>
                            </button>
                            <button type="button" @click="status = 'berjalan'; filterOpen = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === 'berjalan' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                <span>Sedang Berjalan</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px]" :class="status === 'berjalan' ? 'bg-indigo-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $proyekBerjalan ?? 0 }}</span>
                            </button>
                            <button type="button" @click="status = 'selesai'; filterOpen = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === 'selesai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                <span>Selesai</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px]" :class="status === 'selesai' ? 'bg-indigo-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $proyekSelesai ?? 0 }}</span>
                            </button>
                            <button type="button" @click="status = 'terlambat'; filterOpen = false; fetchProjects();" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer"
                                :class="status === 'terlambat' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                <span>Terlambat</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px]" :class="status === 'terlambat' ? 'bg-indigo-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $proyekTerlambat ?? 0 }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- 3. Kolom Pencarian Proyek --}}
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

                    {{-- Reset Filter Button --}}
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
            </div>

            {{-- PEMBUNGKUS HASIL PROYEK --}}
            <div id="project-results-wrapper" class="flex flex-col gap-3 mt-1">
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