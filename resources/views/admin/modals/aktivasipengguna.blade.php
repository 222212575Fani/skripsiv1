<div x-data="{ 
        open: false, 
        userId: '', 
        userName: '', 
        userNip: '',
        roleId: '',
        timId: ''
     }" 
     @open-modal-aktivasi.window="
        open = true; 
        userId = $event.detail.id; 
        userName = $event.detail.nama; 
        userNip = $event.detail.nip;
        roleId = '';
        timId = '';
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
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A3.333 3.333 0 0118 3.333h-1.5a3.333 3.333 0 00-3.333 3.333V9a3.333 3.333 0 003.333 3.333H18a3.333 3.333 0 013.333 3.333v1.5a3.333 3.333 0 01-3.333 3.333H6a3.333 3.333 0 01-3.333-3.333v-1.5A3.333 3.333 0 016 12.333h1.5a3.333 3.333 0 003.333-3.333V6.667a3.333 3.333 0 00-3.333-3.333H6A3.333 3.333 0 002.667 6.667" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Konfirmasi Aktivasi</h3>
                        <p class="text-xs font-medium text-gray-400">Berikan otorisasi dan hak akses untuk pengguna ini.</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.aktivasi') }}" method="POST">
                @csrf
                <input type="text" name="id_pengguna" x-model="userId" class="hidden">

                <div class="p-8 space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama & NIP) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="userName" readonly
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none text-xs font-medium text-gray-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">NIP / Identitas</label>
                            <input type="text" x-model="userNip" readonly
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl outline-none text-xs font-medium text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Role & Penempatan Tim) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Pilih Peran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="id_role" x-model="roleId" required
                                    :class="roleId === '' ? 'text-gray-400' : 'text-gray-700'"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-xs font-medium appearance-none cursor-pointer">
                                    <option value="" disabled class="text-gray-400">Pilih Peran</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_role }}" class="text-gray-700">{{ $role->nama_role }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Penempatan Tim</label>
                            <div class="relative">
                                <select name="id_tim" x-model="timId"
                                    :class="timId === '' ? 'text-gray-400' : 'text-gray-700'"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-xs font-medium appearance-none cursor-pointer">
                                    <option value="" class="text-gray-400">Pilih Tim Kerja (Opsional)</option>
                                    @foreach($tims as $tim)
                                        <option value="{{ $tim->id_tim }}" 
                                            {{ $tim->sudah_punya_ketua ? 'disabled' : '' }}
                                            class="{{ $tim->sudah_punya_ketua ? 'text-gray-300 bg-gray-50' : 'text-gray-700' }}">
                                            {{ $tim->nama_tim }} {{ $tim->sudah_punya_ketua ? '(Sudah punya ketua)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <button type="button" @click="open = false" 
                        class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold text-xs hover:bg-gray-800 shadow-md transition-all">
                        Aktifkan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>