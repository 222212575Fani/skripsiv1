<x-layoututama title="Manajemen Proyek">
    <div x-data="{}">
        
        {{-- HEADER SLOT: Pencarian --}}
        <x-slot name="headerTitle">
            <form action="{{ route('ketuatim.manajemenproyek') ?? '#' }}" method="GET" id="searchForm" class="relative w-full max-w-md">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari Nama Proyek..." autocomplete="off"
                    class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
            </form>
        </x-slot>

        <div class="flex flex-col gap-4">
            
            {{-- Judul & Tombol Tambah --}}
            <div class="flex justify-between items-end px-2">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Proyek</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Define proyek baru, tunjuk Ketua Proyek, dan kelola alokasi anggota tim.</p>
                </div>
                
                <button @click="$dispatch('open-modal-tambah-proyek')" 
                    class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Tambah Proyek Baru
                </button>
            </div>

            {{-- Card Utama --}}
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden mt-2">
                
                {{-- Filter Tab Status Proyek --}}
                <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-6 overflow-x-auto">
                    @php $currentStatus = request('status', 'semua'); @endphp
                    @foreach(['semua' => 'Semua Proyek', 'perencanaan' => 'Perencanaan', 'berjalan' => 'Sedang Berjalan', 'selesai' => 'Selesai'] as $key => $label)
                        @php
                            if ($key == 'semua') $textColor = 'text-[#5C46F5]';
                            elseif ($key == 'berjalan') $textColor = 'text-amber-600';
                            elseif ($key == 'selesai') $textColor = 'text-green-600';
                            else $textColor = 'text-gray-600';

                            $bgColor = 'bg-gray-100'; 
                            if ($currentStatus == $key) {
                                if ($key == 'semua') $bgColor = 'bg-[#5C46F5]/10';
                                elseif ($key == 'berjalan') $bgColor = 'bg-amber-100';
                                elseif ($key == 'selesai') $bgColor = 'bg-green-100';
                                else $bgColor = 'bg-gray-200';
                            }
                        @endphp
                        <a href="{{ route('ketuatim.manajemenproyek', ['status' => $key]) ?? '#' }}" 
                           class="pb-4 text-[11px] whitespace-nowrap uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                            {{ $label }}
                            <span class="ml-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Tabel Data Proyek --}}
                <div id="data-container" class="px-4 pb-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                                <th class="px-6 py-6 text-center w-[5%]">No</th>
                                <th class="px-6 py-6 w-[35%]">Nama Proyek</th>
                                <th class="px-6 py-6 w-[20%]">Ketua Proyek</th>
                                <th class="px-6 py-6 w-[15%]">Tenggat Waktu</th>
                                <th class="px-6 py-6 text-center w-[10%]">Status</th>
                                <th class="px-6 py-6 text-center w-[15%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium text-sm">
                            @forelse($proyeks ?? [] as $index => $proyek)
                            <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                                <td class="px-6 py-6 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                                    {{ (isset($proyeks) && method_exists($proyeks, 'firstItem') ? $proyeks->firstItem() : 1) + $index }}
                                </td>
                                <td class="px-6 py-6">
                                    <div class="font-bold text-gray-800">{{ $proyek->nama_proyek }}</div>
                                    <div class="text-xs text-gray-400 mt-1">{{ Str::limit($proyek->deskripsi, 50) }}</div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-[#5C46F5] text-[10px] font-bold border border-indigo-100">
                                            {{ strtoupper(substr($proyek->ketuaProyek->nama ?? 'B', 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $proyek->ketuaProyek->nama ?? 'Belum Ditunjuk' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm text-gray-600 font-medium tracking-wide">
                                    {{ $proyek->tenggat_waktu ? \Carbon\Carbon::parse($proyek->tenggat_waktu)->translatedFormat('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $proyek->status == 'Selesai' ? 'bg-green-50 text-green-600 border-green-100' : ($proyek->status == 'Berjalan' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-gray-50 text-gray-600 border-gray-200') }}">
                                        {{ $proyek->status ?? 'Perencanaan' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <button type="button"
                                            @click="$dispatch('open-modal-edit-proyek', { id: '{{ $proyek->id_proyek }}' })" 
                                            class="text-gray-300 hover:text-amber-500 transition-colors p-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-24 text-center text-gray-400 font-bold">Belum ada data proyek terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Paginasi --}}
                    @if(isset($proyeks) && method_exists($proyeks, 'links'))
                    <div class="mt-4 pt-6 border-t border-gray-50 flex items-center justify-between px-2 text-[11px] font-medium text-gray-400 uppercase tracking-widest">
                        <div>Menampilkan {{ $proyeks->firstItem() ?? 0 }}–{{ $proyeks->lastItem() ?? 0 }} dari {{ $proyeks->total() }} data proyek</div>
                        <div>{{ $proyeks->links() }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tempat Modal Nanti (Dapat kamu uncomment jika modalnya sudah dibuat) --}}
        {{-- @include('ketuatim.modals.tambahproyek') --}}
        {{-- @include('ketuatim.modals.editproyek') --}}
    </div>
</x-layoututama>