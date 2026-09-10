<div x-data="{ 
        open: false, 
        id_proyek: '', 
        nama_proyek: '', 
        deskripsi_proyek: '', 
        id_ketua_proyek: '', 
        status_proyek: '',
        tanggal_mulai: '',
        tanggal_target_selesai: '',
        initialStatus: '' 
    }" 
    @open-modal-edit-proyek.window="
        open = true; 
        id_proyek = $event.detail.id_proyek; 
        nama_proyek = $event.detail.nama_proyek; 
        deskripsi_proyek = $event.detail.deskripsi_proyek; 
        id_ketua_proyek = $event.detail.id_ketua_proyek; 
        status_proyek = $event.detail.status_proyek;
        tanggal_mulai = $event.detail.tanggal_mulai;
        tanggal_target_selesai = $event.detail.tanggal_target_selesai;
        initialStatus = $event.detail.status_proyek;
    " 
    @close-modal-edit-proyek.window="open = false"
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
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Ala Referensi --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-[#5C46F5] shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Edit Proyek</h3>
                        <p class="text-xs font-medium text-gray-400">Perbarui informasi, penanggung jawab, dan tenggat waktu proyek.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="`{{ url('ketuatim/proyek') }}/${id_proyek}`" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Proyek & Status) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Proyek <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_proyek" x-model="nama_proyek" required 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Proyek <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status" x-model="status_proyek" required 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 appearance-none cursor-pointer">
                                    <option value="belum_dimulai">Belum Dimulai</option>
                                    <option value="berjalan">Sedang Berjalan</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="terlambat">Terlambat</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ketua Proyek (Full Width) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Proyek <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="id_ketua_proyek" x-model="id_ketua_proyek" required 
                                class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 appearance-none cursor-pointer">
                                <option value="">Pilih Ketua Proyek</option>
                                @foreach($anggotaTim ?? [] as $member)
                                    <option value="{{ $member->pengguna->id_pengguna ?? $member->id_pengguna }}">
                                        {{ $member->pengguna->nama ?? $member->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Mulai & Tenggat Waktu (Grid 2 Kolom) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" x-model="tanggal_mulai" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tenggat Waktu</label>
                            <input type="date" name="tenggat_waktu" x-model="tanggal_target_selesai" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                    </div>

                    {{-- Deskripsi (Full Width Textarea) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Proyek</label>
                        <textarea name="deskripsi" x-model="deskripsi_proyek" rows="3" placeholder="Tuliskan deskripsi singkat proyek..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 resize-none"></textarea>
                    </div>

                </div>

                {{-- Footer Action Menggunakan Komponen <x-button> --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>