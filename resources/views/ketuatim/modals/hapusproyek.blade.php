<div x-data="{ open: false, deleteUrl: '' }" 
     @open-modal-hapus-proyek.window="open = true; deleteUrl = $event.detail.url" 
     @close-modal-hapus-proyek.window="open = false"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[999] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-sm transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Modal dengan Ikon Peringatan Sampah --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-rose-50 border border-rose-100 rounded-xl flex items-center justify-center text-rose-500 shadow-sm relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Hapus Proyek</h3>
                        <p class="text-xs font-medium text-gray-400">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <p class="text-xs font-medium text-gray-600 leading-relaxed">
                    Apakah kamu yakin ingin menghapus data proyek ini secara permanen dari sistem?
                </p>
            </div>

            {{-- Footer Action Menggunakan Komponen Button --}}
            <form :action="deleteUrl" method="POST">
                @csrf
                @method('DELETE')
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3 bg-gray-50/50">
                    {{-- Tombol Batal (Menggunakan warna default ungu) --}}
                    <x-button type="button" @click="open = false" class="w-full flex-1">
                        Batal
                    </x-button>

                    {{-- Tombol Ya, Hapus (Menggunakan warna merah rose) --}}
                    <x-button type="submit" color="bg-rose-500 hover:bg-rose-600" shadow="shadow-md shadow-rose-500/20" class="w-full flex-1">
                        Ya, Hapus
                    </x-button>
                </div>
            </form>

        </div>
    </div>
</div>