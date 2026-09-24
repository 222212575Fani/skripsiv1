@props([
    'paginator' => null,
    'itemName' => 'data',
    'breakpoint' => 'xl',
    'card' => false,
])

@php
    $breakpoint = $breakpoint ?? 'xl';
@endphp

<div class="w-full flex flex-col gap-4 sm:gap-6">
    
    {{-- Slot Atas (Judul, Search Bar, Tombol Tambah, & Tab Filter Status) --}}
    {{ $tabs ?? '' }}

    {{-- KARTU UTAMA TABEL SESUAI REFERENSI GAMBAR: SATU KARTU UTUH BERISI THEAD, TBODY, & PAGINASI DI DALAMNYA --}}
    <div class="bg-white border border-gray-200/80 rounded-2xl overflow-hidden shadow-xs">
        @if(isset($cardHeader))
            <div class="border-b border-gray-100 bg-white">
                {{ $cardHeader }}
            </div>
        @endif
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    @if($breakpoint === 'md')
                        <tr class="text-gray-500 text-[11px] font-semibold uppercase tracking-wider border-b border-gray-200/80 bg-[#FAF9FF] hidden md:table-row">
                            {{ $header }}
                        </tr>
                    @elseif($breakpoint === 'lg')
                        <tr class="text-gray-500 text-[11px] font-semibold uppercase tracking-wider border-b border-gray-200/80 bg-[#FAF9FF] hidden lg:table-row">
                            {{ $header }}
                        </tr>
                    @else
                        <tr class="text-gray-500 text-[11px] font-semibold uppercase tracking-wider border-b border-gray-200/80 bg-[#FAF9FF] hidden xl:table-row">
                            {{ $header }}
                        </tr>
                    @endif
                </thead>
                <tbody class="text-xs font-light divide-y divide-gray-100 bg-white">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        {{-- BAGIAN PAGINASI BERSATU DI DALAM KARTU TABEL (SEPERTI PADA GAMBAR REFERENSI) --}}
        @if(isset($paginator))
        <div class="px-4 sm:px-6 py-3.5 border-t border-gray-200/70 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-light text-gray-500">
            <div class="text-xs text-gray-500 font-light">
                Halaman <span class="font-normal text-gray-700">{{ $paginator->currentPage() }}</span> dari <span class="font-normal text-gray-700">{{ max(1, $paginator->lastPage()) }}</span>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2 max-w-full overflow-x-auto py-1">
                {{-- Tombol Previous (Kotak dengan border abu-abu) --}}
                @if ($paginator->onFirstPage())
                    <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed text-xs shrink-0 font-normal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:border-gray-300 flex items-center justify-center transition-all text-xs shrink-0 shadow-2xs font-normal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Nomor Halaman (Kotak modern sesuai referensi) --}}
                <div class="flex items-center gap-1 text-xs font-light text-gray-600 max-w-[220px] sm:max-w-none overflow-x-auto py-0.5">
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = max(1, $paginator->lastPage());
                        
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                        if ($currentPage <= 3) {
                            $end = min($lastPage, 5);
                        }
                        if ($currentPage >= $lastPage - 2) {
                            $start = max(1, $lastPage - 4);
                        }
                    @endphp

                    @if($start > 1)
                        <a href="{{ $paginator->url(1) }}" class="w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-center transition-all shrink-0">1</a>
                        @if($start > 2)
                            <span class="w-6 h-8 text-gray-400 flex items-center justify-center shrink-0">...</span>
                        @endif
                    @endif

                    @for($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span class="w-8 h-8 rounded-lg bg-[#6E5BC3] text-white flex items-center justify-center font-normal shadow-xs shrink-0">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $paginator->url($page) }}" class="w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-center transition-all shrink-0">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="w-6 h-8 text-gray-400 flex items-center justify-center shrink-0">...</span>
                        @endif
                        <a href="{{ $paginator->url($lastPage) }}" class="w-8 h-8 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 flex items-center justify-center transition-all shrink-0">{{ $lastPage }}</a>
                    @endif
                </div>

                {{-- Tombol Next (Kotak dengan border abu-abu) --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:border-gray-300 flex items-center justify-center transition-all text-xs shrink-0 shadow-2xs font-normal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg border border-gray-200 text-gray-300 flex items-center justify-center cursor-not-allowed text-xs shrink-0 font-normal">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>