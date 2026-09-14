<x-layoutfull title="Detail Proyek & Aktivitas">

    {{-- SLOT HEADER ACTION: TOMBOL KEMBALI BERSIH & SEJAJAR --}}
    <x-slot name="headerAction">
        <a href="{{ route('anggota.proyekaktivitas') }}" 
           class="text-white text-sm font-bold hover:opacity-80 transition-all flex items-center gap-1.5">
            <span>&larr; Kembali</span>
        </a>
    </x-slot>

    <div x-data="{}" class="flex flex-col gap-6 w-full">
        
        {{-- KOTAK PUTIH UTAMA --}}
        <div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 px-2">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">
                        Daftar Aktivitas Proyek ({{ $proyek->nama_proyek }})
                    </h1>
                </div>

                <div class="flex items-center gap-3 w-full md:w-auto">
                    {{-- Live Search --}}
                    <div class="relative flex-1 md:w-64 group/search" x-data="{ search: '{{ request('search') }}' }">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#5C46F5] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" x-model="search" 
                            @input.debounce.300ms="
                                const url = new URL(window.location.href);
                                if (search) { url.searchParams.set('search', search); } else { url.searchParams.delete('search'); }
                                fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                                    .then(res => res.text())
                                    .then(html => {
                                        const parser = new DOMParser();
                                        const doc = parser.parseFromString(html, 'text/html');
                                        const newTable = doc.querySelector('.datatable-container-wrapper') || doc.querySelector('x-datatable');
                                        if (newTable) {
                                            document.querySelector('.datatable-container-wrapper').innerHTML = newTable.innerHTML;
                                        }
                                        window.history.pushState({}, '', url.toString());
                                    });
                            "
                            placeholder="Cari aktivitas..." 
                            class="w-full pl-10 pr-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#5C46F5] rounded-2xl text-xs font-medium text-[#5C46F5] placeholder-[#5C46F5] focus:outline-none focus:border-[#5C46F5] transition-all">
                    </div>

                    @if($isKetuaProyek)
                        <button type="button" @click="$dispatch('open-tambah-aktivitas')" class="px-4 py-2.5 bg-[#5C46F5] text-white rounded-2xl text-xs font-semibold hover:bg-[#4A38D4] transition-all flex items-center gap-2 shadow-sm shadow-[#5C46F5]/30 shrink-0 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            <span>Tambah Aktivitas</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- TABEL / DATATABLE --}}
            <div class="datatable-container-wrapper overflow-hidden [&>div]:border-0 [&>div]:rounded-none [&>div]:shadow-none">
                <x-datatable :paginator="$aktivitasProyek ?? null" item-name="data aktivitas">
                    
                    <x-slot name="tabs">
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
                        <div class="flex items-center gap-3 px-2 flex-wrap mb-2 filter-tabs-container">
                            @foreach($statuses as $key => $label)
                                <a href="{{ url()->current() }}?status={{ $key }}{{ request('search') ? '&search='.request('search') : '' }}" 
                                   class="py-1.5 px-3 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $currentStatus == $key ? 'bg-[#5C46F5]/10 text-[#5C46F5]' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50' }}">
                                    {{ $label }}
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#5C46F5] text-white' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $counts[$key] ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </x-slot>

                    <x-slot name="header">
                        <th class="py-3.5 pl-5 pr-2 text-center w-[6%]">No</th>
                        <th class="py-3.5 px-3 w-[19%]">Nama Aktivitas</th>
                        <th class="py-3.5 px-3 w-[23%]">Deskripsi Aktivitas</th>
                        <th class="py-3.5 px-3 w-[16%]">Penanggung Jawab</th>
                        <th class="py-3.5 px-3 w-[11%]">Tanggal Mulai</th>
                        <th class="py-3.5 px-3 w-[11%]">Tanggal Selesai</th>
                        <th class="py-3.5 px-3 text-center w-[7%]">Progress</th>
                        <th class="py-3.5 px-3 text-center w-[8%]">Status</th>
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
                    
                    <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-top hidden md:table-row border-b border-gray-100 last:border-none">
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
                                        dokumen: [
                                            @foreach($item->dokumenPendukung as $docItem)
                                                {
                                                    nama_dokumen: '{{ addslashes($docItem->nama_dokumen) }}',
                                                    url: '{{ asset('storage/' . $docItem->file_path) }}'
                                                },
                                            @endforeach
                                        ]
                                    })"
                                    class="p-1.5 rounded-xl bg-purple-50 text-[#5C46F5] hover:bg-[#5C46F5] hover:text-white transition-all shadow-xs cursor-pointer"
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
                                        class="p-1.5 rounded-xl bg-purple-50 text-[#5C46F5] hover:bg-[#5C46F5] hover:text-white transition-all shadow-xs cursor-pointer" 
                                        title="Edit Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>

                                    <button type="button" 
                                        @click="$dispatch('open-hapus-aktivitas', { url: '{{ route('anggota.aktivitas.destroy', $item->id_aktivitas) }}' })" 
                                        class="p-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-xs cursor-pointer" 
                                        title="Hapus Aktivitas">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" @click="$dispatch('open-modal-lapor-progress', { id: '{{ $item->id_aktivitas }}', nama: '{{ addslashes($item->nama_aktivitas) }}', progress: '{{ $item->target ?? 0 }}' })" class="px-2.5 py-1.5 rounded-xl bg-purple-50 text-[#5C46F5] hover:bg-[#5C46F5] hover:text-white transition-all text-[10px] font-semibold shadow-xs cursor-pointer" title="Laporkan Progress">Lapor Progress</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center bg-white">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#5C46F5] shadow-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-700">Belum ada aktivitas tercatat</span>
                                    <span class="text-[11px] text-gray-400 font-normal mt-0.5">Aktivitas proyek ini akan muncul setelah ditambahkan.</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </x-datatable>
            </div>

        </div>

    </div>

    {{-- PEMANGGILAN MODAL TERPISAH (Hanya modal CRUD jika ketua, dan modal lapor progress) --}}
    @if($isKetuaProyek)
        {{-- Di sini muat modal tambah, edit, hapus kamu --}}
    @else
        @include('anggota.modals.laporprogress')
    @endif

    {{-- INCLUDE MODAL DETAIL AKTIVITAS TERPISAH --}}
    @include('anggota.modals.detailaktivitas')

    {{-- CSS KUSTOM --}}
    <style>
        .pj-dropdown-scroll { scrollbar-width: thin; scrollbar-color: #9E8CE3 #F8F7FF; }
        .custom-date-input::-webkit-calendar-picker-indicator { cursor: pointer; opacity: 0.7; }
    </style>
</x-layoutfull>