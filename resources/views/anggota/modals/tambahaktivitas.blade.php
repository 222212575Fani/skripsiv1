<div x-data="{ open: false, pj: '', pjNama: '', pjOpen: false, tglMulai: '' }" 
     @open-tambah-aktivitas.window="open = true" 
     @close-tambah-aktivitas.window="open = false"
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
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" 
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-[#F8F7FF] border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-xs shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Tambah Aktivitas Baru</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Buat aktivitas baru dan tambahkan ke dalam proyek ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('anggota.aktivitas.store', $proyek->id_proyek) }}" method="POST" autocomplete="off">
                @csrf

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Nama Aktivitas --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Nama Aktivitas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aktivitas" placeholder="Masukkan nama aktivitas..." minlength="3" maxlength="150" required autocomplete="off"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                    </div>

                    {{-- Deskripsi Aktivitas --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Aktivitas</label>
                        <textarea name="deskripsi_aktivitas" rows="3" maxlength="2000" placeholder="Tuliskan deskripsi atau ringkasan aktivitas..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>

                    {{-- Penanggung Jawab (Buka ke Atas agar TIDAK Keluar Container Modal) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Penanggung Jawab <span class="text-red-500">*</span></label>
                        @php
                            $timId = $proyek->id_tim ?? null;
                            $idKetuaTim = optional($proyek->timKerja)->id_ketua_tim
                                ?? \App\Models\TimKerja::where('id_tim', $timId)->value('id_ketua_tim');
                            $listAnggotaTim = \App\Models\AnggotaTim::with('pengguna')
                                ->where('id_tim', $timId)->whereNull('tanggal_keluar')->get()
                                ->filter(fn ($member) => $member->id_pengguna != $idKetuaTim);
                        @endphp
                        <div class="relative group/filter" @click.outside="pjOpen = false">
                            <input type="hidden" name="id_penanggung_jawab" x-model="pj" required>
                            <button type="button" @click="pjOpen = !pjOpen"
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span :class="pj ? 'text-gray-700 font-medium' : 'text-gray-400 font-light'" x-text="pjNama || 'Pilih Penanggung Jawab'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="pjOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            {{-- Buka ke atas (bottom-full mb-1.5) agar tidak menonjol di luar container modal --}}
                            <div x-show="pjOpen" x-cloak 
                                class="custom-scrollbar absolute left-0 right-0 bottom-full mb-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-44 overflow-y-auto">
                                <div class="px-3 py-2 border-b border-gray-100 flex items-center gap-2 text-[#6E5BC3] text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>Pilih Penanggung Jawab</span>
                                </div>
                                @forelse($listAnggotaTim as $member)
                                    @if($member->pengguna)
                                        <button type="button" @click="pj = '{{ $member->pengguna->id_pengguna }}'; pjNama = '{{ addslashes($member->pengguna->nama) }}'; pjOpen = false"
                                            class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs font-normal transition-all cursor-pointer text-left"
                                            :class="pj == '{{ $member->pengguna->id_pengguna }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                            <span>{{ $member->pengguna->nama }}</span>
                                        </button>
                                    @endif
                                @empty
                                    <p class="px-3.5 py-2.5 text-xs text-gray-400 text-center">Tidak ada anggota tim yang dapat dipilih.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Tanggal Mulai & Selesai) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                            @php
                                $minMulai = max(date('Y-m-d'), $proyek->tanggal_mulai ?? date('Y-m-d'));
                                $maxMulai = $proyek->tanggal_target_selesai ?? null;
                            @endphp
                            <input type="date" name="tanggal_mulai" required x-model="tglMulai"
                                min="{{ $minMulai }}"
                                @if($maxMulai) max="{{ $maxMulai }}" @endif
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tanggal Selesai (Target) <span class="text-red-500">*</span></label>
                            @php
                                $maxSelesai = $proyek->tanggal_target_selesai ?? null;
                            @endphp
                            <input type="date" name="tanggal_target_selesai" required 
                                :min="tglMulai ? tglMulai : '{{ $minMulai }}'"
                                @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 cursor-pointer">
                        </div>
                    </div>

                </div>

                {{-- Footer Action --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">
                        Simpan Aktivitas
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>