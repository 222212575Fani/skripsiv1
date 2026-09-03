<x-layoututama title="Dashboard Ketua Tim">
    <div class="flex flex-col gap-8 pb-10" x-data="{
        search: '{{ request('search') }}',
        status: '{{ request('status', 'semua') }}',
        fetchProjects() {
            let url = `{{ route('ketuatim.dashboard') }}?search=${encodeURIComponent(this.search)}&status=${this.status}`;
            
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
    }">
        
        {{-- SECTION 1: KARTU MEMANJANG UTAMA (UNGU) --}}
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

        {{-- SECTION 2: 6 KARTU STATISTIK MENGGUNAKAN cardstatistikdashboard --}}
        @if($timKerja)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            {{-- Card 1: Total Proyek --}}
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $totalProyek }}" 
                subtitle="Aktif terdaftar" 
                color="text-[#5C46F5]" 
                bg="bg-indigo-50" 
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
            />

            {{-- Card 2: Belum Dimulai --}}
            <x-cardstatistikdashboard 
                title="Belum Dimulai" 
                value="{{ $belumDimulai }}" 
                subtitle="Tahap Perencanaan" 
                color="text-orange-600" 
                bg="bg-orange-100" 
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            {{-- Card 3: Sedang Berjalan --}}
            <x-cardstatistikdashboard 
                title="Sedang Berjalan" 
                value="{{ $berjalan }}" 
                subtitle="Dalam eksekusi" 
                color="text-amber-600" 
                bg="bg-amber-50" 
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" 
            />

            {{-- Card 4: Selesai --}}
            <x-cardstatistikdashboard 
                title="Selesai" 
                value="{{ $selesai }}" 
                subtitle="Tervalidasi 100%" 
                color="text-emerald-600" 
                bg="bg-emerald-50" 
                svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            {{-- Card 5: Terlambat --}}
            <x-cardstatistikdashboard 
                title="Terlambat" 
                value="{{ $terlambat }}" 
                subtitle="Lewat tenggat waktu" 
                color="text-rose-600" 
                bg="bg-rose-50" 
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />

            {{-- Card 6: Jumlah Anggota Tim --}}
            <x-cardstatistikdashboard 
                title="Jumlah Anggota Tim" 
                value="{{ \App\Models\AnggotaTim::where('id_tim', $timKerja->id_tim)->whereNull('tanggal_keluar')->count() }}" 
                subtitle="Pegawai aktif tergabung" 
                color="text-blue-600" 
                bg="bg-blue-50" 
                svgPath="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" 
            />

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
                    <p class="text-xs text-gray-400 mt-0.5">Menampilkan data proyek {{ $timKerja->nama_tim ?? '' }}</p>
                </div>

                {{-- Form Live Search & Filter Status (Tanpa Reload Halaman) --}}
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        
                        {{-- Input Live Search dengan Debounce 400ms --}}
                        <input type="text" x-model="search" @input.debounce.400ms="fetchProjects()" placeholder="Cari nama proyek..." 
                            class="w-full pl-10 pr-9 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5]">

                        {{-- Tombol Clear (X) Jika Ada Teks Pencarian --}}
                        <template x-if="search">
                            <button @click="search = ''; fetchProjects();" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                    </div>

                    {{-- Dropdown Filter Status --}}
                    <select x-model="status" @change="fetchProjects()" 
                        class="bg-gray-50 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5]">
                        <option value="semua">Semua Proyek</option>
                        <option value="belum_dimulai">Belum Dimulai</option>
                        <option value="berjalan">Berjalan</option>
                        <option value="selesai">Selesai</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>
            </div>

            {{-- PEMBUNGKUS HASIL YANG AKAN DI-UPDATE SECARA DINAMIS TANPA FOLDERS PARTIALS --}}
            <div id="project-results-wrapper">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
                    @forelse($proyekTim as $p)
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

                {{-- PAGINATION LINKS --}}
                <div class="mt-6 pt-4 border-t border-gray-100">
                    {{ $proyekTim->links() }}
                </div>
            </div>

        </div>
        @endif

    </div>
</x-layoututama>