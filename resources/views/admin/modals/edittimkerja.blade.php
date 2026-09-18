<div x-data="{ 
        open: false, 
        id: '', 
        nama: '', 
        deskripsi: '', 
        ketua: '', 
        ketuaName: 'Pilih Ketua Tim',
        openKetuaDropdown: false,
        status: '',
        statusName: 'Aktif',
        openStatusDropdown: false,
        initialStatus: '' 
     }" 
     @open-modal-edit-tim.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        deskripsi = $event.detail.deskripsi; 
        ketua = $event.detail.ketua; 
        status = $event.detail.status;
        initialStatus = $event.detail.status;

        statusName = status === 'nonaktif' ? 'Non-Aktif' : 'Aktif';

        let selectedUser = document.querySelector(`[data-user-id='${ketua}']`);
        ketuaName = selectedUser ? selectedUser.dataset.userName : 'Pilih Ketua Tim';
     " 
     @close-modal-edit-tim.window="open = false"
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

    <div class="flex min-h-full items-center justify-center p-4">
        {{-- overflow-visible agar dropdown kustom tidak terpotong batas modal --}}
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Edit Tim Kerja</h3>
                        <p class="text-xs font-medium text-gray-400">Ubah struktur dan informasi tim kerja ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ url('admin/manajementimkerja/update') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="id_tim" x-model="id">

                <div class="p-8 space-y-5">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Tim <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_tim" x-model="nama" required 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700">
                        </div>

                        {{-- Dropdown Kustom Status Tim (Font Normal) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status</label>
                            <input type="hidden" name="status_tim" x-model="status" required>

                            <button @click="openStatusDropdown = !openStatusDropdown; openKetuaDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="statusName" class="text-gray-700 font-normal"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openStatusDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1">
                                <button type="button" @click="status = 'aktif'; statusName = 'Aktif'; openStatusDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Aktif</button>
                                <button type="button" @click="status = 'nonaktif'; statusName = 'Non-Aktif'; openStatusDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Non-Aktif</button>
                            </div>
                        </div>
                    </div>

                    <div x-show="status === 'nonaktif' && initialStatus !== 'nonaktif'" 
                         x-transition class="p-3 bg-red-50 text-red-600 text-xs font-medium rounded-xl border border-red-100 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Perhatian: Menonaktifkan tim akan menghentikan seluruh kolaborasi terkait.</span>
                    </div>

                    {{-- Dropdown Kustom Ketua Tim dengan Scroll & Font Normal --}}
                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Ketua Tim <span class="text-red-500">*</span></label>
                        <input type="hidden" name="id_ketua_tim" x-model="ketua" required>

                        <button @click="openKetuaDropdown = !openKetuaDropdown; openStatusDropdown = false;" @click.outside="openKetuaDropdown = false" type="button" 
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                            <span x-text="ketuaName" :class="ketua === '' ? 'text-gray-400 font-normal' : 'text-gray-700 font-normal'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openKetuaDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openKetuaDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1 max-h-48 overflow-y-auto">
                            <button type="button" @click="ketua = ''; ketuaName = 'Pilih Ketua Tim'; openKetuaDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pilih Ketua Tim</button>
                            @foreach($users as $user)
                                <button type="button" 
                                    data-user-id="{{ $user->id_pengguna }}"
                                    data-user-name="{{ $user->nama }}"
                                    @click="ketua = '{{ $user->id_pengguna }}'; ketuaName = '{{ $user->nama }}'; openKetuaDropdown = false;"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">
                                    {{ $user->nama }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Deskripsi Tim Kerja</label>
                        <textarea name="deskripsi_tim" x-model="deskripsi" rows="3" placeholder="Tuliskan deskripsi singkat..."
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 resize-none"></textarea>
                    </div>

                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
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