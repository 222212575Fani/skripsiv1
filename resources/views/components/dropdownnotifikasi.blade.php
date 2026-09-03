<div class="relative" x-data="{ notifOpen: false }">
    {{-- Tombol Lonceng --}}
    <button @click="notifOpen = !notifOpen" class="relative text-gray-400 hover:text-[#5C46F5] transition-colors focus:outline-none p-2 rounded-xl hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        {{-- Titik Merah Indikator Notifikasi Baru --}}
        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
    </button>

    {{-- Kotak Dropdown Notifikasi yang Lebih Ramping (w-72 md:w-80) --}}
    <div x-show="notifOpen" x-cloak @click.away="notifOpen = false" x-transition 
         class="absolute right-0 mt-3 w-72 md:w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
        
        {{-- Header Dropdown --}}
        <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="text-xs font-black uppercase tracking-wider text-gray-800">Notifikasi</h3>
            <button class="text-[10px] font-bold text-[#5C46F5] hover:underline">Tandai sudah dibaca</button>
        </div>

        {{-- Daftar List Notifikasi --}}
        <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
            
            {{-- Item Notifikasi 1 --}}
            <div class="p-3.5 hover:bg-gray-50/80 transition-colors flex items-start gap-3 cursor-pointer relative">
                <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 pr-2">
                    <p class="text-xs font-bold text-gray-800 leading-tight">Proyek Baru Ditugaskan</p>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">Anda telah ditunjuk sebagai Ketua Tim pada proyek baru.</p>
                    <span class="text-[10px] text-gray-400 font-medium mt-1 block">10 menit yang lalu</span>
                </div>
                <span class="w-2 h-2 bg-[#5C46F5] rounded-full absolute top-4 right-3"></span>
            </div>

            {{-- Item Notifikasi 2 --}}
            <div class="p-3.5 hover:bg-gray-50/80 transition-colors flex items-start gap-3 cursor-pointer relative">
                <div class="w-7 h-7 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1 pr-2">
                    <p class="text-xs font-bold text-gray-800 leading-tight">Tenggat Waktu Proyek</p>
                    <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">Proyek "Pembangunan Jembatan" mendekati tenggat waktu.</p>
                    <span class="text-[10px] text-gray-400 font-medium mt-1 block">1 jam yang lalu</span>
                </div>
                <span class="w-2 h-2 bg-[#5C46F5] rounded-full absolute top-4 right-3"></span>
            </div>

        </div>

        {{-- Footer Dropdown --}}
        <div class="p-2.5 border-t border-gray-100 bg-gray-50/50 text-center">
            <a href="#" class="text-xs font-bold text-[#5C46F5] hover:underline">Lihat semua notifikasi</a>
        </div>
    </div>
</div>