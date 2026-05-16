<x-layoutauth title="Daftar Akun - Sistem Manajemen Proyek">
    
    <x-authsidepanel 
        greeting="Halo!" 
        title="Bergabung dan Kelola Proyek Lebih Terstruktur" 
    />

    <div class="flex items-center justify-center px-4 md:px-10 py-6">
        <div class="w-full max-w-[470px]">
            <div class="text-[#4A34F1] text-[36px] font-bold leading-none mb-2">*</div>
            <h1 class="text-[30px] md:text-[36px] font-extrabold text-[#121212] leading-tight">
                Daftar Akun Baru
            </h1>
            <p class="mt-2 text-[14px] leading-6 text-[#7C7C7C]">
                Silakan isi data diri untuk mulai menggunakan sistem
            </p>

            <form class="mt-8" action="{{ route('register.post') }}" method="POST">
                @csrf
                
                {{-- Box Notifikasi Jika Validasi Input Gagal --}}
                @if($errors->any())
                    <div class="p-4 mb-5 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100" role="alert">
                        <ul class="list-disc pl-5 font-semibold space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Input Nama --}}
                <x-authinput label="Nama Lengkap" id="nama" name="nama" type="text" placeholder="Masukkan nama lengkap" :value="old('nama')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 8a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                {{-- Input NIP --}}
                <x-authinput label="NIP" id="nip" name="nip" type="text" placeholder="Masukkan NIP Anda" :value="old('nip')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                {{-- Input Email --}}
                <x-authinput label="Email" id="email" name="email" type="email" placeholder="Masukkan email Anda" :value="old('email')">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                {{-- Input Password Utama --}}
                <x-authinput label="Password" id="password" name="password" type="password" placeholder="Buat password minimal 8 karakter">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                {{-- Input Konfirmasi Password (Pencocok Aturan Validasi Laravel) --}}
                <x-authinput label="Konfirmasi Password" id="password_confirmation" name="password_confirmation" type="password" placeholder="Ketik ulang password Anda">
                    <x-slot:icon>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </x-slot:icon>
                </x-authinput>

                <button type="submit" class="w-full h-[48px] mt-4 rounded-[10px] bg-[#5C46F5] text-white text-[14px] font-semibold shadow-lg hover:opacity-95 transition">
                    Daftar Sekarang
                </button>
            </form>

            <p class="mt-8 text-center text-[13px] text-[#7A7A7A]">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#5C46F5] font-medium hover:underline">
                    Masuk ke akun
                </a>
            </p>
        </div>
    </div>

    @push('scripts')
    <script>
        // Fungsi melihat password
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Fungsi pengunci otomatis input NIP maks 18 karakter angka
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