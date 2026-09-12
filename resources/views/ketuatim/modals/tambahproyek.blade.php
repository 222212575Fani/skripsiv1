<div x-data="{ 
        open: false, 
        ketua: '', 
        ketuaNama: '', 
        ketuaOpen: false, 
        status: 'belum_dimulai', 
        statusLabel: 'Belum Dimulai', 
        statusOpen: false 
    }" 
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
    
    {{-- CSS Kustom untuk Ikon Kalender Ungu --}}
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

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
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
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
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
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal resize-none"></textarea>
                    </div>

                    {{-- Grid 2 Kolom (Ketua Proyek & Status Proyek) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- Dropdown Kustom Ketua Proyek --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Proyek <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="ketuaOpen = false">
                                <input type="hidden" name="id_ketua_proyek" x-model="ketua" required>
                                <button type="button" @click="ketuaOpen = !ketuaOpen; statusOpen = false;"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#5C46F5] rounded-2xl text-xs font-medium transition-all cursor-pointer">
                                    <span :class="ketuaNama ? 'text-gray-700' : 'text-gray-400'" x-text="ketuaNama || 'Pilih Ketua Proyek'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="ketuaOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="ketuaOpen" x-cloak x-transition class="pj-dropdown-scroll absolute left-0 right-0 mt-2 bg-white border border-purple-100 rounded-[24px] shadow-xl p-2 z-[1000] space-y-1 max-h-44 overflow-y-auto">
                                    @forelse($anggotaTim ?? [] as $anggota)
                                        <button type="button" 
                                            @click="ketua = '{{ $anggota->id_pengguna }}'; ketuaNama = '{{ addslashes($anggota->nama ?? $anggota->pengguna->nama) }}'; ketuaOpen = false"
                                            class="w-full flex items-center px-3.5 py-2.5 rounded-xl text-xs font-normal transition-all cursor-pointer text-left"
                                            :class="ketua == '{{ $anggota->id_pengguna }}' ? 'bg-[#F8F7FF] text-[#5C46F5] font-semibold' : 'text-gray-700 hover:bg-gray-50'">
                                            <span>{{ $anggota->nama ?? $anggota->pengguna->nama }}</span>
                                        </button>
                                    @empty
                                        <p class="px-3.5 py-2.5 text-xs text-gray-400 text-center">Tidak ada anggota tersedia.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Kustom Status Proyek --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Proyek <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="statusOpen = false">
                                <input type="hidden" name="status" x-model="status" required>
                                <button type="button" @click="statusOpen = !statusOpen; ketuaOpen = false;"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#5C46F5] rounded-2xl text-xs font-medium transition-all cursor-pointer">
                                    <span class="text-gray-700" x-text="statusLabel"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200" :class="statusOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="statusOpen" x-cloak x-transition class="absolute left-0 right-0 mt-2 bg-white border border-purple-100 rounded-[24px] shadow-xl p-2 z-[1000] space-y-1">
                                    <button type="button" @click="status = 'belum_dimulai'; statusLabel = 'Belum Dimulai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2 rounded-xl text-xs transition-all cursor-pointer text-left"
                                        :class="status === 'belum_dimulai' ? 'bg-[#F8F7FF] text-[#5C46F5] font-semibold' : 'text-gray-700 hover:bg-gray-50'">Belum Dimulai</button>
                                    <button type="button" @click="status = 'berjalan'; statusLabel = 'Berjalan'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2 rounded-xl text-xs transition-all cursor-pointer text-left"
                                        :class="status === 'berjalan' ? 'bg-[#F8F7FF] text-[#5C46F5] font-semibold' : 'text-gray-700 hover:bg-gray-50'">Berjalan</button>
                                    <button type="button" @click="status = 'selesai'; statusLabel = 'Selesai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2 rounded-xl text-xs transition-all cursor-pointer text-left"
                                        :class="status === 'selesai' ? 'bg-[#F8F7FF] text-[#5C46F5] font-semibold' : 'text-gray-700 hover:bg-gray-50'">Selesai</button>
                                    <button type="button" @click="status = 'terlambat'; statusLabel = 'Terlambat'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2 rounded-xl text-xs transition-all cursor-pointer text-left"
                                        :class="status === 'terlambat' ? 'bg-[#F8F7FF] text-[#5C46F5] font-semibold' : 'text-gray-700 hover:bg-gray-50'">Terlambat</button>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Grid 2 Kolom (Tanggal Mulai & Tanggal Selesai) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tenggat_waktu" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer">
                        </div>
                    </div>

                </div>

                {{-- Footer Action --}}
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