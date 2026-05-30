<x-layoutauth title="Login - Sistem Manajemen Proyek">
    
    <x-authsidepanel title="Mulai Kelola Proyek Anda dengan Mudah" />

    <div class="flex items-center justify-center px-4 md:px-10 py-6">
        <div class="w-full max-w-[470px]">
            <div class="text-[#4A34F1] text-[36px] font-bold leading-none mb-2">*</div>
            <h1 class="text-[30px] md:text-[36px] font-extrabold text-[#121212] leading-tight">
                Masuk ke Akun
            </h1>
            <p class="mt-2 text-[14px] leading-6 text-[#7C7C7C] max-w-[360px]">
                Silakan Masuk untuk Mengelola Proyek
            </p>

            <form class="mt-10" action="{{ route('login.post') }}" method="POST">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-5 rounded-[10px] bg-red-50 border border-red-200 px-4 py-3 text-[13px] text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <x-authinput label="Email" id="email" name="email" type="email" placeholder="Masukkan email Anda" :value="old('email')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 8a7 7 0 1114 0H3z" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                <x-authinput label="Password" id="password" name="password" type="password" placeholder="Masukkan password Anda">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 8V6a5 5 0 1110 0v2h.5A1.5 1.5 0 0117 9.5v7A1.5 1.5 0 0115.5 18h-11A1.5 1.5 0 013 16.5v-7A1.5 1.5 0 014.5 8H5zm2 0h6V6a3 3 0 10-6 0v2z" clip-rule="evenodd"/>
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                <div class="mb-7 flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border border-[#BFBFBF] accent-[#5C46F5]">
                    <label for="remember" class="text-[14px] text-[#666666]">Ingatkan Saya</label>
                </div>

                <button type="submit" class="w-full h-[48px] rounded-[10px] bg-[#5C46F5] text-white text-[14px] font-semibold shadow-[0_8px_18px_rgba(92,70,245,0.28)] hover:opacity-95 transition">
                    Login
                </button>
            </form>

            <div class="mt-8">
                <p class="mt-5 text-center text-[13px] text-[#7A7A7A]">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-[#5C46F5] font-medium hover:underline">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- 1. Mengimpor library CDN SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        {{-- 2. Menangkap session Flash Error dari AuthController --}}
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: "{{ session('error') }}",
                confirmButtonColor: '#5C46F5',
                customClass: {
                    popup: 'rounded-[20px]'
                }
            });
        @endif

        {{-- 3. Menangkap session Flash Success dari AuthController --}}
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                confirmButtonColor: '#5C46F5',
                customClass: {
                    popup: 'rounded-[20px]'
                }
            });
        @endif
    </script>
    @endpush

</x-layoutauth>