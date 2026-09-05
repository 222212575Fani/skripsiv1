@props(['members' => [], 'title' => 'Daftar Ketua Proyek', 'subtitle' => 'ketua proyek aktif tahun ini'])

<div class="bg-white rounded-[24px] p-6 border border-gray-100 shadow-sm space-y-5">
    {{-- Header Card --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-gray-900 tracking-tight">{{ $title }}</h3>
            <p class="text-xs font-medium text-gray-400 mt-0.5">{{ count($members) }} {{ $subtitle }} ({{ now()->year }})</p>
        </div>
        <span class="px-3.5 py-1.5 bg-indigo-50/80 text-[#5C46F5] rounded-2xl text-xs font-bold border border-indigo-100/60 shadow-2xs">
            Periode {{ now()->year }}
        </span>
    </div>

    {{-- List Ketua Proyek --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($members as $member)
        <a href="{{ route('ketuatim.manajemenproyek', ['search' => $member->nama]) }}" 
            class="flex items-center justify-between p-4 rounded-2xl bg-[#F8F7FF] border border-[#EDE9FE] hover:bg-[#EDE9FE] hover:border-[#DDD6FE] hover:shadow-sm transition-all duration-200 group">
            
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold text-xs shadow-sm shrink-0">
                    {{ strtoupper(substr($member->nama ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#5C46F5] transition-colors truncate">{{ $member->nama }}</h4>
                    <p class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $member->sub_teks ?? 'Ketua Proyek' }}</p>
                    <p class="text-[10px] text-gray-400 font-medium truncate">NIP. {{ $member->nip ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                {{-- Badge Jumlah Proyek (Rapi Satu Baris & Elegan) --}}
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-white text-[#5C46F5] border border-[#DDD6FE] shadow-2xs">
                    Mengetuai {{ $member->jumlah_tugas }} Proyek
                </span>
                
                {{-- Tombol Panah Navigasi --}}
                <div class="w-9 h-9 rounded-full bg-white border border-[#DDD6FE] text-[#5C46F5] flex items-center justify-center shadow-xs shrink-0 group-hover:bg-[#5C46F5] group-hover:text-white group-hover:border-transparent transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-2 py-8 text-center text-xs text-gray-400">
            Belum ada data ketua proyek yang terdaftar untuk periode tahun {{ now()->year }}.
        </div>
        @endforelse
    </div>
</div>