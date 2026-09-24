<div x-data="{ 
        open: false, 
        id: '', 
        nama: '', 
        deskripsi: '', 
        pj: '', 
        pjNama: '',
        pjOpen: false,
        tglMulai: '', 
        tglSelesai: '' 
    }" 
    @open-modal-edit-aktivitas.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        deskripsi = $event.detail.deskripsi; 
        pj = $event.detail.pj; 
        pjNama = $event.detail.pjNama;
        tglMulai = $event.detail.tglMulai; 
        tglSelesai = $event.detail.tglSelesai;
    " 
    @open-edit-aktivitas.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        deskripsi = $event.detail.deskripsi; 
        pj = $event.detail.pj; 
        pjNama = $event.detail.pjNama;
        tglMulai = $event.detail.tglMulai; 
        tglSelesai = $event.detail.tglSelesai;
    "
    @close-modal-edit-aktivitas.window="open = false"
    @close-edit-aktivitas.window="open = false"
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
    
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    {{-- Kotak & Ikon Pensil Berwarna Ungu Pakai Inline Style --}}
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-purple-600 shadow-xs shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-purple-600" style="color: #6E5BC3; stroke: #6E5BC3;" fill="none" viewBox="0 0 24 24" stroke-width="2.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Edit Data Aktivitas</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Ubah informasi nama, deskripsi, penanggung jawab, serta rentang tanggal aktivitas.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form :action="'{{ url('anggota/aktivitas') }}/' + id" method="POST">
                @csrf
                @method('PUT')

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Nama Aktivitas --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Nama Aktivitas <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aktivitas" x-model="nama" minlength="3" maxlength="150" required autocomplete="off"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700">
                    </div>

                    {{-- Deskripsi Aktivitas --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Deskripsi Aktivitas</label>
                        <textarea name="deskripsi_aktivitas" x-model="deskripsi" rows="3" maxlength="2000"
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 resize-none"></textarea>
                    </div>

                    {{-- Penanggung Jawab (Buka ke Bawah) --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Penanggung Jawab <span class="text-red-500">*</span></label>
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
                                class="w-full flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                <span :class="pj ? 'text-gray-700 font-light' : 'text-gray-400 font-light'" x-text="pjNama || 'Pilih Penanggung Jawab'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="pjOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            
                            {{-- Dropdown Buka ke Bawah (top-full mt-1.5) dengan custom-scrollbar --}}
                            <div x-show="pjOpen" x-cloak 
                                class="custom-scrollbar absolute left-0 right-0 top-full mt-1.5 bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-48 overflow-y-auto">
                                <div class="px-3 py-2 border-b border-gray-100 flex items-center gap-2 text-[#6E5BC3] text-xs font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>Pilih Penanggung Jawab</span>
                                </div>
                                @forelse($listAnggotaTim as $member)
                                    @if($member->pengguna)
                                        <button type="button" @click="pj = '{{ $member->pengguna->id_pengguna }}'; pjNama = '{{ addslashes($member->pengguna->nama) }}'; pjOpen = false"
                                            class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-xs font-light transition-all cursor-pointer text-left"
                                            :class="pj == '{{ $member->pengguna->id_pengguna }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                            <span>{{ $member->pengguna->nama }}</span>
                                        </button>
                                    @endif
                                @empty
                                    <p class="px-3.5 py-2.5 text-xs text-gray-400 text-center font-light">Tidak ada anggota tim yang dapat dipilih.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal Mulai & Tanggal Selesai --}}
                    @php
                        $minMulai = max(date('Y-m-d'), $proyek->tanggal_mulai ?? date('Y-m-d'));
                        $maxSelesai = $proyek->tanggal_target_selesai ?? null;
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" x-model="tglMulai" required 
                                min="{{ $minMulai }}"
                                @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Tanggal Selesai (Target) <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_target_selesai" x-model="tglSelesai" required 
                                :min="tglMulai ? tglMulai : '{{ $minMulai }}'"
                                @if($maxSelesai) max="{{ $maxSelesai }}" @endif
                                class="custom-date-input w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-700 cursor-pointer">
                        </div>
                    </div>

                </div>

                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>