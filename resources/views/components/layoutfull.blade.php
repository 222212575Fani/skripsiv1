<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SIS PROJECT' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#F8F7FF] font-sans antialiased overflow-x-hidden m-0 p-0 relative min-h-screen">

    {{-- KONTANTER UTAMA LAYOUT FULL --}}
    <div class="min-h-screen w-full flex flex-col bg-[#F8F7FF]">
        
        {{-- BILAH ATAS UNGU: Menggunakan max-w-7xl dan padding yang persis sama dengan konten bawah --}}
        <div class="w-full bg-[#6E5BC3] pt-7 pb-16 relative">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between">
                <div>
                    {{ $headerAction ?? '' }}
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-white/40 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-white/40 inline-block"></span>
                    <span class="w-3 h-3 rounded-full bg-white/40 inline-block"></span>
                </div>
            </div>
        </div>

        {{-- KONTEN UTAMA --}}
        <div class="flex-1 w-full max-w-7xl mx-auto px-6 lg:px-10 -mt-8 pb-12 relative z-10">
            {{ $slot }}
        </div>

    </div>

</body>
</html>