<x-layoutfull title="Detail Proyek & Aktivitas">

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
                                    class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
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
                                class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    <span class="{{ $currentStatus === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-light' }}">{{ $statuses[$currentStatus] ?? 'Semua Aktivitas' }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-light bg-[#604EE6] text-white">
                                        {{ $counts[$currentStatus] ?? 0 }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
                                       class="flex items-center justify-between px-4 py-2 hover:bg-[#F8F7FF] transition-colors {{ $currentStatus == $key ? 'bg-purple-50 text-[#604EE6] font-normal' : 'text-gray-700 font-light' }}">
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
                                    dokumen: @json($item->dokumenPendukung->map(fn($d) => [
                                        'nama' => $d->nama_dokumen,
                                        'url' => asset('storage/' . $d->file_path)
                                    ]))
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
                                    @click="$dispatch('open-edit-aktivitas', { 
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
                                    @click="$dispatch('open-hapus-aktivitas', { url: '{{ route('anggota.aktivitas.destroy', $item->id_aktivitas) }}' })" 
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

    {{-- KUMPULAN MODAL (TAMBAH, EDIT, HAPUS, DETAIL) --}}
    @if($isKetuaProyek)
        {{-- 1. MODAL TAMBAH AKTIVITAS --}}
        <div x-data="{ open: false, pj: '', pjNama: '', pjOpen: false, tglMulai: '' }" 
             @open-tambah-aktivitas.window="open = true" 
             @close-tambah-aktivitas.window="open = false"
             x-show="open" 
             x-cloak
             class="fixed inset-0 z-[999] overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="open = false" 
                     class="relative w-full max-w-2xl transform overflow-visible rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
                    
                    <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#F8F7FF] border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Tambah Aktivitas Baru</h3>
                                <p class="text-xs font-medium text-gray-400">Buat aktivitas baru dan tambahkan ke dalam proyek ini.</p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('anggota.aktivitas.store', $proyek->id_proyek) }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="p-8 space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Nama Aktivitas <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_aktivitas" placeholder="Masukkan nama aktivitas..." required autocomplete="off"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Aktivitas</label>
                                <textarea name="deskripsi_aktivitas" rows="3" placeholder="Tuliskan deskripsi atau ringkasan aktivitas..."
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab <span class="text-red-500">*</span></label>
                                @php
                                    $timId = $proyek->id_tim ?? null;
                                    $idKetuaTim = optional($proyek->timKerja)->id_ketua_tim
                                        ?? \App\Models\TimKerja::where('id_tim', $timId)->value('id_ketua_tim');
                                    $listAnggotaTim = \App\Models\AnggotaTim::with('pengguna')
                                        ->where('id_tim', $timId)->whereNull('tanggal_keluar')->get()
                                        ->filter(fn ($member) => $member->id_pengguna != $idKetuaTim);
                                @endphp
                                <div class="relative group/filter" @click.outside="pjOpen = false">
                                    <input type="hidden" name="id_penanggung_jawab" x-model="pj">
                                    <button type="button" @click="pjOpen = !pjOpen"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] rounded-2xl text-xs font-normal transition-all cursor-pointer">
                                        <span :class="pj ? 'text-gray-700 font-medium' : 'text-gray-400 font-light'" x-text="pjNama || 'Pilih Penanggung Jawab'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 group-hover/filter:text-[#6E5BC3] transition-all duration-200" :class="pjOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="pjOpen" x-cloak x-transition class="pj-dropdown-scroll absolute left-0 right-0 mt-2 bg-white border border-gray-100 rounded-[24px] shadow-[0_14px_28px_rgba(0,0,0,0.14)] p-2 z-[1000] space-y-1 max-h-44 overflow-y-auto">
                                        <div class="px-3 py-2 border-b border-gray-100 flex items-center gap-2 text-[#6E5BC3] text-xs font-normal">
                                            <span>Pilih Penanggung Jawab</span>
                                        </div>
                                        @forelse($listAnggotaTim as $member)
                                            @if($member->pengguna)
                                                <button type="button" @click="pj = '{{ $member->pengguna->id_pengguna }}'; pjNama = '{{ addslashes($member->pengguna->nama) }}'; pjOpen = false"
                                                    class="w-full flex items-center px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer"
                                                    :class="pj == '{{ $member->pengguna->id_pengguna }}' ? 'bg-[#F8F7FF] text-[#6E5BC3]' : 'text-gray-700 hover:bg-gray-50'">
                                                    <span>{{ $member->pengguna->nama }}</span>
                                                </button>
                                            @endif
                                        @empty
                                            <p class="px-3.5 py-2.5 text-xs text-gray-400">Tidak ada anggota tim yang dapat dipilih.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                                    @php
                                        $minMulai = max(date('Y-m-d'), $proyek->tanggal_mulai ?? date('Y-m-d'));
                                        $maxMulai = $proyek->tanggal_target_selesai ?? null;
                                    @endphp
                                    <input type="date" name="tanggal_mulai" required x-model="tglMulai"
                                        min="{{ $minMulai }}" @if($maxMulai) max="{{ $maxMulai }}" @endif
                                        class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Selesai (Target) <span class="text-red-500">*</span></label>
                                    @php $maxSelesai = $proyek->tanggal_target_selesai ?? null; @endphp
                                    <input type="date" name="tanggal_target_selesai" required 
                                        :min="tglMulai ? tglMulai : '{{ $minMulai }}'" @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                        class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                            <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white cursor-pointer">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white cursor-pointer">Simpan Aktivitas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. MODAL HAPUS AKTIVITAS --}}
        <div x-data="{ open: false, deleteUrl: '' }" 
             @open-hapus-aktivitas.window="open = true; deleteUrl = $event.detail.url" 
             @close-hapus-aktivitas.window="open = false"
             x-show="open" 
             x-cloak
             class="fixed inset-0 z-[999] overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="open = false" 
                     class="relative w-full max-w-sm transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100">
                    
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-center text-rose-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Hapus Aktivitas</h3>
                                <p class="text-xs font-medium text-gray-400">Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <p class="text-xs font-medium text-gray-600 leading-relaxed">
                            Apakah kamu yakin ingin menghapus data aktivitas ini secara permanen dari sistem?
                        </p>
                    </div>

                    <form :action="deleteUrl" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 bg-gray-50/50">
                            <x-button type="button" @click="open = false" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20" class="w-full flex-1">Batal</x-button>
                            <x-button type="submit" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20" class="w-full flex-1">Ya, Hapus</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. MODAL EDIT AKTIVITAS --}}
        <div x-data="{ 
                open: false, id: '', nama: '', deskripsi: '', pj: '', pjNama: '', pjOpen: false, tglMulai: '', tglSelesai: '' 
             }" 
             @open-edit-aktivitas.window="
                open = true; 
                id = $event.detail.id; 
                nama = $event.detail.nama; 
                deskripsi = $event.detail.deskripsi; 
                pj = $event.detail.pj; 
                pjNama = $event.detail.pjNama;
                tglMulai = $event.detail.tglMulai; 
                tglSelesai = $event.detail.tglSelesai;
             " 
             @close-edit-aktivitas.window="open = false"
             x-show="open" 
             x-cloak
             class="fixed inset-0 z-[999] overflow-y-auto" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="open = false" 
                     class="relative w-full max-w-2xl transform overflow-visible rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100">
                    
                    <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <path stroke="#6E5BC3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">Edit Data Aktivitas</h3>
                                <p class="text-xs font-medium text-gray-400">Ubah informasi nama, deskripsi, penanggung jawab, serta rentang tanggal aktivitas.</p>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form :action="'{{ url('anggota/aktivitas') }}/' + id" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-8 space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Nama Aktivitas <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_aktivitas" x-model="nama" required autocomplete="off"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Aktivitas</label>
                                <textarea name="deskripsi_aktivitas" x-model="deskripsi" rows="3"
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700 resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab <span class="text-red-500">*</span></label>
                                @php
                                    $timId = $proyek->id_tim ?? null;
                                    $idKetuaTim = optional($proyek->timKerja)->id_ketua_tim
                                        ?? \App\Models\TimKerja::where('id_tim', $timId)->value('id_ketua_tim');
                                    $listAnggotaTim = \App\Models\AnggotaTim::with('pengguna')
                                        ->where('id_tim', $timId)->whereNull('tanggal_keluar')->get()
                                        ->filter(fn ($member) => $member->id_pengguna != $idKetuaTim);
                                @endphp
                                <div class="relative group/filter" @click.outside="pjOpen = false">
                                    <input type="hidden" name="id_penanggung_jawab" x-model="pj">
                                    <button type="button" @click="pjOpen = !pjOpen"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white border border-gray-200 rounded-2xl text-xs font-normal cursor-pointer">
                                        <span :class="pj ? 'text-gray-700 font-medium' : 'text-gray-400 font-light'" x-text="pjNama || 'Pilih Penanggung Jawab'"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="pjOpen" x-cloak x-transition class="pj-dropdown-scroll absolute left-0 right-0 mt-2 bg-white border border-gray-100 rounded-[24px] shadow-lg p-2 z-[1000] space-y-1 max-h-44 overflow-y-auto">
                                        @foreach($listAnggotaTim as $member)
                                            @if($member->pengguna)
                                                <button type="button" @click="pj = '{{ $member->pengguna->id_pengguna }}'; pjNama = '{{ addslashes($member->pengguna->nama) }}'; pjOpen = false"
                                                    class="w-full flex items-center px-3.5 py-2.5 rounded-xl text-xs font-normal text-gray-700 hover:bg-gray-50 cursor-pointer">
                                                    <span>{{ $member->pengguna->nama }}</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @php
                                $minMulai = max(date('Y-m-d'), $proyek->tanggal_mulai ?? date('Y-m-d'));
                                $maxSelesai = $proyek->tanggal_target_selesai ?? null;
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_mulai" x-model="tglMulai" required 
                                        min="{{ $minMulai }}" @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                        class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Selesai (Target) <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_target_selesai" x-model="tglSelesai" required 
                                        :min="tglMulai ? tglMulai : '{{ $minMulai }}'" @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                        class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-medium text-gray-700">
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                            <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white cursor-pointer">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white cursor-pointer">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 4. MODAL DETAIL AKTIVITAS --}}
        <div x-data="{ 
                open: false, 
                nama: '', 
                pj: '', 
                pm: '', 
                progress: 0, 
                status: '', 
                tglMulai: '', 
                tglSelesai: '',
                kendalaInternal: [],
                kendalaEksternal: [],
                dokumen: [],
                riwayatProgress: []
            }" 
            @open-modal-detail-aktivitas.window="
                open = true;
                nama = $event.detail.nama;
                pj = $event.detail.pj;
                pm = $event.detail.pm;
                progress = $event.detail.progress;
                status = $event.detail.status;
                tglMulai = $event.detail.tglMulai;
                tglSelesai = $event.detail.tglSelesai;
                kendalaInternal = $event.detail.kendalaInternal || [];
                kendalaEksternal = $event.detail.kendalaEksternal || [];
                dokumen = $event.detail.dokumen || [];
                riwayatProgress = $event.detail.riwayatProgress || [];
            " 
            @close-modal-detail-aktivitas.window="open = false"
            x-show="open" 
            x-cloak
            class="fixed inset-0 z-[999] overflow-y-auto" 
            style="display: none;"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="open = false" 
                     class="relative w-full max-w-2xl transform overflow-hidden rounded-[28px] bg-white text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100 flex flex-col max-h-[90vh]">
                    
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100 bg-white sticky top-0 z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-xs shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-[#6E5BC3] uppercase tracking-wider">Detail Aktivitas</span>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight mt-0.5" x-text="nama"></h3>
                            </div>
                        </div>
                        <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Konten Modal --}}
                    <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar">
                        
                        {{-- Informasi Utama --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/60 p-4 rounded-2xl border border-purple-100/60">
                            <div>
                                <span class="text-[11px] font-bold text-gray-700 block mb-1">Penanggung Jawab</span>
                                <span class="text-xs font-normal text-gray-800 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="font-normal text-gray-700" x-text="pj"></span>
                                </span>
                            </div>
                            <div>
                                <span class="text-[11px] font-light text-gray-700 block mb-1">Ketua Proyek</span>
                                <span class="text-xs font-normal text-gray-800 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span class="font-normal text-gray-700" x-text="pm"></span>
                                </span>
                            </div>
                            <div class="sm:col-span-2 pt-2 border-t border-purple-100/60 flex items-center justify-between text-xs">
                                <span class="text-gray-700 flex items-center gap-1.5 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Rentang Waktu:
                                </span>
                                <span class="font-normal text-gray-700"><span x-text="tglMulai"></span> — <span x-text="tglSelesai"></span></span>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-medium text-gray-500">Persentase Progress</span>
                                <span class="font-bold text-[#604EE6]" x-text="(progress || 0) + '%'"></span>
                            </div>
                            <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" :style="`width: ${Math.min(100, Math.max(0, parseFloat(progress) || 0))}%`"></div>
                            </div>
                        </div>

                        {{-- Riwayat Pelaporan Progress (Format Tabel di dalam Container) --}}
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700">Riwayat Pelaporan Progress</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-purple-50 text-[#6E5BC3]" x-text="riwayatProgress.length + ' Laporan'"></span>
                            </div>

                            <div class="bg-white border border-purple-100/80 rounded-2xl overflow-hidden shadow-2xs">
                                <template x-if="riwayatProgress.length > 0">
                                    <div class="overflow-x-auto max-h-64 custom-scrollbar">
                                        <table class="w-full text-left border-collapse">
                                            <thead class="bg-gray-50/90 border-b border-gray-100 text-[10px] uppercase font-bold text-gray-500 tracking-wider sticky top-0 z-10 backdrop-blur-xs">
                                                <tr>
                                                    <th class="py-2.5 px-4 whitespace-nowrap">Waktu Pelaporan</th>
                                                    <th class="py-2.5 px-4 text-right whitespace-nowrap">Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 text-xs">
                                                <template x-for="(rp, idx) in riwayatProgress" :key="rp.id || idx">
                                                    <tr class="hover:bg-purple-50/20 transition-colors align-middle">
                                                        {{-- Tanggal & Waktu Pelaporan --}}
                                                        <td class="py-2.5 px-4 whitespace-nowrap align-middle">
                                                            <div class="flex items-center gap-2 text-gray-800 font-semibold text-xs">
                                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                                     <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                 </svg>
                                                                 <span x-text="rp.tanggal_lengkap || rp.tanggal"></span>
                                                            </div>
                                                        </td>

                                                        {{-- Progress --}}
                                                        <td class="py-2.5 px-4 text-right whitespace-nowrap align-middle">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#2EBD85] border border-emerald-100 text-xs font-bold" x-text="rp.progress + '%'"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </template>

                                <template x-if="riwayatProgress.length === 0">
                                    <div class="flex flex-col items-center justify-center py-7 px-4 gap-2 text-center bg-gray-50/40">
                                        <div class="w-9 h-9 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shadow-2xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500">Belum ada riwayat pelaporan progress untuk aktivitas ini.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Daftar Kendala --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-amber-50/40 border border-amber-100 rounded-2xl p-4 space-y-2">
                                <span class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    Kendala Internal
                                </span>
                                <template x-if="kendalaInternal.length > 0">
                                    <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                        <template x-for="item in kendalaInternal">
                                            <li x-text="item"></li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="kendalaInternal.length === 0">
                                    <p class="text-[11px] text-gray-500">Tidak ada kendala internal.</p>
                                </template>
                            </div>

                            <div class="bg-rose-50/40 border border-rose-100 rounded-2xl p-4 space-y-2">
                                <span class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Kendala Eksternal
                                </span>
                                <template x-if="kendalaEksternal.length > 0">
                                    <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                        <template x-for="item in kendalaEksternal">
                                            <li x-text="item"></li>
                                        </template>
                                    </ul>
                                </template>
                                <template x-if="kendalaEksternal.length === 0">
                                    <p class="text-[11px] text-gray-500">Tidak ada kendala eksternal.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Daftar Dokumen Pendukung --}}
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-gray-700 block">Daftar Dokumen Pendukung</span>
                            <div class="bg-gray-50/50 border border-purple-100/60 rounded-2xl p-4 max-h-48 overflow-y-auto custom-scrollbar">
                                <template x-if="dokumen.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="doc in dokumen">
                                            <a :href="doc.url || '#'" target="_blank" class="flex items-center justify-between p-2.5 bg-white hover:bg-purple-50/40 border border-gray-200 hover:border-purple-200 rounded-xl transition-all text-xs group/doc">
                                                <div class="flex items-center gap-2.5 truncate">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                    <span class="font-medium text-gray-700 group-hover/doc:text-[#6E5BC3] truncate" x-text="doc.nama"></span>
                                                </div>
                                                <span class="text-[10px] font-bold text-[#6E5BC3] bg-purple-50 px-2.5 py-1 rounded-lg shrink-0">Lihat</span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="dokumen.length === 0">
                                    <div class="flex flex-col items-center justify-center py-6 gap-2 text-center">
                                        <div class="w-10 h-10 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shadow-xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs text-gray-500">Belum ada dokumen pendukung yang dilampirkan.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- Footer Modal --}}
                    <div class="px-8 py-4 border-t border-gray-100 flex items-center justify-end bg-gray-50/50">
                        <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white transition-all cursor-pointer shadow-sm shadow-[#6E5BC3]/20">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include('anggota.modals.laporprogress')
    @endif

    {{-- CSS KUSTOM DATATABLE, HAPUS BULATAN NOMOR HALAMAN, & DATE PICKER --}}
    <style>
        .pj-dropdown-scroll {
            scrollbar-width: thin;
            scrollbar-color: #9E8CE3 #F8F7FF;
            scrollbar-gutter: stable;
        }
        .pj-dropdown-scroll::-webkit-scrollbar { width: 6px; }
        .pj-dropdown-scroll::-webkit-scrollbar-track { background: #F8F7FF; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb:hover { background: #6E5BC3; }
        
        .datatable-container-wrapper nav span[aria-current="page"] > span,
        .datatable-container-wrapper div[class*="rounded-full"],
        .datatable-container-wrapper .relative.inline-flex.items-center.px-4.py-2 {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .custom-date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(32%) sepia(85%) saturate(1450%) hue-rotate(230deg) brightness(95%) contrast(96%);
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }
        .custom-date-input::-webkit-calendar-picker-indicator:hover { opacity: 1; }
    </style>
</x-layoutfull>