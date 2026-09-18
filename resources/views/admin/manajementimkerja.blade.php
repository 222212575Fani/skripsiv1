<x-layoututama title="Manajemen Tim Kerja">
    <div class="flex-1 h-full overflow-y-auto p-6 lg:p-10 bg-[#F8F7FF]">
        <div x-data="{}" class="flex flex-col gap-6 max-w-7xl mx-auto w-full">
            
            {{-- KOTAK PUTIH LUAR UTAMA DENGAN SUDUT MELENGKUNG --}}
            <div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
                
                {{-- BARIS ATAS: JUDUL, SEARCH DAN TOMBOL TAMBAH --}}
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 px-2">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Daftar Tim Kerja</h1>
                    </div>

                    {{-- KONTROL DESKTOP (Hanya Tampil di Layar >= xl): Search di Kiri, Tombol Tambah di Kanan --}}
                    <div class="hidden xl:flex items-center gap-3">
                        {{-- Live Search --}}
                        <div class="flex items-center gap-3 px-4 py-2 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-64" x-data="{ search: '{{ request('search') }}' }">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" 
                                x-model="search"
                                @input.debounce.300ms="
                                    const url = new URL(window.location.href);
                                    if (search) {
                                        url.searchParams.set('search', search);
                                    } else {
                                        url.searchParams.delete('search');
                                    }
                                    window.location.href = url.toString();
                                "
                                placeholder="Cari Nama Tim..." 
                                autocomplete="off"
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-[#6E5BC3] placeholder:text-[#6E5BC3] w-full p-0 focus:ring-0">
                        </div>

                        {{-- Tombol Tambah Tim Kerja --}}
                        <x-button @click="$dispatch('open-modal-tambah')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Tim Kerja</span>
                        </x-button>
                    </div>

                    {{-- KONTROL MOBILE / TABLET (< xl): Berurutan ke Bawah: 1) Tambah Tim Kerja, 2) Cari Nama Tim --}}
                    <div class="flex xl:hidden flex-col gap-3 w-full">
                        {{-- 1. Tombol Tambah Tim Kerja (Full Width) --}}
                        <x-button @click="$dispatch('open-modal-tambah')" class="w-full justify-center py-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Tim Kerja</span>
                        </x-button>

                        {{-- 2. Cari Nama Tim (Full Width) --}}
                        <div class="flex items-center gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-full" x-data="{ search: '{{ request('search') }}' }">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" 
                                x-model="search"
                                @input.debounce.300ms="
                                    const url = new URL(window.location.href);
                                    if (search) {
                                        url.searchParams.set('search', search);
                                    } else {
                                        url.searchParams.delete('search');
                                    }
                                    window.location.href = url.toString();
                                "
                                placeholder="Cari Nama Tim..." 
                                autocomplete="off"
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-[#6E5BC3] placeholder:text-[#6E5BC3] w-full p-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- PEMBUNGKUS MANUAL HANYA UNTUK TABEL DENGAN BORDER UNGU TIPIS --}}
                <div class="rounded-2xl border border-purple-100">
                    <x-datatable :paginator="$timKerja" item-name="data tim kerja">
                        
                        {{-- SLOT TAB FILTER STATUS --}}
                        <x-slot name="tabs">
                            @php 
                                $currentStatus = request('status', 'semua'); 
                                $statuses = [
                                    'semua' => 'Semua Tim', 
                                    'aktif' => 'Aktif', 
                                    'nonaktif' => 'Non-Aktif'
                                ];
                            @endphp

                            {{-- TAB FILTER HORIZONTAL DESKTOP (Hanya Tampil di Layar >= xl) --}}
                            <div class="hidden xl:flex items-center gap-3 px-2 flex-wrap mb-2 filter-tabs-container">
                                @foreach($statuses as $key => $label)
                                    <a href="{{ route('admin.manajementimkerja', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                                       class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus == $key ? 'bg-[#6E5BC3]/10 text-[#6E5BC3]' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                                        {{ $label }}
                                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $counts[$key] ?? 0 }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>

                            {{-- FILTER DROPDOWN MOBILE / TABLET (< xl): Berurutan ke Bawah setelah Cari --}}
                            <div class="xl:hidden w-full relative" x-data="{ open: false }" @click.outside="open = false">
                                <button @click="open = !open" 
                                    type="button" 
                                    class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-normal text-[#6E5BC3] transition-all cursor-pointer shadow-2xs">
                                    <div class="flex items-center gap-2 truncate">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                        </svg>
                                        <span class="font-normal text-gray-700">Filter: <span class="text-[#6E5BC3] font-normal">{{ $statuses[$currentStatus] ?? 'Semua Tim' }}</span></span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#6E5BC3] text-white">
                                            {{ $counts[$currentStatus] ?? 0 }}
                                        </span>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                {{-- Dropdown Popup --}}
                                <div x-show="open" x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-2xl shadow-xl py-1.5 z-50 overflow-hidden text-xs">
                                    @foreach($statuses as $key => $label)
                                        <a href="{{ route('admin.manajementimkerja', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                                           class="flex items-center justify-between px-4 py-2.5 hover:bg-[#F8F7FF] transition-colors {{ $currentStatus == $key ? 'bg-[#6E5BC3]/10 text-[#6E5BC3] font-bold' : 'text-gray-700 font-medium' }}">
                                            <div class="flex items-center gap-2">
                                                @if($currentStatus == $key)
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#6E5BC3]"></span>
                                                @else
                                                    <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                                @endif
                                                <span>{{ $label }}</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $counts[$key] ?? 0 }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </x-slot>

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
                            $statusClass = $tim->status_tim == 'aktif' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100';
                        @endphp

                        <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden xl:table-row border-b border-gray-100 last:border-none">
                            <td class="py-4 px-4 text-center text-gray-500 font-bold text-xs">
                                {{ $timKerja->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                    <span class="font-bold text-gray-900 text-xs wrap-break-word">{{ $tim->nama_tim }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600 leading-relaxed whitespace-normal wrap-break-word font-normal text-justify">
                                {{ $tim->deskripsi_tim ?? 'Deskripsi tim kerja belum diinputkan' }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 text-xs">{{ $tim->ketua->nama ?? 'Tidak Ada' }}</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Akun Terdaftar</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border inline-block {{ $statusClass }}">
                                    {{ $tim->status_tim }}
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right align-middle">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                        @click="$dispatch('open-modal-edit-tim', { id: '{{ $tim->id_tim }}', nama: '{{ addslashes($tim->nama_tim) }}', deskripsi: '{{ addslashes($tim->deskripsi_tim ?? '') }}', ketua: '{{ $tim->id_ketua_tim ?? '' }}', status: '{{ $tim->status_tim }}' })" 
                                        class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all shadow-xs cursor-pointer" title="Edit Tim Kerja">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border tracking-wider {{ $statusClass }}">
                                            {{ $tim->status_tim }}
                                        </span>

                                        {{-- Icon Aksi di Kanan Atas --}}
                                        <div class="flex items-center gap-1.5">
                                            <button type="button"
                                                @click="$dispatch('open-modal-edit-tim', { id: '{{ $tim->id_tim }}', nama: '{{ addslashes($tim->nama_tim) }}', deskripsi: '{{ addslashes($tim->deskripsi_tim ?? '') }}', ketua: '{{ $tim->id_ketua_tim ?? '' }}', status: '{{ $tim->status_tim }}' })" 
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
                                            <h4 class="text-[15px] font-bold text-gray-900 wrap-break-word leading-snug">{{ $tim->nama_tim }}</h4>
                                            <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span class="font-medium text-gray-700">{{ $tim->ketua->nama ?? 'Belum ada ketua' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Desain Deskripsi Tim Kerja yang Diperbagus --}}
                                    <div class="bg-white/90 rounded-2xl p-3 border border-purple-100/70 shadow-2xs">
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                            </svg>
                                            <span>Deskripsi</span>
                                        </div>
                                        <p class="text-xs text-gray-600 leading-relaxed text-left wrap-break-word font-normal">
                                            {{ $tim->deskripsi_tim ?? 'Deskripsi tim kerja belum diinputkan' }}
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

            </div>

            {{-- Include Modals --}}
            @include('admin.modals.tambahtimkerja')
            @include('admin.modals.edittimkerja')
        </div>
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