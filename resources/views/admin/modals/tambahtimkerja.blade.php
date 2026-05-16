<div x-data="{ open: false }" 
     @open-modal-tambah.window="open = true" 
     @close-modal-tambah.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    {{-- Backdrop: Efek Glassmorphism Tipis (1.5px) --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            {{-- Header: Rata Kiri & Clean --}}
            <div class="flex items-start justify-between p-8 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-[900] text-gray-900 tracking-tight">Tambah Tim Kerja</h3>
                        <p class="text-sm font-medium text-gray-400">Lengkapi data untuk membuat tim kerja baru.</p>
                    </div>
                </div>
                {{-- Tombol Close X --}}
                <button @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.timkerja.store') }}" method="POST">
                @csrf
                {{-- Body Form --}}
                <div class="p-8 pt-4 space-y-6">
                    {{-- Nama Tim --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Nama Tim <span class="text-red-500">*</span></label>
                        <div class="col-span-2">
                            <input type="text" name="nama_tim" placeholder="Masukkan nama tim" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-bold placeholder:text-gray-300">
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="grid grid-cols-3 gap-4 items-start">
                        <label class="text-sm font-bold text-gray-600 pt-2 tracking-tight">Deskripsi</label>
                        <div class="col-span-2">
                            <textarea name="deskripsi_tim" rows="3" placeholder="Tuliskan deskripsi singkat tentang tim ini"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-bold resize-none placeholder:text-gray-300"></textarea>
                        </div>
                    </div>

                    {{-- Ketua Tim dengan Ikon Dropdown SVG --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Ketua Tim <span class="text-red-500">*</span></label>
                        <div class="col-span-2 relative">
                            <select name="id_ketua_tim" required
                                class="w-full px-4 py-3 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-bold appearance-none cursor-pointer text-gray-700">
                                <option value="" disabled selected>Pilih Ketua Tim</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id_pengguna }}">{{ $user->nama }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Status Tim --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Status</label>
                        <div class="col-span-2 flex gap-6">
                            <label class="inline-flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="status_tim" value="aktif" checked 
                                    class="w-4 h-4 text-[#5C46F5] border-gray-300 focus:ring-[#5C46F5]">
                                <span class="text-sm font-bold text-gray-500 group-hover:text-gray-700 transition-colors">Aktif</span>
                            </label>
                            <label class="inline-flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="status_tim" value="nonaktif" 
                                    class="w-4 h-4 text-[#5C46F5] border-gray-300 focus:ring-[#5C46F5]">
                                <span class="text-sm font-bold text-gray-500 group-hover:text-gray-700 transition-colors">Non-Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer Action: Sebelahan di Kanan --}}
                <div class="p-8 border-t border-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="open = false" 
                        class="px-6 py-3 bg-white border border-gray-200 text-red-500 rounded-full font-[800] text-xs hover:bg-red-50 hover:border-red-200 transition-all uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-8 py-3 bg-[#5C46F5] text-white rounded-full font-[800] text-xs hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all active:scale-[0.98] uppercase tracking-widest">
                        Simpan Tim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>