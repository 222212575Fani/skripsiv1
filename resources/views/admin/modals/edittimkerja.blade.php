<div x-data="{ 
        open: false, 
        id: '', 
        nama: '', 
        deskripsi: '', 
        ketua: '', 
        status: '' 
     }" 
     @open-modal-edit-tim.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        deskripsi = $event.detail.deskripsi; 
        ketua = $event.detail.ketua; 
        status = $event.detail.status;
     " 
     @close-modal-edit-tim.window="open = false"
     x-show="open" 
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

    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <div @click.away="open = false" 
             class="relative w-full max-w-xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-start justify-between p-8 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#5C46F5]/10 rounded-full flex items-center justify-center text-[#5C46F5]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-[900] text-gray-900 tracking-tight">Edit Tim Kerja</h3>
                        <p class="text-sm font-medium text-gray-400">Ubah struktur dan informasi tim kerja ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- SOLUSI FINAL: Menggunakan url() absolut yang terjamin bebas dari masalah Route Cache --}}
            <form action="{{ url('admin/manajementimkerja/update') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="id_tim" x-model="id">

                <div class="p-8 pt-4 space-y-6">
                    {{-- Nama Tim --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Nama Tim <span class="text-red-500">*</span></label>
                        <div class="col-span-2">
                            <input type="text" name="nama_tim" x-model="nama" required autocomplete="off"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium text-gray-700">
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="grid grid-cols-3 gap-4 items-start">
                        <label class="text-sm font-bold text-gray-600 pt-2 tracking-tight">Deskripsi</label>
                        <div class="col-span-2">
                            <textarea name="deskripsi_tim" x-model="deskripsi" rows="3" placeholder="Tuliskan deskripsi singkat..."
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium text-gray-700 resize-none placeholder:text-gray-400 placeholder:font-normal"></textarea>
                        </div>
                    </div>

                    {{-- Ketua Tim --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Ketua Tim <span class="text-red-500">*</span></label>
                        <div class="col-span-2 relative">
                            <select name="id_ketua_tim" required x-model="ketua"
                                :class="ketua === '' ? 'text-gray-400' : 'text-gray-700'"
                                class="w-full px-4 py-3 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium appearance-none cursor-pointer">
                                <option value="" disabled class="text-gray-400">Pilih Ketua Tim</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id_pengguna }}" class="text-gray-700">{{ $user->nama }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Status Tim --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Status</label>
                        <div class="col-span-2 flex gap-6">
                            <label class="inline-flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="status_tim" value="aktif" x-model="status" 
                                    class="w-4 h-4 text-[#5C46F5] border-gray-300 focus:ring-[#5C46F5]">
                                <span class="text-sm font-medium text-gray-500 group-hover:text-gray-700 transition-colors">Aktif</span>
                            </label>
                            <label class="inline-flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="status_tim" value="nonaktif" x-model="status" 
                                    class="w-4 h-4 text-[#5C46F5] border-gray-300 focus:ring-[#5C46F5]">
                                <span class="text-sm font-medium text-gray-500 group-hover:text-gray-700 transition-colors">Non-Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="p-8 border-t border-gray-50 flex items-center justify-end gap-3 bg-gray-50/30">
                    <button type="button" @click="open = false" class="px-6 py-3 bg-white border border-gray-200 text-red-500 rounded-full font-[800] text-xs hover:bg-red-50 transition-all uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" class="px-8 py-3 bg-[#5C46F5] text-white rounded-full font-[800] text-xs hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all active:scale-[0.98] uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>