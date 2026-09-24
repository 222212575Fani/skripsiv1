<div x-data="{ 
        open: false, 
        userId: '', 
        userName: '', 
        userNip: '',
        roleId: '',
        roleName: 'Pilih Peran',
        openRoleDropdown: false,
        timId: '',
        timName: 'Pilih Tim Kerja (Opsional)',
        openTimDropdown: false
    }" 
    @open-modal-aktivasi.window="
        open = true; 
        userId = $event.detail.id; 
        userName = $event.detail.nama; 
        userNip = $event.detail.nip;
        roleId = '';
        roleName = 'Pilih Peran';
        timId = '';
        timName = 'Pilih Tim Kerja (Opsional)';
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

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        {{-- overflow-hidden diubah ke overflow-visible agar kotak dropdown tidak terpotong batas modal --}}
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A3.333 3.333 0 0118 3.333h-1.5a3.333 3.333 0 00-3.333 3.333V9a3.333 3.333 0 003.333 3.333H18a3.333 3.333 0 013.333 3.333v1.5a3.333 3.333 0 01-3.333 3.333H6a3.333 3.333 0 01-3.333-3.333v-1.5A3.333 3.333 0 016 12.333h1.5a3.333 3.333 0 003.333-3.333V6.667a3.333 3.333 0 00-3.333-3.333H6A3.333 3.333 0 002.667 6.667" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Konfirmasi Aktivasi</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Berikan otorisasi dan hak akses untuk pengguna ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.aktivasi') }}" method="POST">
                @csrf
                <input type="text" name="id_pengguna" x-model="userId" class="hidden">

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Alert Error di Dalam Modal --}}
                    @if(session('error'))
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 animate-fade-in">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-rose-900">Aktivasi Gagal</h4>
                                <p class="text-xs text-rose-700 mt-0.5 font-medium leading-relaxed">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    @endif

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

                    {{-- Grid 2 Kolom (Role & Penempatan Tim) dengan Dropdown Scroll Kustom Buka ke Atas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- DROPDOWN KUSTOM: PILIH PERAN (Buka ke Atas) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Pilih Peran <span class="text-red-500">*</span></label>
                            
                            <input type="hidden" name="id_role" x-model="roleId" required>

                            <button @click="openRoleDropdown = !openRoleDropdown; openTimDropdown = false;" @click.outside="openRoleDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="roleName" :class="roleId === '' ? 'text-gray-400 font-light' : 'text-gray-700 font-medium'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openRoleDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openRoleDropdown" x-cloak 
                                class="custom-scrollbar absolute left-0 bottom-full mb-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-40 overflow-y-auto">
                                @foreach($roles as $role)
                                    @if(strtolower($role->nama_role) !== 'admin')
                                        <button type="button" 
                                            @click="roleId = '{{ $role->id_role }}'; roleName = '{{ $role->nama_role }}'; openRoleDropdown = false;"
                                            class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer"
                                            :class="roleId == '{{ $role->id_role }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : ''">
                                            {{ $role->nama_role }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- DROPDOWN KUSTOM: PENEMPATAN TIM (Buka ke Atas) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Penempatan Tim</label>
                            
                            <input type="hidden" name="id_tim" x-model="timId">

                            <button @click="openTimDropdown = !openTimDropdown; openRoleDropdown = false;" @click.outside="openTimDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#6E5BC3] focus:border-[#6E5BC3] focus:ring-2 focus:ring-[#6E5BC3]/20 rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="timName" :class="timId === '' ? 'text-gray-400 font-light' : 'text-gray-700 font-medium'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openTimDropdown" x-cloak 
                                class="custom-scrollbar absolute left-0 bottom-full mb-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-40 overflow-y-auto">
                                <button type="button" 
                                    @click="timId = ''; timName = 'Pilih Tim Kerja (Opsional)'; openTimDropdown = false;"
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-light transition-all cursor-pointer">
                                    Pilih Tim Kerja (Opsional)
                                </button>
                                @foreach($tims as $tim)
                                    <button type="button" 
                                        @click="timId = '{{ $tim->id_tim }}'; timName = '{{ $tim->nama_tim }}'; openTimDropdown = false;"
                                        class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer"
                                        :class="timId == '{{ $tim->id_tim }}' ? 'bg-purple-50/70 text-[#6E5BC3] font-semibold' : ''">
                                        {{ $tim->nama_tim }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">
                        Aktifkan Sekarang
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>