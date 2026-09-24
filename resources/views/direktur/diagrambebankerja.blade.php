<div class="bg-white rounded-2xl sm:rounded-[28px] shadow-sm border border-gray-100 p-4 sm:p-6 flex flex-col gap-4 sm:gap-6"
    x-data="bebanKerjaChartComponent()">

    {{-- Header: Judul di Kiri & Tombol Filter di Kanan --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3.5 sm:gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">Beban Kerja Anggota Tim</h3>
        </div>

        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 w-full lg:w-auto">
            {{-- 1. Filter Tim Kerja (Berjejer ke bawah pada layar diperkecil persis Daftar Proyek) --}}
            <div class="relative w-full lg:w-auto shrink-0">
                <button @click="openTimDropdown = !openTimDropdown; bulanOpen = false;" @click.outside="openTimDropdown = false" type="button" 
                    class="flex items-center justify-between gap-3 px-4 py-2 bg-white border rounded-full text-xs font-normal text-[#604EE6] transition-all cursor-pointer shadow-2xs w-full lg:w-auto focus:outline-none"
                    :class="openTimDropdown ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="truncate max-w-50" x-text="timFilterName"></span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="openTimDropdown" x-cloak class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-72 bg-white border border-purple-100 rounded-3xl shadow-xl p-3 z-50 space-y-1 max-h-56 overflow-y-auto custom-scrollbar">
                    <button type="button" 
                        @click="selectedTim = 'all'; timFilterName = 'Semua Tim Kerja'; openTimDropdown = false; fetchData();"
                        class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                        :class="selectedTim === 'all' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                        <span>Semua Tim Kerja</span>
                        <span x-show="selectedTim === 'all'" class="text-[#6E5BC3]">✓</span>
                    </button>
                    @foreach($daftarTim as $t)
                        <button type="button" 
                            @click="selectedTim = '{{ $t->id_tim ?? $t->id }}'; timFilterName = '{{ $t->nama_tim }}'; openTimDropdown = false; fetchData();"
                            class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs transition-all cursor-pointer flex items-center justify-between"
                            :class="selectedTim === '{{ $t->id_tim ?? $t->id }}' ? 'bg-[#F8F7FF] text-[#6E5BC3] font-light' : 'text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-light'">
                            <span class="truncate">{{ $t->nama_tim }}</span>
                            <span x-show="selectedTim === '{{ $t->id_tim ?? $t->id }}'" class="text-[#6E5BC3]">✓</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 2. Filter Periode (Bulan & Tahun, Berjejer ke bawah pada layar diperkecil persis Daftar Proyek) --}}
            <div class="relative w-full lg:w-auto shrink-0">
                <button @click="bulanOpen = !bulanOpen; openTimDropdown = false;" @click.outside="bulanOpen = false" type="button" 
                    class="flex items-center justify-between gap-3 px-4 py-2 bg-white border text-[#604EE6] rounded-full text-xs font-light transition-all cursor-pointer w-full lg:w-auto shadow-2xs focus:outline-none"
                    :class="bulanOpen ? 'border-[#604EE6] ring-2 ring-purple-100 bg-white' : 'border-purple-200 hover:border-purple-300 hover:bg-[#F8F7FF]'">
                    <div class="flex items-center gap-1.5 truncate">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#604EE6] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate" x-text="getFilterLabel()"></span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#604EE6] transition-transform duration-200 shrink-0" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="bulanOpen" x-cloak class="absolute left-0 lg:left-auto lg:right-0 mt-2 w-full lg:w-80 bg-white border border-purple-100 rounded-3xl shadow-xl p-4 z-50 space-y-4">
                    <div class="flex flex-col gap-1.5">
                        <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                        <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1 custom-scrollbar">
                            @php
                                $maxTahun = max((int)date('Y'), 2027);
                            @endphp
                            @for($i = $maxTahun; $i >= 1990; $i--)
                                <button @click="selectedYear = '{{ $i }}'; fetchData();" 
                                    :class="selectedYear === '{{ $i }}' ? 'bg-[#6E5BC3] text-white' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                    class="py-1.5 px-1 rounded-xl text-[11px] font-light transition-all cursor-pointer text-center">
                                    {{ $i }}
                                </button>
                            @endfor
                        </div>
                    </div>

                    <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest border-t border-gray-100 pt-3">PILIH BULAN</div>
                    <div>
                        <button @click="selectedMonth = 'all'; fetchData();" 
                            :class="selectedMonth === 'all' ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                            class="w-full py-2 px-4 rounded-xl text-xs font-light transition-all cursor-pointer text-center mb-2">
                            Semua Bulan
                        </button>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-3 gap-2">
                        <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                            <button @click="selectedMonth = (index + 1).toString(); fetchData();" 
                                :class="selectedMonth === (index + 1).toString() ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                class="py-2.5 px-2 rounded-xl text-xs font-light transition-all cursor-pointer text-center"
                                x-text="namaBulan">
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Highlight Banner Rangkuman Beban Kerja --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 sm:gap-4">
        <div class="bg-linear-to-r from-[#6E5BC3] to-[#8470E5] rounded-[22px] sm:rounded-3xl p-4 text-white flex flex-col justify-between shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-medium text-purple-100 uppercase tracking-wider">Beban Tertinggi</span>
            <span class="text-base sm:text-lg font-bold mt-1 truncate" :title="summary.topPerson" x-text="summary.topPerson"></span>
            <span class="text-[10px] sm:text-[11px] text-purple-100 mt-0.5">Personil dengan penugasan terbanyak</span>
        </div>

        <div class="bg-[#F8F7FF] border border-purple-100 rounded-[22px] sm:rounded-3xl p-4 flex flex-col justify-between shadow-xs">
            <span class="text-[10px] sm:text-[11px] font-medium text-gray-500 uppercase tracking-wider">Total Personil Terdaftar</span>
            <span class="text-base sm:text-lg font-bold text-gray-800 mt-1" x-text="summary.totalAnggota + ' Pegawai'"></span>
            <span class="text-[10px] sm:text-[11px] text-gray-400 mt-0.5" x-text="summary.totalProyekAktif + ' Proyek aktif pada periode ini'"></span>
        </div>
    </div>

    {{-- Area Diagram Batang Horizontal (Horizontal Stacked Bar) --}}
    <div class="relative w-full mt-1 sm:mt-2" :style="{ minHeight: chartHeight + 'px', height: chartHeight + 'px' }">
        <div x-show="hasValidData" class="w-full h-full relative" :style="{ minHeight: chartHeight + 'px', height: chartHeight + 'px' }">
            <canvas id="bebanKerjaChart" class="w-full h-full"></canvas>
        </div>

        {{-- Empty State --}}
        <div x-show="!hasValidData" x-cloak class="w-full h-72 flex flex-col items-center justify-center bg-gray-50/50 rounded-3xl border border-dashed border-purple-200 p-6 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <h4 class="text-xs font-bold text-gray-700">Belum Ada Catatan Beban Kerja Personil</h4>
            <p class="text-[11px] text-gray-400 mt-0.5">Tidak ditemukan data penugasan proyek untuk tim dan periode yang dipilih.</p>
        </div>
    </div>
