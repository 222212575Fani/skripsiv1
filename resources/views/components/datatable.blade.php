@props(['paginator', 'itemName' => 'data', 'breakpoint' => 'xl', 'card' => true])

@if($card)
{{-- KOTAK PUTIH LUAR UTAMA DENGAN SUDUT MELENGKUNG --}}
<div class="bg-white rounded-[28px] shadow-xs border border-gray-100 overflow-hidden w-full p-6 flex flex-col gap-6">
@else
<div class="w-full flex flex-col gap-6">
@endif
    
    {{-- Slot Atas (Judul, Search Bar, Tombol Tambah, & Tab Filter Status) --}}
    {{ $tabs ?? '' }}

    {{-- KOTAK KEDUA: KOTAK ROUNDED DENGAN BORDER GARIS PINGGIR UNGU UNTUK TABEL --}}
    <div class="bg-white border border-[#DDD6FE] rounded-[22px] overflow-hidden shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    @if($breakpoint === 'md')
                        <tr class="text-gray-900 text-xs font-semibold border-b border-[#DDD6FE] bg-[#F8F7FF]/50 hidden md:table-row">
                            {{ $header }}
                        </tr>
                    @elseif($breakpoint === 'lg')
                        <tr class="text-gray-900 text-xs font-semibold border-b border-[#DDD6FE] bg-[#F8F7FF]/50 hidden lg:table-row">
                            {{ $header }}
                        </tr>
                    @else
                        <tr class="text-gray-900 text-xs font-semibold border-b border-[#DDD6FE] bg-[#F8F7FF]/50 hidden xl:table-row">
                            {{ $header }}
                        </tr>
                    @endif
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
                <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold">
                    &lsaquo;
                </a>
            @endif

            {{-- Nomor Halaman --}}
            <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                @foreach ($paginator->getUrlRange(1, max(1, $paginator->lastPage())) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-7 h-7 rounded-full bg-white text-[#6E5BC3] border border-[#6E5BC3] flex items-center justify-center font-bold shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" class="w-7 h-7 rounded-full hover:bg-purple-50 hover:text-[#6E5BC3] text-gray-600 flex items-center justify-center transition-all">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            </div>

            {{-- Tombol Next (Bulat Ungu) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 rounded-full bg-[#6E5BC3] text-white hover:bg-[#5C4AB5] flex items-center justify-center transition-all shadow-sm shadow-[#6E5BC3]/30 font-bold">
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