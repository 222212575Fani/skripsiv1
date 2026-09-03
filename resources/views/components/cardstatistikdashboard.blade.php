@props([
    'title', 
    'value', 
    'subtitle', 
    'color' => 'text-[#5C46F5]', 
    'bg' => 'bg-indigo-50', 
    'svgPath'
])

<div class="bg-white p-6 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-start">
    <div>
        <p class="text-xs font-semibold {{ $color }} mt-2">{{ $title }}</p>
        <p class="text-3xl font-bold {{ $color }} mt-2">{{ $value }}</p>
        <p class="text-[11px] font-medium {{ $color }} mt-2">
            <span>{{ $subtitle }}</span>
        </p>
    </div>
    <div class="w-12 h-12 rounded-2xl {{ $bg }} flex items-center justify-center {{ $color }} shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $svgPath }}" />
        </svg>
    </div>
</div>