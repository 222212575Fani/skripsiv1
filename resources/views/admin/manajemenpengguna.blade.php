<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - Admin SIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-gradient {
            background: linear-gradient(180deg, #5C46F5 0%, #2D1BAB 100%);
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #5C46F5; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F4F7FE] m-0 p-0 text-[#1A1A1A]">
    <div class="flex min-h-screen">
        <aside class="w-[300px] sidebar-gradient text-white flex flex-col shadow-xl z-20 relative">
            <div class="p-8">
                <h1 class="text-lg font-extrabold leading-tight tracking-tight uppercase">Badan Pusat Statistik</h1>
                <p class="text-[11px] font-medium opacity-80 mt-1 whitespace-nowrap uppercase">Sistem Manajemen Proyek</p>
            </div>

            <nav class="flex-1 px-4 mt-4">
                <div class="space-y-2">
                    <p class="px-4 text-[10px] font-bold text-white/50 uppercase tracking-[0.2em] mb-4">Menu Utama</p>
                    <a href="{{ route('admin.manajemenpengguna') }}" class="group relative flex items-center gap-3 px-4 py-3.5 text-sm font-bold text-white bg-white/10 rounded-xl transition-all overflow-hidden">
                        <div class="absolute left-0 top-0 h-full w-1 bg-[#8FD0FF]"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#8FD0FF]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Manajemen Pengguna
                    </a>
                    <a href="{{ route('admin.manajementimkerja') }}" class="group relative flex items-center gap-3 px-4 py-3.5 text-sm font-medium text-white/60 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Manajemen Tim Kerja
                    </a>
                </div>
            </nav>

            <div class="p-8 mt-auto text-left">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">© 2026 Badan Pusat Statistik</p>
            </div>
        </aside>

        <main class="flex-1 flex flex-col min-w-0">
            <header class="h-20 bg-white flex items-center justify-between px-10 border-b border-gray-100 shadow-sm relative z-30">
                <h2 class="text-xl font-extrabold text-[#5C46F5]">Manajemen Pengguna</h2>
                
                <div class="relative">
                    <button id="profileBtn" class="flex items-center gap-4 pl-6 border-l border-gray-200 focus:outline-none hover:opacity-80 transition-opacity">
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()->nama }}</p>
                            <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest text-right mt-0.5">
                                {{ Auth::user()->role->nama_role ?? 'Administrator' }}
                            </p>
                        </div>
                        <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold shadow-lg shadow-[#5C46F5]/20">
                            {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                        </div>
                    </button>

                    <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transition-all transform origin-top-right">
                        <div class="p-5 flex items-center gap-4 border-b border-gray-50 bg-gray-50/50">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-extrabold text-gray-900 leading-tight">{{ Auth::user()->nama }}</p>
                                <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest mt-0.5">{{ Auth::user()->role->nama_role ?? 'Admin' }}</p>
                            </div>
                        </div>
                        <div class="p-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-10 z-10 relative flex-1">
                <div class="flex flex-col items-center mb-12">
                    <div class="relative w-full max-w-2xl">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-[#5C46F5]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" placeholder="Cari berdasarkan Nama atau NIP pengguna..." class="w-full h-16 bg-white border-none rounded-[20px] pl-16 pr-8 text-base shadow-xl shadow-blue-900/5 focus:ring-2 focus:ring-[#5C46F5] outline-none transition-all placeholder:text-gray-400 font-bold">
                    </div>
                </div>

                <div class="flex justify-between items-center mb-8 px-2 font-bold">
                    <div class="flex gap-8 border-b border-gray-200">
                        <button class="pb-4 text-sm font-bold border-b-2 border-[#5C46F5] text-[#5C46F5]">Semua</button>
                        <button class="pb-4 text-sm font-bold text-gray-400 hover:text-[#5C46F5] transition">Pending</button>
                        <button class="pb-4 text-sm font-bold text-gray-400 hover:text-[#5C46F5] transition">Aktif</button>
                        <button class="pb-4 text-sm font-bold text-gray-400 hover:text-[#5C46F5] transition">Nonaktif</button>
                    </div>
                    <button class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/30 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Tambah Baru
                    </button>
                </div>

                <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100 text-[#7C7C7C] text-[11px] uppercase tracking-widest font-bold">
                                <th class="px-8 py-6 text-center">No</th>
                                <th class="px-8 py-6">NIP</th>
                                <th class="px-8 py-6">Nama</th>
                                <th class="px-8 py-6">Tim</th>
                                <th class="px-8 py-6">Peran</th>
                                <th class="px-8 py-6 text-center">Status</th>
                                <th class="px-8 py-6 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 font-bold">
                            @forelse($users as $index => $user)
                            <tr class="hover:bg-blue-50/30 transition-all">
                                <td class="px-8 py-5 text-sm text-gray-400 text-center">{{ $users->firstItem() + $index }}</td>
                                <td class="px-8 py-5 text-sm font-semibold text-gray-500">{{ $user->nip }}</td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-800">{{ $user->nama }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-600 italic">
                                    {{ $user->anggotaTim->where('tanggal_keluar', null)->first()->tim->nama_tim ?? '-' }}
                                </td>
                                <td class="px-8 py-5 text-sm text-gray-600">
                                    {{ $user->role->nama_role ?? 'Belum Ditentukan' }}
                                </td>
                                <td class="px-8 py-5 text-center">
                                    @php
                                        $statusStyles = [
                                            'aktif' => 'bg-green-50 text-green-600 border-green-100',
                                            'pending' => 'bg-amber-50 text-amber-500 border-amber-100',
                                            'nonaktif' => 'bg-red-50 text-red-600 border-red-100'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $statusStyles[$user->status_akun] ?? 'bg-gray-50 text-gray-500 border-gray-100' }}">
                                        {{ $user->status_akun }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if($user->status_akun == 'pending')
                                            <button class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-bold rounded-lg shadow-md hover:scale-105 transition active:scale-95 uppercase">Aktivasi</button>
                                        @else
                                            <button class="p-2 text-gray-400 hover:text-[#5C46F5] transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-8 py-16 text-center text-gray-400 italic">Data tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="px-8 py-5 border-t border-gray-100 flex justify-between items-center bg-white">
                        @if ($users->total() > 0)
                            <p class="text-sm text-gray-500 font-bold">Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna</p>
                        @else
                            <p class="text-sm text-gray-500 font-bold">Menampilkan 0 pengguna</p>
                        @endif

                        <div class="flex items-center gap-2 font-bold">
                            @if ($users->onFirstPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                                </a>
                            @endif

                            @php
                                $start = max(1, $users->currentPage() - 2);
                                $end = max(1, min($users->lastPage(), $users->currentPage() + 2));
                            @endphp

                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $users->currentPage())
                                    <span class="w-9 h-9 flex items-center justify-center rounded-lg bg-[#5C46F5] text-white font-bold shadow-md shadow-[#5C46F5]/20">{{ $i }}</span>
                                @else
                                    <a href="{{ $users->url($i) }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">{{ $i }}</a>
                                @endif
                            @endfor

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </a>
                            @else
                                <span class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-100 text-gray-300 cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            profileBtn.addEventListener('click', (e) => { profileDropdown.classList.toggle('hidden'); e.stopPropagation(); });
            document.addEventListener('click', (e) => { if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) profileDropdown.classList.add('hidden'); });
        });
    </script>
</body>
</html>