<div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-6 flex flex-col gap-6"
    x-data="progressChartComponent()">

    {{-- Header Container Bawah: Judul di Kiri & Tombol Filter Gabungan di Kanan --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-900">Rerata Progress per Tim Kerja</h3>
            <p class="text-xs text-gray-400 mt-0.5">Statistik tren pencapaian rata-rata progress proyek bulanan dan tahunan.</p>
        </div>

        {{-- Filter Gabungan Bulan & Tahun --}}
        <div class="relative">
            <button @click="bulanOpen = !bulanOpen" @click.outside="bulanOpen = false" type="button" 
                class="flex items-center justify-between gap-3 px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] text-[#6E5BC3] rounded-2xl text-xs font-medium transition-all cursor-pointer min-w-[170px] shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span x-text="getFilterLabel()"></span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] transition-transform duration-200" :class="bulanOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            {{-- Panel Pop-up Pilihan Tahun & Bulan --}}
            <div x-show="bulanOpen" x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-purple-100 rounded-[28px] shadow-xl p-4 z-50 space-y-4">
                <div class="flex flex-col gap-1.5">
                    <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest">PILIH TAHUN</div>
                    <div class="grid grid-cols-5 gap-1.5 max-h-36 overflow-y-auto pr-1">
                        <button @click="selectedYear = 'all'; fetchData()" 
                            :class="selectedYear === 'all' ? 'bg-[#6E5BC3] text-white' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                            class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center col-span-5">
                            Semua Tahun
                        </button>
                        @for($i = date('Y'); $i >= 1990; $i--)
                            <button @click="selectedYear = '{{ $i }}'; fetchData()" 
                                :class="selectedYear === '{{ $i }}' ? 'bg-[#6E5BC3] text-white' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                                class="py-1.5 px-1 rounded-xl text-[11px] font-bold transition-all cursor-pointer text-center">
                                {{ $i }}
                            </button>
                        @endfor
                    </div>
                </div>

                <div class="text-[10px] font-bold text-[#6E5BC3] uppercase tracking-widest border-t border-gray-100 pt-3">PILIH BULAN</div>
                <div>
                    <button @click="selectedMonth = 'all'; fetchData()" 
                        :class="selectedMonth === 'all' ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                        class="w-full py-2 px-4 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center mb-2">
                        Semua Bulan
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <template x-for="(namaBulan, index) in ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']">
                        <button @click="selectedMonth = (index + 1).toString(); fetchData();" 
                            :class="selectedMonth === (index + 1).toString() ? 'bg-[#6E5BC3] text-white shadow-sm' : 'bg-purple-50/50 text-[#6E5BC3] hover:bg-purple-100'"
                            class="py-2.5 px-2 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-center"
                            x-text="namaBulan">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Card Utama: Tim dengan Progress Tertinggi --}}
    <div class="grid grid-cols-1 gap-4">
        <div class="bg-gradient-to-r from-[#6E5BC3] to-[#8470E5] rounded-[24px] p-5 text-white flex flex-col justify-between shadow-sm">
            <span class="text-[11px] font-medium text-purple-100 uppercase tracking-wider">Tim dengan Progress Tertinggi</span>
            
            <template x-if="hasValidData">
                <span class="text-lg font-bold mt-2 truncate" x-text="topTimText"></span>
            </template>
            <template x-if="!hasValidData">
                <span class="text-sm font-semibold mt-2">Tidak ada data untuk periode ini</span>
            </template>

            <span class="text-[11px] text-purple-100 mt-1">Performa tertinggi dari database saat ini</span>
        </div>
    </div>

    {{-- Area Diagram Batang / Chart.js dengan Kondisi Empty State --}}
    <div class="relative h-80 w-full mt-2">
        <div x-show="hasValidData" class="w-full h-full">
            <canvas id="revenueByMonthChart" class="w-full h-full"></canvas>
        </div>

        <div x-show="!hasValidData" x-cloak class="w-full h-full flex flex-col items-center justify-center bg-gray-50/50 rounded-[24px] border border-dashed border-purple-200 p-6 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-purple-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h4 class="text-xs font-bold text-gray-700">Belum Ada Data Grafik yang Ditampilkan</h4>
            <p class="text-[11px] text-gray-400 mt-0.5">Belum ada catatan progress atau data statistik tim untuk periode yang dipilih.</p>
        </div>
    </div>
