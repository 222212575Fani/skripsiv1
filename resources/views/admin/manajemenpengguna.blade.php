<x-layoututama title="Manajemen Pengguna">
    <div class="flex-1 h-full overflow-y-auto p-6 lg:p-10 bg-[#F8F7FF]">
        <div x-data="{}" class="flex flex-col gap-6 max-w-7xl mx-auto w-full">
            
            {{-- KOTAK PUTIH LUAR UTAMA DENGAN SUDUT MELENGKUNG --}}
            <div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
                
                {{-- BARIS ATAS: JUDUL, SEARCH BAR, DAN TOMBOL TAMBAH --}}
                <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 px-2">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Daftar Pengguna</h1>
                    </div>

                    {{-- KONTROL DESKTOP (Hanya Tampil di Layar >= xl): Search di Kiri, Tombol Tambah di Kanan --}}
                    <div class="hidden xl:flex items-center gap-3">
                        {{-- Live Search --}}
                        <div class="flex items-center gap-3 px-4 py-2 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-64" x-data="{ search: '{{ request('search') }}' }">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="search" 
                                @input.debounce.300ms="
                                    const url = new URL(window.location.href);
                                    if (search) { url.searchParams.set('search', search); } else { url.searchParams.delete('search'); }
                                    window.location.href = url.toString();
                                "
                                placeholder="Cari Nama atau NIP..." 
                                autocomplete="off"
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-[#6E5BC3] placeholder:text-[#6E5BC3] w-full p-0 focus:ring-0">
                        </div>

                        {{-- Tombol Tambah Pengguna --}}
                        <x-button @click="$dispatch('open-modal-tambah-pengguna')"> 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Pengguna</span>
                        </x-button>
                    </div>

                    {{-- KONTROL MOBILE / TABLET (< xl): Berurutan ke Bawah: 1) Tambah Pengguna, 2) Cari Nama atau NIP --}}
                    <div class="flex xl:hidden flex-col gap-3 w-full">
                        {{-- 1. Tombol Tambah Pengguna (Full Width) --}}
                        <x-button @click="$dispatch('open-modal-tambah-pengguna')" class="w-full justify-center py-2.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Pengguna</span>
                        </x-button>

                        {{-- 2. Cari Nama atau NIP (Full Width) --}}
                        <div class="flex items-center gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-full" x-data="{ search: '{{ request('search') }}' }">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="search" 
                                @input.debounce.300ms="
                                    const url = new URL(window.location.href);
                                    if (search) { url.searchParams.set('search', search); } else { url.searchParams.delete('search'); }
                                    window.location.href = url.toString();
                                "
                                placeholder="Cari Nama atau NIP..." 
                                autocomplete="off"
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-[#6E5BC3] placeholder:text-[#6E5BC3] w-full p-0 focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- PEMBUNGKUS MANUAL HANYA UNTUK TABEL DENGAN BORDER UNGU TIPIS --}}
                <div class="rounded-2xl border border-purple-100">
                    <x-datatable :paginator="$users" item-name="data pengguna">
                        
                        {{-- SLOT TAB FILTER STATUS --}}
                        <x-slot name="tabs">
                            @php 
                                $currentStatus = request('status', 'semua'); 
                                $statuses = ['semua' => 'Semua Pengguna', 'aktif' => 'Aktif', 'pending' => 'Pending', 'nonaktif' => 'Non-Aktif'];
                            @endphp

                            {{-- TAB FILTER HORIZONTAL DESKTOP (Hanya Tampil di Layar >= xl) --}}
                            <div class="hidden xl:flex items-center gap-3 px-2 flex-wrap mb-2 filter-tabs-container">
                                @foreach($statuses as $key => $label)
                                    <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
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
                                        <span class="font-normal text-gray-700">Filter: <span class="text-[#6E5BC3] font-normal">{{ $statuses[$currentStatus] ?? 'Semua Pengguna' }}</span></span>
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
                                        <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
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
                            <th class="py-3.5 px-4 text-center w-[8%]">No</th>
                            <th class="py-3.5 px-4 w-[20%]">NIP</th>
                            <th class="py-3.5 px-4 w-[28%]">Nama Pengguna</th>
                            <th class="py-3.5 px-4 w-[18%]">Role</th>
                            <th class="py-3.5 px-4 text-center w-[15%]">Status</th>
                            <th class="py-3.5 pr-6 text-right w-[11%]">Aksi</th>
                        </x-slot>

                        {{-- SLOT ISI DATA (LOOPING) --}}
                        @forelse($users as $index => $user)
                        @php
                            $statusClass = match($user->status_akun) {
                                'aktif' => 'bg-green-50 text-green-600 border-green-100',
                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                default => 'bg-red-50 text-red-600 border-red-100'
                            };
                        @endphp
                        
                        <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden xl:table-row border-b border-gray-100 last:border-none">
                            <td class="py-4 px-4 text-center text-gray-500 font-bold text-xs">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600 font-medium">
                                {{ $user->nip }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-900 text-xs">{{ $user->nama }}</span>
                                        <span class="text-[10px] text-gray-400 font-normal">Akun Terdaftar</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600 font-medium">
                                {{ $user->role->nama_role ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-center align-middle">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border inline-block {{ $statusClass }}">
                                    {{ $user->status_akun }}
                                </span>
                            </td>
                            <td class="py-4 pr-6 text-right align-middle">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($user->status_akun == 'pending')
                                        <button type="button" 
                                            @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                            class="px-4 py-1.5 bg-[#6E5BC3] text-white text-[10px] font-black rounded-xl hover:bg-[#5C4AB5] transition-all uppercase shadow-md shadow-[#6E5BC3]/20">
                                            Aktivasi
                                        </button>
                                    @else
                                        <button type="button"
                                            @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                            class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all shadow-xs cursor-pointer" title="Edit Pengguna">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    @endif
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
                                            {{ $user->status_akun }}
                                        </span>

                                        {{-- Icon Aksi di Kanan Atas --}}
                                        <div class="flex items-center gap-1.5">
                                            @if($user->status_akun == 'pending')
                                                <button type="button" 
                                                    @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                                    class="px-3.5 py-1 bg-[#6E5BC3] text-white text-[10px] font-black rounded-xl hover:bg-[#5C4AB5] transition-all uppercase shadow-xs cursor-pointer">
                                                    Aktivasi
                                                </button>
                                            @else
                                                <button type="button"
                                                    @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                                    class="w-8 h-8 rounded-full bg-white text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white border border-purple-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" title="Edit Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Baris Utama: Ikon Orang di Sisi Kiri & Informasi di Sampingnya --}}
                                    <div class="flex items-start gap-3.5">
                                        {{-- Ikon Orang --}}
                                        <div class="w-10 h-10 rounded-2xl bg-white border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-2xs mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>

                                        {{-- Samping Ikon: Nama, Judul NIP (angka hitam & lebih besar), dan Peran (tanpa efek ungu) --}}
                                        <div class="flex-1 min-w-0 flex flex-col gap-1">
                                            <h4 class="text-[15px] font-bold text-gray-900 wrap-break-word leading-snug">{{ $user->nama }}</h4>
                                            <div class="flex items-center gap-1.5 text-xs">
                                                <span class="text-gray-500 font-medium">NIP</span>
                                                <span class="text-sm font-normal text-black tracking-wide">{{ $user->nip }}</span>
                                            </div>
                                            <div class="text-xs text-gray-600 font-medium">
                                                {{ $user->role->nama_role ?? '-' }}
                                            </div>
                                        </div>
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
                                    title="Tidak Ada Pengguna Ditemukan" 
                                    message="Tidak ada data pengguna yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                                />
                            </td>
                        </tr>
                        @endforelse

                    </x-datatable>
                </div>

            </div>

            {{-- Include Modals --}}
            @include('admin.modals.aktivasipengguna')
            @include('admin.modals.tambahpengguna')
            @include('admin.modals.editpengguna')

        </div>
    </div>

    {{-- CSS KUSTOM UNTUK MEMBUANG PEMBUNGKUS DI SLOT TABS & PAGINATION --}}
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