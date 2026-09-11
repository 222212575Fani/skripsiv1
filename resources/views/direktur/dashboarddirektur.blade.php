<x-layoututama title="Dashboard Monitoring Direktur">
    <div class="flex flex-col gap-6" x-data="{ 
        initChart() {
            const ctx = document.getElementById('progressTimChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($namaTim ?? []) !!},
                    datasets: [{
                        label: 'Rerata Progress (%)',
                        data: {!! json_encode($rerataProgressTim ?? []) !!},
                        backgroundColor: '#6E5BC3',
                        borderRadius: 12,
                        borderSkipped: false,
                        barThickness: 36,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
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
                            grid: { color: '#F3F4F6', borderDash: [4, 4] },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '500' } }
                        }
                    }
                }
            });
        }
    }" x-init="initChart()">

        {{-- 1. HEADER CONTAINER UNGU ESTETIK --}}
        <div class="bg-gradient-to-r from-[#6E5BC3] to-[#8470E5] rounded-[28px] shadow-sm p-6 text-white">
            <h2 class="text-base font-bold">Halo, {{ auth()->user()->nama ?? 'Direktur' }}! 👋</h2>
            <p class="text-xs text-purple-100 mt-1">Selamat Datang di Dashboard Monitoring Direktorat Sistem Informasi Statistik</p>
        </div>

        {{-- 2. CARD STATISTIK PROYEK DALAM DIREKTORAT SIS (GLOBAL) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <x-cardstatistikdashboard 
                title="Total Proyek" 
                value="{{ $statsDirektorat['total'] ?? 0 }}" 
                subtitle="Seluruh Direktorat" 
                color="text-indigo-600" 
                bg="bg-indigo-50"
                svgPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" 
            />

            <x-cardstatistikdashboard 
                title="Belum Dimulai" 
                value="{{ $statsDirektorat['belum_dimulai'] ?? 0 }}" 
                subtitle="Menunggu Jadwal" 
                color="text-amber-600" 
                bg="bg-amber-50"
                svgPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            <x-cardstatistikdashboard 
                title="Berjalan" 
                value="{{ $statsDirektorat['berjalan'] ?? 0 }}" 
                subtitle="Aktif Dikerjakan" 
                color="text-blue-600" 
                bg="bg-blue-50"
                svgPath="M13 10V3L4 14h7v7l9-11h-7z" 
            />

            <x-cardstatistikdashboard 
                title="Selesai" 
                value="{{ $statsDirektorat['selesai'] ?? 0 }}" 
                subtitle="Tuntas Dikerjakan" 
                color="text-emerald-600" 
                bg="bg-emerald-50"
                svgPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
            />

            <x-cardstatistikdashboard 
                title="Terlambat" 
                value="{{ $statsDirektorat['terlambat'] ?? 0 }}" 
                subtitle="Melebihi Deadline" 
                color="text-rose-600" 
                bg="bg-rose-50"
                svgPath="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" 
            />

        </div>

        {{-- 3. GRAFIK RERATA PROGRESS PER TIM KERJA --}}
        <div class="bg-white rounded-[28px] shadow-sm border border-gray-100 p-6 flex flex-col gap-6">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Rerata Progress per Tim Kerja</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Statistik tren pencapaian rata-rata progress proyek antar tim kerja.</p>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold border border-emerald-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span>Performa Aktif</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-gray-50/60 border border-gray-100 flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-gray-400">Total Tim Kerja</span>
                    <span class="text-base font-bold text-gray-900">{{ count($daftarTim ?? []) }} Tim</span>
                    <span class="text-[10px] text-gray-400">Aktif dalam Direktorat</span>
                </div>
                <div class="p-4 rounded-2xl bg-gray-50/60 border border-gray-100 flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-gray-400">Rerata Keseluruhan</span>
                    <span class="text-base font-bold text-[#6E5BC3]">
                        {{ count($rerataProgressTim) > 0 ? number_format(array_sum($rerataProgressTim) / count($rerataProgressTim), 1) : 0 }}%
                    </span>
                    <span class="text-[10px] text-emerald-600 font-semibold">↑ Kinerja stabil</span>
                </div>
                <div class="p-4 rounded-2xl bg-gray-50/60 border border-gray-100 flex flex-col gap-1">
                    <span class="text-[11px] font-medium text-gray-400">Tim Puncak Performa</span>
                    <span class="text-base font-bold text-gray-900 truncate">
                        @php
                            if(!empty($daftarTim)) {
                                $topTim = collect($daftarTim)->sortByDesc('rerata_progress')->first();
                                echo $topTim->nama_tim ?? '-';
                            } else {
                                echo '-';
                            }
                        @endphp
                    </span>
                    <span class="text-[10px] text-gray-400">Pencapaian tertinggi</span>
                </div>
            </div>

            <div class="relative h-80 w-full mt-2">
                <canvas id="progressTimChart"></canvas>
            </div>
        </div>

        {{-- 4. CARD MASING-MASING TIM KERJA --}}
        <div class="flex flex-col gap-4">
            <h3 class="text-sm font-bold text-gray-900 px-1">Daftar Tim Kerja</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($daftarTim as $tim)
                    <div class="bg-white border border-purple-100 rounded-[28px] p-5 flex flex-col gap-4 shadow-xs hover:border-[#6E5BC3]/40 transition-all">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-purple-100/60">
                            <div>
                                <span class="text-[9px] font-bold text-[#6E5BC3] uppercase tracking-wider bg-purple-50 px-2 py-0.5 rounded-md">Tim Kerja</span>
                                <h4 class="text-xs font-bold text-gray-900 mt-1">{{ $tim->nama_tim }}</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-xl bg-purple-50 text-[#6E5BC3] text-[11px] font-extrabold">
                                {{ $tim->proyek_count ?? 0 }} Proyek
                            </span>
                        </div>

                        <div class="flex flex-col gap-2 text-xs">
                            <div class="flex items-center justify-between text-gray-500">
                                <span class="font-light">Ketua Tim:</span>
                                <span class="font-medium text-gray-800">{{ $tim->ketuaTim->nama_lengkap ?? ($tim->ketua->nama ?? 'Belum ditentukan') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-500">
                                <span class="font-light">Rerata Progress Tim:</span>
                                <span class="font-bold text-[#6E5BC3]">{{ number_format($tim->rerata_progress ?? 0, 1) }}%</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#2EBD85] h-2 rounded-full transition-all duration-300" style="width: {{ $tim->rerata_progress ?? 0 }}%;"></div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-purple-100/60 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400 font-light">Aktif: {{ $tim->proyek_berjalan_count ?? 0 }} Proyek Berjalan</span>
                            <a href="#" class="px-3 py-1.5 rounded-xl bg-purple-50 text-[#6E5BC3] hover:bg-[#6E5BC3] hover:text-white transition-all text-xs font-semibold cursor-pointer">
                                Lihat Detail
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center bg-white rounded-[28px] border border-dashed border-purple-200">
                        <p class="text-xs text-gray-400 italic">Belum ada data tim kerja yang tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-layoututama>