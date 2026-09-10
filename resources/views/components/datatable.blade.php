@props(['paginator', 'itemName' => 'data'])

{{-- KOTAK PUTIH LUAR UTAMA DENGAN SUDUT MELENGKUNG --}}
<div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
    
    {{-- Slot Atas (Judul, Search Bar, Tombol Tambah, & Tab Filter Status) --}}
    {{ $tabs ?? '' }}

    {{-- KOTAK KEDUA: KOTAK ROUNDED DENGAN BORDER GARIS PINGGIR UNGU UNTUK TABEL --}}
    <div class="bg-white border border-[#DDD6FE] rounded-[22px] overflow-hidden shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-gray-900 text-xs font-semibold border-b border-[#DDD6FE] bg-[#F8F7FF]/50 hidden md:table-row">
                        {{ $header }}
                    </tr>
                </thead>
                <tbody class="text-sm font-medium divide-y divide-gray-100">
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>

    {{-- BAGIAN PAGINASI DI DALAM KOTAK PUTIH LUAR --}}
    @if(isset($paginator))
    <div class="px-2 pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-500">
        <div>
            Menampilkan 
            <span class="font-bold text-gray-700">{{ $paginator->firstItem() ?? 0 }}</span> 
            sampai 
            <span class="font-bold text-gray-700">{{ $paginator->lastItem() ?? 0 }}</span> 
            dari 
            <span class="font-bold text-gray-700">{{ $paginator->total() }}</span> 
            {{ $itemName }}
        </div>

        <div class="flex items-center gap-2">
            {{-- Tombol Previous (Bulat Ungu) --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 rounded-full bg-purple-100 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-xs font-bold">
                    &lsaquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 rounded-full bg-[#5C46F5] text-white hover:bg-[#4A38D4] flex items-center justify-center transition-all shadow-sm shadow-[#5C46F5]/30 font-bold">
                    &lsaquo;
                </a>
            @endif

            {{-- Nomor Halaman (Kontainer Lonjong) --}}
            <div class="bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full flex items-center gap-2 text-xs font-semibold text-gray-600">
                @foreach ($paginator->getUrlRange(1, max(1, $paginator->lastPage())) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-7 h-7 rounded-full bg-white text-[#5C46F5] border border-[#5C46F5] flex items-center justify-center font-bold shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-7 h-7 rounded-full hover:bg-gray-200/60 text-gray-600 flex items-center justify-center transition-all">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Tombol Next (Bulat Ungu) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 rounded-full bg-[#5C46F5] text-white hover:bg-[#4A38D4] flex items-center justify-center transition-all shadow-sm shadow-[#5C46F5]/30 font-bold">
                    &rsaquo;
                </a>
            @else
                <span class="w-9 h-9 rounded-full bg-purple-100 text-purple-300 flex items-center justify-center cursor-not-allowed shadow-xs font-bold">
                    &rsaquo;
                </span>
            @endif
        </div>
    </div>
    @endif

</div>