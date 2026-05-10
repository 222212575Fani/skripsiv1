<aside class="w-[280px] bg-white border-r border-gray-100 flex flex-col z-20 relative transition-all duration-300">
    <div class="p-8 mb-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-[#5C46F5] rounded-xl flex items-center justify-center text-white shadow-lg shadow-[#5C46F5]/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-gray-900 leading-none uppercase tracking-tight">SIS Project</h1>
                <p class="text-[10px] font-bold text-gray-400 uppercase mt-1 tracking-wider">BPS RI</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-1.5">
        <p class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Menu Utama</p>

        @php
            $menus = [
                ['route' => 'admin.manajemenpengguna', 'label' => 'Manajemen Pengguna', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['route' => 'admin.manajementimkerja', 'label' => 'Manajemen Tim Kerja', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
            ];
        @endphp

        @foreach($menus as $menu)
            @php $isActive = request()->routeIs($menu['route']); @endphp
            <a href="{{ route($menu['route']) }}" 
                class="group relative flex items-center gap-3 px-4 py-3 text-sm rounded-[14px] transition-all duration-200 
                {{ $isActive 
                    ? 'bg-[#F5F3FF] text-[#5C46F5] font-bold' 
                    : 'text-gray-500 hover:bg-gray-50 hover:text-[#5C46F5] font-medium' }}">
                
                {{-- Indikator Garis Sisi Kiri --}}
                @if($isActive)
                    <div class="absolute left-0 top-1/4 bottom-1/4 w-[4px] bg-[#5C46F5] rounded-r-full"></div>
                @endif
                
                <svg xmlns="http://www.w3.org/2000/svg" 
                    class="h-5 w-5 transition-colors {{ $isActive ? 'text-[#5C46F5]' : 'text-gray-400 group-hover:text-[#5C46F5]' }}" 
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu['icon'] }}" />
                </svg>
                
                {{ $menu['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="p-8 border-t border-gray-50">
        {{-- Diubah menjadi Badan Pusat Statistik dan tidak italic --}}
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">© 2026 Badan Pusat Statistik</p>
    </div>
</aside>