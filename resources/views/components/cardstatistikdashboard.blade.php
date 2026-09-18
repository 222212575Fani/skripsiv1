@props([
    'title', 
    'value', 
    'subtitle' => null, 
    'percent' => null,
    'percentColor' => null,
    'trend' => null,
    'color' => 'text-[#6E5BC3]', 
    'bg' => 'bg-indigo-50', 
    'svgPath'
])

@php
    $percentTextColor = $percentColor ?? $color;
@endphp

<div {{ $attributes->merge(['class' => 'bg-white p-3.5 sm:p-4 xl:p-4 rounded-[20px] sm:rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-center transition-all hover:shadow-md min-w-0']) }}>
    <div class="flex flex-col justify-center min-w-0 flex-1 mr-2">
        <!-- Judul Atas (Warna abu gelap/hitam & font normal) -->
        <p class="text-[10px] sm:text-[11px] font-normal uppercase tracking-wider text-gray-900 truncate" title="{{ $title }}">{{ $title }}</p>
        
        <!-- Angka Nilai -->
        <p class="text-2xl sm:text-2xl xl:text-3xl font-extrabold {{ $color }} mt-0.5 sm:mt-1 tracking-tight leading-none">{{ $value }}</p>
        
        <!-- Persentase Bawah (Jika dioper) atau Subtitle Standar -->
        @if($percent)
            <div class="flex items-center gap-1 mt-1 sm:mt-1.5 text-[10px] sm:text-[11px] font-medium {{ $percentTextColor }} min-w-0" title="{{ $percent }}">
                @if($trend === 'up')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                @elseif($trend === 'down')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                    </svg>
                @elseif($trend === 'chart')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                @elseif($trend === 'check')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @elseif($trend === 'alert')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
                <span class="truncate">{{ $percent }}</span>
            </div>
        @elseif($subtitle)
            <p class="text-[10px] sm:text-[11px] font-normal {{ $color }} mt-1 sm:mt-1.5 truncate opacity-90" title="{{ $subtitle }}">
                <span>{{ $subtitle }}</span>
            </p>
        @endif
    </div>

    <!-- Ikon di Kanan -->
    <div class="w-10 h-10 sm:w-11 sm:h-11 xl:w-11 xl:h-11 rounded-xl sm:rounded-2xl {{ $bg }} flex items-center justify-center {{ $color }} shrink-0 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-5.5 sm:w-5.5 xl:h-5.5 xl:w-5.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $svgPath }}" />
        </svg>
    </div>
</div>