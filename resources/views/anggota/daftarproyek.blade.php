{{--
    =========================================================================
    HALAMAN: DAFTAR PROYEK YANG DIKETUAI
    Route anggota.daftarproyek -> AnggotaProyekController@daftarProyek.
    Tabel proyek yang diketuai pengguna (tampilan kartu pada layar kecil). Tombol Kelola membuka
    halaman kelola aktivitas proyek (anggota.proyek.aktivitas).
    =========================================================================
--}}
<x-layoututama title="Daftar Proyek">
    <div x-data="{ 
        search: '{{ request('search') }}',
        performSearch() {
            const url = new URL(window.location.href);
            if (this.search) { 
                url.searchParams.set('search', this.search); 
            } else { 
                url.searchParams.delete('search'); 
            }
            url.searchParams.delete('page');
            
            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('daftar-proyek-table-wrapper');
                    const targetTable = document.getElementById('daftar-proyek-table-wrapper');
                    if (newTable && targetTable) {
                        targetTable.innerHTML = newTable.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetTable);
                        }
                    }
                    const newTabs = doc.getElementById('proyek-tabs-wrapper');
                    const targetTabs = document.getElementById('proyek-tabs-wrapper');
                    if (newTabs && targetTabs) {
                        targetTabs.innerHTML = newTabs.innerHTML;
                    }
                    const newMobileFilter = doc.getElementById('proyek-mobile-filter');
                    const targetMobileFilter = document.getElementById('proyek-mobile-filter');
                    if (newMobileFilter && targetMobileFilter) {
                        targetMobileFilter.innerHTML = newMobileFilter.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetMobileFilter);
                        }
                    }
                    window.history.pushState({}, '', url.toString());
                })
                .catch(err => console.error('Error fetching search:', err));
        }
    }" class="flex flex-col gap-5 sm:gap-6 w-full">

        @php 
            $currentStatus = request('status', 'semua'); 
            $statuses = [
                'semua'         => 'Semua Proyek', 
                'belum_dimulai' => 'Belum Dimulai', 
                'berjalan'      => 'Sedang Berjalan', 
                'selesai'       => 'Selesai', 
                'terlambat'     => 'Terlambat'
            ];
        @endphp

        {{-- BARIS 1: JUDUL HALAMAN & SUBTITLE --}}
        <div class="px-1">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Daftar Proyek</h1>
            <p class="text-xs sm:text-sm text-gray-500 font-light mt-1">Daftar proyek yang Anda ketuai</p>
        </div>

        {{-- KONTROL DESKTOP (Layar >= xl): Underline Tabs & Kotak Cari --}}
        <div class="hidden xl:flex items-end justify-between gap-4 border-b border-gray-200/80 px-1">
            {{-- Underline Tabs --}}
            <div id="proyek-tabs-wrapper" class="flex items-center gap-6 overflow-x-auto text-xs font-normal scrollbar-none -mb-px">
                @foreach($statuses as $key => $label)
                    <a href="{{ route('anggota.daftarproyek', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                       class="pb-3 flex items-center gap-2 transition-all relative whitespace-nowrap border-b-2 {{ $currentStatus == $key ? 'text-[#604EE6] border-[#604EE6] font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300 font-light' }}">
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-normal {{ $currentStatus == $key ? 'bg-purple-100 text-[#604EE6]' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Kotak Pencarian --}}
            <div class="flex items-center gap-3 pb-2.5">
                <div class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs w-64">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                        x-model="search" 
                        @input.debounce.400ms="performSearch()"
                        placeholder="Cari proyek atau aktivitas..." 
                        autocomplete="off"
                        class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
                    <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- KONTROL MOBILE / TABLET (Layar < xl) --}}
        <div class="flex xl:hidden flex-col gap-3 w-full px-1">
            {{-- Kolom Pencarian --}}
            <div class="w-full flex items-center gap-2 px-3.5 py-2.5 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="search" 
                    @input.debounce.400ms="performSearch()"
                    placeholder="Cari proyek atau aktivitas..." 
                    autocomplete="off"
                    class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
                <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Filter Dropdown Mobile --}}
            <div id="proyek-mobile-filter" class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border rounded-lg text-xs font-normal text-gray-700 transition-all cursor-pointer shadow-2xs focus:outline-none"
                    :class="open ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                    <div class="flex items-center gap-2 truncate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-8.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z" />
                        </svg>
                        <span class="truncate {{ $currentStatus === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-normal' }}">
                            {{ $statuses[$currentStatus] ?? 'Semua Proyek' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-[#604EE6]">
                            {{ $counts[$currentStatus] ?? 0 }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <div x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-2xl shadow-xl p-2 z-50 space-y-1 text-xs">
                    @foreach($statuses as $key => $label)
                        <a href="{{ route('anggota.daftarproyek', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all cursor-pointer {{ $currentStatus == $key ? 'bg-[#604EE6]/10 text-[#604EE6] font-bold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-normal' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus == $key ? 'bg-[#604EE6]' : 'bg-transparent' }}"></span>
                                <span>{{ $label }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#604EE6] text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABEL PROYEK UTUH --}}
        <div id="daftar-proyek-table-wrapper" class="w-full">
            <x-datatable :paginator="$proyeks ?? null" item-name="data proyek" breakpoint="xl" :card="false">
                
                {{-- SLOT HEADER KOLOM TABEL --}}
                <x-slot name="header">
                    <th class="py-3.5 pl-6 pr-2 text-center w-[4%] whitespace-nowrap">No</th>
                    <th class="py-3.5 px-3 text-left w-[28%] whitespace-nowrap">Nama Proyek</th>
                    <th class="py-3.5 px-2 text-center w-[10%] whitespace-nowrap">Jumlah Aktivitas</th>
                    <th class="py-3.5 px-3 text-left w-[16%] whitespace-nowrap">Penanggung Jawab</th>
                    <th class="py-3.5 px-2 text-center w-[11%] whitespace-nowrap">Status</th>
                    <th class="py-3.5 px-2 text-center w-[11%] whitespace-nowrap">Progress</th>
                    <th class="py-3.5 px-2 text-center w-[7%] whitespace-nowrap">Mulai</th>
                    <th class="py-3.5 px-2 text-center w-[7%] whitespace-nowrap">Selesai</th>
                    <th class="py-3.5 pr-6 text-right w-[6%] whitespace-nowrap">Aksi</th>
                </x-slot>

                {{-- SLOT ISI DATA (LOOPING PROYEK) --}}
                @forelse($proyeks ?? [] as $index => $proyek)
                    @php
                        $status = $proyek->status_proyek ?? 'belum_dimulai';
                        $statusConfig = match($status) {
                            'selesai'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
                            'berjalan'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200/60', 'dot' => 'bg-blue-500', 'label' => 'Sedang Berjalan'],
                            'terlambat' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => 'Terlambat'],
                            default     => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200/60', 'dot' => 'bg-amber-500', 'label' => 'Belum Dimulai'] 
                        };
                        $aktivitasList = $proyek->aktivitasProyek ?? collect();
                        $persen = round($proyek->persen_progress ?? 0);
                    @endphp

                    {{-- BARIS TABEL DESKTOP --}}
                    <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-middle hidden xl:table-row border-b border-gray-100 last:border-none">
                        {{-- 1. No --}}
                        <td class="py-4 pl-6 pr-2 text-center text-gray-500 font-light text-xs align-middle whitespace-nowrap">
                            {{ $proyeks->firstItem() + $index }}
                        </td>

                        {{-- 2. Nama Proyek --}}
                        <td class="py-4 px-3 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#604EE6] shrink-0 shadow-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="font-normal text-gray-800 text-xs break-words" title="{{ $proyek->nama_proyek }}">
                                        {{ $proyek->nama_proyek }}
                                    </span>
                                    @if($proyek->timKerja)
                                        <span class="text-[10px] text-gray-400 font-light truncate max-w-[240px]">{{ $proyek->timKerja->nama_tim }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- 3. Jumlah Aktivitas --}}
                        <td class="py-4 px-2 text-center font-normal text-gray-700 text-xs align-middle whitespace-nowrap">
                            {{ $aktivitasList->count() }}
                        </td>

                        {{-- 4. Penanggung Jawab --}}
                        <td class="py-4 px-3 align-middle">
                            @if($proyek->ketuaProyek)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-purple-100 text-[#604EE6] flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($proyek->ketuaProyek->nama, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-xs text-gray-800 font-normal truncate max-w-[150px]">{{ $proyek->ketuaProyek->nama }}</span>
                                        <span class="text-[10px] text-gray-400 font-light">Ketua Proyek</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 font-light italic">Belum Ditunjuk</span>
                            @endif
                        </td>

                        {{-- 5. Status --}}
                        <td class="py-4 px-2 text-center align-middle whitespace-nowrap">
                            <span class="inline-flex items-center justify-center gap-1.5 w-32 px-3 py-1 rounded-full text-[11px] font-normal {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} border {{ $statusConfig['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                <span>{{ $statusConfig['label'] }}</span>
                            </span>
                        </td>

                        {{-- 6. Progress Bar --}}
                        <td class="py-4 px-2 text-center align-middle whitespace-nowrap">
                            <div class="inline-flex items-center gap-2 w-24">
                                <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" style="width: {{ $persen }}%"></div>
                                </div>
                                <span class="text-xs font-light text-gray-700 shrink-0">{{ $persen }}%</span>
                            </div>
                        </td>

                        {{-- 7. Tanggal Mulai --}}
                        <td class="py-4 px-2 text-center text-xs text-gray-600 font-light whitespace-nowrap align-middle">
                            {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                        </td>

                        {{-- 8. Tanggal Selesai --}}
                        <td class="py-4 px-2 text-center text-xs text-gray-600 font-light whitespace-nowrap align-middle">
                            {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}
                        </td>

                        {{-- 9. Aksi (Kelola) --}}
                        <td class="py-4 pr-6 text-right align-middle whitespace-nowrap">
                            <div class="flex justify-end">
                                <a href="{{ route('anggota.proyek.aktivitas', $proyek->id_proyek) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#604EE6] text-white hover:bg-[#503ED8] transition-all text-xs font-medium shadow-2xs shrink-0 cursor-pointer"
                                   title="Kelola Aktivitas Proyek">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Kelola</span>
                                </a>
                            </div>
                        </td>
                    </tr>

                    {{-- TAMPILAN RESPONSIVE CARD (Layar < xl) --}}
                    <tr class="xl:hidden border-b border-gray-100 last:border-none">
                        <td colspan="9" class="p-4 bg-white">
                            <div class="flex flex-col gap-3 p-4 rounded-xl border border-gray-100 bg-[#FAF9FF]/50 shadow-2xs">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-[#604EE6] shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-medium text-gray-900 text-xs truncate">{{ $proyek->nama_proyek }}</span>
                                            @if($proyek->timKerja)
                                                <span class="text-[10px] text-gray-400 font-light truncate">{{ $proyek->timKerja->nama_tim }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center justify-center gap-1.5 min-w-28 whitespace-nowrap px-3 py-0.5 rounded-full text-[10px] font-normal {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} border {{ $statusConfig['border'] }} shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                        <span>{{ $statusConfig['label'] }}</span>
                                    </span>
                                </div>

                                {{-- Jumlah Aktivitas --}}
                                <div class="flex items-center justify-between py-1.5 border-y border-gray-100 text-xs">
                                    <span class="text-[11px] text-gray-500 font-light">Jumlah Aktivitas:</span>
                                    <span class="text-xs font-semibold text-gray-700">{{ $aktivitasList->count() }}</span>
                                </div>

                                {{-- Progress & Tanggal --}}
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 sm:gap-4 text-xs text-gray-600 font-light pt-1">
                                    <div class="flex items-center gap-2 w-full sm:flex-1 sm:max-w-sm">
                                        <span class="text-[10px] text-gray-400 shrink-0">Progress:</span>
                                        <div class="flex-1 min-w-12 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-[#604EE6] h-full rounded-full" style="width: {{ $persen }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-medium text-gray-700 shrink-0">{{ $persen }}%</span>
                                    </div>
                                    <span class="text-[11px] text-gray-500 whitespace-nowrap">
                                        {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d/m/Y') : '-' }} - 
                                        {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->format('d/m/Y') : '-' }}
                                    </span>
                                </div>

                                {{-- Tombol Aksi Kelola --}}
                                <div class="pt-2 flex justify-end">
                                    <a href="{{ route('anggota.proyek.aktivitas', $proyek->id_proyek) }}" 
                                       class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-[#604EE6] text-white hover:bg-[#503ED8] transition-all text-xs font-medium shadow-2xs cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Kelola Aktivitas</span>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-8">
                            <x-emptystate 
                                title="Tidak Ada Proyek Ditemukan" 
                                message="Tidak ada proyek yang sesuai dengan kriteria pencarian atau status yang dipilih." 
                            />
                        </td>
                    </tr>
                @endforelse
            </x-datatable>
        </div>
    </div>
</x-layoututama>