@props(['members' => [], 'title' => 'Daftar Ketua Proyek', 'subtitle' => 'ketua proyek aktif periode ini'])

<div class="bg-white rounded-2xl sm:rounded-[24px] p-4 sm:p-6 border border-gray-100 shadow-sm space-y-4 sm:space-y-5"
    x-data="{ 
        selectedYear: '{{ request('tahun', date('Y')) }}', 
        selectedMonth: '{{ request('bulan', 'semua') }}', 
        openPeriodeDropdown: false,
        isLoading: false,
        filterTahun(y) {
            this.selectedYear = y;
            this.fetchMembers();
        },
        filterBulan(m) {
            this.selectedMonth = m;
            this.fetchMembers();
        },
        fetchMembers() {
            this.isLoading = true;
            let url = new URL(window.location.href);
            url.searchParams.set('tahun', this.selectedYear);
            url.searchParams.set('bulan', this.selectedMonth);

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newContainer = doc.getElementById('cardmember-members-wrapper');
                let newSubtitle = doc.getElementById('cardmember-subtitle-count');
                
                if (newContainer && document.getElementById('cardmember-members-wrapper')) {
                    document.getElementById('cardmember-members-wrapper').innerHTML = newContainer.innerHTML;
                }
                if (newSubtitle && document.getElementById('cardmember-subtitle-count')) {
                    document.getElementById('cardmember-subtitle-count').innerHTML = newSubtitle.innerHTML;
                }
                window.history.pushState({}, '', url.toString());
            })
            .catch(err => console.error('Error filtering members:', err))
            .finally(() => {
                this.isLoading = false;
            });
        }
    }">
    
    {{-- Header Card & Tombol Pill Filter Periode --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="text-base font-bold text-gray-900 tracking-tight">{{ $title }}</h3>
        </div>

        {{-- Tombol Pill Filter Periode: Full-Width di Layar Kecil (< sm), Kompak di Sebelah Kanan saat Layar Lebar (sm+) --}}
        <div class="relative w-full sm:w-auto">
            <button @click="openPeriodeDropdown = !openPeriodeDropdown" @click.outside="openPeriodeDropdown = false" type="button" 
                class="w-full sm:w-auto flex items-center justify-between gap-2.5 px-4 py-2 bg-white border rounded-full text-xs font-normal text-[#604EE6] transition-all cursor-pointer shadow-2xs focus:outline-none"
                :class="openPeriodeDropdown ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                <div class="flex items-center gap-2 truncate">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="truncate" x-text="selectedMonth === 'semua' ? 'Periode ' + selectedYear : 'Bulan ' + ({'01':'Januari','02':'Februari','03':'Maret','04':'April','05':'Mei','06':'Juni','07':'Juli','08':'Agustus','09':'September','10':'Oktober','11':'November','12':'Desember'}[selectedMonth] || selectedMonth) + ' ' + selectedYear"></span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0 ml-2" :class="openPeriodeDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            {{-- Dropdown Popup Memanjang di Mobile, Kompak di Sebelah Kanan saat Layar Lebar --}}
            <div x-show="openPeriodeDropdown" x-cloak 
                class="absolute left-0 sm:left-auto right-0 mt-2 w-full sm:w-72 bg-white border border-purple-100 rounded-[24px] shadow-xl p-4 z-50 space-y-3 text-xs">
                
                {{-- BAGIAN PILIH TAHUN (1990 s/d Tahun Sekarang secara Dinamis) --}}
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-1.5">Pilih Tahun</span>
                    <div class="max-h-28 overflow-y-auto custom-scrollbar pr-1 grid grid-cols-4 gap-1.5">
                        @for($y = (int)date('Y'); $y >= 1990; $y--)
                            <button type="button" 
                                @click="filterTahun('{{ $y }}')"
                                class="py-1 rounded-xl text-center font-normal transition-all cursor-pointer"
                                :class="selectedYear == '{{ $y }}' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-gray-50 text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                {{ $y }}
                            </button>
                        @endfor
                    </div>
                </div>

                <hr class="border-purple-100/60">

                {{-- BAGIAN PILIH BULAN --}}
                <div>
                    <span class="block text-[10px] font-extrabold text-gray-400 uppercase tracking-wider mb-1.5">Pilih Bulan</span>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" 
                            @click="filterBulan('semua')"
                            class="col-span-3 py-1.5 rounded-xl text-center font-normal transition-all cursor-pointer"
                            :class="selectedMonth === 'semua' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-gray-50 text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                            Semua Bulan
                        </button>

                        @foreach([
                            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
                            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
                        ] as $num => $namaBulan)
                            <button type="button" 
                                @click="filterBulan('{{ $num }}')"
                                class="py-1 rounded-xl text-center font-normal transition-all cursor-pointer truncate px-1"
                                :class="selectedMonth === '{{ $num }}' ? 'bg-[#6E5BC3] text-white font-bold shadow-xs' : 'bg-gray-50 text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3]'">
                                {{ $namaBulan }}
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- List Ketua Proyek (2 Card ke Samping pada Layar Lebar, 1 Card pada Layar Kecil) --}}
    <div id="cardmember-members-wrapper" 
        class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-opacity duration-200"
        :class="{ 'opacity-40 pointer-events-none': isLoading }">
        @forelse($members as $member)
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-3 p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-[#F8F7FF] border border-[#EDE9FE] hover:bg-[#EDE9FE] hover:border-[#DDD6FE] hover:shadow-sm transition-all duration-200 group">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-10 h-10 rounded-full bg-linear-to-tr from-[#6E5BC3] to-[#8470E5] flex items-center justify-center text-white font-bold text-xs shadow-sm shrink-0">
                    {{ strtoupper(substr($member->nama ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-gray-900 group-hover:text-[#6E5BC3] transition-colors truncate">{{ $member->nama }}</h4>
                    <p class="text-[11px] font-medium text-gray-500 mt-0.5">{{ $member->sub_teks ?? 'Ketua Proyek' }}</p>
                    <p class="text-[10px] text-gray-400 font-medium truncate">NIP. {{ $member->nip ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-white text-[#6E5BC3] border border-[#DDD6FE] shadow-2xs">
                    Mengetuai {{ $member->jumlah_tugas ?? 0 }} Proyek
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-2 w-full">
            <x-emptystate 
                padding="py-12 px-4"
                title="Tidak Ada Ketua Proyek" 
                message="Tidak ada ketua proyek yang aktif pada periode ini." 
            />
        </div>
        @endforelse
    </div>
</div>