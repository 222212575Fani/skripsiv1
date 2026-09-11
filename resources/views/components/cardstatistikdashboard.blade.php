@props([
    'title', 
    'value', 
    'subtitle', 
    'color' => 'text-[#5C46F5]', 
    'bg' => 'bg-indigo-50', 
    'svgPath'
])

<div class="bg-white p-5 rounded-[24px] shadow-sm border border-gray-100 flex justify-between items-center transition-all hover:shadow-md">
    <div class="flex flex-col justify-center">
        <!-- Judul Atas (Diubah jadi warna hitam & font normal) -->
        <p class="text-[11px] font-normal uppercase tracking-wider text-gray-900">{{ $title }}</p>
        
        <!-- Angka Nilai -->
        <p class="text-3xl font-extrabold {{ $color }} mt-1 tracking-tight">{{ $value }}</p>
        
        <!-- Subtitle Bawah (Font diubah menjadi normal, warna tetap mengikuti variabel) -->
        <p class="text-[11px] font-normal {{ $color }} mt-1.5 whitespace-nowrap opacity-90">
            <span>{{ $subtitle }}</span>
        </p>
    </div>

    <!-- Ikon di Kanan -->
    <div class="w-12 h-12 rounded-2xl {{ $bg }} flex items-center justify-center {{ $color }} shrink-0 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $svgPath }}" />
        </svg>
    </div>
</div>