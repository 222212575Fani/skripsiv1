<x-layoututama title="Dashboard Ketua Tim">
    <div class="flex flex-col gap-8 pb-10">
        
        {{-- SECTION 1: KARTU MEMANJANG UTAMA (UNGU) - TANPA BADGE STATUS TIM --}}
        <div class="bg-gradient-to-r from-[#5C46F5] to-[#7864F8] rounded-[24px] shadow-lg shadow-[#5C46F5]/15 p-8 text-white flex flex-col justify-between items-start gap-4">
            <div>
                <span class="px-3.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur-md">
                    Pusat Kendali Tim Kerja
                </span>
                <h1 class="text-2xl lg:text-3xl font-bold mt-3">
                    Dashboard Monitoring {{ $timKerja->nama_tim ?? 'Belum Ada Tim' }}
                </h1>
                <p class="text-xs lg:text-sm text-indigo-100 font-normal mt-1 max-w-2xl text-justify">
                    Selamat datang kembali, <span class="font-semibold text-white">{{ auth()->user()->nama ?? auth()->user()->name }}</span>. Pantau kinerja dan progres seluruh proyek tim Anda di sini.
                </p>
            </div>
        </div>

        {{-- SECTION 2: 6 KARTU STATISTIK --}}
        @if($timKerja)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Card 1: Total Proyek --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-[#5C46F5] mt-2">Total Proyek</p>
                    <p class="text-3xl font-bold text-[#5C46F5] mt-2">{{ $totalProyek }}</p>
                    <p class="text-[11px] font-medium text-[#5C46F5] mt-2">
                        <span>Aktif terdaftar</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-[#5C46F5] shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
            </div>

            {{-- Card 2: Belum Dimulai --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-orange-600 mt-2">Belum Dimulai</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">{{ $belumDimulai }}</p>
                    <p class="text-[11px] font-medium text-orange-600 mt-2">
                        <span>Tahap Perencanaan</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-orange-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            {{-- Card 3: Sedang Berjalan --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-amber-600 mt-2">Sedang Berjalan</p>
                    <p class="text-3xl font-bold text-amber-600 mt-2">{{ $berjalan }}</p>
                    <p class="text-[11px] font-medium text-amber-600 mt-2">
                        <span>Dalam eksekusi</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
            </div>

            {{-- Card 4: Selesai --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 mt-2">Selesai</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $selesai }}</p>
                    <p class="text-[11px] font-medium text-emerald-600 mt-2">
                        <span>Tervalidasi 100%</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>

            {{-- Card 5: Terlambat --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-rose-600 mt-2">Terlambat</p>
                    <p class="text-3xl font-bold text-rose-600 mt-2">{{ $terlambat }}</p>
                    <p class="text-[11px] font-medium text-rose-500 mt-2">
                        <span>Lewat tenggat waktu</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>

            {{-- Card 6: Jumlah Anggota Tim --}}
            <div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-blue-600 mt-2">Jumlah Anggota Tim</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ \App\Models\AnggotaTim::where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar')->count() }}
                    </p>
                    <p class="text-[11px] font-medium text-blue-600 mt-2">
                        <span>Pegawai aktif tergabung</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>

        </div>
        @endif

        {{-- SECTION 3: KOTAK UTAMA DENGAN FILTER & JUDUL DINAMIS --}}
        @if($timKerja)
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-8 flex flex-col gap-6">
            
            {{-- Header, Pencarian, & Filter Dinamis --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        @if(request('status') == 'belum_dimulai') Proyek Belum Dimulai
                        @elseif(request('status') == 'berjalan') Proyek Sedang Berjalan
                        @elseif(request('status') == 'selesai') Proyek Selesai
                        @elseif(request('status') == 'terlambat') Proyek Terlambat
                        @else Semua Proyek
                        @endif
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Menampilkan data proyek {{ $timKerja->nama_tim ?? 'Belum Ada Tim' }}</p>
                </div>

                {{-- Form Pencarian & Filter Status --}}
                <form action="{{ route('ketuatim.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama proyek..." 
                            class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5]">
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                        class="bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5]">
                        <option value="semua" {{ request('status') == 'semua' || !request('status') ? 'selected' : '' }}>Semua Proyek</option>
                        <option value="belum_dimulai" {{ request('status') == 'belum_dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </form>
            </div>

            {{-- DATA DUMMY SEMENTARA --}}
            @php
                $semuaDummyProyek = [
                    ['nama_proyek' => 'E-Commerce Platform BPS', 'status_proyek' => 'belum_dimulai', 'persen_progress' => 0.00, 'tanggal_mulai' => null, 'tanggal_target_selesai' => null, 'tanggal_selesai_aktual' => null, 'color' => 'bg-pink-500'],
                    ['nama_proyek' => 'Mobile Banking App', 'status_proyek' => 'berjalan', 'persen_progress' => 45.00, 'tanggal_mulai' => '2026-02-15', 'tanggal_target_selesai' => '2026-09-15', 'tanggal_selesai_aktual' => null, 'color' => 'bg-amber-500'],
                    ['nama_proyek' => 'Brand Animation Video', 'status_proyek' => 'selesai', 'persen_progress' => 100.00, 'tanggal_mulai' => '2026-02-01', 'tanggal_target_selesai' => '2026-03-08', 'tanggal_selesai_aktual' => '2026-03-05', 'color' => 'bg-emerald-500'],
                    ['nama_proyek' => 'Dashboard Analytics', 'status_proyek' => 'terlambat', 'persen_progress' => 60.00, 'tanggal_mulai' => '2026-01-10', 'tanggal_target_selesai' => '2026-02-10', 'tanggal_selesai_aktual' => null, 'color' => 'bg-rose-500'],
                    ['nama_proyek' => 'Sensus Penduduk Online', 'status_proyek' => 'berjalan', 'persen_progress' => 70.00, 'tanggal_mulai' => '2026-02-20', 'tanggal_target_selesai' => '2026-09-25', 'tanggal_selesai_aktual' => null, 'color' => 'bg-amber-500'],
                    ['nama_proyek' => 'Sistem Keuangan Internal', 'status_proyek' => 'selesai', 'persen_progress' => 100.00, 'tanggal_mulai' => '2026-01-01', 'tanggal_target_selesai' => '2026-03-01', 'tanggal_selesai_aktual' => '2026-03-04', 'color' => 'bg-emerald-500'],
                ];

                $currentStatus = request('status', 'semua');
                $currentSearch = strtolower(request('search', ''));

                $filteredDummy = collect($semuaDummyProyek)->filter(function($item) use ($currentStatus, $currentSearch) {
                    $matchStatus = ($currentStatus == 'semua' || empty($currentStatus) || $item['status_proyek'] == $currentStatus);
                    $matchSearch = (empty($currentSearch) || str_contains(strtolower($item['nama_proyek']), $currentSearch));
                    return $matchStatus && $matchSearch;
                });
            @endphp

            {{-- Grid 6 Card Per Proyek Menggunakan Komponen carddashboard --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                @forelse($filteredDummy as $p)
                    <x-carddashboard :proyek="$p" />
                @empty
                <div class="col-span-2 py-16 text-center text-gray-400 text-xs font-semibold bg-gray-50/30 rounded-2xl border border-dashed border-gray-200">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                    </div>
                    Tidak ada data proyek yang ditemukan sesuai filter/pencarian.
                </div>
                @endforelse
            </div>

            {{-- PAGINATION NEXT & PREV --}}
            <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 font-medium">
                    Menampilkan halaman <span class="font-bold text-gray-800">1</span> dari <span class="font-bold text-gray-800">1</span>
                </p>
                <div class="flex items-center gap-2">
                    <button type="button" disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                        Previous
                    </button>
                    <button type="button" disabled class="px-4 py-2 bg-gray-100 text-gray-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                        Next
                    </button>
                </div>
            </div>

        </div>
        @endif

    </div>
</x-layoututama>