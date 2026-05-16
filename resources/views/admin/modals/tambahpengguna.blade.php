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
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            {{-- Header Modal --}}
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

            {{-- Form Input Utama --}}
            <form action="{{ route('admin.pengguna.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="p-8 pt-4 space-y-5">
                    
                    {{-- Baris 1: Nama Lengkap & NIP --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" placeholder="Masukkan nama..." required value="{{ old('nama') }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" placeholder="18 digit NIP..." required maxlength="18" value="{{ old('nip') }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                    </div>

                    {{-- Baris 2: Email & Password (AMBIL ALIH ANTI-AUTOFILL EDGE) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Email BPS <span class="text-red-500">*</span></label>
                            <input type="email" name="nama_email_baru" id="nama_email_baru" placeholder="user@bps.go.id" required autocomplete="none" value="{{ old('nama_email_baru') }}"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="kata_sandi_baru" id="kata_sandi_baru" placeholder="Minimal 8 karakter..." required autocomplete="new-password"
                                class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-4 focus:ring-[#5C46F5]/5 focus:border-[#5C46F5] outline-none text-sm font-bold placeholder:text-gray-300">
                        </div>
                    </div>

                    {{-- Baris 3: Status Akun, Peran (Role), & Tim Kerja --}}
                    <div class="grid grid-cols-3 gap-4 pt-2">
                        
                        {{-- Dropdown Status Akun --}}
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Status Akun <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="status_akun" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="aktif" {{ old('status_akun') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="pending" {{ old('status_akun') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="nonaktif" {{ old('status_akun') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Peran (Role) --}}
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Peran (Role) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="id_role" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="" disabled selected>Pilih Peran</option>
                                    @if(isset($roles) && $roles->count() > 0)
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                                                {{ $role->nama_role }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Penempatan Tim Kerja (Opsional) --}}
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600 tracking-tight">Tim Kerja</label>
                            <div class="relative">
                                <select name="id_tim" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl appearance-none focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer text-gray-700">
                                    <option value="" selected>Belum Ada Tim</option>
                                    @if(isset($tims) && $tims->count() > 0)
                                        @foreach($tims as $tim)
                                            <option value="{{ $tim->id_tim }}" {{ old('id_tim') == $tim->id_tim ? 'selected' : '' }}>
                                                {{ $tim->nama_tim }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Action Footer Buttons --}}
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