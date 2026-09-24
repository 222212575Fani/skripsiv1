<div x-data="{ 
        open: false, 
        id_proyek: '', 
        nama_proyek: '', 
        deskripsi_proyek: '', 
        id_ketua_proyek: '', 
        ketuaProyekNama: '',
        ketuaOpen: false,
        status_proyek: '',
        statusNama: '',
        statusOpen: false,
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
        
        // Cari nama ketua proyek berdasarkan ID saat modal dibuka
        let selectedMember = document.querySelector(`input[name='member_data_${id_ketua_proyek}']`);
        ketuaProyekNama = $event.detail.ketuaProyekNama || '';

        status_proyek = $event.detail.status_proyek;
        if(status_proyek === 'belum_dimulai') statusNama = 'Belum Dimulai';
        else if(status_proyek === 'berjalan') statusNama = 'Sedang Berjalan';
        else if(status_proyek === 'selesai') statusNama = 'Selesai';
        else if(status_proyek === 'terlambat') statusNama = 'Terlambat';
        else statusNama = 'Pilih Status Proyek';

        tanggal_mulai = $event.detail.tanggal_mulai;
        tanggal_target_selesai = $event.detail.tanggal_target_selesai;
        initialStatus = $event.detail.status_proyek;
    " 
    @close-modal-edit-proyek.window="open = false"
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
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Ala Referensi --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Edit Proyek</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Perbarui informasi, penanggung jawab, dan tenggat waktu proyek.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="`{{ url('ketuatim/proyek') }}/${id_proyek}`" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Proyek & Status) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Nama Proyek <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_proyek" x-model="nama_proyek" minlength="3" maxlength="200" required 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                        </div>

                        {{-- Dropdown Kustom Status Proyek (Buka ke Bawah) --}}
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Status Proyek <span class="text-red-500">*</span></label>
                            <div class="relative" @click.outside="statusOpen = false">
                                <input type="hidden" name="status" x-model="status_proyek">
                                <button type="button" @click="statusOpen = !statusOpen; ketuaOpen = false;"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                    <span :class="status_proyek ? 'text-gray-700 font-light' : 'text-gray-400 font-light'" x-text="statusNama || 'Pilih Status Proyek'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-all duration-200" :class="statusOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="statusOpen" x-cloak 
                                    class="custom-scrollbar absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1">
                                    <button type="button" @click="status_proyek = 'belum_dimulai'; statusNama = 'Belum Dimulai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status_proyek === 'belum_dimulai' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                        <span>Belum Dimulai</span>
                                    </button>
                                    <button type="button" @click="status_proyek = 'berjalan'; statusNama = 'Sedang Berjalan'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status_proyek === 'berjalan' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                        <span>Sedang Berjalan</span>
                                    </button>
                                    <button type="button" @click="status_proyek = 'selesai'; statusNama = 'Selesai'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status_proyek === 'selesai' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                        <span>Selesai</span>
                                    </button>
                                    <button type="button" @click="status_proyek = 'terlambat'; statusNama = 'Terlambat'; statusOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs transition-all cursor-pointer text-left font-light"
                                        :class="status_proyek === 'terlambat' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                                        <span>Terlambat</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ketua Proyek (Full Width Dropdown Kustom Buka ke Bawah) --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Ketua Proyek <span class="text-red-500">*</span></label>
                        <div class="relative" @click.outside="ketuaOpen = false">
                            <input type="hidden" name="id_ketua_proyek" x-model="id_ketua_proyek">
                            <button type="button" @click="ketuaOpen = !ketuaOpen; statusOpen = false;"
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                <span :class="id_ketua_proyek ? 'text-gray-700 font-light' : 'text-gray-400 font-light'" x-text="ketuaProyekNama || 'Pilih Ketua Proyek'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-all duration-200" :class="ketuaOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="ketuaOpen" x-cloak 
                                class="custom-scrollbar absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-40 overflow-y-auto">
                                @foreach($anggotaTim ?? [] as $member)
                                    @php
                                        $mId = $member->pengguna->id_pengguna ?? $member->id_pengguna;
                                        $mNama = $member->pengguna->nama ?? $member->nama;
                                    @endphp
                                    <button type="button" @click="id_ketua_proyek = '{{ $mId }}'; ketuaProyekNama = '{{ addslashes($mNama) }}'; ketuaOpen = false"
                                        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs font-light transition-all cursor-pointer text-left"
                                        :class="id_ketua_proyek == '{{ $mId }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                        <span>{{ $mNama }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Mulai & Tenggat Waktu (Grid 2 Kolom dengan Ikon Kalender Ungu) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" x-model="tanggal_mulai" 
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tenggat Waktu</label>
                            <input type="date" name="tenggat_waktu" x-model="tanggal_target_selesai" 
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                    </div>

                    {{-- Deskripsi (Full Width Textarea) --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Deskripsi Proyek</label>
                        <textarea name="deskripsi" x-model="deskripsi_proyek" rows="3" maxlength="2000" placeholder="Tuliskan deskripsi singkat proyek..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>

                </div>

                {{-- Footer Action Menggunakan Komponen <x-button> --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit" class="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CSS KUSTOM DROPDOWN & ICON KALENDER UNGU --}}
<style>
    .pj-dropdown-scroll {
        scrollbar-width: thin;
        scrollbar-color: #9E8CE3 #F8F7FF;
        scrollbar-gutter: stable;
    }
    .pj-dropdown-scroll::-webkit-scrollbar { width: 6px; }
    .pj-dropdown-scroll::-webkit-scrollbar-track { background: #F8F7FF; border-radius: 9999px; }
    .pj-dropdown-scroll::-webkit-scrollbar-thumb { background: #9E8CE3; border-radius: 9999px; }
    .pj-dropdown-scroll::-webkit-scrollbar-thumb:hover { background: #6E5BC3; }
    
    .custom-date-input::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(32%) sepia(85%) saturate(1450%) hue-rotate(230deg) brightness(95%) contrast(96%);
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }
    .custom-date-input::-webkit-calendar-picker-indicator:hover { opacity: 1; }
</style>