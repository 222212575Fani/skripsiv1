<div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none">
    
    {{-- 1. POP-UP BERHASIL --}}
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 4500)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="pointer-events-auto flex items-start gap-4 p-4 w-[400px] bg-[#F4FBFA] border border-[#E8F5F3] rounded-[16px] shadow-lg relative">
            
            {{-- Ikon Bulat Putih -> Hijau --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm mt-0.5">
                <div class="w-7 h-7 rounded-full bg-[#52C488] flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Teks --}}
            <div class="flex-1 text-left pr-6">
                <h3 class="text-[15px] font-bold text-gray-800 tracking-tight">Berhasil Disimpan</h3>
                <p class="text-[13px] text-gray-500 font-medium mt-1 leading-relaxed">{{ session('success') }}</p>
            </div>
            
            {{-- Tombol Close --}}
            <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    {{-- 2. POP-UP GAGAL / ERROR VALIDASI --}}
    @if(session('error') || $errors->any())
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="pointer-events-auto flex items-start gap-4 p-4 w-[400px] bg-[#FEF5F5] border border-[#FCE8E8] rounded-[16px] shadow-lg relative">
            
            {{-- Ikon Bulat Putih -> Merah --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm mt-0.5">
                <div class="w-7 h-7 rounded-full bg-[#EF5350] flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Teks --}}
            <div class="flex-1 text-left pr-6">
                <h3 class="text-[15px] font-bold text-gray-800 tracking-tight">Terjadi Kesalahan</h3>
                <p class="text-[13px] text-gray-500 font-medium mt-1 leading-relaxed">{{ session('error') ?? $errors->first() }}</p>
            </div>
            
            {{-- Tombol Close --}}
            <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

</div>