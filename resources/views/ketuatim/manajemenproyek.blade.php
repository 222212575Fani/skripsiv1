<x-layoututama title="Manajemen Proyek">
    <div x-data>
        
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

        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-end px-1">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Proyek</h1>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Kelola proyek tim, dan tetapkan Ketua Proyek!</p>
                </div>
                
                <x-button @click="$dispatch('open-modal-tambah-proyek')"> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Proyek Baru
                </x-button>
            </div>

            {{-- Memanggil Komponen Reusable <x-datatable> --}}
            <x-datatable :paginator="$proyeks ?? null" item-name="data proyek">
                
                {{-- Slot untuk Filter Tab Status --}}
                <x-slot name="tabs">
                    <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-5 overflow-x-auto">
                        @php $currentStatus = request('status', 'semua'); @endphp
                        @foreach([
                            'semua' => 'Semua Proyek', 
                            'belum_dimulai' => 'Belum Dimulai', 
                            'berjalan' => 'Sedang Berjalan', 
                            'selesai' => 'Selesai', 
                            'terlambat' => 'Terlambat'
                        ] as $key => $label)
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
                               class="pb-4 text-[11px] uppercase tracking-wider font-bold transition-all border-b-2 whitespace-nowrap {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                                {{ $label }}
                                <span class="ml-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                    {{ $counts[$key] ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </x-slot>

                {{-- Slot Definisi Header Tabel --}}
                <x-slot name="header">
                    <th class="px-4 py-4 text-center w-[5%]">No</th>
                    <th class="px-4 py-4 w-[16%]">Nama Proyek</th>
                    <th class="px-4 py-4 w-[20%]">Deskripsi</th>
                    <th class="px-4 py-4 w-[15%]">Ketua Proyek</th>
                    <th class="px-4 py-4 w-[11%]">Mulai</th>
                    <th class="px-4 py-4 w-[11%]">Selesai</th>
                    <th class="px-4 py-4 text-center w-[12%]">Status</th>
                    <th class="px-4 py-4 text-center w-[10%]">Aksi</th>
                </x-slot>

                {{-- Slot Isi Baris Tabel --}}
                @forelse($proyeks ?? [] as $index => $proyek)
                <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                    <td class="px-4 py-4 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                        {{ $proyeks->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-700 font-medium">
                        {{ $proyek->nama_proyek }}
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-500 font-normal">
                        {{ $proyek->deskripsi_proyek ?? '-' }}
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[10px] font-bold shadow-sm shrink-0">
                                {{ strtoupper(substr($proyek->ketuaProyek->nama ?? 'B', 0, 1)) }}
                            </div>
                            <span class="text-xs text-gray-600 font-medium truncate max-w-[100px]">{{ $proyek->ketuaProyek->nama ?? 'Belum Ditunjuk' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                        {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-600 font-medium whitespace-nowrap">
                        {{ $proyek->tanggal_target_selesai ? \Carbon\Carbon::parse($proyek->tanggal_target_selesai)->translatedFormat('d M Y') : '-' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        @php
                            $status = $proyek->status_proyek ?? 'belum_dimulai';
                            $statusClass = match($status) {
                                'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'berjalan' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'terlambat' => 'bg-rose-50 text-rose-600 border-rose-100',
                                default => 'bg-orange-100 text-orange-600 border-orange-200' 
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-[9px] font-semibold uppercase border inline-block whitespace-nowrap {{ $statusClass }}">
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button type="button"
                                    @click="$dispatch('open-modal-edit-proyek', { id: '{{ $proyek->id_proyek }}' })" 
                                    class="text-purple-300 hover:text-purple-500 transition-colors p-1.5 inline-flex items-center justify-center" title="Edit Proyek">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>

                            <form action="{{ route('ketuatim.manajemenproyek.destroy', $proyek->id_proyek) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus proyek ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-300 hover:text-rose-500 transition-colors p-1.5 inline-flex items-center justify-center" title="Hapus Proyek">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="py-20 text-center text-gray-400 font-bold text-xs">Data tidak ditemukan.</td></tr>
                @endforelse

            </x-datatable>
        </div>

        @include('ketuatim.modals.tambahproyek')
    </div>
</x-layoututama>