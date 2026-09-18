<div x-data="{ 
        open: false, 
        role: '', 
        roleName: 'Pilih Peran',
        openRoleDropdown: false,
        tim: '', 
        timName: 'Pilih Tim Kerja',
        openTimDropdown: false,
        status: 'aktif',
        statusName: 'Aktif',
        openStatusDropdown: false
    }" 
     @open-modal-tambah-pengguna.window="open = true; role = ''; roleName = 'Pilih Peran'; tim = ''; timName = 'Pilih Tim Kerja'; status = 'aktif'; statusName = 'Aktif';" 
     @close-modal-tambah-pengguna.window="open = false"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tambah Pengguna</h3>
                        <p class="text-xs font-medium text-gray-400">Lengkapi data akun pengguna baru di bawah ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pengguna.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="p-8 space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Lengkap & NIP) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" placeholder="Masukkan nama..." required autocomplete="off"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" placeholder="18 digit NIP..." required maxlength="18" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Email BPS & Password) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Email BPS <span class="text-red-500">*</span></label>
                            <input type="email" name="nama_email_baru" placeholder="user@bps.go.id" required autocomplete="new-password"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="kata_sandi_baru" placeholder="Minimal 8 karakter..." required minlength="8" autocomplete="new-password"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                    </div>

                    {{-- Grid 3 Kolom (Status Akun, Peran, Tim Kerja) dengan Dropdown Kustom --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        
                        {{-- Dropdown Status Akun --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Akun <span class="text-red-500">*</span></label>
                            <input type="hidden" name="status_akun" x-model="status" required>

                            <button @click="openStatusDropdown = !openStatusDropdown; openRoleDropdown = false; openTimDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="statusName" class="text-gray-700 font-normal"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openStatusDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1">
                                <button type="button" @click="status = 'aktif'; statusName = 'Aktif'; openStatusDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Aktif</button>
                                <button type="button" @click="status = 'pending'; statusName = 'Pending'; openStatusDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pending</button>
                                <button type="button" @click="status = 'nonaktif'; statusName = 'Non-Aktif'; openStatusDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Non-Aktif</button>
                            </div>
                        </div>

                        {{-- Dropdown Peran (Role) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Peran (Role) <span class="text-red-500">*</span></label>
                            <input type="hidden" name="id_role" x-model="role" required>

                            <button @click="openRoleDropdown = !openRoleDropdown; openStatusDropdown = false; openTimDropdown = false;" @click.outside="openRoleDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="roleName" :class="role === '' ? 'text-gray-400 font-normal' : 'text-gray-700 font-normal'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openRoleDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openRoleDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1 max-h-48 overflow-y-auto">
                                <button type="button" @click="role = ''; roleName = 'Pilih Peran'; openRoleDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pilih Peran</button>
                                @foreach($roles as $r)
                                    <button type="button" 
                                        @click="role = '{{ $r->id_role }}'; roleName = '{{ $r->nama_role }}'; openRoleDropdown = false;"
                                        class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">
                                        {{ $r->nama_role }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Dropdown Tim Kerja --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tim Kerja</label>
                            <input type="hidden" name="id_tim" x-model="tim">

                            <button @click="openTimDropdown = !openTimDropdown; openStatusDropdown = false; openRoleDropdown = false;" @click.outside="openTimDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="timName" :class="tim === '' ? 'text-gray-400 font-normal' : 'text-gray-700 font-normal'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openTimDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1 max-h-48 overflow-y-auto">
                                <button type="button" @click="tim = ''; timName = 'Pilih Tim Kerja'; openTimDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pilih Tim Kerja</button>
                                @foreach($tims as $t)
                                    <button type="button" 
                                        @click="tim = '{{ $t->id_tim }}'; timName = '{{ $t->nama_tim }}'; openTimDropdown = false;"
                                        class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">
                                        {{ $t->nama_tim }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit">
                        Simpan Pengguna
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>