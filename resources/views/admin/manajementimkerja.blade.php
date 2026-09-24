<x-layoututama title="Manajemen Tim Kerja">
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
                    const newTable = doc.getElementById('timkerja-table-wrapper');
                    const targetTable = document.getElementById('timkerja-table-wrapper');
                    if (newTable && targetTable) {
                        targetTable.innerHTML = newTable.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetTable);
                        }
                    }
                    const newTabs = doc.getElementById('timkerja-tabs-wrapper');
                    const targetTabs = document.getElementById('timkerja-tabs-wrapper');
                    if (newTabs && targetTabs) {
                        targetTabs.innerHTML = newTabs.innerHTML;
                    }
                    const newMobileFilter = doc.getElementById('timkerja-mobile-filter');
                    const targetMobileFilter = document.getElementById('timkerja-mobile-filter');
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
                'semua' => 'Semua Tim', 
                'aktif' => 'Aktif', 
                'nonaktif' => 'Non-Aktif'
            ];
        @endphp

        {{-- BARIS 1: JUDUL HALAMAN & SUBTITLE --}}
        <div class="px-1">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Daftar Tim Kerja</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data seluruh tim kerja, penanggung jawab, dan status tim.</p>
        </div>

        {{-- KONTROL DESKTOP (Hanya Tampil di Layar >= xl): Underline Tabs & Kotak Cari + Tombol Tambah --}}
        <div class="hidden xl:flex items-center justify-between gap-4 border-b border-gray-200/80 px-1">
            {{-- Underline Tabs --}}
            <div id="timkerja-tabs-wrapper" class="flex items-center gap-6 overflow-x-auto text-xs font-semibold scrollbar-none">
                @foreach($statuses as $key => $label)
                    <a href="{{ route('admin.manajementimkerja', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                       class="pb-3 flex items-center gap-2 transition-all relative whitespace-nowrap {{ $currentStatus == $key ? 'text-[#6E5BC3] border-b-2 border-[#6E5BC3] font-bold' : 'text-gray-500 hover:text-gray-800' }}">
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-purple-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Kotak Pencarian Modern & Tombol Tambah --}}
            <div class="flex items-center gap-3 pb-2.5">
                <div class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs w-60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                        x-model="search"
                        @input.debounce.400ms="performSearch()"
                        placeholder="Cari..." 
                        autocomplete="off"
                        class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light font-light w-full p-0">
                    <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <x-button @click="$dispatch('open-modal-tambah')" class="shrink-0 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Tim Kerja</span>
                </x-button>
            </div>
        </div>

        {{-- KONTROL MOBILE / TABLET (Tampilan saat Layar Diperkecil < xl): Berurutan ke Bawah: 1) Tambah, 2) Pencarian, 3) Filter --}}
        <div class="flex xl:hidden flex-col gap-3 w-full px-1">
            {{-- 1. Tombol Tambah Tim Kerja (Full Width) --}}
            <x-button @click="$dispatch('open-modal-tambah')" class="w-full justify-center py-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Tim Kerja</span>
            </x-button>

            {{-- 2. Kolom Pencarian (Full Width) --}}
            <div class="w-full flex items-center gap-2 px-3.5 py-2.5 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                    x-model="search"
                    @input.debounce.400ms="performSearch()"
                    placeholder="Cari Nama Tim..." 
                    autocomplete="off"
                    class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
                <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- 3. Filter Dropdown (Full Width) --}}
            <div id="timkerja-mobile-filter" class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border rounded-lg text-xs font-normal text-gray-700 transition-all cursor-pointer shadow-2xs focus:outline-none"
                    :class="open ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                    <div class="flex items-center gap-2 truncate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="truncate {{ $currentStatus === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-normal' }}">{{ $statuses[$currentStatus] ?? 'Semua Tim' }}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-[#604EE6]">
                            {{ $counts[$currentStatus] ?? 0 }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Dropdown Popup --}}
                <div x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-2xl shadow-xl p-2 z-50 space-y-1 text-xs">
                    @foreach($statuses as $key => $label)
                        <a href="{{ route('admin.manajementimkerja', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all cursor-pointer {{ $currentStatus == $key ? 'bg-[#6E5BC3]/10 text-[#6E5BC3] font-bold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus == $key ? 'bg-[#6E5BC3]' : 'bg-transparent' }}"></span>
                                <span>{{ $label }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABEL TIM KERJA TUNGGAL (SATU KARTU UTUH DENGAN HEADER & PAGINASI) --}}
        <div id="timkerja-table-wrapper" class="w-full">
            <x-datatable :paginator="$timKerja"  item-name="data tim kerja" :card="false">

                        {{-- SLOT HEADER KOLOM --}}
                        <x-slot name="header">
                            <th class="py-3.5 px-4 text-center w-[6%]">No</th>
                            <th class="py-3.5 px-4 w-[22%]">Nama Tim</th>
                            <th class="py-3.5 px-4 w-[32%]">Deskripsi Tim Kerja</th>
                            <th class="py-3.5 px-4 w-[20%]">Ketua Tim</th>
                            <th class="py-3.5 px-4 text-center w-[10%]">Status</th>
                            <th class="py-3.5 pr-6 text-right w-[10%]">Aksi</th>
                        </x-slot>

                        {{-- SLOT ISI DATA (LOOPING) --}}
                        @forelse($timKerja as $index => $tim)
                        @php
                            $statusConfig = $tim->status_tim == 'aktif' 
                                ? ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Aktif']
                                : ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => 'Non-Aktif'];
                        @endphp

                        <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-middle hidden xl:table-row border-b border-gray-100 last:border-none">
                            <td class="py-4 px-4 text-center text-gray-500 font-light text-xs align-middle">
                                {{ $timKerja->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <span class="font-light text-gray-800 text-xs wrap-break-word">{{ $tim->nama_tim }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600 leading-relaxed whitespace-normal wrap-break-word font-light text-justify align-middle">
                                @if(!empty(trim($tim->deskripsi_tim ?? '')))
                                    {{ $tim->deskripsi_tim }}
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                        <span>Deskripsi belum diinputkan</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 align-middle">
                                @if($tim->ketua)
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-light text-gray-800 text-xs">{{ $tim->ketua->nama }}</span>
                                            <span class="text-[10px] text-gray-400 font-light">Akun Terdaftar</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                        <span>Belum Ditunjuk</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-light border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    <span>{{ $statusConfig['label'] }}</span>
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right align-middle">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                        @click="$dispatch('open-modal-edit-tim', { id: '{{ $tim->id_tim }}', nama: '{{ addslashes($tim->nama_tim) }}', deskripsi: '{{ addslashes($tim->deskripsi_tim ?? '') }}', ketua: '{{ $tim->id_ketua_tim ?? '' }}', ketuaName: '{{ addslashes($tim->ketua->nama ?? 'Pilih Ketua Tim') }}', status: '{{ $tim->status_tim }}' })" 
                                        class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:text-[#6E5BC3] hover:border-[#6E5BC3] hover:bg-purple-50 transition-all shadow-2xs cursor-pointer" title="Edit Tim Kerja">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- BARIS KHUSUS CARD (Tampilan saat Layar Diperkecil < xl) --}}
                        <tr class="xl:hidden border-b border-purple-100/40 last:border-none">
                            <td colspan="6" class="p-2.5 sm:p-3 bg-white">
                                <div class="bg-[#F8F7FF]/50 hover:bg-[#F8F7FF] border border-purple-100 rounded-[22px] p-4 flex flex-col gap-3.5 shadow-2xs transition-all">
                                    {{-- Baris Atas: Indikator Status di Sisi Kiri Atas & Icon Aksi di Kanan Atas --}}
                                    <div class="flex items-center justify-between gap-2">
                                        {{-- Indikator Status di Kiri Atas --}}
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-light border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                            <span>{{ $statusConfig['label'] }}</span>
                                        </span>

                                        {{-- Icon Aksi di Kanan Atas --}}
                                        <div class="flex items-center gap-1.5">
                                            <button type="button"
                                                @click="$dispatch('open-modal-edit-tim', { id: '{{ $tim->id_tim }}', nama: '{{ addslashes($tim->nama_tim) }}', deskripsi: '{{ addslashes($tim->deskripsi_tim ?? '') }}', ketua: '{{ $tim->id_ketua_tim ?? '' }}', ketuaName: '{{ addslashes($tim->ketua->nama ?? 'Pilih Ketua Tim') }}', status: '{{ $tim->status_tim }}' })" 
                                                class="w-8 h-8 rounded-full bg-white text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white border border-purple-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" title="Edit Tim Kerja">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Baris Utama: Ikon Tim di Sisi Kiri & Nama Tim + Ketua di Kanan --}}
                                    <div class="flex items-center gap-3.5">
                                        {{-- Ikon Tim --}}
                                        <div class="w-10 h-10 rounded-2xl bg-white border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>

                                        {{-- Samping Ikon: Nama Tim & Ketua (dengan ikon orang) --}}
                                        <div class="flex-1 min-w-0 flex flex-col gap-1">
                                            <h4 class="text-[14px] font-normal text-gray-800 wrap-break-word leading-snug">{{ $tim->nama_tim }}</h4>
                                            <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                @if($tim->ketua)
                                                    <span class="font-light text-gray-700">{{ $tim->ketua->nama }}</span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                        <span>Belum Ditunjuk</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Desain Deskripsi Tim Kerja yang Diperbagus --}}
                                    <div class="bg-white/90 rounded-2xl p-3 border border-purple-100/70 shadow-2xs">
                                        <div class="flex items-center gap-1.5 text-[10px] font-normal text-gray-400 uppercase tracking-wider mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                            </svg>
                                            <span>Deskripsi</span>
                                        </div>
                                        <p class="text-xs text-gray-600 leading-relaxed text-left wrap-break-word font-light">
                                            @if(!empty(trim($tim->deskripsi_tim ?? '')))
                                                {{ $tim->deskripsi_tim }}
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                    <span>Deskripsi belum diinputkan</span>
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="bg-white">
                                <x-emptystate 
                                    :border="false" 
                                    padding="py-16 px-4" 
                                    title="Tidak Ada Tim Kerja Ditemukan" 
                                    message="Tidak ada data tim kerja yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                                />
                            </td>
                        </tr>
                        @endforelse

                    </x-datatable>
        </div>

            {{-- Include Modals --}}
            @include('admin.modals.tambahtimkerja')
            @include('admin.modals.edittimkerja')
        </div>

    {{-- CSS KUSTOM UNTUK MEMBUANG PEMBUNGKUS DI SLOT TABS & PAGINATION --</span> --}}
    <style>
        x-datatable > div > div:has(.filter-tabs-container),
        .overflow-hidden > div:has(.filter-tabs-container) {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .flex.items-center.gap-2.bg-gray-50, 
        nav[role="navigation"] div > div:nth-child(2),
        .rounded-full.bg-gray-50 {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    </style>
</x-layoututama>