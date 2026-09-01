<x-layoututama title="Proyek & Aktivitas">
    <div x-data="{ tab: '{{ ($proyekKetua->count() > 0) ? 'ketua' : 'anggota' }}' }" class="flex flex-col gap-6">
        
        {{-- HEADER JUDUL --}}
        <div class="flex justify-between items-end px-2">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Proyek & Aktivitas Anda</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Kelola aktivitas harian Anda sebagai anggota dan pantau proyek yang Anda ketuai.</p>
            </div>
        </div>

        {{-- JIKA DIA PUNYA PROYEK YANG DIKETUAI, MUNCULKAN TAB SWITCHER --}}
        @if($proyekKetua->count() > 0)
        <div class="flex items-center gap-4 border-b border-gray-200 px-2">
            <button @click="tab = 'ketua'" 
                :class="tab === 'ketua' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all">
                Proyek yang Saya Ketuai 
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] bg-indigo-50 text-[#5C46F5]">{{ $proyekKetua->count() }}</span>
            </button>
            <button @click="tab = 'anggota'" 
                :class="tab === 'anggota' ? 'border-[#5C46F5] text-[#5C46F5]' : 'border-transparent text-gray-400 hover:text-gray-600'"
                class="pb-3 text-xs font-bold uppercase tracking-wider border-b-2 transition-all">
                Proyek sebagai Anggota (Aktivitas Saya)
                <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600">{{ $proyekAnggota->count() }}</span>
            </button>
        </div>
        @endif

        {{-- SECTION 1: TAB KETUA PROYEK (Hanya muncul jika dia memimpin proyek) --}}
        @if($proyekKetua->count() > 0)
        <div x-show="tab === 'ketua'" class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Daftar Proyek di Bawah Kepemimpinan Anda</h2>
                <span class="text-xs text-gray-400 font-medium">Hak Akses: CRUD Aktivitas & Kelola Target</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                            <th class="py-3 px-4">Nama Proyek</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi (Kelola Aktivitas)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium">
                        @foreach($proyekKetua as $proyek)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $proyek->nama_proyek }}</td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-bold uppercase bg-amber-50 text-amber-600 border border-amber-100">
                                    {{ $proyek->status_proyek }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="#" class="px-4 py-2 bg-indigo-50 text-[#5C46F5] hover:bg-[#5C46F5] hover:text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                    Kelola Aktivitas
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- SECTION 2: TAB / HALAMAN TUGAS ANGGOTA (Selalu tampil untuk semua anggota) --}}
        <div x-show="tab === 'anggota' || {{ $proyekKetua->count() == 0 ? 'true' : 'false' }}" class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Daftar Tugas / Aktivitas Tanggung Jawab Anda</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Silakan lakukan pembaruan progress mingguan dan unggah dokumen pendukung di sini.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-[#5C46F5]">
                    Total Tugas: {{ $proyekAnggota->count() ?? 0 }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-400 text-[10px] uppercase tracking-widest font-bold border-b border-gray-100">
                            <th class="py-3 px-4">Nama Proyek Asal</th>
                            <th class="py-3 px-4">Target Absolut</th>
                            <th class="py-3 px-4 text-center">Aksi (Update Progress)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm font-medium">
                        @forelse($proyekAnggota ?? [] as $proyek)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $proyek->nama_proyek }}</td>
                            <td class="py-4 px-4 text-gray-500">Target Pekerjaan</td>
                            <td class="py-4 px-4 text-center">
                                <button type="button" class="px-4 py-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                    Input Progress
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-400 text-xs font-bold">
                                Belum ada tugas atau aktivitas yang dibebankan kepada Anda.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layoututama>