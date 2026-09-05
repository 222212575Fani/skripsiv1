@props([
    'type' => 'button',
    'color' => 'bg-[#5C46F5] hover:bg-[#4A38D4]', // Default warna ungu utama
    'shadow' => 'shadow-md shadow-[#5C46F5]/20',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => "px-5 py-2.5 {$color} text-white rounded-xl font-bold text-xs transition-all inline-flex items-center justify-center gap-2 shrink-0 {$shadow}"]) }}>
    {{ $slot }}
</button>