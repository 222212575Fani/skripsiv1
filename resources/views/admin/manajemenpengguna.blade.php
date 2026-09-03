<x-layoututama title="Manajemen Pengguna">
    <div x-data="{}">
        
        {{-- Kolom Pencarian di Header dengan Auto-Reset Saat Dihapus --}}
        <x-slot name="headerTitle">
            <form action="{{ route('admin.manajemenpengguna') }}" method="GET" id="searchForm" class="relative w-full" x-data="{ search: '{{ request('search') }}' }">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" id="searchInput" name="search" x-model="search" 
                    @input.debounce.400ms="if(search === '') { window.location.href = '{{ route('admin.manajemenpengguna') }}' + ('{{ request('status') }}' ? '?status={{ request('status') }}' : ''); }"
                    placeholder="Cari Nama atau NIP..." autocomplete="off"
                    class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
            </form>
        </x-slot>

        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-end px-1">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Otorisasi dan pengaturan hak akses pengguna sistem.</p>
                </div>
                
                <x-button @click="$dispatch('open-modal-tambah-pengguna')"> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pengguna
                </x-button>
            </div>

            {{-- Memanggil Komponen Reusable <x-datatable> --}}
            <x-datatable :paginator="$users" item-name="data pengguna">
                
                {{-- Slot untuk Filter Tab Status --}}
                <x-slot name="tabs">
                    <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-5">
                        @php $currentStatus = request('status', 'semua'); @endphp
                        @foreach(['semua' => 'Semua Pengguna', 'aktif' => 'Aktif', 'pending' => 'Pending', 'nonaktif' => 'Non-Aktif'] as $key => $label)
                            @php
                                if ($key == 'semua') $textColor = 'text-[#5C46F5]';
                                elseif ($key == 'aktif') $textColor = 'text-green-600';
                                elseif ($key == 'pending') $textColor = 'text-amber-600';
                                else $textColor = 'text-red-600';

                                $bgColor = 'bg-gray-100'; 
                                if ($currentStatus == $key) {
                                    if ($key == 'semua') $bgColor = 'bg-[#5C46F5]/10';
                                    elseif ($key == 'aktif') $bgColor = 'bg-green-100';
                                    elseif ($key == 'pending') $bgColor = 'bg-amber-100';
                                    else $bgColor = 'bg-red-100';
                                }
                            @endphp
                            <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}" 
                               class="pb-4 text-[11px] uppercase tracking-wider font-bold transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                                {{ $label }}
                                <span class="ml-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                    {{ $counts[$key] ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </x-slot>

                {{-- Slot Definisi Header Tabel (7 Kolom) --}}
                <x-slot name="header">
                    <th class="px-6 py-4 text-center w-[8%]">No</th>
                    <th class="px-6 py-4 w-[18%]">NIP</th>
                    <th class="px-6 py-4 w-[22%]">Nama</th>
                    <th class="px-6 py-4 w-[15%]">Role</th>
                    <th class="px-6 py-4 w-[20%]">Tim Kerja</th>
                    <th class="px-6 py-4 text-center w-[10%]">Status</th>
                    <th class="px-6 py-4 text-center w-[10%]">Aksi</th>
                </x-slot>

                {{-- Slot Isi Baris Tabel (Looping Data Pengguna) --}}
                @forelse($users as $index => $user)
                <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                    <td class="px-6 py-4 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                        {{ $users->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600 font-medium tracking-wide">
                        {{ $user->nip }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[10px] font-bold shadow-sm shadow-[#5C46F5]/20 shrink-0">
                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                            </div>
                            <span class="font-bold text-gray-800 text-xs truncate max-w-[150px]">{{ $user->nama }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-600 font-medium">
                        {{ $user->role->nama_role ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[11px] font-bold tracking-tight px-3 py-1 rounded-md inline-block truncate max-w-[160px] {{ $user->nama_tim != '-' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-400 italic bg-transparent' }}">
                            {{ $user->nama_tim }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border inline-block {{ $user->status_akun == 'aktif' ? 'bg-green-50 text-green-600 border-green-100' : ($user->status_akun == 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-red-50 text-red-600 border-red-100') }}">
                            {{ $user->status_akun }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($user->status_akun == 'pending')
                            <button type="button" 
                                @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-black rounded-xl hover:bg-[#4A38D4] transition-all uppercase shadow-md shadow-[#5C46F5]/20">
                                Aktivasi
                            </button>
                        @else
                            <button type="button"
                                    @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                    class="text-purple-300 hover:text-purple-500 transition-colors p-2 inline-flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-20 text-center text-gray-400 font-bold text-xs">Data tidak ditemukan.</td></tr>
                @endforelse

            </x-datatable>
        </div>

        @include('admin.modals.aktivasipengguna')
        @include('admin.modals.tambahpengguna')
        @include('admin.modals.editpengguna')
    </div>
</x-layoututama>