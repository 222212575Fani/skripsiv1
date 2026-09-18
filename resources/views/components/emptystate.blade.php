@props([
    'title' => 'Tidak Ada Data Ditemukan',
    'message' => 'Tidak ada data yang sesuai dengan kata kunci pencarian atau filter yang Anda pilih.',
    'border' => true,
    'padding' => 'py-20 px-6'
])

<div {{ $attributes->merge(['class' => 'text-center bg-white flex flex-col items-center justify-center gap-3 w-full ' . $padding . ($border ? ' rounded-[28px] border border-dashed border-purple-200 shadow-sm' : '')]) }}>
    <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-[#6E5BC3] mb-1 shrink-0">
        @if(isset($customIcon))
            {{ $customIcon }}
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        @endif
    </div>
    <h3 class="text-sm font-bold text-gray-800">{{ $title }}</h3>
    <p class="text-xs text-gray-400 max-w-sm font-normal">
        {{ $message }}
    </p>
</div>

