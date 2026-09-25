<x-layoutauth title="Daftar Akun - Sistem Manajemen Proyek">
    
    <x-authsidepanel 
        greeting="Ayo!" 
        title="Daftarkan Diri dan Mulai Kelola Proyek" 
    />

    <div class="flex items-center justify-center px-4 sm:px-6 md:px-8 py-4 sm:py-6">
        <div class="w-full max-w-[420px]">
            <h1 class="text-2xl sm:text-[28px] font-extrabold text-[#121212] leading-tight">
                Daftar Akun Baru
            </h1>
            <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#7C7C7C]">
                Silakan isi data diri untuk mulai menggunakan sistem
            </p>

            <form class="mt-5" action="{{ route('register.post') }}" method="POST" autocomplete="off" novalidate
                x-data="{
                    nama: '{{ old('nama', '') }}',
                    nip: '{{ old('nip', '') }}',
                    email: '{{ old('email', '') }}',
                    password: '',
                    password_confirmation: ''
                }">
                @csrf
                
                {{-- Box Notifikasi Jika Validasi Input Gagal --}}
                @if($errors->any())
                    <div class="p-3.5 mb-4 text-xs text-red-800 rounded-lg bg-red-50 border border-red-100" role="alert">
                        <ul class="list-disc pl-5 font-semibold space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Input Nama --}}
                <x-authinput label="Nama Lengkap" id="nama" name="nama" type="text" placeholder="Masukkan nama lengkap" maxlength="100" :value="old('nama')" autocomplete="off" x-model="nama">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 8a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                {{-- Input NIP --}}
                <x-authinput label="NIP" id="nip" name="nip" type="text" placeholder="Masukkan 18 digit NIP Anda" maxlength="18" pattern="[0-9]{18}" title="NIP harus 18 digit angka" :value="old('nip')" autocomplete="off" x-model="nip" @input="nip = $el.value = $el.value.replace(/[^0-9]/g, '').slice(0, 18)">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                    <x-slot:hint>
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
                    </x-slot:hint>
                </x-authinput>

                {{-- Input Email --}}
                <x-authinput label="Email" id="email" name="email" type="email" placeholder="Masukkan email @bps.go.id" maxlength="100" :value="old('email')" autocomplete="off" x-model="email"
                    x-bind:class="(email.trim().length > 0 && !(email.trim().toLowerCase().endsWith('@bps.go.id') && email.indexOf('@bps.go.id') > 0)) ? '!border-rose-400 !focus:border-rose-500 !focus:ring-rose-100' : ''">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </x-slot:icon>
                    <x-slot:hint>
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
                    </x-slot:hint>
                </x-authinput>

                {{-- Input Password Utama --}}
                <x-authinput label="Password" id="password" name="password" type="password" placeholder="Buat password minimal 8 karakter" minlength="8" maxlength="100" autocomplete="new-password" x-model="password">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                    <x-slot:hint>
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
                    </x-slot:hint>
                </x-authinput>

                {{-- Input Konfirmasi Password (Pencocok Aturan Validasi Laravel) --}}
                <x-authinput label="Konfirmasi Password" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang password Anda" minlength="8" maxlength="100" autocomplete="new-password" x-model="password_confirmation">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                    <x-slot:hint>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] transition-all duration-200"
                              :class="(password_confirmation.length >= 8 && password_confirmation === password) 
                                      ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70 font-medium' 
                                      : ((password_confirmation.length > 0 && password_confirmation !== password) 
                                          ? 'bg-rose-50 text-rose-600 border border-rose-200/70 font-medium' 
                                          : 'bg-gray-50 text-gray-500 border border-gray-200/70')">
                            <template x-if="password_confirmation.length >= 8 && password_confirmation === password">
                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="password_confirmation.length > 0 && password_confirmation !== password">
                                <svg class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </template>
                            <template x-if="password_confirmation.length === 0 || (password_confirmation.length < 8 && password_confirmation === password)">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300 shrink-0"></span>
                            </template>
                            <span x-text="(password_confirmation.length >= 8 && password_confirmation === password) 
                                          ? 'Password cocok' 
                                          : ((password_confirmation.length > 0 && password_confirmation !== password) 
                                              ? 'Password belum cocok' 
                                              : 'Kesesuaian password')">
                            </span>
                        </span>
                    </x-slot:hint>
                </x-authinput>

                <button type="submit" class="w-full h-[44px] mt-2 rounded-lg sm:rounded-[10px] bg-[#604EE6] text-white text-[14px] font-semibold shadow-lg hover:opacity-95 transition">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-5 flex flex-col items-center gap-3">
                <p class="text-center text-[13px] text-[#7A7A7A]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-[#604EE6] font-semibold hover:underline">
                        Masuk ke akun
                    </a>
                </p>

                {{-- Navigasi Kembali ke Beranda / Landing Page --}}
                <a href="{{ route('home') }}" class="text-[13px] text-[#604EE6] hover:text-[#503ED8] hover:underline transition-colors font-semibold">
                    Kembali ke Beranda
                </a>

                {{-- Copyright untuk layar kecil (Mobile / < md) di bagian putih --}}
                <p class="md:hidden mt-2 text-center text-[12px] text-[#7A7A7A]">
                    © 2026 Direktorat Sistem Informasi Statistik
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Pengunci otomatis input NIP maks 18 karakter angka
        document.addEventListener('DOMContentLoaded', function() {
            const nipInput = document.getElementById('nip');
            
            if(nipInput) {
                nipInput.setAttribute('maxlength', '18');

                nipInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);
                });
            }
        });
    </script>
    @endpush

</x-layoutauth>