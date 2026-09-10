<aside class="w-[280px] bg-[#F5F3FF] text-[#5C46F5] flex flex-col z-20 relative transition-all duration-300 min-h-screen justify-between border-r border-purple-100/60">
    <div class="flex flex-col">
        <!-- Logo / Brand -->
        <div class="p-8 mb-2">
            <div class="flex items-center gap-4">
                {{-- Tombol X untuk mobile --}}
                <button @click="sidebarOpen = false" class="md:hidden text-[#5C46F5]/70 hover:text-[#5C46F5] transition-colors focus:outline-none shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex items-center gap-4 flex-1">
                    <img src="{{ asset('images/logo_bps.png') }}" alt="Logo BPS" class="h-10 w-auto object-contain bg-transparent">
                    <div>
                        <h1 class="text-sm font-extrabold text-[#5C46F5] leading-none uppercase tracking-tight">SIS Project</h1>
                        <p class="text-[10px] font-bold text-[#5C46F5]/70 uppercase mt-1.5 tracking-widest">BPS RI</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto">
            @php
                $user = auth()->user();
                $role = $user->role->nama_role ?? '';
                $userId = $user->id_pengguna ?? $user->id;

                $isKetuaProyek = \App\Models\AnggotaProyek::where('id_pengguna', $userId)
                    ->where('id_peran_proyek', 1)
                    ->exists();

                $isAnggotaProyek = \App\Models\AnggotaProyek::where('id_pengguna', $userId)
                    ->where('id_peran_proyek', 2)
                    ->exists() || \App\Models\AktivitasProyek::where('id_penanggung_jawab', $userId)->exists();

                $menus = [];

                if ($role === 'Admin') {
                    $menus = [
                        ['route' => 'admin.manajemenpengguna', 'label' => 'Manajemen Pengguna', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['route' => 'admin.manajementimkerja', 'label' => 'Manajemen Tim Kerja', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ];
                } elseif ($role === 'Direktur') {
                    $menus = [
                        ['route' => 'direktur.dashboard', 'label' => 'Dashboard Monitoring', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ];
                } elseif ($role === 'Ketua Tim') {
                    $menus = [
                        ['route' => 'ketuatim.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'ketuatim.manajemenproyek', 'label' => 'Manajemen Proyek', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ];
                } else {
                    // Menu Proyek (Jika menjadi Ketua Proyek)
                    if ($isKetuaProyek) {
                        $menus[] = [
                            'route' => 'anggota.proyekaktivitas', 
                            'label' => 'Proyek', 
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
                        ];
                    }
                    
                    // Menu Aktivitas Saya (Jika menjadi Anggota Proyek / PJ Tugas)
                    if ($isAnggotaProyek || (!$isKetuaProyek && !$isAnggotaProyek)) {
                        $menus[] = [
                            'route' => 'anggota.aktivitassaya', 
                            'label' => 'Aktivitas Saya', 
                            'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
                        ];
                    }
                }
            @endphp

            @forelse($menus as $menu)
                @php $isActive = request()->routeIs($menu['route']); @endphp
                
                <a href="{{ route($menu['route']) }}" 
                    class="group flex items-center justify-between px-4 py-3 text-xs rounded-2xl transition-all duration-200 font-bold {{ $isActive ? 'bg-[#5C46F5] text-white shadow-md shadow-[#5C46F5]/25 font-extrabold' : 'text-[#5C46F5]/80 hover:bg-[#5C46F5] hover:text-white hover:shadow-md hover:shadow-[#5C46F5]/25' }}">
                    
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $isActive ? 'text-white' : 'text-[#5C46F5]/80 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}" />
                        </svg>
                        <span>{{ $menu['label'] }}</span>
                    </div>
                </a>
            @empty
                <p class="px-4 text-xs text-[#5C46F5]/50 italic">Tidak ada menu tersedia.</p>
            @endforelse
        </nav>
    </div>

    <!-- Footer Sidebar -->
    <div class="p-8 border-t border-purple-200/50">
        <p class="text-[10px] font-bold text-[#5C46F5]/60 uppercase tracking-widest">&copy; 2026 Badan Pusat Statistik</p>
    </div>
</aside>