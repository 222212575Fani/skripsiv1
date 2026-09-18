@props([
    'type' => 'button',
    'color' => 'bg-[#6E5BC3] hover:bg-[#5C4AB5]', // Default warna ungu utama
    'shadow' => 'shadow-md shadow-[#6E5BC3]/20',
])

<button type="{{ $type }}" {{ $attributes->merge(['class' => "px-5 py-2.5 {$color} text-white rounded-xl font-bold text-xs transition-all inline-flex items-center justify-center gap-2 shrink-0 {$shadow}"]) }}>
    {{ $slot }}
</button>