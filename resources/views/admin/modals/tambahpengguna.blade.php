<div x-data="{ open: false, role: '', tim: '' }" 
     @open-modal-tambah-pengguna.window="open = true" 
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
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tambah Pengguna</h3>
                        <p class="text-xs font-medium text-gray-400">Lengkapi data akun pengguna baru di bawah ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
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
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" placeholder="18 digit NIP..." required maxlength="18" autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Email BPS & Password) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Email BPS <span class="text-red-500">*</span></label>
                            <input type="email" name="nama_email_baru" placeholder="user@bps.go.id" required autocomplete="new-password"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="kata_sandi_baru" placeholder="Minimal 8 karakter..." required minlength="8" autocomplete="new-password"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 placeholder:text-gray-400 placeholder:font-normal">
                        </div>
                    </div>

                    {{-- Grid 3 Kolom (Status Akun, Peran, Tim Kerja) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Akun <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status_akun" required class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer appearance-none">
                                    <option value="aktif">Aktif</option>
                                    <option value="pending">Pending</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Peran (Role) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="id_role" x-model="role" required 
                                    :class="role === '' ? 'text-gray-400' : 'text-gray-700'"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium cursor-pointer appearance-none">
                                    <option value="" disabled selected class="text-gray-400">Pilih Peran</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id_role }}" class="text-gray-700">{{ $r->nama_role }}</option>
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
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tim Kerja</label>
                            <div class="relative">
                                <select name="id_tim" x-model="tim"
                                    :class="tim === '' ? 'text-gray-400' : 'text-gray-700'"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium cursor-pointer appearance-none">
                                    <option value="" class="text-gray-400">Pilih Tim Kerja</option>
                                    @foreach($tims as $t)
                                        <option value="{{ $t->id_tim }}" class="text-gray-700">{{ $t->nama_tim }}</option>
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

                {{-- Footer Action ala Referensi --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <button type="button" @click="open = false" 
                        class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-6 py-2.5 bg-[#5C46F5] text-white rounded-xl font-bold text-xs hover:bg-[#4A38D4] shadow-md shadow-[#5C46F5]/20 transition-all">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>