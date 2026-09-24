<div x-data="{ 
        open: false, 
        namaProyek: '',
        ketua: '', 
        ketuaNama: '', 
        ketuaOpen: false, 
        status: 'belum_dimulai', 
        statusLabel: 'Belum Dimulai', 
        statusOpen: false 
    }" 
     @open-modal-tambah-proyek.window="open = true; namaProyek = '';" 
     @close-modal-tambah-proyek.window="open = false"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-999 overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    {{-- CSS Kustom untuk Ikon Kalender Ungu & Scrollbar Estetik --}}
    <style>
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(38%) sepia(85%) saturate(1541%) hue-rotate(230deg) brightness(95%) contrast(92%);
            cursor: pointer;
        }
        .pj-dropdown-scroll {
            scrollbar-width: thin;
            scrollbar-color: #9E8CE3 #F8F7FF;
            scrollbar-gutter: stable;
        }
        .pj-dropdown-scroll::-webkit-scrollbar { width: 6px; }
        .pj-dropdown-scroll::-webkit-scrollbar-track { background: #F8F7FF; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 9999px; }
        .pj-dropdown-scroll::-webkit-scrollbar-thumb:hover { background: #6E5BC3; }
    </style>

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Modal dengan Ikon Folder Proyek --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm relative shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Tambah Proyek Baru</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Lengkapi data proyek baru di bawah ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('ketuatim.manajemenproyek.store') ?? '#' }}" method="POST" autocomplete="off">
                @csrf

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Nama Proyek --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Nama Proyek <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_proyek" placeholder="Masukkan nama proyek..." maxlength="200" required autocomplete="off"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                    </div>

                    {{-- Deskripsi Proyek --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Deskripsi Proyek</label>
                        <textarea name="deskripsi" rows="3" maxlength="2000" placeholder="Tuliskan deskripsi atau ringkasan proyek..." 
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>

                    {{-- Grid 2 Kolom (Ketua Proyek & Status Proyek) dengan Dropdown Buka ke Bawah --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- Dropdown Kustom Ketua Proyek --}}
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Ketua Proyek <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="ketuaOpen = false">
                                <input type="hidden" name="id_ketua_proyek" x-model="ketua" required>
                                <button type="button" @click="ketuaOpen = !ketuaOpen; statusOpen = false;"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                    <span :class="ketuaNama ? 'text-gray-700 font-light' : 'text-gray-400 font-light'" x-text="ketuaNama || 'Pilih Ketua Proyek'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="ketuaOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="ketuaOpen" x-cloak 
                                    class="custom-scrollbar absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-48 overflow-y-auto">
                                    @forelse($anggotaTim ?? [] as $anggota)
                                        <button type="button" 
                                            @click="ketua = '{{ $anggota->id_pengguna }}'; ketuaNama = '{{ addslashes($anggota->nama ?? $anggota->pengguna->nama) }}'; ketuaOpen = false"
                                            class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                            :class="ketua == '{{ $anggota->id_pengguna }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                            <span>{{ $anggota->nama ?? $anggota->pengguna->nama }}</span>
                                        </button>
                                    @empty
                                        <p class="px-3.5 py-2.5 text-xs text-gray-400 text-center font-light">Tidak ada anggota tersedia.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Kustom Status Proyek (Buka ke Bawah) --}}
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Status Proyek <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="statusOpen = false">
                                <input type="hidden" name="status" x-model="status" required>
                                <button type="button" @click="statusOpen = !statusOpen; ketuaOpen = false;"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                    <span class="text-gray-700 font-light" x-text="statusLabel"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="statusOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="statusOpen" x-cloak 
                                    class="custom-scrollbar absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1">
                                    <button type="button" @click="status = 'belum_dimulai'; statusLabel = 'Belum Dimulai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status === 'belum_dimulai' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">Belum Dimulai</button>
                                    <button type="button" @click="status = 'berjalan'; statusLabel = 'Berjalan'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status === 'berjalan' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">Berjalan</button>
                                    <button type="button" @click="status = 'selesai'; statusLabel = 'Selesai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status === 'selesai' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">Selesai</button>
                                    <button type="button" @click="status = 'terlambat'; statusLabel = 'Terlambat'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status === 'terlambat' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">Terlambat</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Grid 2 Kolom (Tanggal Mulai & Tanggal Selesai) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tenggat_waktu" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                    </div>

                </div>

                {{-- Footer Action --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
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