<x-layoututama title="Manajemen Proyek">
    <div x-data="{}" class="flex flex-col gap-6 w-full">
        
        {{-- MEMANGGIL KOMPONEN DATATABLE --}}
        <x-datatable :paginator="$proyeks ?? null" item-name="data proyek">
            
            <x-slot name="tabs">
                {{-- BARIS ATAS: JUDUL, LIVE SEARCH, DAN TOMBOL TAMBAH --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Daftar Proyek</h1>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        {{-- Live Search dengan Hover & Focus Ungu yang Konsisten --}}
                        <div class="relative flex-1 md:w-64 group/search" x-data="{ search: '{{ request('search') }}' }">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 group-hover/search:text-[#5C46F5] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
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
                                placeholder="Cari Proyek / Ketua..." 
                                autocomplete="off"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50/50 hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#5C46F5] rounded-2xl text-xs font-normal text-gray-700 hover:text-[#5C46F5] focus:text-[#5C46F5] placeholder:text-gray-400 group-hover/search:placeholder:text-[#9E8CE3] focus:placeholder:text-[#9E8CE3] focus:outline-none focus:border-[#5C46F5] transition-all">
                        </div>

                        <x-button @click="$dispatch('open-modal-tambah-proyek')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Tambah Proyek Baru</span>
                        </x-button>
                    </div>
                </div>

                {{-- TAB FILTER STATUS PROYEK --}}
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
                <div class="flex items-center gap-3 px-2 flex-wrap">
                    @foreach($statuses as $key => $label)
                        <a href="{{ route('ketuatim.manajemenproyek', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                           class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus == $key ? 'bg-[#5C46F5]/10 text-[#5C46F5]' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                            {{ $label }}
                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#5C46F5] text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </x-slot>

            {{-- SLOT HEADER KOLOM TABEL --}}
            <x-slot name="header">
                <th class="py-3.5 px-4 text-center w-[6%]">No</th>
                <th class="py-3.5 px-4 w-[24%]">Nama Proyek</th>
                <th class="py-3.5 px-4 w-[24%]">Deskripsi</th>
                <th class="py-3.5 px-4 w-[18%]">Ketua Proyek</th>
                <th class="py-3.5 px-4 w-[10%]">Mulai</th>
                <th class="py-3.5 px-4 w-[10%]">Selesai</th>
                <th class="py-3.5 px-4 text-center w-[10%]">Status</th>
                <th class="py-3.5 px-4 text-center w-[8%]">Aksi</th>
            </x-slot>

            {{-- SLOT ISI DATA (LOOPING PROYEK) --}}
            @forelse($proyeks ?? [] as $index => $proyek)
            @php
                $status = $proyek->status_proyek ?? 'belum_dimulai';
                $statusClass = match($status) {
                    'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'berjalan' => 'bg-amber-50 text-amber-600 border-amber-100',
                    'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                    default => 'bg-orange-50 text-orange-600 border-orange-100' 
                };
            @endphp

            <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden md:table-row">
                <td class="py-4 px-4 text-center text-gray-500 font-bold text-xs">
                    {{ $proyeks->firstItem() + $index }}
                </td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shrink-0 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <span class="font-normal text-gray-900 text-xs break-words">{{ $proyek->nama_proyek }}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-xs text-gray-600 font-normal leading-relaxed">
                    {{ $proyek->deskripsi_proyek ?? '-' }}
                </td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shrink-0 shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-900 text-xs">{{ $proyek->ketuaProyek->nama ?? 'Belum Ditunjuk' }}</span>
                            <span class="text-[10px] text-gray-400 font-normal">Ketua Proyek</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                    {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                </td>
                <td class="py-4 px-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                    {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}
                </td>
                <td class="py-4 px-4 text-center align-middle">
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border inline-block whitespace-nowrap {{ $statusClass }}">
                        {{ ucwords(str_replace('_', ' ', $status)) }}
                    </span>
                </td>
                <td class="py-4 px-4 text-center align-middle">
                    <div class="flex items-center justify-center gap-1">
                        <button type="button"
                                @click="$dispatch('open-modal-edit-proyek', {{ json_encode($proyek) }})" 
                                class="text-purple-300 hover:text-purple-500 transition-colors p-2 inline-flex items-center justify-center cursor-pointer" title="Edit Proyek">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </button>

                        <button type="button" 
                                @click="$dispatch('open-modal-hapus-proyek', { url: @js(route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek)) })"
                                class="text-rose-300 hover:text-rose-500 transition-colors p-2 inline-flex items-center justify-center cursor-pointer" title="Hapus Proyek">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-16 text-center bg-white">
                    <div class="flex flex-col items-center justify-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shadow-xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-700">Belum ada data proyek tersedia</span>
                            <span class="text-[11px] text-gray-400 font-normal mt-0.5">Data proyek akan muncul setelah ditambahkan ke sistem.</span>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse

        </x-datatable>

        {{-- Memanggil komponen modal kustom --}}
        @include('ketuatim.modals.tambahproyek')
        @include('ketuatim.modals.editproyek')
        @include('ketuatim.modals.hapusproyek')
    </div>
</x-layoututama>