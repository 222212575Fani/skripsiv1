@props(['title' => 'PROXIS', 'showNotification' => true])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PROXIS' }}</title>
    
    <!-- Vite CSS & JS (Tanpa Alpine di app.js) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- TAMBAHKAN ALPINE CDN KHUSUS DI SINI AGAR TIDAK BENTROK DENGAN SWAGGER DI FILE LAIN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Google Fonts Preconnect & Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300..1000;1,300..1000&display=swap" rel="stylesheet">
    
    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        body { font-family: 'Mulish', sans-serif; background-color: #F8F7FF; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 10px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8F7FF] font-sans antialiased overflow-x-hidden m-0 p-0 relative min-h-screen text-[#2D2A4A]">

    {{-- KONTAINER UTAMA LAYOUT FULL --}}
    <div class="min-h-screen w-full flex flex-col bg-[#F8F7FF]">
        
        {{-- BILAH ATAS UNGU --}}
        <div class="w-full bg-[#6E5BC3] pt-7 pb-16 relative shadow-inner">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between">
                <div>
                    {{ $headerAction ?? '' }}
                </div>
                <div class="flex items-center gap-4">
                    @if($showNotification)
                        <x-notificationbell :white="true" />
                    @endif
                </div>
            </div>
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="flex-1 w-full max-w-7xl mx-auto px-6 lg:px-10 -mt-8 pb-12 relative z-10">
            {{ $slot }}
        </div>

        {{-- FOOTER MEMANJANG GLOBAL (WARNA UNGU SERAGAM DENGAN SIDEBAR) --}}
        <footer class="w-full bg-[#F5F3FF] border-t border-purple-200/60 py-3 px-6 text-center shrink-0 z-20">
            <p class="text-[10px] md:text-[11px] font-bold text-[#6E5BC3]/70 uppercase tracking-widest">&copy; 2026 Badan Pusat Statistik</p>
        </footer>

    </div>

    <x-toast />

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>