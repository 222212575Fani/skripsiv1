<x-layoutadmin title="Manajemen Pengguna">
    {{-- Slot Header: Search Bar --}}
    <x-slot name="headerTitle">
        {{-- Form tanpa preventDefault agar bisa ditekan Enter --}}
        <form action="{{ route('admin.manajemenpengguna') }}" method="GET" id="searchForm" class="relative w-full">
            {{-- Pertahankan status saat mencari --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari pengguna berdasarkan nama/NIP..." autocomplete="off"
                class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
        </form>
    </x-slot>

    <div class="flex flex-col gap-2">
        <div class="flex justify-between items-end mb-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Otorisasi akun baru dan manajemen hak akses.</p>
            </div>
            <button class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Tambah Pengguna
            </button>
        </div>

        <div class="flex items-center gap-8 mb-4 border-b border-gray-100 px-2">
            @php 
                $currentStatus = request('status', 'semua'); 
                $searchQuery = request('search') ? ['search' => request('search')] : [];
            @endphp
            
            <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => 'semua'], $searchQuery)) }}" 
               class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == 'semua' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Semua Status
            </a>
            
            <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => 'pending'], $searchQuery)) }}" 
               class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == 'pending' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Pending
                <span class="ml-1.5 px-2 py-0.5 bg-amber-100 text-amber-600 rounded-full text-[9px]">{{ $counts['pending'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => 'aktif'], $searchQuery)) }}" 
               class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == 'aktif' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Aktif
                <span class="ml-1.5 px-2 py-0.5 bg-green-100 text-green-600 rounded-full text-[9px]">{{ $counts['aktif'] ?? 0 }}</span>
            </a>

            <a href="{{ route('admin.manajemenpengguna', array_merge(['status' => 'non-aktif'], $searchQuery)) }}" 
               class="pb-4 text-[11px] uppercase tracking-widest font-black transition-all border-b-2 {{ $currentStatus == 'non-aktif' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                Non-Aktif
                <span class="ml-1.5 px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[9px]">{{ $counts['nonaktif'] ?? 0 }}</span>
            </a>
        </div>

        <div id="data-container">
            <div class="w-full">
                <table class="w-full text-left border-separate border-spacing-y-2 table-fixed">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-[0.2em] font-bold">
                            <th class="px-6 py-3 text-center w-[5%]">No</th>
                            <th class="px-6 py-3 w-[20%]">NIP</th>
                            <th class="px-6 py-3 w-[25%]">Nama Pengguna</th>
                            <th class="px-6 py-3 w-[15%]">Tim Kerja</th>
                            <th class="px-6 py-3 w-[15%]">Peran</th>
                            <th class="px-6 py-3 text-center w-[10%]">Status</th>
                            <th class="px-6 py-3 text-center w-[10%]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-medium text-sm">
                        @forelse($users as $index => $user)
                        <tr class="group bg-white hover:shadow-[0_8px_25px_-5px_rgba(92,70,245,0.15)] hover:scale-[1.003] transition-all duration-200 cursor-pointer shadow-sm">
                            <td class="px-6 py-4 text-gray-400 text-center font-bold rounded-l-lg border-y border-l-8 border-l-transparent group-hover:border-l-[#5C46F5] border-gray-100/50 transition-all">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-600 border-y border-gray-100/50 uppercase">{{ $user->nip }}</td>
                            <td class="px-6 py-4 border-y border-gray-100/50">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="shrink-0 w-9 h-9 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white text-xs font-black shadow-sm">
                                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-gray-800 truncate">{{ $user->nama }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 border-y border-gray-100/50 truncate">
                                {{ $user->anggotaTim->where('tanggal_keluar', null)->first()->tim->nama_tim ?? '-' }}
                            </td>
                            
                            {{-- LOGIKA KOLOM PERAN --}}
                            <td class="px-6 py-4 text-gray-500 border-y border-gray-100/50 whitespace-nowrap">
                                @if($user->status_akun == 'pending' || empty($user->role))
                                    <span class="text-gray-300 font-normal italic">Menunggu Aktivasi</span>
                                @else
                                    <span class="font-bold">{{ $user->role->nama_role }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center border-y border-gray-100/50">
                                @php
                                    $statusClasses = [
                                        'aktif' => 'bg-green-50 text-green-600 border-green-100',
                                        'pending' => 'bg-amber-50 text-amber-500 border-amber-100',
                                        'nonaktif' => 'bg-red-50 text-red-600 border-red-100',
                                    ];
                                @endphp
                                <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $statusClasses[$user->status_akun] ?? 'bg-gray-50 text-gray-400 border-gray-100' }}">
                                    {{ $user->status_akun }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center rounded-r-lg border-y border-r border-gray-100/50">
                                <div class="flex justify-center items-center">
                                    @if($user->status_akun == 'pending')
                                        <button class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-black rounded-lg shadow-md hover:bg-[#4A38D4] transition-all uppercase tracking-wider">Aktivasi</button>
                                    @else
                                        <button class="p-2 text-gray-400 hover:text-[#5C46F5] transition-colors"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- PESAN JIKA DATA TIDAK DITEMUKAN --}}
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                @if(request('search'))
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium text-sm">Pengguna dengan nama atau NIP <span class="text-[#5C46F5] font-black">"{{ request('search') }}"</span> tidak ditemukan.</span>
                                        <span class="text-xs mt-1">Coba gunakan kata kunci lain atau periksa kembali ejaan Anda.</span>
                                    </div>
                                @else
                                    <span class="italic text-gray-400 font-bold">Data belum tersedia.</span>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col md:flex-row justify-between items-center px-4">
                <div class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold">
                    Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() ?? 0 }} Pengguna
                </div>
                <div class="mt-4 md:mt-0 custom-pagination">
                    {{ $users->links() }}
                </div>
            </div>
        </div> {{-- Tutup data-container --}}
    </div>

    {{-- SCRIPT LIVE SEARCH ANTI-GAGAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const dataContainer = document.getElementById('data-container');
            let timeout = null;

            if (searchInput && dataContainer) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    
                    timeout = setTimeout(() => {
                        const searchValue = this.value;
                        const url = new URL(window.location.href);
                        
                        if (searchValue) {
                            url.searchParams.set('search', searchValue);
                        } else {
                            url.searchParams.delete('search');
                        }
                        url.searchParams.delete('page'); // Reset ke halaman 1

                        // Update URL bar
                        window.history.pushState({}, '', url);

                        // Ambil data baru via AJAX
                        fetch(url, {
                            headers: { 
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html' 
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.text();
                        })
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContainer = doc.getElementById('data-container');
                            
                            if (newContainer) {
                                dataContainer.innerHTML = newContainer.innerHTML;
                            } else {
                                throw new Error('Elemen tidak ditemukan di respons');
                            }
                        })
                        .catch(error => {
                            console.error('Live search gagal, mencoba memuat ulang halaman...', error);
                            // Fallback jika fetch gagal (misal server timeout/error)
                            window.location.href = url.href;
                        });
                    }, 500); // Delay 500ms
                });
            }
        });
    </script>
</x-layoutadmin>

{{-- GAYA UNTUK PAGINASI BAWAAN LARAVEL (TAILWIND) --}}
<style>
    .custom-pagination nav div:first-child { display: none; }
    .custom-pagination nav span, .custom-pagination nav a {
        padding: 6px 12px !important;
        font-size: 11px !important;
        font-weight: 800 !important;
        border-radius: 8px !important;
        margin: 0 2px !important;
        border: none !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .custom-pagination nav span[aria-current="page"] span {
        background-color: #5C46F5 !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(92, 70, 245, 0.3);
    }
</style>