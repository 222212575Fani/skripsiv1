<x-layoututama title="Manajemen Pengguna">
    <div class="flex-1 h-full overflow-y-auto p-6 lg:p-10 bg-[#F8F7FF]">
        <div x-data="{}" class="flex flex-col gap-6 max-w-7xl mx-auto w-full">
            
            {{-- KOTAK PUTIH LUAR UTAMA DENGAN SUDUT MELENGKUNG --}}
            <div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
                
                {{-- BARIS ATAS: JUDUL, SEARCH BAR, DAN TOMBOL TAMBAH --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Daftar Pengguna</h1>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Live Search --}}
                        <div class="relative flex-1 md:w-64 group/search" x-data="{ search: '{{ request('search') }}' }">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-hover/search:text-[#5C46F5] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" x-model="search" 
                                @input.debounce.300ms="
                                    const url = new URL(window.location.href);
                                    if (search) { url.searchParams.set('search', search); } else { url.searchParams.delete('search'); }
                                    window.location.href = url.toString();
                                "
                                placeholder="Cari Nama atau NIP..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#5C46F5] rounded-2xl text-xs font-normal text-gray-700 focus:outline-none focus:border-[#5C46F5] transition-all">
                        </div>

                        <x-button @click="$dispatch('open-modal-tambah-pengguna')"> 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Pengguna</span>
                        </x-button>
                    </div>
                </div>

                {{-- PEMBUNGKUS MANUAL HANYA UNTUK TABEL DENGAN BORDER UNGU TIPIS --}}
                <div class="rounded-2xl border border-purple-100 overflow-hidden">
                    <x-datatable :paginator="$users" item-name="data pengguna">
                        
                        {{-- SLOT TAB FILTER STATUS --}}
                        <x-slot name="tabs">
                            @php 
                                $currentStatus = request('status', 'semua'); 
                                $statuses = ['semua' => 'Semua Pengguna', 'aktif' => 'Aktif', 'pending' => 'Pending', 'nonaktif' => 'Non-Aktif'];
                            @endphp
                            <div class="flex items-center gap-3 px-2 flex-wrap mb-2 filter-tabs-container">
                                @foreach($statuses as $key => $label)
                                    <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                                       class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus == $key ? 'bg-[#5C46F5]/10 text-[#5C46F5]' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                                        {{ $label }}
                                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#5C46F5] text-white' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $counts[$key] ?? 0 }}
                                        </span>
                                    </a>
                                @endforeach
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
                        
                        <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden md:table-row border-b border-gray-100 last:border-none">
                            <td class="py-4 px-4 text-center text-gray-500 font-bold text-xs">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600 font-medium">
                                {{ $user->nip }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shrink-0 shadow-xs">
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
                                            class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-black rounded-xl hover:bg-[#4A38D4] transition-all uppercase shadow-md shadow-[#5C46F5]/20">
                                            Aktivasi
                                        </button>
                                    @else
                                        <button type="button"
                                            @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                            class="p-1.5 rounded-xl bg-purple-50 text-[#5C46F5] hover:bg-[#5C46F5] hover:text-white transition-all shadow-xs cursor-pointer" title="Edit Pengguna">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center bg-white">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="w-12 h-12 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shadow-xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-700">Data tidak ditemukan</span>
                                        <span class="text-[11px] text-gray-400 font-normal mt-0.5">Belum ada data pengguna yang terdaftar pada sistem.</span>
                                    </div>
                                </div>
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