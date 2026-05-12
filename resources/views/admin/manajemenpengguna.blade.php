<x-layoutadmin title="Manajemen Pengguna">
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
        {{-- HEADER & TOMBOL TAMBAH PENGGUNA --}}
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Otorisasi dan pengaturan hak akses pengguna.</p>
            </div>
            
            <button class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Tambah Pengguna
            </button>
        </div>

        {{-- FILTER TAB (Angka Selalu Tampil) --}}
        <div class="flex items-center gap-8 border-b border-gray-100 px-2 mt-4">
            @php $currentStatus = request('status', 'semua'); @endphp
            @foreach(['semua' => 'Semua', 'pending' => 'Pending', 'aktif' => 'Aktif', 'non-aktif' => 'Non-Aktif'] as $key => $label)
                <a href="{{ route('admin.manajemenpengguna', ['status' => $key, 'search' => request('search')]) }}" 
                   class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == $key ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                    {{ $label }}
                    @php 
                        $cKey = ($key == 'non-aktif') ? 'nonaktif' : $key; 
                        // Atur warna badge berdasarkan status
                        $badgeClass = match($key) {
                            'pending' => 'bg-amber-100 text-amber-600',
                            'aktif'   => 'bg-green-100 text-green-600',
                            'non-aktif' => 'bg-red-100 text-red-600',
                            default   => 'bg-[#5C46F5]/10 text-[#5C46F5]'
                        };
                        // Ambil nilai count, jika kosong jadikan 0
                        $countVal = $counts[$cKey] ?? 0;
                    @endphp
                    
                    {{-- Kondisi if dihilangkan agar 0 tetap tercetak --}}
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] {{ $badgeClass }}">
                        {{ $countVal }}
                    </span>
                </a>
            @endforeach
        </div>

        <div id="data-container">
            <table class="w-full text-left border-separate border-spacing-y-2 table-fixed">
                <thead>
                    <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold">
                        <th class="px-6 py-3 text-center w-[5%]">No</th>
                        <th class="px-6 py-3 w-[20%]">NIP</th>
                        <th class="px-6 py-3 w-[25%]">Nama</th>
                        <th class="px-6 py-3 w-[15%]">Tim</th>
                        <th class="px-6 py-3 w-[15%] font-bold">Role</th>
                        <th class="px-6 py-3 text-center w-[10%]">Status</th>
                        <th class="px-6 py-3 text-center w-[10%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="font-medium text-sm">
                    @forelse($users as $index => $user)
                    <tr class="bg-white hover:shadow-md transition-all shadow-sm rounded-xl overflow-hidden">
                        <td class="px-6 py-4 text-center text-gray-400 font-bold rounded-l-xl">{{ $users->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-bold text-gray-600 uppercase">{{ $user->nip }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#5C46F5] flex items-center justify-center text-white text-xs font-black uppercase">{{ substr($user->nama, 0, 1) }}</div>
                                <span class="font-bold text-gray-800 truncate">{{ $user->nama }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 truncate">{{ $user->anggotaTim->first()->tim->nama_tim ?? '-' }}</td>
                        
                        <td class="px-6 py-4">
                            @if($user->status_akun == 'pending') 
                                <span class="text-gray-400 italic">Menunggu Aktivasi</span> 
                            @else 
                                <span class="font-bold text-gray-700">{{ $user->role->nama_role ?? '-' }}</span> 
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $user->status_akun == 'aktif' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-500' }}">
                                {{ $user->status_akun }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center rounded-r-xl">
                            @if($user->status_akun == 'pending')
                                <button type="button" onclick="openModalAktivasi('{{ $user->id }}', '{{ $user->nama }}', '{{ $user->nip }}')" class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-black rounded-lg hover:bg-[#4A38D4] transition-all uppercase">Aktivasi</button>
                            @else
                                <button class="text-gray-400 hover:text-[#5C46F5] p-2 transition-colors" title="Edit Pengguna">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            @endif
                        </td>
                    </tr>

                    @empty
                    {{-- EMPTY STATE --}}
                    <tr>
                        <td colspan="7" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-gray-900 font-bold text-lg">Data Tidak Ditemukan</h3>
                                <p class="text-gray-500 font-medium text-sm mt-1">Belum ada pengguna dengan kriteria ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4">{{ $users->links() }}</div>
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