<x-layoututama title="Dashboard Monitoring Direktur">
    <div class="flex flex-col gap-4 sm:gap-6">

        {{-- 1. HEADER CONTAINER UNGU ESTETIK --}}
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-2xl sm:rounded-[28px] shadow-sm p-4 sm:p-6 text-white">
            <h2 class="text-sm sm:text-base font-bold">Halo, {{ $sapaanWaktu }}, {{ auth()->user()->nama ?? 'Direktur' }}! 👋</h2>
            <p class="text-xs text-purple-100 mt-1">Selamat Datang di Dashboard Monitoring Direktorat Sistem Informasi Statistik</p>
        </div>

        {{-- 2. CARD STATISTIK PROYEK DALAM DIREKTORAT SIS (GLOBAL) --}}
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-3 sm:gap-3.5 xl:gap-4">
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $statsDirektorat['total'] ?? 0 }}" 
                percent="{{ $statsDirektorat['total_persen_text'] ?? 'Total proyek terdaftar' }}" 
                percentColor="text-indigo-600" 
                trend="chart" 
                color="text-indigo-600" 
                bg="bg-indigo-50" 
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />

            <x-cardstatistikdashboard 
                title="Belum Dimulai" 
                value="{{ $statsDirektorat['belum_dimulai'] ?? 0 }}" 
                percent="{{ $statsDirektorat['belum_dimulai_persen'] ?? '0% dari total' }}" 
                percentColor="text-amber-600" 
                trend="chart" 
                color="text-amber-600" 
                bg="bg-amber-50" 
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

            <x-cardstatistikdashboard 
                title="Proyek Berjalan" 
                value="{{ $statsDirektorat['berjalan'] ?? 0 }}" 
                percent="{{ $statsDirektorat['berjalan_persen'] ?? '0% dari total' }}" 
                percentColor="text-blue-600" 
                trend="chart" 
                color="text-blue-600" 
                bg="bg-blue-50" 
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" />

            <x-cardstatistikdashboard 
                title="Proyek Selesai" 
                value="{{ $statsDirektorat['selesai'] ?? 0 }}" 
                percent="{{ $statsDirektorat['selesai_persen'] ?? '0% dari total' }}" 
                percentColor="text-emerald-600" 
                trend="check" 
                color="text-emerald-600" 
                bg="bg-emerald-50" 
                svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />

            <x-cardstatistikdashboard 
                title="Proyek Terlambat" 
                value="{{ $statsDirektorat['terlambat'] ?? 0 }}" 
                percent="{{ $statsDirektorat['terlambat_persen'] ?? '0% dari total' }}" 
                percentColor="{{ ($statsDirektorat['terlambat'] ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}" 
                trend="{{ ($statsDirektorat['terlambat'] ?? 0) > 0 ? 'alert' : 'check' }}" 
                color="text-rose-600" 
                bg="bg-rose-50" 
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </div>

        {{-- 3. KOTAK UTAMA (CARD PUTIH BESAR) UNTUK DAFTAR PROYEK DARI DATABASE --}}
        <div class="bg-white rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col gap-4 sm:gap-6"
             x-data="{ 
                 searchProyek: '', 
                 statusFilter: 'semua', 
                 statusFilterName: 'Semua Status',
                 openStatusDropdown: false,
                 timFilter: 'semua', 
                 timFilterName: 'Semua Tim Kerja',
                 openTimDropdown: false,
                 hasVisible: true,

                 checkMatch(nama, status, idTim) {
                     let matchSearch = this.searchProyek === '' || nama.toLowerCase().includes(this.searchProyek.toLowerCase());
                     let matchStatus = this.statusFilter === 'semua' || status === this.statusFilter;
                     let matchTim = this.timFilter === 'semua' || String(idTim) === String(this.timFilter);
                     return matchSearch && matchStatus && matchTim;
                 },

                 updateVisibility() {
                     this.$nextTick(() => {
                         let cards = document.querySelectorAll('.proyek-card-wrapper');
                         if (cards.length === 0) {
                             this.hasVisible = true;
                             return;
                         }
                         let visibleCount = 0;
                         cards.forEach(card => {
                             if (card.style.display !== 'none') {
                                 visibleCount++;
                             }
                         });
                         this.hasVisible = visibleCount > 0;
                     });
                 }
             }"
             x-init="$watch('searchProyek', () => updateVisibility()); $watch('statusFilter', () => updateVisibility()); $watch('timFilter', () => updateVisibility());">
            
            {{-- Header + Search & Custom Dropdown Filter --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Proyek</h3>
                </div>

                {{-- Controls: 3 Baris Full-Width Panjang Sama pada Layar Kecil (< lg), Sebaris Horizontal pada Layar Besar (lg+) --}}
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 w-full lg:w-auto">
                    {{-- 1. Input Search Proyek --}}
                    <div class="relative w-full lg:w-60">
                        <div class="flex items-center gap-3 px-4 py-2 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 rounded-full text-xs font-normal text-[#604EE6] transition-all shadow-2xs w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" x-model="searchProyek" placeholder="Cari proyek..." 
                                class="bg-transparent border-none focus:outline-none text-xs font-light text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0 focus:ring-0">
                            <button type="button" x-show="searchProyek" @click="searchProyek = ''" class="text-[#604EE6] hover:text-[#524397] transition-colors shrink-0 cursor-pointer" title="Hapus pencarian">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- 2. Custom Dropdown Filter Tim Kerja --}}
                    <div class="relative w-full lg:w-auto shrink-0">
                        <button @click="openTimDropdown = !openTimDropdown; openStatusDropdown = false;" @click.outside="openTimDropdown = false" type="button" 
                            class="flex items-center justify-between gap-3 px-4 py-2 bg-white border rounded-full text-xs font-light text-[#604EE6] transition-all cursor-pointer shadow-2xs w-full lg:w-auto focus:outline-none"
                            :class="openTimDropdown ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                            <div class="flex items-center gap-1.5 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="truncate max-w-[200px]" :class="timFilter === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="timFilterName"></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openTimDropdown" x-cloak class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-72 bg-white border border-purple-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1 max-h-56 overflow-y-auto custom-scrollbar">
                            <button type="button" 
                                @click="timFilter = 'semua'; timFilterName = 'Semua Tim Kerja'; openTimDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="timFilter === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Semua Tim Kerja</span>
                                <span x-show="timFilter === 'semua'" class="text-[#6E5BC3]">✓</span>
                            </button>
                            @foreach($daftarTim as $t)
                                <button type="button" 
                                    @click="timFilter = '{{ $t->id_tim ?? $t->id }}'; timFilterName = '{{ $t->nama_tim }}'; openTimDropdown = false;"
                                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                    :class="timFilter === '{{ $t->id_tim ?? $t->id }}' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                    <span class="truncate">{{ $t->nama_tim }}</span>
                                    <span x-show="timFilter === '{{ $t->id_tim ?? $t->id }}'" class="text-[#6E5BC3]">✓</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. Custom Dropdown Filter Status Proyek --}}
                    <div class="relative w-full lg:w-auto shrink-0">
                        <button @click="openStatusDropdown = !openStatusDropdown; openTimDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                            class="flex items-center justify-between gap-3 px-4 py-2 bg-white border rounded-full text-xs font-light text-[#604EE6] transition-all cursor-pointer shadow-2xs w-full lg:w-auto focus:outline-none"
                            :class="openStatusDropdown ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                            <div class="flex items-center gap-1.5 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="truncate max-w-[140px]" :class="statusFilter === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light'" x-text="statusFilterName"></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openStatusDropdown" x-cloak class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-56 bg-white border border-purple-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1">
                            <button type="button" 
                                @click="statusFilter = 'semua'; statusFilterName = 'Semua Status'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="statusFilter === 'semua' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Semua Status</span>
                                <span x-show="statusFilter === 'semua'" class="text-[#6E5BC3]">✓</span>
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'belum_dimulai'; statusFilterName = 'Belum Dimulai'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="statusFilter === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Belum Dimulai</span>
                                <span x-show="statusFilter === 'belum_dimulai'" class="text-[#6E5BC3]">✓</span>
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'berjalan'; statusFilterName = 'Sedang Berjalan'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="statusFilter === 'berjalan' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Sedang Berjalan</span>
                                <span x-show="statusFilter === 'berjalan'" class="text-[#6E5BC3]">✓</span>
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'selesai'; statusFilterName = 'Selesai'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="statusFilter === 'selesai' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Selesai</span>
                                <span x-show="statusFilter === 'selesai'" class="text-[#6E5BC3]">✓</span>
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'terlambat'; statusFilterName = 'Terlambat'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                                :class="statusFilter === 'terlambat' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                <span>Terlambat</span>
                                <span x-show="statusFilter === 'terlambat'" class="text-[#6E5BC3]">✓</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid Card Proyek Dari Database (1 Kolom Horizontal saat Perkecil, 3 Kolom saat Normal) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-2">
                @forelse($semuaProyek ?? collect() as $p)
                    @php
                        $namaProj = $p->nama_proyek ?? '';
                        $statusProj = $p->status_proyek ?? $p->status ?? 'belum_dimulai';
                        $idTimProj = $p->id_tim ?? $p->tim_kerja_id ?? ($p->timKerja->id_tim ?? $p->timKerja->id ?? '');
                    @endphp
                    
                    <div class="h-full proyek-card-wrapper"
                        x-show="checkMatch(@js($namaProj), @js($statusProj), @js($idTimProj))">
                        @include('direktur.cardproyekdirektur', ['proyek' => $p])
                    </div>
                @empty
                    <div class="col-span-1 lg:col-span-3 w-full">
                        <x-emptystate 
                            title="Tidak Ada Proyek Ditemukan" 
                            message="Belum ada data proyek yang tersedia dalam direktorat." 
                        />
                    </div>
                @endforelse

                @if(isset($semuaProyek) && count($semuaProyek) > 0)
                    {{-- Empty Message saat filter tidak cocok --}}
                    <div x-show="!hasVisible" 
                         x-cloak 
                         class="col-span-1 lg:col-span-3 w-full">
                        <x-emptystate 
                            title="Tidak Ada Proyek Ditemukan" 
                            message="Tidak ada proyek yang sesuai dengan kata kunci atau filter yang dipilih." 
                        />
                    </div>
                @endif
            </div>

            {{-- DOT PAGINATION (15 Proyek Per Halaman Sesuai Standar) --}}
            @if(isset($semuaProyek) && method_exists($semuaProyek, 'hasPages') && $semuaProyek->hasPages())
                <div class="flex items-center justify-center gap-2 sm:gap-2.5 mt-6 mb-2">
                    @foreach ($semuaProyek->getUrlRange(1, $semuaProyek->lastPage()) as $page => $url)
                        @if ($page == $semuaProyek->currentPage())
                            <span class="h-2.5 w-8 sm:w-10 bg-[#6E5BC3] rounded-full transition-all duration-300 shadow-xs cursor-default" title="Halaman {{ $page }}" aria-current="page"></span>
                        @else
                            <a href="{{ $url }}" class="h-2.5 w-2.5 bg-[#6E5BC3]/25 hover:bg-[#6E5BC3]/60 rounded-full transition-all duration-300 cursor-pointer" title="Ke Halaman {{ $page }}" aria-label="Ke Halaman {{ $page }}"></a>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>

        {{-- 4. CONTAINER BAWAH: MEMANGGIL KOMPONEN DIAGRAM PROGRESS --}}
        @include('direktur.diagramprogress', [
            'daftarTim' => $daftarTim ?? [],
            'rerataProgressTim' => $rerataProgressTim ?? [],
            'namaTim' => $namaTim ?? []
        ])

        {{-- 5. CONTAINER BAWAH: MEMANGGIL KOMPONEN DIAGRAM BEBAN KERJA ANGGOTA TIM --}}
        @include('direktur.diagrambebankerja', [
            'daftarTim' => $daftarTim ?? [],
            'bebanKerjaInitial' => $bebanKerjaInitial ?? []
        ])

    </div>

    {{-- MEMANGGIL KOMPONEN MODAL DETAIL AKTIVITAS DI BAGIAN BAWAH --}}
    @include('anggota.detailaktivitas')

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-layoututama>