<x-layoutfull title="Detail Proyek & Aktivitas" :showNotification="false">

    {{-- SLOT HEADER ACTION: TOMBOL KEMBALI BERSIH & SEJAJAR --}}
    <x-slot name="headerAction">
        <a href="{{ route('anggota.proyekaktivitas') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-light backdrop-blur-sm transition-all border border-white/15 shadow-xs">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Kembali ke Daftar Proyek</span>
        </a>
    </x-slot>

    {{-- SLOT HERO: INFORMASI PROYEK & JUDUL ELEGAN --}}
    <x-slot name="hero">
        <div class="flex flex-col gap-1 text-white pt-1">
            <h1 class="text-xl sm:text-2xl font-light tracking-wide text-white">
                {{ $proyek->nama_proyek }}
            </h1>
            <p class="text-xs sm:text-sm text-purple-100/90 font-light">
                Kelola seluruh penugasan, jadwal, dan progres aktivitas proyek.
            </p>
        </div>
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
            url.searchParams.delete('page');
            
            fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.getElementById('aktivitas-table-wrapper');
                    const targetTable = document.getElementById('aktivitas-table-wrapper');
                    if (newTable && targetTable) {
                        targetTable.innerHTML = newTable.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetTable);
                        }
                    }
                    const newTabs = doc.getElementById('aktivitas-tabs-wrapper');
                    const targetTabs = document.getElementById('aktivitas-tabs-wrapper');
                    if (newTabs && targetTabs) {
                        targetTabs.innerHTML = newTabs.innerHTML;
                    }
                    const newFilter = doc.getElementById('aktivitas-mobile-filter');
                    const targetFilter = document.getElementById('aktivitas-mobile-filter');
                    if (newFilter && targetFilter) {
                        targetFilter.innerHTML = newFilter.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetFilter);
                        }
                    }
                    window.history.pushState({}, '', url.toString());
                })
                .catch(err => console.error('Error fetching search:', err));
        }
    }" class="w-full">
        
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

        {{-- TABEL / DATATABLE TUNGGAL DENGAN KARTU TERSATU PADU --}}
        <div id="aktivitas-table-wrapper" class="w-full">
            <x-datatable :paginator="$aktivitasProyek ?? null" item-name="data aktivitas" breakpoint="xl" :card="false">
                
                {{-- HEADER KARTU: TABS FILTER & PENCARIAN + TOMBOL AKSI LANGSUNG MENYATU DI KARTU TABEL --}}
                <x-slot name="cardHeader">
                    {{-- Kontrol Desktop (Layar >= xl) --}}
                    <div class="hidden xl:flex items-center justify-between gap-4 px-6 pt-4 pb-0 bg-white">
                        {{-- Underline Tabs --}}
                        <div id="aktivitas-tabs-wrapper" class="flex items-center gap-6 overflow-x-auto overflow-y-hidden no-scrollbar text-xs font-light">
                            @foreach($statuses as $key => $label)
                                <a href="{{ url()->current() }}?status={{ $key }}{{ request('search') ? '&search='.request('search') : '' }}"
                                   class="pb-3 flex items-center gap-2 transition-all relative whitespace-nowrap {{ $currentStatus == $key ? 'text-[#6E5BC3] border-b-2 border-[#6E5BC3] font-normal -mb-px' : 'text-gray-500 hover:text-gray-800 font-light' }}">
                                    <span>{{ $label }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-light {{ $currentStatus == $key ? 'bg-purple-100 text-[#6E5BC3]' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $counts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>

                        {{-- Kotak Pencarian & Tombol Tambah --}}
                        <div class="flex items-center gap-3 pb-3">
                            <div class="flex items-center gap-2 px-3.5 py-2 bg-gray-50/70 border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-xl text-xs transition-all shadow-2xs w-64">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" 
                                    x-model="search" 
                                    @input.debounce.400ms="fetchAktivitas()"
                                    placeholder="Cari aktivitas..." 
                                    autocomplete="off"
                                    class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0 font-light">
                                <button type="button" x-show="search" @click="search = ''; fetchAktivitas();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            @if($isKetuaProyek)
                                <x-button @click="$dispatch('open-tambah-aktivitas')" class="shrink-0 whitespace-nowrap py-2 px-4 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span>Tambah Aktivitas</span>
                                </x-button>
                            @endif
                        </div>
                    </div>

                    {{-- Kontrol Mobile / Tablet (< xl) --}}
                    <div class="flex xl:hidden flex-col gap-3 p-4 bg-white">
                        @if($isKetuaProyek)
                            <x-button @click="$dispatch('open-tambah-aktivitas')" class="w-full justify-center py-2.5 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Tambah Aktivitas</span>
                            </x-button>
                        @endif

                        <div class="w-full flex items-center gap-2 px-3.5 py-2.5 bg-gray-50/70 border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-xl text-xs transition-all shadow-2xs">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" 
                                x-model="search" 
                                @input.debounce.400ms="fetchAktivitas()"
                                placeholder="Cari aktivitas..." 
                                autocomplete="off"
                                class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0 font-light">
                            <button type="button" x-show="search" @click="search = ''; fetchAktivitas();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Filter Dropdown Mobile --}}
                        <div id="aktivitas-mobile-filter" class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" 
                                type="button" 
                                class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border rounded-xl text-xs font-normal text-gray-700 transition-all cursor-pointer shadow-2xs focus:outline-none"
                                :class="open ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                                <div class="flex items-center gap-2 truncate">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    <span class="{{ $currentStatus === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light' }}">{{ $statuses[$currentStatus] ?? 'Semua Aktivitas' }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-light bg-[#604EE6] text-white">
                                        {{ $counts[$currentStatus] ?? 0 }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 right-0 mt-2 w-full bg-white border border-gray-100 rounded-xl shadow-lg py-1.5 z-50 overflow-hidden text-xs">
                                @foreach($statuses as $key => $label)
                                    <a href="{{ url()->current() }}?status={{ $key }}{{ request('search') ? '&search='.request('search') : '' }}"
                                       class="flex items-center justify-between px-4 py-2 hover:bg-[#F8F7FF] transition-colors {{ $currentStatus == $key ? 'bg-purple-50 text-[#604EE6] font-light' : 'text-gray-700 font-light' }}">
                                        <div class="flex items-center gap-2">
                                            @if($currentStatus == $key)
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6]"></span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-transparent"></span>
                                            @endif
                                            <span>{{ $label }}</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-light {{ $currentStatus == $key ? 'bg-[#604EE6] text-white' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $counts[$key] ?? 0 }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </x-slot>

                <x-slot name="header">
                    <th class="py-3.5 pl-5 pr-2 text-center w-[4%] font-semibold text-gray-500">No</th>
                    <th class="py-3.5 px-3 w-[23%] font-semibold text-gray-500">Nama Aktivitas</th>
                    <th class="py-3.5 px-3 w-[21%] font-semibold text-gray-500">Deskripsi</th>
                    <th class="py-3.5 px-3 w-[17%] font-semibold text-gray-500">Penanggung Jawab</th>
                    <th class="py-3.5 px-3 w-[8%] font-semibold text-gray-500">Mulai</th>
                    <th class="py-3.5 px-3 w-[8%] font-semibold text-gray-500">Selesai</th>
                    <th class="py-3.5 px-3 text-center w-[9%] font-semibold text-gray-500">Progress</th>
                    <th class="py-3.5 px-3 text-center w-[5%] font-semibold text-gray-500">Status</th>
                    <th class="py-3.5 pl-3 pr-6 text-right w-[5%] font-semibold text-gray-500">Aksi</th>
                </x-slot>

                    @forelse($aktivitasProyek ?? [] as $index => $item)
                    @php
                        $statusAktif = $item->status_aktivitas ?? 'belum_dimulai';
                        $statusConfig = match($statusAktif) {
                            'selesai'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
                            'terlambat' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => 'Terlambat'],
                            'berjalan'  => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200/60', 'dot' => 'bg-blue-500', 'label' => 'Sedang Berjalan'],
                            default     => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200/60', 'dot' => 'bg-amber-500', 'label' => 'Belum Dimulai']
                        };
                    @endphp
                    
                    {{-- BARIS TABEL DESKTOP (Hanya Tampil di Layar >= xl) --}}
                    <tr class="bg-white hover:bg-[#F9F8FF] transition-colors align-middle hidden xl:table-row border-b border-gray-100 last:border-none">
                        <td class="py-3.5 pl-5 pr-2 text-center text-gray-400 font-light text-xs align-middle">
                            {{ $aktivitasProyek->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-3 font-light text-gray-800 text-xs align-middle">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="leading-snug wrap-break-word">{{ $item->nama_aktivitas ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-3 text-xs text-gray-600 font-light leading-relaxed align-middle">
                            @if(!empty(trim($item->deskripsi_aktivitas ?? '')))
                                <span class="line-clamp-2" title="{{ $item->deskripsi_aktivitas }}">{{ $item->deskripsi_aktivitas }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span>Deskripsi belum diinputkan</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 align-middle">
                            @if($item->penanggungJawab)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-light text-gray-800 text-xs truncate">{{ $item->penanggungJawab->nama }}</span>
                                        <span class="text-[10px] text-gray-400 font-light">PJ Aktivitas</span>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span>Belum Ditunjuk</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-xs text-gray-600 font-light whitespace-nowrap align-middle">
                            @if($item->tanggal_mulai)
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span>Belum diatur</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 text-xs text-gray-600 font-light whitespace-nowrap align-middle">
                            @if($item->tanggal_target_selesai)
                                {{ \Carbon\Carbon::parse($item->tanggal_target_selesai)->translatedFormat('d M Y') }}
                            @else
                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                    <span>Belum diatur</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-3 align-middle text-center">
                            <div class="flex items-center gap-2 justify-center">
                                <div class="w-16 bg-gray-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                    <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($item->target ?? 0))) }}%;"></div>
                                </div>
                                <span class="text-xs font-light text-gray-700 w-8 text-left shrink-0">{{ number_format($item->target ?? 0, 0) }}%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-3 text-center align-middle">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-light border whitespace-nowrap {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                <span>{{ $statusConfig['label'] }}</span>
                            </span>
                        </td>
                        <td class="py-3.5 pl-3 pr-6 text-right align-middle">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Tombol Detail Aktivitas Mengirim Array Dokumen Berserta URL Fisiknya --}}
                                <button type="button" 
                                    @click="$dispatch('open-modal-detail-aktivitas', {
                                        nama: '{{ addslashes($item->nama_aktivitas) }}',
                                        pj: '{{ addslashes($item->penanggungJawab->nama ?? 'Belum Ditunjuk') }}',
                                        pm: '{{ addslashes($proyek->ketuaProyek->nama ?? 'Belum Ditunjuk') }}',
                                        progress: {{ $item->target ?? 0 }},
                                        status: '{{ $item->status_aktivitas }}',
                                        tglMulai: '{{ $item->tanggal_mulai }}',
                                        tglSelesai: '{{ $item->tanggal_target_selesai }}',
                                        kendalaInternal: @js($item->kendala_internal ?? []),
                                        kendalaEksternal: @js($item->kendala_eksternal ?? []),
                                        riwayatProgress: @js($item->riwayat_progress ?? []),
                                        dokumen: [
                                            @foreach($item->dokumenPendukung as $docItem)
                                                {
                                                    nama_dokumen: '{{ addslashes($docItem->nama_dokumen) }}',
                                                    url: '{{ asset('storage/' . $docItem->file_path) }}'
                                                },
                                            @endforeach
                                        ]
                                    })"
                                    class="w-7 h-7 rounded-lg bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white flex items-center justify-center transition-all shadow-2xs cursor-pointer"
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
                                        class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all shadow-2xs cursor-pointer" 
                                        title="Edit Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <button type="button" 
                                        @click="$dispatch('open-modal-hapus-aktivitas', { url: '{{ route('anggota.aktivitas.destroy', $item->id_aktivitas) }}' })" 
                                        class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white flex items-center justify-center transition-all shadow-2xs cursor-pointer" 
                                        title="Hapus Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" @click="$dispatch('open-modal-lapor-progress', { id: '{{ $item->id_aktivitas }}', nama: '{{ addslashes($item->nama_aktivitas) }}', progress: '{{ $item->target ?? 0 }}' })" class="px-2.5 py-1.5 rounded-lg bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] transition-all text-[11px] font-medium shadow-xs shadow-[#6E5BC3]/20 inline-flex items-center gap-1 cursor-pointer" title="Laporkan Progress">
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
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-light border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                        <span>{{ $statusConfig['label'] }}</span>
                                    </span>

                                    {{-- Icon Aksi di Kanan Atas --}}
                                    <div class="flex items-center gap-1.5">
                                        {{-- Tombol Detail Aktivitas --}}
                                        <button type="button" 
                                            @click="$dispatch('open-modal-detail-aktivitas', {
                                                nama: '{{ addslashes($item->nama_aktivitas) }}',
                                                pj: '{{ addslashes($item->penanggungJawab->nama ?? 'Belum Ditunjuk') }}',
                                                pm: '{{ addslashes($proyek->ketuaProyek->nama ?? 'Belum Ditunjuk') }}',
                                                progress: {{ $item->target ?? 0 }},
                                                status: '{{ $item->status_aktivitas }}',
                                                tglMulai: '{{ $item->tanggal_mulai }}',
                                                tglSelesai: '{{ $item->tanggal_target_selesai }}',
                                                kendalaInternal: @js($item->kendala_internal ?? []),
                                                kendalaEksternal: @js($item->kendala_eksternal ?? []),
                                                riwayatProgress: @js($item->riwayat_progress ?? []),
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
                                            <h4 class="text-[14px] font-normal text-gray-800 wrap-break-word leading-snug">{{ $item->nama_aktivitas ?? '-' }}</h4>
                                        </div>
                                        
                                        {{-- Penanggung Jawab (dengan ikon orang) --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            @if($item->penanggungJawab)
                                                <span class="font-light text-gray-700">{{ $item->penanggungJawab->nama }}</span>
                                                <span class="text-[10px] text-gray-400 font-light">(PJ)</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                    <span>Belum Ditunjuk</span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Info Periode Tanggal (dengan ikon kalender) --}}
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500 font-light">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            @if($item->tanggal_mulai && $item->tanggal_target_selesai)
                                                <span>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_target_selesai)->translatedFormat('d M Y') }}</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                    <span>Belum diatur</span>
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Progress Bar --}}
                                        <div class="flex flex-col gap-1.5 pt-1">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="text-gray-500 font-light">Progress</span>
                                                <span class="font-light text-gray-700">{{ number_format($item->target ?? 0, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                                <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" style="width: {{ min(100, max(0, floatval($item->target ?? 0))) }}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Desain Deskripsi Aktivitas --}}
                                <div class="bg-white/90 rounded-2xl p-3 border border-purple-100/70 shadow-2xs">
                                    <div class="flex items-center gap-1.5 text-[10px] font-normal text-gray-400 uppercase tracking-wider mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        <span>Deskripsi Aktivitas</span>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed text-left wrap-break-word font-light">
                                        @if(!empty(trim($item->deskripsi_aktivitas ?? '')))
                                            {{ $item->deskripsi_aktivitas }}
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