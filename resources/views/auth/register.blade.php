<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 p-0 font-sans bg-[#ECEAF7]">
    <div class="min-h-screen flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-[1180px] rounded-[26px] bg-white shadow-[0_18px_40px_rgba(91,79,197,0.18)] p-4 md:p-5">
            <div class="grid grid-cols-1 md:grid-cols-[0.95fr_1.15fr] gap-5 min-h-[700px]">

                <!-- Panel kiri -->
                <div class="relative overflow-hidden rounded-[22px] bg-gradient-to-br from-[#8FD0FF] via-[#4D5BFF] to-[#D9E8F7]">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_35%,rgba(34,0,255,0.88),transparent_38%),radial-gradient(circle_at_60%_18%,rgba(173,106,255,0.55),transparent_30%),radial-gradient(circle_at_85%_75%,rgba(255,255,255,0.5),transparent_35%)]"></div>

                    <div class="relative h-full flex flex-col justify-between p-8 md:p-10 text-white">
                        <div class="text-[54px] leading-none font-bold opacity-95">
                            *
                        </div>

                        <div class="max-w-[300px]">
                            <p class="text-[16px] text-white/90 mb-4">Selamat Datang!</p>
                            <h2 class="text-[26px] md:text-[34px] font-extrabold leading-[1.15]">
                                Buat Akun dan Mulai Akses Sistem
                            </h2>
                        </div>

                        <div class="text-[14px] text-white/90">
                            © 2026 Direktorat Sistem Informasi Statistik
                        </div>
                    </div>
                </div>

                <!-- Panel kanan -->
                <div class="flex items-center justify-center px-4 md:px-10 py-6">
                    <div class="w-full max-w-[470px]">
                        <div class="text-[#4A34F1] text-[36px] font-bold leading-none mb-2">*</div>

                        <h1 class="text-[30px] md:text-[36px] font-extrabold text-[#121212] leading-tight">
                            Buat Akun Baru
                        </h1>

                        <p class="mt-2 text-[14px] leading-6 text-[#7C7C7C] max-w-[360px]">
                            Daftarkan akun Anda untuk Mengakses Sistem Manajemen Proyek Direktorat Sistem Informasi Statistik
                        </p>

                        <form class="mt-8">
                            <div class="mb-5">
                                <label for="name" class="block text-[14px] font-semibold text-[#232323] mb-2">
                                    Nama Lengkap
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white px-4 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]"
                                >
                            </div>

                            <div class="mb-5">
                                <label for="nip" class="block text-[14px] font-semibold text-[#232323] mb-2">
                                    NIP
                                </label>
                                <input
                                    type="text"
                                    id="nip"
                                    name="nip"
                                    placeholder="Masukkan NIP Anda"
                                    class="w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white px-4 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]"
                                >
                            </div>

                            <div class="mb-5">
                                <label for="email" class="block text-[14px] font-semibold text-[#232323] mb-2">
                                    Email
                                </label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Masukkan email Anda"
                                        class="w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white pl-12 pr-4 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]"
                                    >

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9B9B9B]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 8a7 7 0 1114 0H3z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label for="password" class="block text-[14px] font-semibold text-[#232323] mb-2">
                                    Password
                                </label>

                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        placeholder="Masukkan password Anda"
                                        class="w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white pl-12 pr-11 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]"
                                    >

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9B9B9B]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 8V6a5 5 0 1110 0v2h.5A1.5 1.5 0 0117 9.5v7A1.5 1.5 0 0115.5 18h-11A1.5 1.5 0 013 16.5v-7A1.5 1.5 0 014.5 8H5zm2 0h6V6a3 3 0 10-6 0v2z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>

                                    <button
                                        type="button"
                                        onclick="togglePassword('password', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9B9B9B] hover:text-[#6B56FF] transition"
                                        aria-label="Lihat password"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-7">
                                <label for="password_confirmation" class="block text-[14px] font-semibold text-[#232323] mb-2">
                                    Konfirmasi Password
                                </label>

                                <div class="relative">
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="Masukkan ulang password Anda"
                                        class="w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white pl-12 pr-11 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]"
                                    >

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9B9B9B]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 8V6a5 5 0 1110 0v2h.5A1.5 1.5 0 0117 9.5v7A1.5 1.5 0 0115.5 18h-11A1.5 1.5 0 013 16.5v-7A1.5 1.5 0 014.5 8H5zm2 0h6V6a3 3 0 10-6 0v2z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>

                                    <button
                                        type="button"
                                        onclick="togglePassword('password_confirmation', this)"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9B9B9B] hover:text-[#6B56FF] transition"
                                        aria-label="Lihat konfirmasi password"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="w-full h-[48px] rounded-[10px] bg-[#5C46F5] text-white text-[14px] font-semibold shadow-[0_8px_18px_rgba(92,70,245,0.28)] hover:opacity-95 transition"
                            >
                                Daftar
                            </button>
                        </form>

                        <div class="mt-8">
                            <p class="mt-5 text-center text-[13px] text-[#7A7A7A]">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="text-[#5C46F5] font-medium hover:underline">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>