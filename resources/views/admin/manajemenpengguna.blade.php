<x-layoututama title="Manajemen Pengguna">
    <div x-data="{ 
        search: '{{ request('search') }}',
        performSearch() {
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
                    const newTable = doc.getElementById('pengguna-table-wrapper');
                    const targetTable = document.getElementById('pengguna-table-wrapper');
                    if (newTable && targetTable) {
                        targetTable.innerHTML = newTable.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetTable);
                        }
                    }
                    const newTabs = doc.getElementById('pengguna-tabs-wrapper');
                    const targetTabs = document.getElementById('pengguna-tabs-wrapper');
                    if (newTabs && targetTabs) {
                        targetTabs.innerHTML = newTabs.innerHTML;
                    }
                    const newMobileFilter = doc.getElementById('pengguna-mobile-filter');
                    const targetMobileFilter = document.getElementById('pengguna-mobile-filter');
                    if (newMobileFilter && targetMobileFilter) {
                        targetMobileFilter.innerHTML = newMobileFilter.innerHTML;
                        if (window.Alpine) {
                            window.Alpine.initTree(targetMobileFilter);
                        }
                    }
                    window.history.pushState({}, '', url.toString());
                })
                .catch(err => console.error('Error fetching search:', err));
        }
    }" class="flex flex-col gap-5 sm:gap-6 w-full">
        
        @php 
            $currentStatus = request('status', 'semua'); 
            $statuses = ['semua' => 'Semua Pengguna', 'aktif' => 'Aktif', 'pending' => 'Pending', 'nonaktif' => 'Non-Aktif'];
        @endphp

        {{-- BARIS 1: JUDUL HALAMAN & SUBTITLE --}}
        <div class="px-1">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Daftar Pengguna</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data seluruh pengguna sistem, hak akses, dan status akun.</p>
        </div>

        {{-- KONTROL DESKTOP (Hanya Tampil di Layar >= xl): Underline Tabs & Kotak Cari + Tombol Tambah --}}
        <div class="hidden xl:flex items-end justify-between gap-4 border-b border-gray-200/80 px-1">
            {{-- Underline Tabs --}}
            <div id="pengguna-tabs-wrapper" class="flex items-center gap-6 overflow-x-auto text-xs font-normal scrollbar-none -mb-px">
                @foreach($statuses as $key => $label)
                    <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                       class="pb-3 flex items-center gap-2 transition-all relative whitespace-nowrap border-b-2 {{ $currentStatus == $key ? 'text-[#604EE6] border-[#604EE6] font-normal' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300 font-light' }}">
                        <span>{{ $label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-light {{ $currentStatus == $key ? 'bg-purple-100 text-[#604EE6]' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts[$key] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Kotak Pencarian Modern & Tombol Tambah --}}
            <div class="flex items-center gap-3 pb-2.5">
                <div class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs w-60">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="search" 
                        @input.debounce.400ms="performSearch()"
                        placeholder="Cari..." 
                        autocomplete="off"
                        class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
                    <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <x-button @click="$dispatch('open-modal-tambah-pengguna')" class="shrink-0 whitespace-nowrap"> 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    <span>Tambah Pengguna</span>
                </x-button>
            </div>
        </div>

        {{-- KONTROL MOBILE / TABLET (Tampilan saat Layar Diperkecil < xl): Berurutan ke Bawah: 1) Tambah, 2) Pencarian, 3) Filter --}}
        <div class="flex xl:hidden flex-col gap-3 w-full px-1">
            {{-- 1. Tombol Tambah Pengguna (Full Width) --}}
            <x-button @click="$dispatch('open-modal-tambah-pengguna')" class="w-full justify-center py-2.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                <span>Tambah Pengguna</span>
            </x-button>

            {{-- 2. Kolom Pencarian (Full Width) --}}
            <div class="w-full flex items-center gap-2 px-3.5 py-2.5 bg-white border border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF] focus-within:border-[#604EE6] focus-within:ring-2 focus-within:ring-purple-100 focus-within:bg-white rounded-lg text-xs transition-all shadow-2xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="search" 
                    @input.debounce.400ms="performSearch()"
                    placeholder="Cari Nama atau NIP..." 
                    autocomplete="off"
                    class="bg-transparent border-none focus:outline-none text-xs text-gray-800 placeholder:text-gray-400 placeholder:font-light w-full p-0">
                <button type="button" x-show="search" @click="search = ''; performSearch();" class="text-gray-400 hover:text-gray-600 shrink-0 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- 3. Filter Dropdown (Full Width) --}}
            <div id="pengguna-mobile-filter" class="w-full relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" 
                    type="button" 
                    class="w-full flex items-center justify-between px-3.5 py-2.5 bg-white border rounded-lg text-xs font-normal text-gray-700 transition-all cursor-pointer shadow-2xs focus:outline-none"
                    :class="open ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-gray-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                    <div class="flex items-center gap-2 truncate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span class="truncate {{ $currentStatus === 'semua' ? 'text-gray-400 font-light' : 'text-gray-700 font-normal' }}">{{ $statuses[$currentStatus] ?? 'Semua Pengguna' }}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 ml-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-[#604EE6]">
                            {{ $counts[$currentStatus] ?? 0 }}
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                {{-- Dropdown Popup --}}
                <div x-show="open" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute left-0 right-0 mt-2 w-full bg-white border border-purple-100 rounded-2xl shadow-xl p-2 z-50 space-y-1 text-xs">
                    @foreach($statuses as $key => $label)
                        <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => $key], request('search') ? ['search' => request('search')] : [])) }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all cursor-pointer {{ $currentStatus == $key ? 'bg-[#604EE6]/10 text-[#604EE6] font-bold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-normal' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus == $key ? 'bg-[#604EE6]' : 'bg-transparent' }}"></span>
                                <span>{{ $label }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $currentStatus == $key ? 'bg-[#604EE6] text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- TABEL PENGGUNA TUNGGAL (SATU KARTU UTUH DENGAN HEADER & PAGINASI) --}}
        <div id="pengguna-table-wrapper" class="w-full">
            <x-datatable :paginator="$users" item-name="data pengguna" :card="false">

                {{-- SLOT HEADER KOLOM --}}
                <x-slot name="header">
                    <th class="py-3.5 px-4 text-center w-[8%] font-semibold text-gray-500">No</th>
                    <th class="py-3.5 px-4 w-[20%] font-semibold text-gray-500">NIP</th>
                    <th class="py-3.5 px-4 w-[28%] font-semibold text-gray-500">Nama Pengguna</th>
                    <th class="py-3.5 px-4 w-[18%] font-semibold text-gray-500">Role</th>
                    <th class="py-3.5 px-4 text-center w-[15%] font-semibold text-gray-500">Status</th>
                    <th class="py-3.5 pr-6 text-right w-[11%] font-semibold text-gray-500">Aksi</th>
                </x-slot>

                {{-- SLOT ISI DATA (LOOPING) --}}
                @forelse($users as $index => $user)
                @php
                    $statusConfig = match($user->status_akun) {
                        'aktif' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200/60', 'dot' => 'bg-emerald-500', 'label' => 'Aktif'],
                        'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200/60', 'dot' => 'bg-amber-500', 'label' => 'Pending'],
                        default => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200/60', 'dot' => 'bg-rose-500', 'label' => 'Non-Aktif']
                    };
                @endphp
                
                <tr class="bg-white hover:bg-[#F8F7FF] transition-colors align-middle hidden xl:table-row border-b border-gray-100 last:border-none">
                    <td class="py-4 px-4 text-center text-gray-400 font-light text-xs align-middle">
                        {{ $users->firstItem() + $index }}
                    </td>
                    <td class="py-4 px-4 text-xs text-gray-600 font-light align-middle">
                        {{ $user->nip }}
                    </td>
                    <td class="py-4 px-4 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-[#604EE6] shrink-0 shadow-2xs font-light text-xs">
                                {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-light text-gray-800 text-xs">{{ $user->nama }}</span>
                                <span class="text-[10px] text-gray-400 font-light">Akun Terdaftar</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-xs align-middle">
                        @if($user->role)
                            <div class="flex flex-col gap-0.5">
                                <span class="font-normal text-gray-800">{{ $user->role->nama_role }}</span>
                                @php
                                    $namaRoleLower = strtolower($user->role->nama_role);
                                @endphp
                                @if(str_contains($namaRoleLower, 'ketua'))
                                    @if(!empty($user->nama_tim) && $user->nama_tim !== '-')
                                        <span class="text-[10.5px] text-[#604EE6] font-light flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                            <span class="truncate max-w-[180px]">Ketua {{ $user->nama_tim }}</span>
                                        </span>
                                    @else
                                        <span class="text-[10.5px] text-amber-600 font-light flex items-center gap-1" title="Tetapkan tim pada menu Manajemen Tim Kerja">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                            <span>Belum ada tim (Atur di Tim Kerja)</span>
                                        </span>
                                    @endif
                                @elseif(str_contains($namaRoleLower, 'anggota'))
                                    @if(!empty($user->nama_tim) && $user->nama_tim !== '-')
                                        <span class="text-[10.5px] text-[#604EE6] font-light flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                            <span class="truncate max-w-[180px]">Tim: {{ $user->nama_tim }}</span>
                                        </span>
                                    @else
                                        <span class="text-[10.5px] text-gray-400 font-light flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                            <span>Belum masuk tim</span>
                                        </span>
                                    @endif
                                @elseif(str_contains($namaRoleLower, 'direktur'))
                                    <span class="text-[10.5px] text-[#604EE6] font-light flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                        <span>Pimpinan Struktural</span>
                                    </span>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Peran belum ditetapkan</span>
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-center align-middle">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-light border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                            <span>{{ $statusConfig['label'] }}</span>
                        </span>
                    </td>
                    <td class="py-4 pr-6 text-right align-middle">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($user->status_akun == 'pending')
                                <button type="button" 
                                    @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                    class="px-3.5 py-1.5 bg-[#604EE6] text-white text-xs font-medium rounded-lg hover:bg-[#503ED8] transition-all shadow-xs">
                                    Aktivasi
                                </button>
                            @else
                                <button type="button"
                                    @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                    class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:text-[#604EE6] hover:border-[#604EE6] hover:bg-purple-50 transition-all shadow-2xs cursor-pointer" title="Edit Pengguna">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- BARIS KHUSUS CARD (Tampilan saat Layar Diperkecil < xl) --}}
                        <tr class="xl:hidden border-b border-purple-100/40 last:border-none">
                            <td colspan="6" class="p-2.5 sm:p-3 bg-white">
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
                                            @if($user->status_akun == 'pending')
                                                <button type="button" 
                                                    @click="$dispatch('open-modal-aktivasi', { id: '{{ $user->id_pengguna }}', nama: '{{ $user->nama }}', nip: '{{ $user->nip }}' })"
                                                    class="px-3.5 py-1 bg-[#604EE6] text-white text-[10px] font-medium rounded-xl hover:bg-[#503ED8] transition-all uppercase shadow-xs cursor-pointer">
                                                    Aktivasi
                                                </button>
                                            @else
                                                <button type="button"
                                                    @click="$dispatch('open-modal-edit-pengguna', { id: '{{ $user->id_pengguna }}', nama: '{{ addslashes($user->nama) }}', nip: '{{ $user->nip }}', status: '{{ $user->status_akun }}', role: '{{ $user->id_role ?? '' }}', tim: '{{ $user->id_tim_aktif ?? '' }}' })" 
                                                    class="w-8 h-8 rounded-full bg-white text-[#604EE6] hover:bg-[#604EE6] hover:text-white border border-purple-100 flex items-center justify-center transition-all shadow-xs cursor-pointer" title="Edit Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Baris Utama: Ikon Orang di Sisi Kiri & Informasi di Sampingnya --}}
                                    <div class="flex items-start gap-3.5">
                                        {{-- Ikon Orang --}}
                                        <div class="w-10 h-10 rounded-2xl bg-white border border-purple-100 flex items-center justify-center text-[#604EE6] shrink-0 shadow-2xs mt-0.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>

                                        {{-- Samping Ikon: Nama, NIP, dan Peran --}}
                                        <div class="flex-1 min-w-0 flex flex-col gap-1.5">
                                            <h4 class="text-sm font-normal text-gray-800 wrap-break-word leading-snug">{{ $user->nama }}</h4>
                                            
                                            {{-- NIP Pengguna --}}
                                            <div class="flex items-center gap-1.5 text-xs text-gray-600 font-light">
                                                <span>NIP</span>
                                                <span>{{ $user->nip }}</span>
                                            </div>

                                            {{-- Peran Pengguna & Tim Kerja --}}
                                            <div class="flex flex-col gap-0.5 text-xs pt-0.5">
                                                @if($user->role)
                                                    <div class="inline-flex items-center gap-1.5 text-gray-700 font-normal">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>
                                                        <span>{{ $user->role->nama_role }}</span>
                                                    </div>
                                                    @php
                                                        $namaRoleLower = strtolower($user->role->nama_role);
                                                    @endphp
                                                    @if(str_contains($namaRoleLower, 'ketua'))
                                                        @if(!empty($user->nama_tim) && $user->nama_tim !== '-')
                                                            <span class="text-[11px] text-[#604EE6] font-light pl-5 flex items-center gap-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                                                <span>Ketua {{ $user->nama_tim }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-[11px] text-amber-600 font-light pl-5 flex items-center gap-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                                                <span>Belum ada tim (Atur di Tim Kerja)</span>
                                                            </span>
                                                        @endif
                                                    @elseif(str_contains($namaRoleLower, 'anggota'))
                                                        @if(!empty($user->nama_tim) && $user->nama_tim !== '-')
                                                            <span class="text-[11px] text-[#604EE6] font-light pl-5 flex items-center gap-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                                                <span>Tim: {{ $user->nama_tim }}</span>
                                                            </span>
                                                        @else
                                                            <span class="text-[11px] text-gray-400 font-light pl-5 flex items-center gap-1.5">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                                                <span>Belum masuk tim</span>
                                                            </span>
                                                        @endif
                                                    @elseif(str_contains($namaRoleLower, 'direktur'))
                                                        <span class="text-[11px] text-[#604EE6] font-light pl-5 flex items-center gap-1.5">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0"></span>
                                                            <span>Pimpinan Struktural</span>
                                                        </span>
                                                    @endif
                                                @else
                                                    <div class="inline-flex items-center gap-1.5 text-amber-600 font-light">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                        <span class="text-[11px] sm:text-xs font-light">Peran belum ditetapkan</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="bg-white">
                                <x-emptystate 
                                    :border="false" 
                                    padding="py-16 px-4" 
                                    title="Tidak Ada Pengguna Ditemukan" 
                                    message="Tidak ada data pengguna yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih." 
                                />
                            </td>
                        </tr>
                        @endforelse

                    </x-datatable>
            </div>

            {{-- Include Modals --}}
            @include('admin.modals.aktivasipengguna')
            @include('admin.modals.tambahpengguna')
            @include('admin.modals.editpengguna')

        </div>

    {{-- CSS KUSTOM UNTUK MEMBUANG PEMBUNGKUS DI SLOT TABS & PAGINATION --}}
    <style>
        x-datatable > div > div:has(.filter-tabs-container),
        .overflow-hidden > div:has(.filter-tabs-container) {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .flex.items-center.gap-2.bg-gray-50, 
        nav[role="navigation"] div > div:nth-child(2),
        .rounded-full.bg-gray-50 {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    </style>
</x-layoututama>