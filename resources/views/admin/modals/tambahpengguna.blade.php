<div x-data="{ open: false }" 
     @open-modal-tambah-pengguna.window="open = true" 
     @close-modal-tambah-pengguna.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">
    
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             class="relative w-full max-w-4xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            {{-- Header --}}
            <div class="flex items-start justify-between p-8 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#5C46F5]/5 rounded-full flex items-center justify-center text-[#5C46F5]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-[900] text-gray-900 tracking-tight">Tambah Pengguna</h3>
                        <p class="text-sm font-medium text-gray-400">Lengkapi data akun pengguna baru di bawah ini.</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pengguna.store') }}" method="POST" autocomplete="off">
                @csrf
                {{-- Hack Anti-Autofill: Browser akan mengisi dummy ini, bukan field asli kamu --}}
                <input type="text" style="display:none" aria-hidden="true">
                <input type="password" style="display:none" aria-hidden="true">

                <div class="p-8 pt-4 space-y-8">
                    
                    {{-- Baris 1: Informasi Dasar (Nama & NIP) --}}
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" placeholder="Masukkan nama..." required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" maxlength="18" placeholder="18 digit NIP..." required
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                    </div>

                    {{-- Baris 2: Kredensial (Email & Password) --}}
                    <div class="grid grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Email BPS <span class="text-red-500">*</span></label>
                            <input type="email" name="email" placeholder="user@bps.go.id" required readonly onfocus="this.removeAttribute('readonly');"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" placeholder="Minimal 8 karakter..." required readonly onfocus="this.removeAttribute('readonly');"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                    </div>

                    {{-- Baris 3: Pengaturan (Dropdowns di Bawah) --}}
                    <div class="grid grid-cols-3 gap-6 pt-4 border-t border-gray-50">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Status Akun <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status_akun" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="aktif">Aktif</option>
                                    <option value="pending">Pending</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Peran (Role) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="id_role" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="" disabled selected>Pilih Peran</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Tim Kerja</label>
                            <div class="relative">
                                <select name="id_tim" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="">Belum Ada Tim</option>
                                    @foreach($tims as $tim)
                                        <option value="{{ $tim->id_tim }}">{{ $tim->nama_tim }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-8 border-t border-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="open = false" 
                        class="px-6 py-3 bg-white border border-gray-200 text-red-500 rounded-full font-[800] text-xs uppercase tracking-widest hover:bg-red-50 transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-8 py-3 bg-[#5C46F5] text-white rounded-full font-[800] text-xs uppercase tracking-widest hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all active:scale-[0.98]">
                        Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>