<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Autentikasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Font Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="m-0 p-0 bg-[#ECEAF7]">
    <div class="min-h-screen flex items-center justify-center px-6 py-10">
        <div class="w-full max-w-[1180px] rounded-[26px] bg-white shadow-[0_18px_40px_rgba(91,79,197,0.18)] p-4 md:p-5">
            <div class="grid grid-cols-1 md:grid-cols-[0.95fr_1.15fr] gap-5 min-h-[700px]">
                {{-- Area ini akan otomatis diisi oleh halaman Login/Register --}}
                {{ $slot }}
            </div>
        </div>
    </div>

    {{-- SweetAlert2 untuk notifikasi sukses/error --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#5C46F5', 
            });
        </script>
    @endif
    @stack('scripts')
</body>
</html>