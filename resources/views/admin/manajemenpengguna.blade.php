<x-layoutadmin title="Manajemen Pengguna">
    <x-slot name="headerTitle">
        <div class="relative w-full">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" placeholder="Cari pengguna..." class="w-full pl-12 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none transition-all text-sm font-medium">
        </div>
    </x-slot>

    <div class="flex flex-col gap-8">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Otorisasi akun baru dan manajemen hak akses.</p>
            </div>
            <button class="px-6 py-3 bg-[#5C46F5] text-white rounded-xl font-bold text-sm hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Tambah Pengguna
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-separate border-spacing-0">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-gray-400 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-8 py-5 text-center w-16">No</th>
                        <th class="px-8 py-5">NIP</th>
                        <th class="px-8 py-5">Nama Pengguna</th>
                        <th class="px-8 py-5">Tim Kerja</th>
                        <th class="px-8 py-5">Peran</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium text-sm">
                    @forelse($users as $index => $user)
                    <tr class="group hover:bg-white hover:shadow-[0_10px_30px_-5px_rgba(92,70,245,0.2)] hover:scale-[1.005] transition-all duration-200 cursor-pointer relative hover:z-10">
                        
                        <td class="absolute left-0 top-0 bottom-0 w-1 bg-[#5C46F5] opacity-0 group-hover:opacity-100 transition-opacity duration-200"></td>

                        <td class="px-8 py-5 text-gray-400 text-center font-bold">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td class="px-8 py-5 font-bold text-gray-600">
                            {{ $user->nip }}
                        </td>
                        <td class="px-8 py-5 font-bold text-gray-800">
                            {{ $user->nama }}
                        </td>
                        <td class="px-8 py-5 text-gray-500 italic">
                            {{ $user->anggotaTim->where('tanggal_keluar', null)->first()->tim->nama_tim ?? '-' }}
                        </td>
                        <td class="px-8 py-5 text-gray-500 font-bold">
                            @if($user->status_akun == 'pending')
                                <span class="text-gray-300 font-normal italic">Menunggu Aktivasi</span>
                            @else
                                {{ $user->role->nama_role ?? '-' }}
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $user->status_akun == 'aktif' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-amber-50 text-amber-500 border-amber-100' }}">
                                {{ $user->status_akun }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($user->status_akun == 'pending')
                                <button class="px-4 py-1.5 bg-[#5C46F5] text-white text-[10px] font-black rounded-lg shadow-md hover:bg-[#4A38D4] transition-all uppercase">
                                    Aktivasi
                                </button>
                            @else
                                <div class="flex justify-center">
                                    <button class="p-2 text-gray-400 hover:text-[#5C46F5] transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-8 py-20 text-center text-gray-400 italic">Data belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layoutadmin>