<div x-data="{ 
        open: false, 
        ketua: '', 
        ketuaName: 'Pilih Ketua Tim',
        openKetuaDropdown: false,
        status: 'aktif',
        statusName: 'Aktif',
        openStatusDropdown: false
    }" 
     @open-modal-tambah.window="open = true; ketua = ''; ketuaName = 'Pilih Ketua Tim'; status = 'aktif'; statusName = 'Aktif';" 
     @close-modal-tambah.window="open = false"
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

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        {{-- overflow-visible agar dropdown kustom tidak terpotong batas modal --}}
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Tambah Tim Kerja</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Lengkapi data untuk membuat tim kerja baru.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.timkerja.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Tim & Status) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Tim <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tim" placeholder="Masukkan nama tim" minlength="3" maxlength="100" required
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                        </div>

                        {{-- Dropdown Status Tim (Buka ke Bawah karena di baris atas) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                            <input type="hidden" name="status_tim" x-model="status" required>

                            <button @click="openStatusDropdown = !openStatusDropdown; openKetuaDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="statusName" class="text-gray-700 font-normal"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openStatusDropdown" x-cloak 
                                class="custom-scrollbar absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1">
                                <button type="button" @click="status = 'aktif'; statusName = 'Aktif'; openStatusDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer"
                                    :class="status === 'aktif' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : ''">
                                    Aktif
                                </button>
                                <button type="button" @click="status = 'nonaktif'; statusName = 'Non-Aktif'; openStatusDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer"
                                    :class="status === 'nonaktif' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : ''">
                                    Non-Aktif
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Ketua Tim (Full Width) dengan Dropdown Kustom Buka ke Atas agar TIDAK Keluar Container Modal --}}
                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Tim <span class="text-red-500">*</span></label>
                        <input type="hidden" name="id_ketua_tim" x-model="ketua" required>

                        <button @click="openKetuaDropdown = !openKetuaDropdown; openStatusDropdown = false;" @click.outside="openKetuaDropdown = false" type="button" 
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-medium transition-all cursor-pointer">
                            <span x-text="ketuaName" :class="ketua === '' ? 'text-gray-400 font-light' : 'text-gray-700 font-medium'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openKetuaDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Buka ke atas (bottom-full mb-1.5) agar tidak menonjol di luar container modal ataupun menutupi tombol footer --}}
                        <div x-show="openKetuaDropdown" x-cloak 
                            class="custom-scrollbar absolute left-0 bottom-full mb-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-44 overflow-y-auto">
                            <button type="button" @click="ketua = ''; ketuaName = 'Pilih Ketua Tim'; openKetuaDropdown = false;" 
                                class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-light transition-all cursor-pointer">
                                Pilih Ketua Tim
                            </button>
                            @foreach($users as $user)
                                @php
                                    $teamLed = $ledTeams[$user->id_pengguna] ?? null;
                                @endphp
                                @if($teamLed)
                                    {{-- Sudah memimpin tim lain (Disabled) --}}
                                    <div class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs text-gray-400 bg-gray-50/80 cursor-not-allowed select-none" title="Pegawai ini sudah menjadi Ketua Tim pada {{ $teamLed->nama_tim }}">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                            <span class="truncate">{{ $user->nama }}</span>
                                        </div>
                                        <span class="text-[10px] text-amber-700 bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-full font-medium shrink-0 ml-2">Ketua {{ $teamLed->nama_tim }}</span>
                                    </div>
                                @else
                                    {{-- Belum memimpin tim mana pun (Tersedia) --}}
                                    <button type="button" 
                                        @click="ketua = '{{ $user->id_pengguna }}'; ketuaName = '{{ addslashes($user->nama) }}'; openKetuaDropdown = false;"
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer"
                                        :class="ketua == '{{ $user->id_pengguna }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : ''">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <span class="truncate">{{ $user->nama }}</span>
                                        </div>
                                        <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-full font-semibold shrink-0 ml-2">Tersedia</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Deskripsi (Full Width Textarea) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Tim Kerja</label>
                        <textarea name="deskripsi_tim" rows="3" maxlength="1000" placeholder="Tuliskan deskripsi singkat..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit">
                        Simpan Tim
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>