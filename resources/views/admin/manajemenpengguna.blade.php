<x-layoutadmin title="Manajemen Pengguna">
    <x-slot name="headerTitle">
        {{-- Form Pencarian Pengguna --}}
        <form action="{{ route('admin.manajemenpengguna') }}" method="GET" id="searchForm" class="relative w-full">
            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." autocomplete="off"
                class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
        </form>
    </x-slot>

    <div class="flex flex-col gap-4">
        {{-- HEADER --}}
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Otorisasi dan pengaturan hak akses pengguna.</p>
            </div>
            
            <button class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pengguna
            </button>
        </div>

        <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 overflow-hidden mt-2">
            {{-- FILTER TAB --}}
            <div class="flex items-center gap-8 border-b border-gray-100 px-8 pt-6">
                @php $currentStatus = request('status', 'semua'); @endphp
                @foreach(['semua' => 'Semua', 'pending' => 'Pending', 'aktif' => 'Aktif', 'non-aktif' => 'Non-Aktif'] as $key => $label)
                    <a href="{{ route('admin.manajemenpengguna', ['status' => $key, 'search' => request('search')]) }}" 
                       class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        {{ $label }}
                        @php 
                            $cKey = ($key == 'non-aktif') ? 'nonaktif' : $key; 
                            $badgeClass = match($key) {
                                'pending' => 'bg-amber-100 text-amber-600',
                                'aktif'   => 'bg-green-100 text-green-600',
                                'non-aktif' => 'bg-red-100 text-red-600',
                                default   => 'bg-[#5C46F5]/10 text-[#5C46F5]'
                            };
                            $countVal = $counts[$cKey] ?? 0;
                        @endphp
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] {{ $badgeClass }}">
                            {{ $countVal }}
                        </span>
                    </a>
                @endforeach
            </div>

            <div id="data-container" class="px-4 pb-4">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                            <th class="px-6 py-6 text-center w-[5%]">No</th>
                            <th class="px-6 py-6 w-[20%]">NIP</th>
                            <th class="px-6 py-6 w-[25%] text-center md:text-left">Nama</th>
                            <th class="px-6 py-6 w-[15%]">Tim</th>
                            <th class="px-6 py-6 w-[15%]">Role</th>
                            <th class="px-6 py-6 text-center w-[10%]">Status</th>
                            <th class="px-6 py-6 text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium text-sm">
                        @forelse($users as $index => $user)
                        <tr class="group hover:bg-gray-50/50 transition-all duration-200 border-t border-gray-50">
                            <td class="px-6 py-6 text-center text-gray-400 font-bold border-l-4 border-l-transparent group-hover:border-l-[#5C46F5] transition-all">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-6 font-bold text-gray-600 uppercase tracking-wider">
                                {{ $user->nip }}
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-xs font-black uppercase">
                                        {{ substr($user->nama, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-800 truncate">{{ $user->nama }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6 text-gray-500 truncate italic">
                                {{ $user->anggotaTim->first()->tim->nama_tim ?? '-' }}
                            </td>
                            <td class="px-6 py-6">
                                @if($user->status_akun == 'pending') 
                                    <span class="text-[#5C46F5]/40 italic font-medium text-[11px] tracking-wide uppercase">Menunggu Aktivasi</span> 
                                @else 
                                    <span class="font-bold text-gray-700">{{ $user->role->nama_role ?? '-' }}</span> 
                                @endif
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $user->status_akun == 'aktif' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-500' }}">
                                    {{ $user->status_akun }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                @if($user->status_akun == 'pending')
                                    <button type="button" onclick="openModalAktivasi('{{ $user->id }}', '{{ $user->nama }}', '{{ $user->nip }}')" class="px-5 py-2 bg-[#5C46F5] text-white text-[10px] font-black rounded-xl hover:bg-[#4A38D4] transition-all uppercase shadow-md shadow-[#5C46F5]/20">Aktivasi</button>
                                @else
                                    <button class="text-gray-300 hover:text-[#5C46F5] transition-colors p-2" title="Edit Pengguna">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        {{-- DISERAGAMKAN DENGAN MANAJEMEN TIM KERJA --}}
                        <tr>
                            <td colspan="7" class="py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-200">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-gray-900 font-bold text-lg tracking-tight">Belum Ada Data Pengguna</h3>
                                    <p class="text-gray-400 font-medium text-sm mt-1 max-w-[280px] mx-auto leading-relaxed">
                                        Data pengguna akan muncul di sini setelah akun terdaftar atau diaktivasi.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- FOOTER --}}
                <div class="mt-4 pt-6 border-t border-gray-50 flex items-center justify-between px-2">
                    <div class="text-[11px] font-medium text-gray-400 uppercase tracking-widest">
                        Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data pengguna
                    </div>
                    
                    <div class="flex items-center gap-1">
                        @if ($users->onFirstPage())
                            <span class="p-2 text-gray-200 cursor-not-allowed"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="p-2 text-gray-400 hover:text-[#5C46F5] transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg></a>
                        @endif

                        <div class="bg-[#5C46F5] text-white w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold shadow-lg shadow-[#5C46F5]/30">
                            {{ $users->currentPage() }}
                        </div>

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="p-2 text-gray-400 hover:text-[#5C46F5] transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a>
                        @else
                            <span class="p-2 text-gray-200 cursor-not-allowed"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layoutadmin>

@include('admin.modals.aktivasipengguna')

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const url = new URL(window.location.href);
        url.searchParams.set('search', this.value);
        url.searchParams.delete('page');
        window.history.pushState({}, '', url);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const container = doc.getElementById('data-container');
                if (container) document.getElementById('data-container').innerHTML = container.innerHTML;
            });
    });
</script>