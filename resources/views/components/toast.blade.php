<div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3.5 pointer-events-none max-w-[calc(100vw-2rem)] w-[420px]">
    
    {{-- 1. POP-UP BERHASIL (SUCCESS) --}}
    @if(session('success') && !session('register_success'))
        <div x-data="{ 
                show: true, 
                timeout: null,
                init() { this.timeout = setTimeout(() => this.show = false, 5000); },
                pause() { clearTimeout(this.timeout); },
                resume() { this.timeout = setTimeout(() => this.show = false, 3000); }
             }"
             x-show="show" 
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="pointer-events-auto relative flex items-center gap-3.5 sm:gap-4 px-4 py-3.5 sm:px-4.5 sm:py-4 w-full bg-[#D1F2D9] border-b-[4px] border-[#86EFAC] rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] select-none">
            
            {{-- Tombol Close Bundar Putih dengan Silang Merah di Pojok Kanan Atas --}}
            <button type="button" 
                    @click="show = false" 
                    class="absolute -top-2.5 -right-2.5 sm:-top-3 sm:-right-3 w-6 h-6 sm:w-6.5 sm:h-6.5 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 hover:scale-110 active:scale-95 transition-all duration-150 cursor-pointer z-10" 
                    title="Tutup Notifikasi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Ikon Kiri: Bulat Hijau Solid dengan Centang Putih --}}
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#22C55E] flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            {{-- Konten Teks --}}
            <div class="flex-1 min-w-0 text-left pr-2">
                <h4 class="text-sm sm:text-[15px] font-extrabold text-[#16A34A] tracking-tight leading-tight">
                    Berhasil
                </h4>
                <p class="text-xs sm:text-[13px] text-gray-700 font-normal leading-relaxed mt-0.5 break-words">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif

    {{-- 2. POP-UP INFORMASI (INFO) --}}
    @if(session('info'))
        <div x-data="{ 
                show: true, 
                timeout: null,
                init() { this.timeout = setTimeout(() => this.show = false, 5000); },
                pause() { clearTimeout(this.timeout); },
                resume() { this.timeout = setTimeout(() => this.show = false, 3000); }
             }"
             x-show="show" 
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="pointer-events-auto relative flex items-center gap-3.5 sm:gap-4 px-4 py-3.5 sm:px-4.5 sm:py-4 w-full bg-[#CFE8FF] border-b-[4px] border-[#93C5FD] rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] select-none">
            
            {{-- Tombol Close Bundar Putih dengan Silang Merah di Pojok Kanan Atas --}}
            <button type="button" 
                    @click="show = false" 
                    class="absolute -top-2.5 -right-2.5 sm:-top-3 sm:-right-3 w-6 h-6 sm:w-6.5 sm:h-6.5 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 hover:scale-110 active:scale-95 transition-all duration-150 cursor-pointer z-10" 
                    title="Tutup Notifikasi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Ikon Kiri: Bulat Biru Solid dengan Huruf 'i' Putih --}}
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#0284C7] flex items-center justify-center shrink-0 shadow-xs">
                <span class="text-white font-black font-sans text-lg sm:text-xl leading-none select-none">i</span>
            </div>

            {{-- Konten Teks --}}
            <div class="flex-1 min-w-0 text-left pr-2">
                <h4 class="text-sm sm:text-[15px] font-extrabold text-[#0284C7] tracking-tight leading-tight">
                    Informasi
                </h4>
                <p class="text-xs sm:text-[13px] text-gray-700 font-normal leading-relaxed mt-0.5 break-words">
                    {{ session('info') }}
                </p>
            </div>
        </div>
    @endif

    {{-- 3. POP-UP PERINGATAN (WARNING) --}}
    @if(session('warning'))
        <div x-data="{ 
                show: true, 
                timeout: null,
                init() { this.timeout = setTimeout(() => this.show = false, 5000); },
                pause() { clearTimeout(this.timeout); },
                resume() { this.timeout = setTimeout(() => this.show = false, 3000); }
             }"
             x-show="show" 
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="pointer-events-auto relative flex items-center gap-3.5 sm:gap-4 px-4 py-3.5 sm:px-4.5 sm:py-4 w-full bg-[#FDE8C8] border-b-[4px] border-[#FCD34D] rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] select-none">
            
            {{-- Tombol Close Bundar Putih dengan Silang Merah di Pojok Kanan Atas --}}
            <button type="button" 
                    @click="show = false" 
                    class="absolute -top-2.5 -right-2.5 sm:-top-3 sm:-right-3 w-6 h-6 sm:w-6.5 sm:h-6.5 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 hover:scale-110 active:scale-95 transition-all duration-150 cursor-pointer z-10" 
                    title="Tutup Notifikasi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Ikon Kiri: Bulat Amber Solid dengan Tanda Seru '!' Putih --}}
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F59E0B] flex items-center justify-center shrink-0 shadow-xs">
                <span class="text-white font-black font-sans text-lg sm:text-xl leading-none select-none">!</span>
            </div>

            {{-- Konten Teks --}}
            <div class="flex-1 min-w-0 text-left pr-2">
                <h4 class="text-sm sm:text-[15px] font-extrabold text-[#D97706] tracking-tight leading-tight">
                    Peringatan
                </h4>
                <p class="text-xs sm:text-[13px] text-gray-700 font-normal leading-relaxed mt-0.5 break-words">
                    {{ session('warning') }}
                </p>
            </div>
        </div>
    @endif

    {{-- 4. POP-UP GAGAL / ERROR VALIDASI (ERROR) --}}
    @if((session('error') || $errors->any()) && !session('account_pending'))
        <div x-data="{ 
                show: true, 
                timeout: null,
                init() { this.timeout = setTimeout(() => this.show = false, 6000); },
                pause() { clearTimeout(this.timeout); },
                resume() { this.timeout = setTimeout(() => this.show = false, 3000); }
             }"
             x-show="show" 
             @mouseenter="pause()"
             @mouseleave="resume()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="pointer-events-auto relative flex items-center gap-3.5 sm:gap-4 px-4 py-3.5 sm:px-4.5 sm:py-4 w-full bg-[#FCDADA] border-b-[4px] border-[#FDA4AF] rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] select-none">
            
            {{-- Tombol Close Bundar Putih dengan Silang Merah di Pojok Kanan Atas --}}
            <button type="button" 
                    @click="show = false" 
                    class="absolute -top-2.5 -right-2.5 sm:-top-3 sm:-right-3 w-6 h-6 sm:w-6.5 sm:h-6.5 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 hover:scale-110 active:scale-95 transition-all duration-150 cursor-pointer z-10" 
                    title="Tutup Notifikasi">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Ikon Kiri: Bulat Merah Solid dengan Tanda Silang '×' Putih --}}
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EF4444] flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            {{-- Konten Teks --}}
            <div class="flex-1 min-w-0 text-left pr-2">
                <h4 class="text-sm sm:text-[15px] font-extrabold text-[#DC2626] tracking-tight leading-tight">
                    Terjadi Kesalahan
                </h4>
                <p class="text-xs sm:text-[13px] text-gray-700 font-normal leading-relaxed mt-0.5 break-words">
                    {{ session('error') ?? $errors->first() }}
                </p>
            </div>
        </div>
    @endif

    {{-- 5. DYNAMIC JAVASCRIPT DISPATCHED TOASTS (@toast.window) --}}
    <div x-data="{
            dynamicToasts: [],
            addToast(detail) {
                const id = Date.now() + Math.random();
                const type = detail.type || 'info';
                let defaultTitle = 'Informasi';
                if (type === 'success') defaultTitle = 'Berhasil';
                else if (type === 'error') defaultTitle = 'Terjadi Kesalahan';
                else if (type === 'warning') defaultTitle = 'Peringatan';

                const toast = {
                    id: id,
                    type: type,
                    title: detail.title || defaultTitle,
                    message: detail.message || '',
                };
                this.dynamicToasts.push(toast);
                setTimeout(() => this.removeToast(id), 5000);
            },
            removeToast(id) {
                this.dynamicToasts = this.dynamicToasts.filter(t => t.id !== id);
            }
         }"
         @toast.window="addToast($event.detail)"
         class="contents">
        
        <template x-for="t in dynamicToasts" :key="t.id">
            <div class="pointer-events-auto relative flex items-center gap-3.5 sm:gap-4 px-4 py-3.5 sm:px-4.5 sm:py-4 w-full rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] select-none transition-all duration-300"
                 :class="{
                     'bg-[#D1F2D9] border-b-[4px] border-[#86EFAC]': t.type === 'success',
                     'bg-[#CFE8FF] border-b-[4px] border-[#93C5FD]': t.type === 'info',
                     'bg-[#FDE8C8] border-b-[4px] border-[#FCD34D]': t.type === 'warning',
                     'bg-[#FCDADA] border-b-[4px] border-[#FDA4AF]': t.type === 'error'
                 }">
                
                {{-- Tombol Tutup --}}
                <button type="button" 
                        @click="removeToast(t.id)" 
                        class="absolute -top-2.5 -right-2.5 sm:-top-3 sm:-right-3 w-6 h-6 sm:w-6.5 sm:h-6.5 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-rose-500 hover:text-rose-600 hover:bg-rose-50 hover:scale-110 active:scale-95 transition-all duration-150 cursor-pointer z-10" 
                        title="Tutup Notifikasi">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Ikon --}}
                <template x-if="t.type === 'success'">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#22C55E] flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </template>
                <template x-if="t.type === 'info'">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#0284C7] flex items-center justify-center shrink-0 shadow-xs">
                        <span class="text-white font-black font-sans text-lg sm:text-xl leading-none select-none">i</span>
                    </div>
                </template>
                <template x-if="t.type === 'warning'">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#F59E0B] flex items-center justify-center shrink-0 shadow-xs">
                        <span class="text-white font-black font-sans text-lg sm:text-xl leading-none select-none">!</span>
                    </div>
                </template>
                <template x-if="t.type === 'error'">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#EF4444] flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </template>

                {{-- Konten Teks --}}
                <div class="flex-1 min-w-0 text-left pr-2">
                    <h4 class="text-sm sm:text-[15px] font-extrabold tracking-tight leading-tight"
                        :class="{
                            'text-[#16A34A]': t.type === 'success',
                            'text-[#0284C7]': t.type === 'info',
                            'text-[#D97706]': t.type === 'warning',
                            'text-[#DC2626]': t.type === 'error'
                        }"
                        x-text="t.title"></h4>
                    <p class="text-xs sm:text-[13px] text-gray-700 font-normal leading-relaxed mt-0.5 break-words" x-text="t.message"></p>
                </div>
            </div>
        </template>
    </div>

</div>