</div>

<script>
    function bebanKerjaChartComponent() {
        return {
            selectedTim: 'all',
            timFilterName: 'Semua Tim Kerja',
            selectedYear: '{{ date('Y') }}',
            selectedMonth: 'all',
            openTimDropdown: false,
            bulanOpen: false,

            labels: {!! json_encode($bebanKerjaInitial['labels'] ?? []) !!},
            datasets: {!! json_encode($bebanKerjaInitial['datasets'] ?? []) !!},
            summary: {!! json_encode($bebanKerjaInitial['summary'] ?? ['topPerson' => '-', 'avgWorkload' => 0, 'totalAnggota' => 0, 'totalProyekAktif' => 0]) !!},

            get hasValidData() {
                return this.labels.length > 0 && this.datasets.total && this.datasets.total.some(val => Number(val) > 0);
            },

            get chartHeight() {
                let count = this.labels.length;
                if (count <= 0) return 280;
                // Memberikan tinggi yang cukup per personil (sekitar 40px per baris + padding)
                return Math.max(280, count * 40 + 80);
            },

            getFilterLabel() {
                let mNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                let mText = this.selectedMonth === 'all' ? 'Semua Bulan' : mNames[this.selectedMonth - 1];
                let yText = this.selectedYear === 'all' ? '' : ' ' + this.selectedYear;
                return mText + yText;
            },

            fetchData() {
                fetch(`{{ route('direktur.chart.bebankerja') }}?id_tim=${this.selectedTim}&tahun=${this.selectedYear}&bulan=${this.selectedMonth}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    this.labels = data.labels || [];
                    this.datasets = data.datasets || {};
                    this.summary = data.summary || { topPerson: '-', avgWorkload: 0, totalAnggota: 0, totalProyekAktif: 0 };
                    this.bulanOpen = false;
                    this.openTimDropdown = false;
                    this.updateChart();
                })
                .catch(err => {
                    console.error("Gagal memuat data beban kerja:", err);
                });
            },

            updateChart() {
                if (typeof Chart === 'undefined') {
                    setTimeout(() => this.updateChart(), 100);
                    return;
                }

                this.$nextTick(() => {
                    const canvasEl = document.getElementById('bebanKerjaChart');
                    if (!canvasEl) return;

                    let existingChart = Chart.getChart(canvasEl);

                    if (!this.hasValidData) {
                        if (existingChart) {
                            existingChart.destroy();
                        }
                        return;
                    }

                    // Jika chart sudah ada, update data secara in-place agar transisi mulus dan canvas tidak berkedip/hilang
                    if (existingChart) {
                        existingChart.data.labels = this.labels;
                        existingChart.data.datasets[0].data = this.datasets.berjalan || [];
                        existingChart.data.datasets[1].data = this.datasets.selesai || [];
                        existingChart.data.datasets[2].data = this.datasets.belum_dimulai || [];
                        existingChart.data.datasets[3].data = this.datasets.terlambat || [];
                        existingChart.resize();
                        existingChart.update();
                        return;
                    }

                    // Inisialisasi Chart baru pertama kali
                    const ctx = canvasEl.getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.labels,
                            datasets: [
                                {
                                    label: 'Sedang Berjalan',
                                    data: this.datasets.berjalan || [],
                                    backgroundColor: '#6E5BC3',
                                    hoverBackgroundColor: '#5B46B5',
                                    borderRadius: 6,
                                    borderSkipped: 'start',
                                    barThickness: 18,
                                    maxBarThickness: 24,
                                },
                                {
                                    label: 'Selesai',
                                    data: this.datasets.selesai || [],
                                    backgroundColor: '#A855F7',
                                    hoverBackgroundColor: '#9333EA',
                                    borderRadius: 6,
                                    borderSkipped: 'start',
                                    barThickness: 18,
                                    maxBarThickness: 24,
                                },
                                {
                                    label: 'Belum Dimulai',
                                    data: this.datasets.belum_dimulai || [],
                                    backgroundColor: '#F472B6',
                                    hoverBackgroundColor: '#EC4899',
                                    borderRadius: 6,
                                    borderSkipped: 'start',
                                    barThickness: 18,
                                    maxBarThickness: 24,
                                },
                                {
                                    label: 'Terlambat',
                                    data: this.datasets.terlambat || [],
                                    backgroundColor: '#9D174D',
                                    hoverBackgroundColor: '#831843',
                                    borderRadius: 6,
                                    borderSkipped: 'start',
                                    barThickness: 18,
                                    maxBarThickness: 24,
                                }
                            ]
                        },
                        options: {
                            indexAxis: 'y', // Diagram Batang Horizontal
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    top: 10,
                                    bottom: 10,
                                    left: 5,
                                    right: 15
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        boxWidth: 12,
                                        boxHeight: 12,
                                        useBorderRadius: true,
                                        borderRadius: 3,
                                        font: { size: 11, family: 'sans-serif' },
                                        color: '#6B7280',
                                        padding: 15
                                    }
                                },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 11 },
                                    padding: 10,
                                    cornerRadius: 10,
                                    callbacks: {
                                        title: function(context) {
                                            return 'Pegawai: ' + context[0].label;
                                        },
                                        label: function(context) {
                                            return ` ${context.dataset.label}: ${context.raw} Proyek`;
                                        },
                                        footer: function(tooltipItems) {
                                            let total = 0;
                                            tooltipItems.forEach(item => { total += Number(item.raw) || 0; });
                                            return ' Total Beban: ' + total + ' Proyek';
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    beginAtZero: true,
                                    grid: { 
                                        color: '#F3F4F6', 
                                        borderDash: [5, 5],
                                        drawBorder: false
                                    },
                                    ticks: {
                                        precision: 0,
                                        stepSize: 1,
                                        callback: function(value) { return value + ' Proyek'; },
                                        font: { size: 10, family: 'sans-serif' },
                                        color: '#9CA3AF'
                                    }
                                },
                                y: {
                                    stacked: true,
                                    grid: { display: false },
                                    ticks: { 
                                        font: { size: 11, weight: '600' },
                                        color: '#374151',
                                        padding: 8
                                    }
                                }
                            }
                        }
                    });
                });
            },

            init() {
                this.updateChart();
            }
        }
    }
</script>

