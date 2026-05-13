<div x-data="{ 
        open: false, 
        userId: '', 
        userName: '', 
        userNip: '' 
     }" 
     @open-modal-aktivasi.window="
        open = true; 
        userId = $event.detail.id; 
        userName = $event.detail.nama; 
        userNip = $event.detail.nip;
     " 
     @close-modal-aktivasi.window="open = false"
     x-show="open" 
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
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-start justify-between p-8 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#5C46F5]/5 rounded-full flex items-center justify-center text-[#5C46F5]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M9 12l2 2 4-4m5.618-4.016A3.333 3.333 0 0118 3.333h-1.5a3.333 3.333 0 00-3.333 3.333V9a3.333 3.333 0 003.333 3.333H18a3.333 3.333 0 013.333 3.333v1.5a3.333 3.333 0 01-3.333 3.333H6a3.333 3.333 0 01-3.333-3.333v-1.5A3.333 3.333 0 016 12.333h1.5a3.333 3.333 0 003.333-3.333V6.667a3.333 3.333 0 00-3.333-3.333H6A3.333 3.333 0 002.667 6.667" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-[900] text-gray-900 tracking-tight">Konfirmasi Aktivasi</h3>
                        <p class="text-sm font-medium text-gray-400">Berikan otorisasi untuk pengguna ini.</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.aktivasi') }}" method="POST">
                @csrf
                <input type="hidden" name="id_pengguna" :value="userId">

                <div class="p-8 pt-4 space-y-6">
                    
                    {{-- Nama Lengkap (Read Only Field) --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Nama Lengkap</label>
                        <div class="col-span-2">
                            <input type="text" :value="userName" readonly
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    {{-- NIP (Read Only Field) --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">NIP / Identitas</label>
                        <div class="col-span-2">
                            <input type="text" :value="userNip" readonly
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-xl outline-none text-sm font-bold text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Pilih Role dengan Icon Dropdown --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Pilih Peran <span class="text-red-500">*</span></label>
                        <div class="col-span-2 relative">
                            <select name="id_role" required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-bold appearance-none cursor-pointer text-gray-700">
                                <option value="" disabled selected>Pilih Peran</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Penempatan Tim dengan Icon Dropdown --}}
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-bold text-gray-600 tracking-tight">Penempatan Tim</label>
                        <div class="col-span-2 relative">
                            <select name="id_tim" 
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none transition-all text-sm font-bold appearance-none cursor-pointer text-gray-700">
                                <option value="">Belum Ada Tim</option>
                                @foreach($tims as $tim)
                                    <option value="{{ $tim->id_tim }}">{{ $tim->nama_tim }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="p-8 border-t border-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="open = false" 
                        class="px-6 py-3 bg-white border border-gray-200 text-red-500 rounded-full font-[800] text-xs hover:bg-red-50 hover:border-red-200 transition-all uppercase tracking-widest">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-8 py-3 bg-[#5C46F5] text-white rounded-full font-[800] text-xs hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all active:scale-[0.98] uppercase tracking-widest">
                        Aktifkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>