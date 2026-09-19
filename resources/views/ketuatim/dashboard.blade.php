<x-layoututama title="Dashboard Ketua Tim">
    <div class="flex flex-col gap-8 pb-10" 
        x-data="{
            search: '{{ request('search') }}',
            status: '{{ request('status', 'semua') }}',
            filterOpen: false,
            hasVisible: true,

            checkMatch(namaProyek, statusProyek) {
                let matchSearch = this.search === '' || namaProyek.toLowerCase().includes(this.search.toLowerCase());
                let matchStatus = this.status === 'semua' || statusProyek === this.status;
                return matchSearch && matchStatus;
            },

            updateVisibility() {
                this.$nextTick(() => {
                    let cards = document.querySelectorAll('.proyek-tim-card-wrapper');
                    if (cards.length === 0) {
                        this.hasVisible = true;
                        return;
                    }
                    let visible = false;
                    cards.forEach(card => {
                        if (card.style.display !== 'none') visible = true;
                    });
                    this.hasVisible = visible;
                });
            }
        }"
        x-init="$watch('search', () => updateVisibility()); $watch('status', () => updateVisibility()); updateVisibility();">
        
        {{-- SECTION 1: KARTU MEMANJANG UTAMA --}}
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white flex flex-col justify-between items-start gap-2">
            <h1 class="text-base font-bold">
                Halo, {{ auth()->user()->nama ?? auth()->user()->name }}! 👋
            </h1>
            <p class="text-xs text-purple-100 font-normal">
                Selamat Datang di Dashboard Monitoring Proyek <span class="font-semibold text-white">{{ $timKerja->nama_tim ?? 'Tim Kerja' }}</span>
            </p>
        </div>

        {{-- SECTION 2: KARTU STATISTIK (3 MENYAMPING 2 BARIS PADA LAYAR LEBAR, 1 KOLOM PADA LAYAR KECIL) --}}
        @if($timKerja)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-3.5 xl:gap-4">
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $statsTim['total'] ?? $totalProyek }}" 
                percent="{{ $statsTim['total_persen_text'] ?? '+0% bulan ini' }}" 
                percentColor="text-teal-600" 
                trend="{{ $statsTim['total_trend'] ?? 'up' }}" 
                color="text-indigo-600" 
                bg="bg-indigo-50" 
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />

            <x-cardstatistikdashboard 
                title="Proyek Belum Dimulai" 
                value="{{ $statsTim['belum_dimulai'] ?? $belumDimulai }}" 
                percent="{{ $statsTim['belum_dimulai_persen'] ?? '0% dari total' }}" 
                percentColor="text-amber-600" 
                trend="chart" 
                color="text-amber-600" 
                bg="bg-amber-50" 
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

            <x-cardstatistikdashboard 
                title="Proyek Sedang Berjalan" 
                value="{{ $statsTim['berjalan'] ?? $berjalan }}" 
                percent="{{ $statsTim['berjalan_persen'] ?? '0% dari total' }}" 
                percentColor="text-blue-600" 
                trend="chart" 
                color="text-blue-600" 
                bg="bg-blue-50" 
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" />

            <x-cardstatistikdashboard 
                title="Proyek Selesai" 
                value="{{ $statsTim['selesai'] ?? $selesai }}" 
                percent="{{ $statsTim['selesai_persen'] ?? '0% dari total' }}" 
                percentColor="text-emerald-600" 
                trend="check" 
                color="text-emerald-600" 
                bg="bg-emerald-50" 
                svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

            <x-cardstatistikdashboard 
                title="Proyek Terlambat" 
                value="{{ $statsTim['terlambat'] ?? $terlambat }}" 
                percent="{{ $statsTim['terlambat_persen'] ?? '0% dari total' }}" 
                percentColor="{{ ($statsTim['terlambat'] ?? $terlambat) > 0 ? 'text-rose-600' : 'text-emerald-600' }}" 
                trend="{{ ($statsTim['terlambat'] ?? $terlambat) > 0 ? 'alert' : 'check' }}" 
                color="text-rose-600" 
                bg="bg-rose-50" 
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />

            <x-cardstatistikdashboard 
                title="Jumlah Anggota Tim" 
                value="{{ \App\Models\AnggotaTim::where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar')->count() }}" 
                subtitle="Pegawai aktif tergabung" 
                color="text-blue-600" 
                bg="bg-blue-50" 
                svgPath="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </div>
        @endif

        {{-- SECTION 3: SATU KOTAK PUTIH UTUH (FILTER + GRID PROYEK) --}}
        @if($timKerja)
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-8 flex flex-col gap-6">
            
            {{-- Header + Search & Dropdown Filter --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">
                        Daftar Proyek Tim Kerja
                    </h2>
                </div>

                {{-- Controls: Full-Width Panjang Sama pada Layar Kecil (< lg), Sebaris Horizontal pada Layar Besar (lg+) --}}
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 w-full lg:w-auto">
                    {{-- 1. Input Search Proyek --}}
                    <div class="relative w-full lg:w-60">
                        <div class="flex items-center gap-3 px-4 py-2 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-full">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="search" placeholder="Cari proyek..." 
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-gray-800 placeholder:text-[#6E5BC3]/70 w-full p-0 focus:ring-0">
                            <button type="button" x-show="search" @click="search = ''" class="text-[#6E5BC3] hover:text-[#524397] transition-colors shrink-0 cursor-pointer" title="Hapus pencarian">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- 2. Dropdown Filter Status Client-Side --}}
                    <div class="relative w-full lg:w-auto shrink-0">
                        <button @click="filterOpen = !filterOpen" @click.outside="filterOpen = false" type="button" 
                            class="flex items-center justify-between gap-3 px-4 py-2 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-normal text-[#6E5BC3] transition-all cursor-pointer shadow-2xs w-full lg:w-auto">
                            <div class="flex items-center gap-1.5 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate max-w-35" x-text="
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
                            class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-64 bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1.5">
                            
                            <button @click="status = 'semua'; filterOpen = false;" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                :class="status === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                <span>Semua Proyek</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-normal" :class="status === 'semua' ? 'bg-indigo-100/80 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $totalProyek }}</span>
                            </button>
                            <button @click="status = 'belum_dimulai'; filterOpen = false;" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                :class="status === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                <span>Belum Dimulai</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-normal" :class="status === 'belum_dimulai' ? 'bg-indigo-100/80 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $belumDimulai }}</span>
                            </button>
                            <button @click="status = 'berjalan'; filterOpen = false;" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                :class="status === 'berjalan' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                <span>Sedang Berjalan</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-normal" :class="status === 'berjalan' ? 'bg-indigo-100/80 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $berjalan }}</span>
                            </button>
                            <button @click="status = 'selesai'; filterOpen = false;" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                :class="status === 'selesai' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                <span>Selesai</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-normal" :class="status === 'selesai' ? 'bg-indigo-100/80 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $selesai }}</span>
                            </button>
                            <button @click="status = 'terlambat'; filterOpen = false;" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                :class="status === 'terlambat' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                <span>Terlambat</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-normal" :class="status === 'terlambat' ? 'bg-indigo-100/80 text-[#6E5BC3]' : 'bg-gray-100 text-gray-600'">{{ $terlambat }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- 3. Tombol Reset Filter jika ada filter/pencarian yang aktif --}}
                    <button type="button" 
                        x-show="status !== 'semua' || search !== ''" 
                        x-cloak
                        @click="status = 'semua'; search = '';"
                        class="text-xs text-rose-500 hover:text-rose-700 hover:underline flex items-center justify-center lg:justify-start gap-1 transition-all cursor-pointer font-medium shrink-0 py-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>

            {{-- Grid Proyek dengan Client-Side Filtering ala Halaman Direktur --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start mt-4">
                @php
                    $paginatorProyek = $proyekTim ?? $proyeks ?? collect();
                @endphp

                @forelse($paginatorProyek as $p)
                    @php
                        $namaProj = $p->nama_proyek ?? '';
                        $statusProj = $p->status_proyek ?? $p->status ?? 'belum_dimulai';
                    @endphp

                    <div class="h-full proyek-tim-card-wrapper" x-show="checkMatch(@js($namaProj), @js($statusProj))">
                        <x-carddashboardtim :proyek="$p" />
                    </div>
                @empty
                    <div class="col-span-1 lg:col-span-3 w-full">
                        <x-emptystate 
                            title="Tidak Ada Proyek Ditemukan" 
                            message="Tidak ada proyek yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                        />
                    </div>
                @endforelse

                @if($paginatorProyek->isNotEmpty())
                    {{-- Empty state saat filter atau pencarian tidak ada yang cocok --}}
                    <div x-show="!hasVisible" x-cloak class="col-span-1 lg:col-span-3 w-full">
                        <x-emptystate 
                            title="Tidak Ada Proyek Ditemukan" 
                            message="Tidak ada proyek yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                        />
                    </div>
                @endif
            </div>

        </div>
        @endif

        {{-- SECTION 4: CARD MEMBER TIM DENGAN FILTER PERIODE PILL ESTETIK --}}
        <div>
            <x-cardmember 
                title="Daftar Ketua Proyek" 
                :members="$anggotaTim ?? []" 
            />
        </div>

    </div>

    {{-- MEMANGGIL KOMPONEN MODAL DETAIL AKTIVITAS --}}
    @include('anggota.detailaktivitas')
</x-layoututama>