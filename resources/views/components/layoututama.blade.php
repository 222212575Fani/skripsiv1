<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin - SIS Project' }}</title>
    
    {{-- Memanggil Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Utama --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Alpine.js --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8F7FF; 
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #5C46F5; border-radius: 10px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="m-0 p-0 text-[#1A1A1A] antialiased">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-sidebar />

        <main class="flex-1 flex flex-col min-w-0">
            {{-- Header --}}
            <header class="h-20 bg-white flex items-center justify-between px-10 border-b border-gray-100 shadow-sm relative z-30">
                <div class="flex-1">
                    @if (isset($headerTitle))
                        <div class="max-w-md w-full">{!! $headerTitle !!}</div>
                    @else
                        <h2 class="text-xl font-extrabold text-[#5C46F5]">Dashboard</h2>
                    @endif
                </div>
                
                {{-- Dropdown Profil --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-4 pl-6 border-l border-gray-200 focus:outline-none hover:opacity-80 transition-opacity">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()?->nama ?? 'Guest' }}</p>
                            <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest mt-0.5">{{ Auth::user()?->role?->nama_role ?? 'Visitor' }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white">
                            {{ strtoupper(substr(Auth::user()?->nama ?? 'G', 0, 1)) }}
                        </div>
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false" x-transition class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                        <div class="p-5 border-b border-gray-50 bg-gray-50/50 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#5C46F5] flex items-center justify-center text-white font-bold mx-auto mb-2 text-lg">
                                {{ strtoupper(substr(Auth::user()?->nama ?? 'G', 0, 1)) }}
                            </div>
                            <p class="text-sm font-extrabold text-gray-900">{{ Auth::user()?->nama ?? 'Guest' }}</p>
                            <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest">{{ Auth::user()?->role?->nama_role ?? 'Visitor' }}</p>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <div class="p-10 flex-1">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Komponen Toast Global --}}
    <x-toast />
</body>
</html>