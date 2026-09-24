<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Autentikasi - PROXIS' }}</title>
    
    <!-- Favicon Logo BPS -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_bps.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo_bps.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_bps.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts Preconnect & Optimized Font Loading -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300..1000;1,300..1000&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Mulish', sans-serif; background-color: #ECEAF7; }

        /* Penyeragaman seluruh isian input, textarea, select agar konsisten tipis (font-weight: 300) */
        input:not([type="submit"]):not([type="button"]):not([type="reset"]):not([type="checkbox"]):not([type="radio"]),
        textarea,
        select {
            font-weight: 300 !important;
        }

        /* Penyeragaman seluruh placeholder agar konsisten tipis (font-weight: 300) */
        input::placeholder,
        textarea::placeholder,
        select::placeholder,
        ::placeholder,
        ::-webkit-input-placeholder,
        ::-moz-placeholder,
        :-ms-input-placeholder {
            font-weight: 300 !important;
            font-family: 'Mulish', sans-serif !important;
            color: #9ca3af !important;
            opacity: 1 !important;
        }
    </style>
</head>
<body class="m-0 p-0 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 py-4 sm:py-6">
        <div class="w-full max-w-[960px] rounded-2xl sm:rounded-[22px] bg-white shadow-[0_12px_36px_rgba(96,78,230,0.12)] p-3 sm:p-3.5 md:p-4">
            <div class="grid grid-cols-1 md:grid-cols-[0.9fr_1.1fr] gap-4 md:gap-6 min-h-0 md:min-h-[520px]">
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- SweetAlert2 CDN Global --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Notifikasi Toast Pop-Up Konsisten --}}
    <x-toast />

    {{-- Fungsi Global Toggle Password untuk Semua Halaman Auth --}}
    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            
            if (btn) {
                const openEye = btn.querySelector('.eye-open');
                const closedEye = btn.querySelector('.eye-closed');
                if (openEye && closedEye) {
                    openEye.classList.toggle('hidden', isPassword);
                    closedEye.classList.toggle('hidden', !isPassword);
                }
            }
        }
    </script>

    {{-- Stack untuk script tambahan per halaman --}}
    @stack('scripts')
</body>
</html>