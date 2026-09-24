<div x-data="{ 
        open: false, 
        nama: '',
        nip: '',
        email: '',
        password: '',
        showPassword: false,
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
     @open-modal-tambah-pengguna.window="open = true; nama = ''; nip = ''; email = ''; password = ''; showPassword = false; role = ''; roleName = 'Pilih Peran'; tim = ''; timName = 'Pilih Tim Kerja'; status = 'aktif'; statusName = 'Aktif';" 
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

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        {{-- overflow-visible agar dropdown kustom tidak terpotong batas modal --}}
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-visible rounded-xl sm:rounded-2xl bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#604EE6] shadow-sm shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight truncate">Tambah Pengguna</h3>
                        <p class="text-[11px] sm:text-xs font-medium text-gray-400 truncate">Lengkapi data akun pengguna baru di bawah ini.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pengguna.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="p-4 sm:p-8 space-y-4 sm:space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Lengkap & NIP) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" x-model="nama" placeholder="Masukkan nama lengkap..." maxlength="100" required autocomplete="off"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                        </div>
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">NIP <span class="text-red-500">*</span></label>
                            <input type="text" name="nip" x-model="nip" placeholder="18 digit NIP..." required minlength="18" maxlength="18" pattern="[0-9]{18}" title="NIP harus 18 digit angka" autocomplete="off" @input="nip = $el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 18)" 
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                            <div class="mt-1.5 flex items-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] transition-all duration-200"
                                      :class="nip.length === 18 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 font-medium' : 'bg-gray-50 text-gray-500 border border-gray-200/70'">
                                    <template x-if="nip.length === 18">
                                        <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <template x-if="nip.length !== 18">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                    </template>
                                    <span x-text="nip.length === 18 ? '18 digit angka' : (nip.length > 0 ? '18 digit angka (' + nip.length + '/18)' : '18 digit angka')"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Email BPS & Password) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Email BPS <span class="text-red-500">*</span></label>
                            <input type="email" name="nama_email_baru" x-model="email" placeholder="user@bps.go.id" maxlength="100" required autocomplete="new-password"
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light transition-all"
                                :class="(email.trim().length > 0 && !(email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0)) ? '!border-rose-400 !focus:border-rose-500 !focus:ring-rose-100' : ''">
                            <div class="mt-1.5 flex items-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] transition-all duration-200"
                                      :class="(email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0) 
                                              ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 font-medium' 
                                              : (email.trim().length > 0 
                                                  ? 'bg-rose-50 text-rose-600 border border-rose-200/70 font-medium' 
                                                  : 'bg-gray-50 text-gray-500 border border-gray-200/70')">
                                    <template x-if="email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0">
                                        <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <template x-if="email.trim().length > 0 && !(email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0)">
                                        <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </template>
                                    <template x-if="email.trim().length === 0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                    </template>
                                    <span x-text="(email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0) 
                                                  ? 'Email kantor @bps.go.id valid' 
                                                  : (email.trim().length > 0 
                                                      ? 'Wajib gunakan domain resmi kantor @bps.go.id' 
                                                      : 'Email kantor @bps.go.id')">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="kata_sandi_baru" x-model="password" placeholder="Minimal 8 karakter..." required minlength="8" maxlength="100" autocomplete="new-password"
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light">
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#604EE6] transition p-1 cursor-pointer focus:outline-none" tabindex="-1" title="Lihat / Sembunyikan Password">
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" /></svg>
                                </button>
                            </div>
                            <div class="mt-1.5 flex items-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] transition-all duration-200"
                                      :class="password.length >= 8 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 font-medium' : 'bg-gray-50 text-gray-500 border border-gray-200/70'">
                                    <template x-if="password.length >= 8">
                                        <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </template>
                                    <template x-if="password.length < 8">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                                    </template>
                                    <span>Minimal 8 karakter</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Grid 2 Kolom (Status Akun & Peran) dengan Dropdown Kustom Buka ke Bawah --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        {{-- Dropdown Status Akun --}}
                        <div class="relative">
                            <label class="block text-xs font-normal text-gray-700 mb-2">Status Akun <span class="text-red-500">*</span></label>
                            <input type="hidden" name="status_akun" x-model="status" required>

                            <button @click="openStatusDropdown = !openStatusDropdown; openRoleDropdown = false; openTimDropdown = false;" @click.outside="openStatusDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#604EE6] focus:border-[#604EE6] focus:ring-2 focus:ring-[#604EE6]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                <span x-text="statusName" class="text-gray-700 font-light"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openStatusDropdown" x-cloak 
                                class="custom-scrollbar absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1">
                                <button type="button" @click="status = 'aktif'; statusName = 'Aktif'; openStatusDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer"
                                    :class="status === 'aktif' ? 'bg-purple-50/70 text-[#604EE6] font-light' : ''">
                                    Aktif
                                </button>
                                <button type="button" @click="status = 'pending'; statusName = 'Pending'; openStatusDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer"
                                    :class="status === 'pending' ? 'bg-purple-50/70 text-[#604EE6] font-light' : ''">
                                    Pending
                                </button>
                                <button type="button" @click="status = 'nonaktif'; statusName = 'Non-Aktif'; openStatusDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer"
                                    :class="status === 'nonaktif' ? 'bg-purple-50/70 text-[#604EE6] font-light' : ''">
                                    Non-Aktif
                                </button>
                            </div>
                        </div>

                        {{-- Dropdown Peran --}}
                        <div class="relative">
                            <label class="block text-xs font-normal text-gray-700 mb-2">Peran <span class="text-red-500">*</span></label>
                            <input type="hidden" name="id_role" x-model="role" required>

                            <button @click="openRoleDropdown = !openRoleDropdown; openStatusDropdown = false; openTimDropdown = false;" @click.outside="openRoleDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-gray-200 hover:border-[#604EE6] focus:border-[#604EE6] focus:ring-2 focus:ring-[#604EE6]/20 rounded-xl text-xs font-light transition-all cursor-pointer">
                                <span x-text="roleName" :class="role === '' ? 'text-gray-400 font-light' : 'text-gray-700 font-light'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] transition-transform duration-200" :class="openRoleDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openRoleDropdown" x-cloak 
                                class="custom-scrollbar absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-44 overflow-y-auto">
                                <button type="button" @click="role = ''; roleName = 'Pilih Peran'; openRoleDropdown = false;" 
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-400 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer">
                                    Pilih Peran
                                </button>
                                @foreach($roles as $r)
                                    <button type="button" 
                                        @click="role = '{{ $r->id_role }}'; roleName = '{{ $r->nama_role }}'; openRoleDropdown = false;"
                                        class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer"
                                        :class="role == '{{ $r->id_role }}' ? 'bg-purple-50/70 text-[#604EE6] font-light' : ''">
                                        {{ $r->nama_role }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Baris: Tim Kerja (Lebar Penuh) --}}
                    <div class="relative">
                        <label class="block text-xs font-normal text-gray-700 mb-2">Tim Kerja</label>
                        <input type="hidden" name="id_tim" x-model="tim">

                        <button @click="if (!roleName.toLowerCase().includes('direktur')) { openTimDropdown = !openTimDropdown; openStatusDropdown = false; openRoleDropdown = false; }" @click.outside="openTimDropdown = false" type="button" 
                            :disabled="roleName.toLowerCase().includes('direktur')"
                            class="w-full flex items-center justify-between px-4 py-2.5 border border-gray-200 focus:ring-2 focus:ring-[#604EE6]/20 rounded-xl text-xs font-light transition-all"
                            :class="roleName.toLowerCase().includes('direktur') ? 'opacity-60 bg-gray-100/70 text-gray-400 cursor-not-allowed' : 'bg-white hover:bg-[#F8F7FF] hover:border-[#604EE6] focus:border-[#604EE6] cursor-pointer'">
                            <span x-text="roleName.toLowerCase().includes('direktur') ? 'Tidak Memerlukan Tim Kerja' : timName" :class="(tim === '' || roleName.toLowerCase().includes('direktur')) ? 'text-gray-400 font-light' : 'text-gray-700 font-light'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] transition-transform duration-200" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openTimDropdown && !roleName.toLowerCase().includes('direktur')" x-cloak 
                            class="custom-scrollbar absolute left-0 top-full mt-1.5 w-full bg-white border border-gray-100 rounded-xl shadow-[0_12px_32px_rgba(0,0,0,0.12)] p-1.5 z-50 space-y-1 max-h-44 overflow-y-auto">
                            <button type="button" @click="tim = ''; timName = 'Pilih Tim Kerja'; openTimDropdown = false;" 
                                class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-400 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer">
                                Pilih Tim Kerja
                            </button>
                            @foreach($tims as $t)
                                <button type="button" 
                                    @click="tim = '{{ $t->id_tim }}'; timName = '{{ $t->nama_tim }}'; openTimDropdown = false;"
                                    class="w-full text-left px-3.5 py-2.5 rounded-lg text-xs text-gray-700 hover:bg-purple-50 hover:text-[#604EE6] font-light transition-all cursor-pointer"
                                    :class="tim == '{{ $t->id_tim }}' ? 'bg-purple-50/70 text-[#604EE6] font-light' : ''">
                                    {{ $t->nama_tim }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Petunjuk Dinamis Alur Penetapan Ketua Tim (Memanjang Sepanjang Container) --}}
                    <div x-show="roleName.toLowerCase().includes('ketua')" x-cloak class="w-full p-3 sm:p-3.5 bg-purple-50/70 border border-purple-100 rounded-xl flex items-start gap-2.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-[11px] text-gray-600 font-light leading-relaxed">
                            <span class="font-normal text-[#604EE6]">Petunjuk Penetapan Ketua Tim:</span> Jika tim kerja yang akan dipimpin belum dibuat, Anda dapat mengosongkan pilihan Tim Kerja di atas. Pengguna ini dapat langsung ditetapkan sebagai Ketua Tim saat membuat tim di menu <span class="font-normal text-gray-800">Manajemen Tim Kerja</span> (data tim kerja akun ini akan otomatis terhubung dan terisi).
                        </div>
                    </div>

                    {{-- Petunjuk Dinamis Peran Anggota (Memanjang Sepanjang Container) --}}
                    <div x-show="roleName.toLowerCase().includes('anggota')" x-cloak class="w-full p-3 sm:p-3.5 bg-purple-50/70 border border-purple-100 rounded-xl flex items-start gap-2.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-[11px] text-gray-600 font-light leading-relaxed">
                            <span class="font-normal text-[#604EE6]">Petunjuk Penempatan Anggota:</span> Pilih tim kerja untuk langsung menempatkan pegawai ke dalam tim terkait, atau kosongkan terlebih dahulu jika penempatan tim kerja akan ditentukan kemudian.
                        </div>
                    </div>

                    {{-- Petunjuk Dinamis Peran Direktur (Memanjang Sepanjang Container) --}}
                    <div x-show="roleName.toLowerCase().includes('direktur')" x-cloak class="w-full p-3 sm:p-3.5 bg-purple-50/70 border border-purple-100 rounded-xl flex items-start gap-2.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-[11px] text-gray-600 font-light leading-relaxed">
                            <span class="font-normal text-[#604EE6]">Peran Struktural:</span> Peran Direktur bersifat pengawasan institusional dan tidak memerlukan penempatan ke dalam tim kerja.
                        </div>
                    </div>
                </div>

                {{-- Footer Action --}}
                <div class="px-4 py-3 sm:px-8 sm:py-4.5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
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