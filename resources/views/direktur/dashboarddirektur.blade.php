<x-layoututama title="Dashboard Monitoring Direktur">
    <div class="flex flex-col gap-6">

        {{-- 1. HEADER CONTAINER UNGU ESTETIK --}}
        <div class="bg-gradient-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white">
            <h2 class="text-base font-bold">Halo, {{ auth()->user()->nama ?? 'Direktur' }}! 👋</h2>
            <p class="text-xs text-purple-100 mt-1">Selamat Datang di Dashboard Monitoring Direktorat Sistem Informasi Statistik</p>
        </div>

        {{-- 2. CARD STATISTIK PROYEK DALAM DIREKTORAT SIS (GLOBAL) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-cardstatistikdashboard title="Total Proyek" value="{{ $statsDirektorat['total'] ?? 0 }}" subtitle="Seluruh Direktorat" color="text-indigo-600" bg="bg-indigo-50" svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            <x-cardstatistikdashboard title="Belum Dimulai" value="{{ $statsDirektorat['belum_dimulai'] ?? 0 }}" subtitle="Menunggu Jadwal" color="text-amber-600" bg="bg-amber-50" svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            <x-cardstatistikdashboard title="Berjalan" value="{{ $statsDirektorat['berjalan'] ?? 0 }}" subtitle="Aktif Dikerjakan" color="text-blue-600" bg="bg-blue-50" svgPath="M13 10V3L4 14h7v7l9-11h-7z" />
            <x-cardstatistikdashboard title="Selesai" value="{{ $statsDirektorat['selesai'] ?? 0 }}" subtitle="Tuntas Dikerjakan" color="text-emerald-600" bg="bg-emerald-50" svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            <x-cardstatistikdashboard title="Terlambat" value="{{ $statsDirektorat['terlambat'] ?? 0 }}" subtitle="Melebihi Deadline" color="text-rose-600" bg="bg-rose-50" svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </div>

        {{-- 3. KOTAK UTAMA (CARD PUTIH BESAR) UNTUK DAFTAR PROYEK DARI DATABASE --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-6 flex flex-col gap-6"
             x-data="{ 
                 searchProyek: '', 
                 statusFilter: 'semua', 
                 statusFilterName: 'Semua Status',
                 openStatusDropdown: false,
                 timFilter: 'semua', 
                 timFilterName: 'Semua Tim Kerja',
                 openTimDropdown: false 
             }">
            
            {{-- Header + Search & Custom Dropdown Filter --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Daftar Proyek</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Menampilkan data seluruh proyek dari berbagai tim kerja di Direktorat Sistem Informasi Statistik.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    {{-- Input Search Proyek --}}
                    <div class="relative flex-1 lg:w-64">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6E5BC3]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input type="text" x-model="searchProyek" placeholder="Cari proyek..." 
                            class="w-full pl-11 pr-4 py-2.5 bg-white border border-purple-200 hover:border-purple-300 focus:border-[#6E5BC3] rounded-full text-xs font-semibold text-[#6E5BC3] placeholder:text-[#6E5BC3]/60 focus:outline-none transition-all shadow-2xs">
                    </div>

                    {{-- Custom Dropdown Filter Tim Kerja --}}
                    <div class="relative">
                        <button @click="openTimDropdown = !openTimDropdown; openStatusDropdown = false;" @click.outside="openTimDropdown = false" type="button" 
                            class="w-full sm:w-auto flex items-center justify-between gap-6 px-5 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-semibold text-[#6E5BC3] transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span x-text="timFilterName"></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openTimDropdown" x-cloak class="absolute right-0 sm:left-0 mt-2 w-72 bg-white border border-purple-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                            <button type="button" 
                                @click="timFilter = 'semua'; timFilterName = 'Semua Tim Kerja'; openTimDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Semua Tim Kerja
                            </button>
                            @foreach($daftarTim as $t)
                                <button type="button" 
                                    @click="timFilter = '{{ $t->id_tim ?? $t->id }}'; timFilterName = '{{ $t->nama_tim }}'; openTimDropdown = false;"
                                    class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                    {{ $t->nama_tim }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom Dropdown Filter Status Proyek --}}
                    <div class="relative">
                        <button @click="openStatusDropdown = !openStatusDropdown; openTimDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                            class="w-full sm:w-auto flex items-center justify-between gap-6 px-5 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-semibold text-[#6E5BC3] transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span x-text="statusFilterName"></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openStatusDropdown" x-cloak class="absolute right-0 mt-2 w-52 bg-white border border-purple-100 rounded-[24px] shadow-xl p-3 z-50 space-y-1">
                            <button type="button" 
                                @click="statusFilter = 'semua'; statusFilterName = 'Semua Status'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Semua Status
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'belum_dimulai'; statusFilterName = 'Belum Dimulai'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Belum Dimulai
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'berjalan'; statusFilterName = 'Sedang Berjalan'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Sedang Berjalan
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'selesai'; statusFilterName = 'Selesai'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Selesai
                            </button>
                            <button type="button" 
                                @click="statusFilter = 'terlambat'; statusFilterName = 'Terlambat'; openStatusDropdown = false;"
                                class="w-full text-left px-3.5 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-medium transition-all cursor-pointer">
                                Terlambat
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid Card Proyek Dari Database --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pt-2">
                @forelse($semuaProyek ?? collect() as $p)
                    @php
                        $namaProj = strtolower($p->nama_proyek ?? '');
                        $statusProj = $p->status_proyek ?? $p->status ?? 'belum_dimulai';
                        $idTimProj = $p->id_tim ?? $p->tim_kerja_id ?? ($p->timKerja->id_tim ?? $p->timKerja->id ?? '');
                    @endphp
                    <div class="h-full"
                        x-show="
                            (searchProyek === '' || '{{ $namaProj }}'.includes(searchProyek.toLowerCase())) &&
                            (statusFilter === 'semua' || '{{ $statusProj }}' === statusFilter) &&
                            (timFilter === 'semua' || String('{{ $idTimProj }}') === String(timFilter))
                        ">
                        @include('direktur.cardproyekdirektur', ['proyek' => $p])
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center bg-gray-50/50 rounded-[24px] border border-dashed border-purple-200">
                        <p class="text-xs text-gray-500 font-normal">Belum ada data proyek yang tersedia dalam direktorat.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- 4. CONTAINER BAWAH: MEMANGGIL KOMPONEN DIAGRAM PROGRESS --}}
        @include('direktur.diagramprogress', [
            'daftarTim' => $daftarTim ?? [],
            'rerataProgressTim' => $rerataProgressTim ?? [],
            'namaTim' => $namaTim ?? []
        ])

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-layoututama>