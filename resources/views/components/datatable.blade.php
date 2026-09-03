@props(['paginator', 'itemName' => 'data'])

<div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
    {{-- Slot Opsional untuk Filter Tab di atas tabel jika ada --}}
    {{ $tabs ?? '' }}

    {{-- Tabel Utama --}}
    <div class="w-full overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-gray-400 text-[10px] uppercase tracking-wider font-bold border-b border-gray-100 bg-gray-50/30">
                    {{ $header }}
                </tr>
            </thead>
            <tbody class="font-medium text-sm">
                {{ $slot }}
            </tbody>
        </table>

        {{-- Footer Paginasi Kustom Reusable dengan Dynamic Item Name --}}
        @if(isset($paginator))
        <div class="px-8 py-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-500">
            <div>
                Menampilkan {{ $paginator->firstItem() ?? 0 }} sampai {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} {{ $itemName }}
            </div>

            <div class="flex items-center gap-2">
                {{-- Tombol Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1.5 text-gray-300 cursor-not-allowed flex items-center gap-1 font-semibold">&lsaquo; Previous</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-gray-600 hover:text-[#5C46F5] flex items-center gap-1 font-semibold transition-colors">&lsaquo; Previous</a>
                @endif

                {{-- Nomor Halaman --}}
                @foreach ($paginator->getUrlRange(1, max(1, $paginator->lastPage())) as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-8 h-8 rounded-xl bg-[#5C46F5] text-white flex items-center justify-center font-bold shadow-sm shadow-[#5C46F5]/30">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-xl text-gray-600 hover:bg-gray-100 flex items-center justify-center font-semibold transition-all">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Tombol Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-gray-600 hover:text-[#5C46F5] flex items-center gap-1 font-semibold transition-colors">Next &rsaquo;</a>
                @else
                    <span class="px-3 py-1.5 text-gray-300 cursor-not-allowed flex items-center gap-1 font-semibold">Next &rsaquo;</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>