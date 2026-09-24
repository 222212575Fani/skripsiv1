<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin - PROXIS' }}</title>
    
    <!-- Favicon Logo BPS -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_bps.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo_bps.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_bps.png') }}">
    
    <!-- Vite Assets (Alpine.js & CSS di-handle di sini) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts Preconnect & Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300..1000;1,300..1000&display=swap" rel="stylesheet">
    
    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        body { font-family: 'Mulish', sans-serif; background-color: #F8F7FC; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 10px; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar for Dropdowns & Modals */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #9E8CE3 transparent;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            margin: 6px 0;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #9E8CE3;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #6E5BC3;
        }

        /* Penyeragaman seluruh placeholder agar konsisten tipis (font-weight: 300) */
        input::placeholder,
        textarea::placeholder,
        select::placeholder,
        ::placeholder,
        ::-webkit-input-placeholder,
        ::-moz-placeholder,
        :-ms-input-placeholder {
            font-weight: 300 !important;
            font-family: 'Mulish', sans-serif !important;
            color: #9ca3af !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body class="m-0 p-0 text-[#2D2A4A] antialiased overflow-hidden flex flex-col h-screen h-[100dvh]" x-data="{ sidebarOpen: false }">
    <div class="flex flex-1 min-h-0 overflow-hidden relative">
        
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="-translate-x-full fixed inset-y-0 left-0 z-50 h-full flex-shrink-0 transition-transform duration-300 md:translate-x-0 md:static">
            <x-sidebar />
        </div>

        <main class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#F8F7FC]">
            <header class="h-16 sm:h-20 bg-white flex items-center justify-between px-3.5 sm:px-6 md:px-10 border-b border-purple-100/70 relative z-30 shrink-0">
                <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-[#6E5BC3] hover:text-[#5C4AB5] transition-colors focus:outline-none shrink-0 p-1 rounded-lg hover:bg-purple-50">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="sidebarOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="w-full max-w-md truncate">
                        @if (isset($headerTitle))
                            <div class="w-full">{!! $headerTitle !!}</div>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-3 sm:gap-6 shrink-0">
                    <!-- Bagian Notifikasi -->
                    <div class="flex items-center pr-3 sm:pr-6 border-r border-purple-100">
                        <x-notificationbell />
                    </div>

                    <!-- Dropdown Profil & Logout -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 sm:gap-4 focus:outline-none hover:opacity-80 transition-opacity cursor-pointer">
                            <div class="text-right hidden md:block">
                                <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()?->nama ?? 'Guest' }}</p>
                                <p class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest mt-0.5">{{ Auth::user()?->role?->nama_role ?? 'Visitor' }}</p>
                            </div>
                            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-linear-to-tr from-[#6E5BC3] to-[#8470E5] flex items-center justify-center text-white font-bold text-xs sm:text-sm shadow-md ring-2 ring-white shrink-0">
                                {{ strtoupper(substr(Auth::user()?->nama ?? 'G', 0, 1)) }}
                            </div>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="p-5 border-b border-gray-50 bg-gray-50/50 text-center">
                                <div class="w-12 h-12 rounded-full bg-[#6E5BC3] flex items-center justify-center text-white font-bold mx-auto mb-2 text-lg">
                                    {{ strtoupper(substr(Auth::user()?->nama ?? 'G', 0, 1)) }}
                                </div>
                                <p class="text-sm font-extrabold text-gray-900">{{ Auth::user()?->nama ?? 'Guest' }}</p>
                                <p class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">{{ Auth::user()?->role?->nama_role ?? 'Visitor' }}</p>
                            </div>
                            <div class="p-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 rounded-xl transition-colors cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-3.5 sm:p-6 md:p-10 flex-1 overflow-y-auto flex flex-col gap-4 sm:gap-6">
                @isset($pageHeader)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-1">
                        {{ $pageHeader }}
                    </div>
                @endisset

                <div class="flex flex-col gap-4 sm:gap-6">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    {{-- FOOTER MEMANJANG GLOBAL (SERAGAM DENGAN BACKGROUND SIDEBAR) --}}
    <footer class="w-full bg-white border-t border-purple-100/70 py-3 px-6 text-center shrink-0 z-30">
        <p class="text-[10px] md:text-[11px] font-bold text-[#6E5BC3]/70 uppercase tracking-widest">&copy; 2026 Badan Pusat Statistik</p>
    </footer>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/30 z-40 md:hidden" x-cloak></div>
    <x-toast />

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>