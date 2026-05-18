<x-layoutadmin title="Manajemen Tim Kerja">
    <div x-data="{}">
        
        <x-slot name="headerTitle">
            <form action="{{ route('admin.manajementimkerja') }}" method="GET" id="searchForm" class="relative w-full">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari Nama Tim..." autocomplete="off"
                    class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
            </form>
        </x-slot>

        <div class="flex flex-col gap-4">
            <div class="flex justify-between items-end px-2">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Tim Kerja</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Pengaturan struktur dan kolaborasi tim di lingkungan SIS BPS.</p>
                </div>
                
                {{-- Tombol pemicu modal Tambah Tim --}}
                <button @click="$dispatch('open-modal-tambah')" 
                    class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Tim
                </button>
            </div>

            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden mt-2">
                
                {{-- Tab Status dengan Badge Warna Dinamis --}}
                <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-6">
                    @php $currentStatus = request('status', 'semua'); @endphp
                    @foreach(['semua' => 'Semua Tim', 'aktif' => 'Aktif', 'nonaktif' => 'Non-Aktif'] as $key => $label)
                        @php
                            // 1. Warna teks di dalam bulatan angka
                            if ($key == 'semua') $textColor = 'text-[#5C46F5]';
                            elseif ($key == 'aktif') $textColor = 'text-green-600';
                            else $textColor = 'text-red-600';

                            // 2. Warna background bulatan angka (menyala jika tab aktif)
                            $bgColor = 'bg-gray-100'; 
                            if ($currentStatus == $key) {
                                if ($key == 'semua') $bgColor = 'bg-[#5C46F5]/10';
                                elseif ($key == 'aktif') $bgColor = 'bg-green-100';
                                else $bgColor = 'bg-red-100';
                            }
                        @endphp

                        <a href="{{ route('admin.manajementimkerja', ['status' => $key]) }}" 
                           class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                            {{ $label }}
                            <span class="ml-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                <div id="data-container" class="px-4 pb-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                                <th class="px-6 py-6 text-center w-[5%]">No</th>
                                <th class="px-6 py-6 w-[25%]">Nama Tim</th>
                                <th class="px-6 py-6 w-[30%]">Deskripsi Tim Kerja</th>
                                <th class="px-6 py-6 w-[20%]">Ketua Tim</th>
                                <th class="px-6 py-6 text-center w-[10%]">Status</th>
                                <th class="px-6 py-6 text-center w-[10%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium text-sm">
                            @forelse($timKerja as $index => $tim)
                            <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                                <td class="px-6 py-6 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                                    {{ $timKerja->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-6 font-bold text-gray-800">
                                    {{ $tim->nama_tim }}
                                </td>
                                
                                {{-- KOLOM DESKRIPSI --}}
                                <td class="px-6 py-6">
                                    <div class="text-xs leading-relaxed line-clamp-2 {{ $tim->deskripsi_tim ? 'text-gray-500' : 'text-gray-400 italic' }}" 
                                         title="{{ $tim->deskripsi_tim ?? 'Deskripsi tim kerja belum diinputkan' }}">
                                        {{ $tim->deskripsi_tim ?? 'Deskripsi tim kerja belum diinputkan' }}
                                    </div>
                                </td>

                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[10px] font-bold shadow-sm shadow-[#5C46F5]/20">
                                            {{ strtoupper(substr($tim->ketua->nama ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $tim->ketua->nama ?? 'Tidak Ada' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $tim->status_tim == 'aktif' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                        {{ $tim->status_tim }}
                                    </span>
                                </td>
                                
                                {{-- KOLOM AKSI (Edit) --}}
                                <td class="px-6 py-6 text-center">
                                    <button type="button"
                                            @click="$dispatch('open-modal-edit-tim', { 
                                                id: '{{ $tim->id_tim }}', 
                                                nama: '{{ addslashes($tim->nama_tim) }}', 
                                                deskripsi: '{{ addslashes($tim->deskripsi_tim ?? '') }}', 
                                                ketua: '{{ $tim->id_ketua_tim ?? '' }}', 
                                                status: '{{ $tim->status_tim }}' 
                                            })" 
                                            class="text-gray-300 hover:text-amber-500 transition-colors p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-200">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-900 font-bold text-lg tracking-tight">Belum Ada Data Tim</h3>
                                        <p class="text-gray-400 font-medium text-sm mt-1">Data tim kerja akan muncul di sini setelah ditambahkan.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4 pt-6 border-t border-gray-50 flex items-center justify-between px-2 text-[11px] font-medium text-gray-400 uppercase tracking-widest">
                        <div>Menampilkan {{ $timKerja->firstItem() ?? 0 }}–{{ $timKerja->lastItem() ?? 0 }} dari {{ $timKerja->total() }} data tim</div>
                        <div>{{ $timKerja->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Include Modal --}}
        @include('admin.modals.tambahtimkerja')
        @include('admin.modals.edittimkerja')
        
    </div>
</x-layoutadmin>