<div x-data="{ open: false, ketua: '', status: 'belum_dimulai' }" 
     @open-modal-tambah-proyek.window="open = true" 
     @close-modal-tambah-proyek.window="open = false"
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
            
            {{-- Header Modal dengan Ikon Folder Proyek --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#5C46F5]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tambah Proyek Baru</h3>
                        <p class="text-xs font-medium text-gray-400">Lengkapi data proyek baru di bawah ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('ketuatim.manajemenproyek.store') ?? '#' }}" method="POST" autocomplete="off">
                @csrf

                <div class="p-8 space-y-5">
                    
                    {{-- Nama Proyek --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Proyek <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_proyek" placeholder="Masukkan nama proyek..." required autocomplete="off"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                    </div>

                    {{-- Deskripsi Proyek --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Proyek</label>
                        <textarea name="deskripsi" rows="3" placeholder="Tuliskan deskripsi atau ringkasan proyek..." 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal"></textarea>
                    </div>

                    {{-- Grid 2 Kolom (Ketua Proyek & Status Proyek) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- Dropdown Ketua Proyek --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Proyek <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="id_ketua_proyek" x-model="ketua" required 
                                    :class="ketua === '' ? 'text-gray-400' : 'text-gray-700'"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium cursor-pointer appearance-none">
                                    <option value="" disabled selected class="text-gray-400">Pilih Ketua Proyek</option>
                                    @foreach($anggotaTim ?? [] as $anggota)
                                        <option value="{{ $anggota->id_pengguna }}" class="text-gray-700">{{ $anggota->nama ?? $anggota->pengguna->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Status Proyek --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Proyek <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status" x-model="status" required 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer appearance-none">
                                    <option value="belum_dimulai">Belum Dimulai</option>
                                    <option value="berjalan">Berjalan</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="terlambat">Terlambat</option>
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Grid 2 Kolom (Tanggal Mulai & Tanggal Selesai) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tenggat_waktu" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                    </div>

                </div>

                {{-- Footer Action menggunakan <x-button> yang seragam --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit">
                        Tambah Proyek
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>