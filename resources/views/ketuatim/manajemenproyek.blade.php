<x-layoututama title="Manajemen Proyek">
    <div x-data="{}">
        
        {{-- Kolom Pencarian di Header menggunakan Form Submit Otomatis --}}
        <x-slot name="headerTitle">
            <form action="{{ route('ketuatim.manajemenproyek') }}" method="GET" id="searchForm" class="relative w-full">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                    oninput="clearTimeout(window.searchTimer); window.searchTimer = setTimeout(() => { document.getElementById('searchForm').submit(); }, 400);"
                    placeholder="Cari nama proyek atau ketua proyek..." autocomplete="off"
                    class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-xs font-medium">
            </form>
        </x-slot>

        {{-- Slot Header Judul Halaman yang Seragam dengan Modul Lain --}}
        <x-slot name="pageHeader">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Proyek</h1>
                <p class="text-xs text-gray-400 mt-1 font-medium">Kelola proyek tim, dan tetapkan Ketua Proyek!</p>
            </div>
            
            <x-button @click="$dispatch('open-modal-tambah-proyek')"> 
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Proyek Baru</span>
            </x-button>
        </x-slot>

        {{-- Memanggil Komponen Reusable <x-datatable> --}}
        <x-datatable :paginator="$proyeks ?? null" item-name="data proyek">
            
            {{-- Slot untuk Filter Status yang Responsif --}}
            <x-slot name="tabs">
                @php 
                    $currentStatus = request('status', 'semua'); 
                    $statuses = [
                        'semua' => 'Semua Proyek', 
                        'belum_dimulai' => 'Belum Dimulai', 
                        'berjalan' => 'Sedang Berjalan', 
                        'selesai' => 'Selesai', 
                        'terlambat' => 'Terlambat'
                    ];
                @endphp

                {{-- 1. TAMPILAN TAB FILTER (Hanya muncul di Layar Normal / Desktop) --}}
                <div class="hidden md:flex items-center gap-6 border-b border-gray-100 px-8 pt-5 flex-wrap pb-3">
                    @foreach($statuses as $key => $label)
                        @php
                            if ($key == 'semua') $textColor = 'text-[#5C46F5]';
                            elseif ($key == 'berjalan') $textColor = 'text-amber-600';
                            elseif ($key == 'selesai') $textColor = 'text-emerald-600';
                            elseif ($key == 'terlambat') $textColor = 'text-rose-600';
                            else $textColor = 'text-orange-600';

                            $bgColor = 'bg-gray-100'; 
                            if ($currentStatus == $key) {
                                if ($key == 'semua') $bgColor = 'bg-[#5C46F5]/10';
                                elseif ($key == 'berjalan') $bgColor = 'bg-amber-50';
                                elseif ($key == 'selesai') $bgColor = 'bg-emerald-50';
                                elseif ($key == 'terlambat') $bgColor = 'bg-rose-50';
                                else $bgColor = 'bg-orange-100';
                            }
                        @endphp
                        <a href="{{ route('ketuatim.manajemenproyek', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                           class="pb-2 text-[11px] uppercase tracking-wider font-bold transition-all border-b-2 whitespace-nowrap {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                            {{ $label }}
                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- 2. TAMPILAN DROPDOWN FILTER (Hanya muncul saat Layar Diperkecil / Mobile) --}}
                <div class="flex md:hidden px-6 py-4 border-b border-gray-100 items-center justify-end">
                    <div class="relative w-full sm:w-auto" x-data="{ openFilter: false }">
                        <button @click="openFilter = !openFilter" 
                                class="w-full sm:w-auto flex items-center justify-between gap-3 px-4 py-2 rounded-xl bg-gray-50 hover:bg-purple-50 text-gray-700 hover:text-[#5C46F5] text-xs font-bold border border-gray-100 transition-colors focus:outline-none">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span class="truncate">Filter Status Proyek</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform shrink-0" :class="openFilter ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openFilter" x-cloak @click.away="openFilter = false" x-transition 
                             class="absolute right-0 mt-2 w-full sm:w-56 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 py-1.5">
                            @foreach($statuses as $key => $label)
                                <a href="{{ route('ketuatim.manajemenproyek', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                                   class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold transition-colors {{ $currentStatus == $key ? 'bg-purple-50 text-[#5C46F5]' : 'text-gray-600 hover:bg-gray-50' }}">
                                    <span>{{ $label }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#5C46F5]/10 text-[#5C46F5]' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $counts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-slot>

            {{-- Slot Definisi Header Tabel (Desktop) --}}
            <x-slot name="header">
                <tr class="text-gray-400 text-[10px] uppercase tracking-wider font-bold border-b border-gray-100 bg-gray-50/30 hidden md:table-row">
                    <th class="px-3 py-4 text-center w-[8%]">No</th>
                    <th class="px-3 py-4 w-[27%]">Nama Proyek</th>
                    <th class="px-3 py-4 w-[25%]">Deskripsi</th>
                    <th class="px-3 py-4 w-[20%]">Ketua Proyek</th>
                    <th class="px-3 py-4 w-[12%]">Mulai</th>
                    <th class="px-3 py-4 w-[12%]">Selesai</th>
                    <th class="px-3 py-4 text-center w-[15%]">Status</th>
                    <th class="px-3 py-4 text-center w-[10%]">Aksi</th>
                </tr>
            </x-slot>

            {{-- Bagian Isi Data --}}
            @if(isset($proyeks) && count($proyeks) > 0)
                @foreach($proyeks as $index => $proyek)
                    @php
                        $status = $proyek->status_proyek ?? 'belum_dimulai';
                        $statusClass = match($status) {
                            'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'berjalan' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                            default => 'bg-orange-100 text-orange-600 border-orange-200' 
                        };
                    @endphp

                    {{-- 1. TAMPILAN TABEL BIASA (Desktop) --}}
                    <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50 hidden md:table-row">
                        <td class="px-3 py-4 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                            {{ $proyeks->firstItem() + $index }}
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-700 font-medium">
                            {{ $proyek->nama_proyek }}
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-500 font-normal">
                            {{ $proyek->deskripsi_proyek ?? '-' }}
                        </td>
                        <td class="px-3 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[10px] font-bold shadow-sm shrink-0">
                                    {{ strtoupper(substr($proyek->ketuaProyek->nama ?? 'B', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-600 font-medium truncate max-w-[110px]">{{ $proyek->ketuaProyek->nama ?? 'Belum Ditunjuk' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                            {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-3 py-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                            {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="px-3 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-semibold uppercase border inline-block whitespace-nowrap {{ $statusClass }}">
                                {{ ucwords(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-3 py-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                        @click="$dispatch('open-modal-edit-proyek', { id: '{{ $proyek->id_proyek }}' })" 
                                        class="text-purple-300 hover:text-purple-500 transition-colors p-1.5 inline-flex items-center justify-center" title="Edit Proyek">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>

                                <button type="button" 
                                        @click="$dispatch('open-modal-hapus-proyek', { url: @js(route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek)) })"
                                        class="text-rose-300 hover:text-rose-500 transition-colors p-1.5 inline-flex items-center justify-center" title="Hapus Proyek">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- 2. TAMPILAN CARD VERTIKAL (Mobile) --}}
                    <tr class="block md:hidden border-b border-gray-100 bg-white">
                        <td colspan="8" class="p-4 space-y-3 block w-full">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-xs font-bold text-gray-900">{{ $proyek->nama_proyek }}</h3>
                                    <p class="text-[11px] text-gray-500 mt-1">{{ $proyek->deskripsi_proyek ?? 'Tidak ada deskripsi' }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold uppercase border whitespace-nowrap {{ $statusClass }}">
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-50 text-[11px]">
                                <div>
                                    <span class="text-gray-400 block text-[10px]">Ketua Proyek</span>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="w-5 h-5 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[9px] font-bold">
                                            {{ strtoupper(substr($proyek->ketuaProyek->nama ?? 'B', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-700 truncate">{{ $proyek->ketuaProyek->nama ?? 'Belum Ditunjuk' }}</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-gray-400 block text-[10px]">Timeline</span>
                                    <span class="font-medium text-gray-600 block mt-1">
                                        {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d/m/y') : '-' }} s.d 
                                        {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->format('d/m/y') : '-' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-50">
                                <button type="button" @click="$dispatch('open-modal-edit-proyek', { id: '{{ $proyek->id_proyek }}' })" 
                                        class="px-3 py-1.5 rounded-lg bg-purple-50 text-[#5C46F5] text-xs font-bold hover:bg-purple-100 transition-colors">
                                    Edit
                                </button>
                                <button type="button" @click="$dispatch('open-modal-hapus-proyek', { url: @js(route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek)) })"
                                        class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" class="py-20 text-center text-gray-400 font-bold text-xs">Data tidak ditemukan.</td>
                </tr>
            @endif

        </x-datatable>

        {{-- Memanggil komponen modal kustom --}}
        @include('ketuatim.modals.tambahproyek')
        @include('ketuatim.modals.hapusproyek')
    </div>
</x-layoututama>