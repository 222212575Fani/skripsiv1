<x-layoutadmin title="Manajemen Pengguna">
    <div x-data="{}">
        <x-slot name="headerTitle">
            <form action="{{ route('admin.manajemenpengguna') }}" method="GET" id="searchForm" class="relative w-full">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." autocomplete="off"
                    class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
            </form>
        </x-slot>

        <div class="flex flex-col gap-4">
            <div class="flex justify-between items-end px-2">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Otorisasi dan pengaturan hak akses pengguna sistem.</p>
                </div>
                
                {{-- Tombol Tambah Pengguna --}}
                <button @click="$dispatch('open-modal-tambah-pengguna')" 
                    class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Tambah Pengguna
                </button>
            </div>

            {{-- ALERT NOTIFIKASI ERROR / SUKSES --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-100 text-green-700 rounded-xl text-sm font-bold flex items-center gap-3 shadow-sm mx-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm font-bold flex items-center gap-3 shadow-sm mx-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('error') ?? $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden mt-2">
                
                {{-- Tab Filter Status --}}
                <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-6">
                    @php $currentStatus = request('status', 'semua'); @endphp
                    @foreach(['semua' => 'Semua', 'pending' => 'Pending', 'aktif' => 'Aktif', 'non-aktif' => 'Non-Aktif'] as $key => $label)
                        @php
                            // 1. Menentukan Warna Teks Permanen (Sesuai Logo Tabel)
                            if ($key == 'semua') $textColor = 'text-[#5C46F5]';
                            elseif ($key == 'pending') $textColor = 'text-amber-600';
                            elseif ($key == 'aktif') $textColor = 'text-green-600';
                            else $textColor = 'text-red-600';

                            // 2. Menentukan Warna Background Bulatan (Abu-abu jika tidak dipilih)
                            $bgColor = 'bg-gray-100'; 
                            if ($currentStatus == $key) {
                                // Background menyala jika tab sedang aktif
                                if ($key == 'semua') $bgColor = 'bg-[#5C46F5]/10';
                                elseif ($key == 'pending') $bgColor = 'bg-amber-100';
                                elseif ($key == 'aktif') $bgColor = 'bg-green-100';
                                else $bgColor = 'bg-red-100';
                            }
                            
                            $cKey = ($key == 'non-aktif') ? 'nonaktif' : $key;
                        @endphp

                        <a href="{{ route('admin.manajemenpengguna', ['status' => $key]) }}" 
                           class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                            {{ $label }}
                            <span class="ml-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold transition-all {{ $bgColor }} {{ $textColor }}">
                                {{ $counts[$cKey] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Tabel Data --}}
                <div id="data-container" class="px-4 pb-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                                <th class="px-6 py-6 text-center w-[5%]">No</th>
                                <th class="px-6 py-6 w-[15%]">NIP</th>
                                <th class="px-6 py-6 w-[20%]">Nama</th>
                                <th class="px-6 py-6 w-[15%]">Role</th>
                                <th class="px-6 py-6 w-[20%]">Tim Kerja</th>
                                <th class="px-6 py-6 text-center w-[10%]">Status</th>
                                <th class="px-6 py-6 text-center w-[15%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="font-medium text-sm">
                            @forelse($users as $index => $user)
                            <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                                <td class="px-6 py-6 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                                    {{ $users->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-6 font-bold text-gray-400 italic tracking-wider">
                                    {{ $user->nip }}
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-[10px] font-bold shadow-sm shadow-[#5C46F5]/20">
                                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-800">{{ $user->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-[11px] font-black uppercase tracking-widest text-gray-400">
                                    {{ $user->role->nama_role ?? '-' }}
                                </td>
                                <td class="px-6 py-6">
                                    <span class="text-[12px] font-bold tracking-tight px-3 py-1 rounded-md {{ $user->nama_tim != '-' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-400 italic bg-transparent' }}">
                                        {{ $user->nama_tim }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border {{ $user->status_akun == 'aktif' ? 'bg-green-50 text-green-600 border-green-100' : ($user->status_akun == 'pending' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-red-50 text-red-600 border-red-100') }}">
                                        {{ $user->status_akun }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($user->status_akun == 'pending')
                                        <button type="button" 
                                            @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                            class="px-5 py-2 bg-[#5C46F5] text-white text-[10px] font-black rounded-xl hover:bg-[#4A38D4] transition-all uppercase shadow-md shadow-[#5C46F5]/20">
                                            Aktivasi
                                        </button>
                                    @else
                                        <button type="button"
                                                @click="$dispatch('open-modal-edit-pengguna', { 
                                                    id: '{{ $user->id_pengguna }}', 
                                                    nama: '{{ addslashes($user->nama) }}', 
                                                    nip: '{{ $user->nip }}', 
                                                    status: '{{ $user->status_akun }}', 
                                                    role: '{{ $user->id_role ?? '' }}', 
                                                    tim: '{{ $user->id_tim_aktif ?? '' }}' 
                                                })" 
                                                class="text-gray-300 hover:text-amber-500 transition-colors p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-200">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-gray-900 font-bold text-lg tracking-tight">Belum Ada Data Pengguna</h3>
                                        <p class="text-gray-400 font-medium text-sm mt-1">Data pengguna akan muncul di sini setelah terdaftar di sistem.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4 pt-6 border-t border-gray-50 flex items-center justify-between px-2 text-[11px] font-medium text-gray-400 uppercase tracking-widest">
                        <div>Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data pengguna</div>
                        <div>{{ $users->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Include Semua Modal --}}
        @include('admin.modals.aktivasipengguna')
        @include('admin.modals.tambahpengguna')
        @include('admin.modals.editpengguna')
    </div>
</x-layoutadmin>