</div>

{{-- Memuat Library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    function progressChartComponent() {
        return {
            selectedYear: 'all',
            selectedMonth: 'all',
            bulanOpen: false,
            chartInstance: null,
            labelsData: {!! json_encode($namaTim ?? []) !!},
            valuesData: {!! json_encode($rerataProgressTim ?? []) !!},
            rawTimData: {!! json_encode($daftarTim ?? []) !!},

            get hasValidData() {
                return this.valuesData.length > 0 && this.valuesData.some(val => Number(val) > 0);
            },

            get topTimText() {
                if (!this.rawTimData || this.rawTimData.length === 0) return '-';
                let sorted = [...this.rawTimData].sort((a, b) => b.rerata_progress - a.rerata_progress);
                let top = sorted[0];
                if (!top || top.rerata_progress <= 0) return '-';
                return `${top.nama_tim} (${Number(top.rerata_progress).toFixed(1)}%)`;
            },

            getFilterLabel() {
                let mNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                let mText = this.selectedMonth === 'all' ? 'Semua Bulan' : mNames[this.selectedMonth - 1];
                let yText = this.selectedYear === 'all' ? '' : ' ' + this.selectedYear;
                return mText + yText;
            },

            fetchData() {
                fetch(`{{ route('direktur.chart.data') }}?tahun=${this.selectedYear}&bulan=${this.selectedMonth}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    this.labelsData = data.namaTim;
                    this.valuesData = data.rerataProgressTim;
                    this.rawTimData = data.daftarTim;
                    this.bulanOpen = false;
                    this.updateChart();
                });
            },

            updateChart() {
                if (this.chartInstance) {
                    this.chartInstance.destroy();
                    this.chartInstance = null;
                }

                if (!this.hasValidData) return;

                setTimeout(() => {
                    const canvasEl = document.getElementById('revenueByMonthChart');
                    if (!canvasEl) return;
                    
                    const ctx = canvasEl.getContext('2d');

                    // Membuat efek gradient ungu yang estetik pada batang diagram
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, '#8470E5');
                    gradient.addColorStop(1, '#6E5BC3');

                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.labelsData,
                            datasets: [{
                                label: 'Rerata Progress',
                                data: this.valuesData,
                                backgroundColor: gradient,
                                hoverBackgroundColor: '#5C4AB5',
                                borderRadius: {
                                    topLeft: 12,
                                    topRight: 12,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                },
                                borderSkipped: false,
                                barThickness: 40,
                                maxBarThickness: 50,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#1F2937',
                                    titleFont: { size: 12, weight: 'bold' },
                                    bodyFont: { size: 11 },
                                    padding: 10,
                                    cornerRadius: 10,
                                    displayColors: false,
                                    callbacks: {
                                        title: function(context) {
                                            return 'Tim: ' + context[0].label;
                                        },
                                        label: function(context) {
                                            return ' Rerata Progress: ' + context.raw + '%';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    grid: { 
                                        color: '#F3F4F6', 
                                        borderDash: [5, 5],
                                        drawBorder: false
                                    },
                                    ticks: {
                                        callback: function(value) { return value + '%'; },
                                        font: { size: 11, family: 'sans-serif' },
                                        color: '#9CA3AF'
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { 
                                        font: { size: 11, weight: '600' },
                                        color: '#4B5563'
                                    }
                                }
                            }
                        }
                    });
                }, 50);
            },

            init() {
                this.updateChart();
            }
        }
    }
</script>