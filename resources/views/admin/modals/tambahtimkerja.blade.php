<div x-data="{ open: false, ketua: '' }" 
     @open-modal-tambah.window="open = true" 
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

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header ala Referensi --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tambah Tim Kerja</h3>
                        <p class="text-xs font-medium text-gray-400">Lengkapi data untuk membuat tim kerja baru.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.timkerja.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="p-8 space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Tim & Status) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Tim <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tim" placeholder="Masukkan nama tim" required
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                            <div class="relative">
                                <select name="status_tim" required 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 appearance-none cursor-pointer">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ketua Tim (Full Width / 1 Baris Sendiri) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Tim <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select name="id_ketua_tim" required x-model="ketua"
                                :class="ketua === '' ? 'text-gray-400' : 'text-gray-700'"
                                class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium appearance-none cursor-pointer">
                                <option value="" disabled selected class="text-gray-400">Pilih Ketua Tim</option>
                                @foreach($users as $user)
                                    @php
                                        $isLeaderActive = \App\Models\TimKerja::where('id_ketua_tim', $user->id_pengguna)
                                                                               ->where('status_tim', 'aktif')
                                                                               ->exists();
                                    @endphp
                                    <option value="{{ $user->id_pengguna }}" {{ $isLeaderActive ? 'disabled' : '' }} class="text-gray-700">
                                        {{ $user->nama }}
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

                    {{-- Deskripsi (Full Width Textarea) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Tim Kerja</label>
                        <textarea name="deskripsi_tim" rows="3" placeholder="Tuliskan deskripsi singkat..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
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