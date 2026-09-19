<x-layoutfull title="Detail Proyek & Aktivitas" :showNotification="false">

    {{-- SLOT HEADER ACTION: TOMBOL KEMBALI BERSIH & SEJAJAR --}}
    <x-slot name="headerAction">
        <a href="{{ route('anggota.proyekaktivitas') }}" 
           class="text-white text-sm font-bold hover:opacity-80 transition-all flex items-center gap-1.5">
            <span>&larr; Kembali</span>
        </a>
    </x-slot>

    <div x-data="{ 
        search: '{{ request('search') }}',
        fetchAktivitas() {
            const url = new URL(window.location.href);
            if (this.search) { 
                url.searchParams.set('search', this.search); 
            } else { 
                url.searchParams.delete('search'); 
            }
            
            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.datatable-container-wrapper') || doc.querySelector('x-datatable');
                    if (newTable) {
                        const target = document.querySelector('.datatable-container-wrapper');
                        if (target) {
                            target.innerHTML = newTable.innerHTML;
                            if (window.Alpine) {
                                window.Alpine.initTree(target);
                            }
                        }
                    }
                    const newFilter = doc.querySelector('#mobile-filter-container');
                    const targetFilter = document.querySelector('#mobile-filter-container');
                    if (newFilter && targetFilter) {
                        targetFilter.innerHTML = newFilter.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetFilter);
                        }
                    }
                    window.history.pushState({}, '', url.toString());
                });
        }
    }" class="flex flex-col gap-6 w-full">
        
        {{-- KOTAK PUTIH UTAMA --}}
        <div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
            
            @php 
                $currentStatus = request('status', 'semua'); 
                $statuses = [
                    'semua' => 'Semua Aktivitas', 
                    'belum_dimulai' => 'Belum Dimulai', 
                    'berjalan' => 'Sedang Berjalan', 
                    'selesai' => 'Selesai', 
                    'terlambat' => 'Terlambat'
                ];
            @endphp

            {{-- HEADER HALAMAN & KONTROL --}}
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                {{-- Judul Halaman di Sisi Kiri --}}
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold text-gray-900">
                        Daftar Aktivitas Proyek ({{ $proyek->nama_proyek }})
                    </h1>
                </div>

                {{-- Kontrol Desktop (Layar >= xl): Sebaris di Kanan (Search & Tombol Tambah) --}}
                <div class="hidden xl:flex items-center gap-3 w-auto">
                    {{-- Live Search Desktop --}}
                    <div class="relative w-72 group/search">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#6E5BC3] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        
                        <input type="text" x-model="search" @input.debounce.400ms="fetchAktivitas()" placeholder="Cari aktivitas..." 
                            class="w-full pl-10 pr-9 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-full text-xs font-medium text-[#6E5BC3] placeholder-[#6E5BC3] focus:outline-none focus:border-[#6E5BC3] transition-all shadow-2xs">

                        <template x-if="search">
                            <button @click="search = ''; fetchAktivitas();" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6E5BC3] hover:text-[#5C4AB5]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                    </div>

                    @if($isKetuaProyek)
                        <button type="button" @click="$dispatch('open-tambah-aktivitas')" class="px-5 py-2.5 bg-[#6E5BC3] text-white rounded-full text-xs font-semibold hover:bg-[#5C4AB5] transition-all flex items-center gap-2 shadow-sm shadow-[#6E5BC3]/30 shrink-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Aktivitas</span>
                        </button>
                    @endif
                </div>

                {{-- Kontrol Layar Kecil (< xl): Berurutan ke Bawah Memanjang Penuh --}}
                <div class="flex xl:hidden flex-col gap-2.5 w-full">
                    @if($isKetuaProyek)
                        {{-- 1. Tombol Tambah Aktivitas Baru (Memanjang Penuh) --}}
                        <button type="button" @click="$dispatch('open-tambah-aktivitas')" 
                            class="w-full py-2.5 px-4 bg-[#6E5BC3] text-white rounded-full text-xs font-semibold hover:bg-[#5C4AB5] transition-all flex items-center justify-center gap-2 shadow-sm shadow-[#6E5BC3]/25 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Aktivitas Baru</span>
                        </button>
                    @endif

                    {{-- 2. Input Cari Aktivitas (Memanjang Penuh) --}}
                    <div class="relative w-full">
                        <div class="flex items-center gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] focus-within:bg-white border border-purple-200 hover:border-purple-300 focus-within:border-[#6E5BC3] rounded-full text-xs font-normal text-[#6E5BC3] transition-all shadow-2xs w-full">
                            <span class="text-[#6E5BC3] shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" x-model="search" @input.debounce.400ms="fetchAktivitas()" placeholder="Cari aktivitas..." 
                                class="bg-transparent border-none focus:outline-none text-xs font-normal text-gray-800 placeholder:text-[#6E5BC3]/70 w-full p-0 focus:ring-0">
                            <button type="button" x-show="search" @click="search = ''; fetchAktivitas();" class="text-[#6E5BC3] hover:text-[#524397] transition-colors shrink-0 cursor-pointer" title="Hapus pencarian">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- 3. Filter Status Dropdown (Memanjang Penuh & Rapat) --}}
                    <div id="mobile-filter-container" class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" 
                            type="button" 
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-purple-300 rounded-full text-xs font-normal text-[#6E5BC3] transition-all cursor-pointer shadow-2xs">
                            <div class="flex items-center gap-2 truncate">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                <span class="font-normal text-gray-700">Filter: <span class="text-[#6E5BC3] font-normal">{{ $statuses[$currentStatus] ?? 'Semua Aktivitas' }}</span></span>
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
                                <a href="{{ url()->current() }}?status={{ $key }}{{ request('search') ? '&search='.request('search') : '' }}"
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
                </div>
            </div>

            {{-- TABEL / DATATABLE --}}
            <div class="datatable-container-wrapper overflow-hidden [&>div]:border-0 [&>div]:rounded-none [&>div]:shadow-none [&>div]:p-0 [&>div]:gap-4">
                <x-datatable :paginator="$aktivitasProyek ?? null" item-name="data aktivitas">
                    
                    <x-slot name="tabs">
                        {{-- TAB FILTER HORIZONTAL DESKTOP (Hanya Tampil di Layar >= xl) --}}
                        <div class="hidden xl:flex items-center gap-3 px-2 flex-wrap mb-2 filter-tabs-container">
                            @foreach($statuses as $key => $label)
                                <a href="{{ url()->current() }}?status={{ $key }}{{ request('search') ? '&search='.request('search') : '' }}" 
                                   class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus == $key ? 'bg-[#6E5BC3]/10 text-[#6E5BC3]' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                                    {{ $label }}
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#6E5BC3] text-white' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $counts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </x-slot>

                    <x-slot name="header">
                        <th class="py-3.5 pl-5 pr-2 text-center w-[5%]">No</th>
                        <th class="py-3.5 px-3 w-[18%]">Nama Aktivitas</th>
                        <th class="py-3.5 px-3 w-[22%]">Deskripsi</th>
                        <th class="py-3.5 px-3 w-[15%]">Penanggung Jawab</th>
                        <th class="py-3.5 px-3 w-[10%]">Mulai</th>
                        <th class="py-3.5 px-3 w-[10%]">Selesai</th>
                        <th class="py-3.5 px-3 text-center w-[7%]">Progress</th>
                        <th class="py-3.5 px-3 text-center w-[7%]">Status</th>
                        <th class="py-3.5 pl-3 pr-6 text-right w-[6%]">Aksi</th>
                    </x-slot>

                    @forelse($aktivitasProyek ?? [] as $index => $item)
                    @php
                        $statusAktif = $item->status_aktivitas ?? 'belum_dimulai';
                        $statusClass = match($statusAktif) {
                            'selesai'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                            'berjalan'  => 'bg-blue-50 text-blue-600 border-blue-100',
                            default     => 'bg-amber-50 text-amber-600 border-amber-100'
                        };
                    @endphp
                    
                    {{-- BARIS TABEL DESKTOP (Hanya Tampil di Layar >= xl) --}}
                    <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden xl:table-row border-b border-gray-100 last:border-none">
                        <td class="py-4 pl-5 pr-2 text-center text-gray-500 font-bold text-xs">
                            {{ $aktivitasProyek->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-3 font-normal text-gray-900 text-xs">
                            {{ $item->nama_aktivitas ?? '-' }}
                        </td>
                        <td class="py-4 px-3 text-xs text-gray-900 font-normal leading-snug">
                            {{ $item->deskripsi_aktivitas ?? '-' }}
                        </td>
                        <td class="py-4 px-3 text-xs text-gray-700 font-semibold">
                            {{ $item->penanggungJawab->nama ?? '-' }}
                        </td>
                        <td class="py-4 px-3 text-xs text-gray-600 font-medium whitespace-nowrap">
                            {{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-3 text-xs text-gray-600 font-medium whitespace-nowrap">
                            {{ $item->tanggal_target_selesai ? \Carbon\Carbon::parse($item->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}
                        </td>
                        <td class="py-4 px-3 text-center text-xs text-gray-700 font-medium whitespace-nowrap">{{ number_format($item->target ?? 0, 0) }}%</td>
                        <td class="py-4 px-3 text-center align-middle">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase border inline-block whitespace-nowrap {{ $statusClass }}">
                                {{ ucwords(str_replace('_', ' ', $statusAktif)) }}
                            </span>
                        </td>
                        <td class="py-4 pl-3 pr-6 text-right align-middle">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Tombol Detail Aktivitas Mengirim Array Dokumen Berserta URL Fisiknya --}}
                                <button type="button" 
                                    @click="$dispatch('open-modal-detail-aktivitas', {
                                        nama: '{{ addslashes($item->nama_aktivitas) }}',
                                        pj: '{{ addslashes($item->penanggungJawab->nama ?? '-') }}',
                                        pm: '{{ addslashes($proyek->ketuaProyek->nama ?? '-') }}',
                                        progress: {{ $item->target ?? 0 }},
                                        status: '{{ $item->status_aktivitas }}',
                                        tglMulai: '{{ $item->tanggal_mulai }}',
                                        tglSelesai: '{{ $item->tanggal_target_selesai }}',
                                        kendalaInternal: @js($item->kendala_internal ?? []),
                                        kendalaEksternal: @js($item->kendala_eksternal ?? []),
                                        dokumen: [
                                            @foreach($item->dokumenPendukung as $docItem)
                                                {
                                                    nama_dokumen: '{{ addslashes($docItem->nama_dokumen) }}',
                                                    url: '{{ asset('storage/' . $docItem->file_path) }}'
                                                },
                                            @endforeach
                                        ]
                                    })"
                                    class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all shadow-xs cursor-pointer"
                                    title="Detail Aktivitas">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                @if($isKetuaProyek)
                                    <button type="button" 
                                        @click="$dispatch('open-modal-edit-aktivitas', { 
                                            id: '{{ $item->id_aktivitas }}', 
                                            nama: '{{ addslashes($item->nama_aktivitas) }}', 
                                            deskripsi: '{{ addslashes($item->deskripsi_aktivitas) }}', 
                                            pj: '{{ $item->id_penanggung_jawab }}', 
                                            pjNama: '{{ addslashes($item->penanggungJawab->nama ?? '') }}',
                                            tglMulai: '{{ $item->tanggal_mulai }}', 
                                            tglSelesai: '{{ $item->tanggal_target_selesai }}' 
                                        })" 
                                        class="p-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all shadow-xs cursor-pointer" 
                                        title="Edit Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <button type="button" 
                                        @click="$dispatch('open-modal-hapus-aktivitas', { url: '{{ route('anggota.aktivitas.destroy', $item->id_aktivitas) }}' })" 
                                        class="p-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-xs cursor-pointer" 
                                        title="Hapus Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" @click="$dispatch('open-modal-lapor-progress', { id: '{{ $item->id_aktivitas }}', nama: '{{ addslashes($item->nama_aktivitas) }}', progress: '{{ $item->target ?? 0 }}' })" class="px-2.5 py-1.5 rounded-xl bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] transition-all text-[10px] font-semibold shadow-xs shadow-[#6E5BC3]/20 inline-flex items-center gap-1 cursor-pointer" title="Laporkan Progress">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span>Lapor Progress</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    {{-- BARIS KHUSUS CARD (Tampilan saat Layar Diperkecil < xl) --}}
                    <tr class="xl:hidden border-b border-purple-100/40 last:border-none">
                        <td colspan="9" class="p-2.5 sm:p-3 bg-white">
                            <div class="bg-[#F8F7FF]/50 hover:bg-[#F8F7FF] border border-purple-100 rounded-[22px] p-4 flex flex-col gap-3.5 shadow-2xs transition-all">
                                {{-- Baris Atas: Indikator Status di Sisi Kiri Atas & Icon Aksi di Kanan Atas --}}
                                <div class="flex items-center justify-between gap-2">
                                    {{-- Indikator Status di Kiri Atas --}}
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border tracking-wider {{ $statusClass }}">
                                        {{ ucwords(str_replace('_', ' ', $statusAktif)) }}
                                    </span>

                                    {{-- Icon Aksi di Kanan Atas --}}
                                    <div class="flex items-center gap-1.5">
                                        {{-- Tombol Detail Aktivitas --}}
                                        <button type="button" 
                                            @click="$dispatch('open-modal-detail-aktivitas', {
                                                nama: '{{ addslashes($item->nama_aktivitas) }}',
                                                pj: '{{ addslashes($item->penanggungJawab->nama ?? '-') }}',
                                                pm: '{{ addslashes($proyek->ketuaProyek->nama ?? '-') }}',
                                                progress: {{ $item->target ?? 0 }},
                                                status: '{{ $item->status_aktivitas }}',
                                                tglMulai: '{{ $item->tanggal_mulai }}',
                                                tglSelesai: '{{ $item->tanggal_target_selesai }}',
                                                kendalaInternal: @js($item->kendala_internal ?? []),
                                                kendalaEksternal: @js($item->kendala_eksternal ?? []),
                                                dokumen: [
                                                    @foreach($item->dokumenPendukung as $docItem)
                                                        {
                                                            nama_dokumen: '{{ addslashes($docItem->nama_dokumen) }}',
                                                            url: '{{ asset('storage/' . $docItem->file_path) }}'
                                                        },
                                                    @endforeach
                                                ]
                                            })"
                                            class="w-8 h-8 rounded-full bg-white text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white border border-purple-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" 
                                            title="Detail Aktivitas">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        @if($isKetuaProyek)
                                            {{-- Tombol Edit --}}
                                            <button type="button" 
                                                @click="$dispatch('open-modal-edit-aktivitas', { 
                                                    id: '{{ $item->id_aktivitas }}', 
                                                    nama: '{{ addslashes($item->nama_aktivitas) }}', 
                                                    deskripsi: '{{ addslashes($item->deskripsi_aktivitas) }}', 
                                                    pj: '{{ $item->id_penanggung_jawab }}', 
                                                    pjNama: '{{ addslashes($item->penanggungJawab->nama ?? '') }}',
                                                    tglMulai: '{{ $item->tanggal_mulai }}', 
                                                    tglSelesai: '{{ $item->tanggal_target_selesai }}' 
                                                })" 
                                                class="w-8 h-8 rounded-full bg-white text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white border border-purple-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" 
                                                title="Edit Aktivitas">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            <button type="button" 
                                                @click="$dispatch('open-modal-hapus-aktivitas', { url: '{{ route('anggota.aktivitas.destroy', $item->id_aktivitas) }}' })" 
                                                class="w-8 h-8 rounded-full bg-white text-rose-500 hover:bg-rose-500 hover:text-white border border-rose-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" 
                                                title="Hapus Aktivitas">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" 
                                                @click="$dispatch('open-modal-lapor-progress', { id: '{{ $item->id_aktivitas }}', nama: '{{ addslashes($item->nama_aktivitas) }}', progress: '{{ $item->target ?? 0 }}' })" 
                                                class="px-3 py-1.5 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] transition-all text-xs font-semibold shadow-xs shadow-[#6E5BC3]/20 inline-flex items-center gap-1.5 cursor-pointer" 
                                                title="Laporkan Progress">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span>Lapor Progress</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Baris Utama: Ikon Aktivitas di Kiri & Konten di Kanan (Lurus Sejajar) --}}
                                <div class="flex items-start gap-3.5">
                                    {{-- Ikon Aktivitas --}}
                                    <div class="w-10 h-10 rounded-2xl bg-white border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0 shadow-2xs mt-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                    </div>

                                    {{-- Samping Ikon: Nama Aktivitas, Penanggung Jawab, Tanggal, Progress --}}
                                    <div class="flex-1 min-w-0 flex flex-col gap-1.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="text-[15px] font-bold text-gray-900 wrap-break-word leading-snug">{{ $item->nama_aktivitas ?? '-' }}</h4>
                                        </div>
                                        
                                        {{-- Penanggung Jawab (dengan ikon orang) --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ $item->penanggungJawab->nama ?? '-' }}</span>
                                            <span class="text-[10px] text-gray-400 font-normal">(PJ)</span>
                                        </div>

                                        {{-- Info Periode Tanggal (dengan ikon kalender) --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') : '-' }} s/d {{ $item->tanggal_target_selesai ? \Carbon\Carbon::parse($item->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}</span>
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="flex flex-col gap-1 pt-1">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-400 font-medium">Progress</span>
                                                <span class="font-bold text-[#6E5BC3]">{{ number_format($item->target ?? 0, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-white border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                                <div class="bg-[#6E5BC3] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, $item->target ?? 0)) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Desain Deskripsi Aktivitas --}}
                                <div class="bg-white/90 rounded-2xl p-3 border border-purple-100/70 shadow-2xs">
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        <span>Deskripsi Aktivitas</span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed text-left wrap-break-word font-normal">
                                        {{ $item->deskripsi_aktivitas ?? 'Deskripsi aktivitas belum diinputkan' }}
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="bg-white">
                            <x-emptystate 
                                :border="false" 
                                padding="py-16 px-4" 
                                title="Tidak Ada Aktivitas Ditemukan" 
                                message="Tidak ada aktivitas yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                            />
                        </td>
                    </tr>
                    @endforelse

                </x-datatable>
            </div>

        </div>

    </div>

    {{-- PEMANGGILAN MODAL TERPISAH (Hanya modal CRUD jika ketua, dan modal lapor progress) --}}
    @if($isKetuaProyek)
        @include('anggota.modals.tambahaktivitas')
        @include('anggota.modals.editaktivitas')
        @include('anggota.modals.hapusaktivitas')
    @else
        @include('anggota.modals.laporprogress')
    @endif

    {{-- INCLUDE MODAL DETAIL AKTIVITAS TERPISAH --}}
    @include('anggota.detailaktivitas')

    {{-- CSS KUSTOM --}}
    <style>
        .pj-dropdown-scroll { scrollbar-width: thin; scrollbar-color: #9E8CE3 #F8F7FF; }
        .custom-date-input::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.7; }
    </style>
</x-layoutfull>