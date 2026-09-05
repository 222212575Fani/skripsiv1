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
<body class="m-0 p-0 text-[#1A1A1A] antialiased overflow-hidden" x-data="{ sidebarOpen: false }">
    {{-- 1. Bungkus layar penuh dengan h-screen dan overflow-hidden, ditambah state alpine sidebarOpen --}}
    <div class="flex h-screen overflow-hidden relative">
        
        {{-- Sidebar (Responsif: Fixed & Slide-over di layar kecil/sedang, Static di layar besar) --}}
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed inset-y-0 left-0 z-50 h-full flex-shrink-0 transition-transform duration-300 md:translate-x-0 md:static">
            <x-sidebar />
        </div>

        <main class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
            {{-- Header (Diam / Freeze di atas, dilengkapi Tombol Hamburger tanpa kotak background) --}}
            <header class="h-20 bg-white flex items-center justify-between px-6 md:px-10 border-b border-gray-100 shadow-sm relative z-30 shrink-0">
                <div class="flex items-center gap-4 flex-1">
                    {{-- Tombol Hamburger (Tanpa kotak latar belakang, hover warna ungu) --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-slate-500 hover:text-[#5C46F5] transition-colors focus:outline-none shrink-0">
                        {{-- Ikon Hamburger (Tampil saat sidebar tertutup) --}}
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        {{-- Ikon X / Silang (Tampil saat sidebar terbuka) --}}
                        <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="flex-1 max-w-md">
                        @if (isset($headerTitle))
                            <div class="w-full">{!! $headerTitle !!}</div>
                        @else
                            <h2 class="text-xl font-extrabold text-[#5C46F5]">Dashboard</h2>
                        @endif
                    </div>
                </div>
                
                {{-- Bagian Kanan Header (Notifikasi & Profil) --}}
                <div class="flex items-center gap-4">
                    
                    {{-- Komponen Dropdown Notifikasi --}}
                    <x-dropdownnotifikasi />

                    {{-- Dropdown Profil --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-4 pl-6 border-l border-gray-200 focus:outline-none hover:opacity-80 transition-opacity">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()?->nama ?? 'Guest' }}</p>
                                <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest mt-0.5">{{ Auth::user()?->role?->nama_role ?? 'Visitor' }}</p>
                            </div>
                            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white shrink-0">
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

                </div>
            </header>

            {{-- 2. Main Content (Hanya bagian ini yang bisa di-scroll dengan overflow-y-auto) --}}
            <div class="p-6 md:p-10 flex-1 overflow-y-auto flex flex-col gap-6">
                
                {{-- Slot opsional untuk Header Halaman (Judul & Tombol Aksi Utama yang Seragam) --}}
                @isset($pageHeader)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-1">
                        {{ $pageHeader }}
                    </div>
                @endisset

                {{-- Konten Utama Halaman --}}
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    {{-- Backdrop Hitam untuk Layar Kecil saat Sidebar Terbuka --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/30 z-40 md:hidden" x-cloak></div>

    {{-- Komponen Toast Global --}}
    <x-toast />
</body>
</html>