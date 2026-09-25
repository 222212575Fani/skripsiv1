<x-layoutauth title="Login - Sistem Manajemen Proyek">
    
    {{-- Modal Notifikasi Pendaftaran Berhasil & Akun Pending (Top-Floating Banner Style) --}}
    <x-authnotifmodal />

    <x-authsidepanel title="Mulai Kelola Proyek Anda dengan Mudah" />

    <div class="flex items-center justify-center px-4 sm:px-6 md:px-8 py-4 sm:py-6">
        <div class="w-full max-w-[380px]">
            <h1 class="text-2xl sm:text-[28px] font-extrabold text-[#121212] leading-tight">
                Masuk ke Akun
            </h1>
            <p class="mt-1.5 text-xs sm:text-[13px] leading-relaxed text-[#7C7C7C] max-w-[360px]">
                Silakan Masuk untuk Mengelola Proyek
            </p>

            <form class="mt-6" action="{{ route('login.post') }}" method="POST" autocomplete="off" novalidate
                x-data="{
                    email: '{{ old('email', request()->cookie('remember_email', '')) }}',
                    password: '',
                    emailError: '',
                    passwordError: '',
                    validateForm(e) {
                        this.emailError = '';
                        this.passwordError = '';
                        let hasError = false;

                        const emailVal = this.email.trim();
                        if (!emailVal) {
                            this.emailError = 'Email wajib diisi.';
                            hasError = true;
                        } else if (!emailVal.includes('@') || !emailVal.includes('.')) {
                            this.emailError = 'Format email tidak valid (contoh: nama@bps.go.id).';
                            hasError = true;
                        }

                        if (!this.password) {
                            this.passwordError = 'Password wajib diisi.';
                            hasError = true;
                        }

                        if (hasError) {
                            e.preventDefault();
                            return false;
                        }
                    }
                }"
                @submit="validateForm($event)">
                @csrf
                
                {{-- Menampilkan error validasi dari session('error') atau $errors --}}
                @if (session('error') || $errors->any())
                    <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200/80 flex items-start gap-2.5 text-rose-700 shadow-2xs">
                        <div class="w-5 h-5 rounded-full bg-rose-100 flex items-center justify-center shrink-0 text-rose-600 mt-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="flex-1 text-xs font-semibold leading-relaxed">
                            @if (session('error'))
                                {{ session('error') }}
                            @else
                                {{ $errors->first() }}
                            @endif
                        </div>
                    </div>
                @endif

                <x-authinput 
                    label="Email" 
                    id="email" 
                    name="email" 
                    type="email" 
                    placeholder="Masukkan email Anda" 
                    :value="old('email', request()->cookie('remember_email'))" 
                    autocomplete="off"
                    x-model="email"
                    @input="emailError = ''"
                    x-bind:class="(emailError || {{ $errors->has('email') ? 'true' : 'false' }}) ? '!border-rose-400 !focus:border-rose-500 !focus:ring-rose-100' : ''"
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 8a7 7 0 1114 0H3z" />
                        </svg>
                    </x-slot:icon>
                    <x-slot:error>
                        <div x-show="emailError" x-cloak class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-rose-600">
                            <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="emailError"></span>
                        </div>
                        @error('email')
                            <div x-show="!emailError" class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-rose-600">
                                <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </x-slot:error>
                </x-authinput>

                <x-authinput 
                    label="Password" 
                    id="password" 
                    name="password" 
                    type="password" 
                    placeholder="Masukkan password Anda" 
                    autocomplete="new-password"
                    x-model="password"
                    @input="passwordError = ''"
                    x-bind:class="(passwordError || {{ $errors->has('password') ? 'true' : 'false' }}) ? '!border-rose-400 !focus:border-rose-500 !focus:ring-rose-100' : ''"
                >
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 8V6a5 5 0 1110 0v2h.5A1.5 1.5 0 0117 9.5v7A1.5 1.5 0 0115.5 18h-11A1.5 1.5 0 013 16.5v-7A1.5 1.5 0 014.5 8H5zm2 0h6V6a3 3 0 10-6 0v2z" clip-rule="evenodd"/>
                        </svg>
                    </x-slot:icon>
                    <x-slot:error>
                        <div x-show="passwordError" x-cloak class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-rose-600">
                            <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="passwordError"></span>
                        </div>
                        @error('password')
                            <div x-show="!passwordError" class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-rose-600">
                                <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror
                    </x-slot:error>
                </x-authinput>

                {{-- Fitur Remember Me --}}
                <div class="mb-5 flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember', request()->hasCookie('remember_email')) ? 'checked' : '' }} class="w-4 h-4 rounded border border-[#BFBFBF] accent-[#604EE6] cursor-pointer">
                    <label for="remember" class="text-xs sm:text-[13px] text-[#666666] cursor-pointer select-none">Ingatkan Saya</label>
                </div>

                <button type="submit" class="w-full h-[44px] rounded-lg sm:rounded-[10px] bg-[#604EE6] text-white text-[14px] font-semibold shadow-[0_8px_18px_rgba(96,78,230,0.28)] hover:opacity-95 transition cursor-pointer">
                    Login
                </button>
            </form>

            <div class="mt-6 flex flex-col items-center gap-3">
                <p class="text-center text-[13px] text-[#7A7A7A]">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-[#604EE6] font-semibold hover:underline">
                        Daftar di sini
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

</x-layoutauth>