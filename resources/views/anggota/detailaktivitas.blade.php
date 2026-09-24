<div x-data="{ 
        open: false, 
        nama: '', 
        pj: '', 
        pm: '', 
        progress: 0, 
        status: '', 
        tglMulai: '', 
        tglSelesai: '',
        kendalaInternal: [],
        kendalaEksternal: [],
        dokumen: [],
        riwayatProgress: []
    }" 
    @open-modal-detail-aktivitas.window="
        open = true;
        nama = $event.detail.nama;
        pj = $event.detail.pj;
        pm = $event.detail.pm;
        progress = $event.detail.progress;
        status = $event.detail.status;
        tglMulai = $event.detail.tglMulai;
        tglSelesai = $event.detail.tglSelesai;
        kendalaInternal = $event.detail.kendalaInternal || [];
        kendalaEksternal = $event.detail.kendalaEksternal || [];
        dokumen = $event.detail.dokumen || [];
        riwayatProgress = $event.detail.riwayatProgress || [];
    " 
    @close-modal-detail-aktivitas.window="open = false"
    x-show="open" 
    x-cloak
    class="fixed inset-0 z-999 overflow-y-auto" 
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    
    {{-- Backdrop Blur --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl sm:rounded-[28px] bg-white text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100 flex flex-col max-h-[90vh]">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-8 sm:py-6 border-b border-gray-100 bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-xs shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] sm:text-[10px] font-normal px-2 py-0.5 rounded-full bg-purple-50 text-[#6E5BC3] uppercase tracking-wider">Detail Aktivitas</span>
                        <h3 class="text-sm sm:text-base font-normal text-gray-900 tracking-tight mt-0.5 truncate" x-text="nama"></h3>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Konten Modal (Bisa di-scroll) --}}
            <div class="p-4 sm:p-8 space-y-4 sm:space-y-6 overflow-y-auto custom-scrollbar">
                
                {{-- Informasi Utama --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/60 p-4 rounded-2xl border border-purple-100/60">
                    <div>
                        <span class="text-[11px] font-light text-gray-700 block mb-1">Penanggung Jawab</span>
                        <template x-if="pj && pj !== 'Belum Ditunjuk'">
                            <span class="text-xs font-light text-gray-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="font-light text-gray-700" x-text="pj"></span>
                            </span>
                        </template>
                        <template x-if="!pj || pj === 'Belum Ditunjuk'">
                            <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Belum Ditunjuk</span>
                            </span>
                        </template>
                    </div>
                    <div>
                        <span class="text-[11px] font-light text-gray-700 block mb-1">Ketua Proyek</span>
                        <template x-if="pm && pm !== 'Belum Ditunjuk'">
                            <span class="text-xs font-light text-gray-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="font-light text-gray-700" x-text="pm"></span>
                            </span>
                        </template>
                        <template x-if="!pm || pm === 'Belum Ditunjuk'">
                            <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Belum Ditunjuk</span>
                            </span>
                        </template>
                    </div>
                    <div class="sm:col-span-2 pt-2 border-t border-purple-100/60 flex items-center justify-between text-xs">
                        <span class="text-gray-700 flex items-center gap-1.5 font-light">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Rentang Waktu:
                        </span>
                        <template x-if="tglMulai && tglSelesai && tglMulai !== 'Belum diatur' && tglSelesai !== 'Belum diatur'">
                            <span class="font-light text-gray-700"><span x-text="tglMulai"></span> — <span x-text="tglSelesai"></span></span>
                        </template>
                        <template x-if="!tglMulai || !tglSelesai || tglMulai === 'Belum diatur' || tglSelesai === 'Belum diatur'">
                            <span class="inline-flex items-center gap-1.5 text-amber-600 font-light text-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shrink-0"></span>
                                <span>Belum diatur</span>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-light text-gray-500">Persentase Progress</span>
                        <span class="font-normal text-[#604EE6]" x-text="(progress || 0) + '%'"></span>
                    </div>
                    <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                        <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" :style="`width: ${Math.min(100, Math.max(0, parseFloat(progress) || 0))}%`"></div>
                    </div>
                </div>

                {{-- Riwayat Pelaporan Progress (Format Tabel di dalam Container) --}}
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-light text-gray-700">Riwayat Pelaporan Progress</span>
                        <span class="text-[10px] font-normal px-2 py-0.5 rounded-full bg-purple-50 text-[#6E5BC3]" x-text="riwayatProgress.length + ' Laporan'"></span>
                    </div>

                    <div class="bg-white border border-purple-100/80 rounded-2xl overflow-hidden shadow-2xs">
                        <template x-if="riwayatProgress.length > 0">
                            <div class="overflow-x-auto max-h-64 custom-scrollbar">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50/90 border-b border-gray-100 text-[10px] uppercase font-light text-gray-500 tracking-wider sticky top-0 z-10 backdrop-blur-xs">
                                        <tr>
                                            <th class="py-2.5 px-4 whitespace-nowrap font-light">Waktu Pelaporan</th>
                                            <th class="py-2.5 px-4 text-right whitespace-nowrap font-light">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-xs">
                                        <template x-for="(rp, idx) in riwayatProgress" :key="rp.id || idx">
                                            <tr class="hover:bg-purple-50/20 transition-colors align-middle">
                                                {{-- Tanggal & Waktu Pelaporan --}}
                                                <td class="py-2.5 px-4 whitespace-nowrap align-middle">
                                                    <div class="flex items-center gap-2 text-gray-800 font-light text-xs">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span x-text="rp.tanggal_lengkap || rp.tanggal"></span>
                                                    </div>
                                                </td>

                                                {{-- Progress --}}
                                                <td class="py-2.5 px-4 text-right whitespace-nowrap align-middle">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-[#2EBD85] border border-emerald-100 text-xs font-light" x-text="rp.progress + '%'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                        <template x-if="riwayatProgress.length === 0">
                            <div class="flex flex-col items-center justify-center py-7 px-4 gap-2 text-center bg-gray-50/40">
                                <div class="w-9 h-9 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shadow-2xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500 font-light">Belum ada riwayat pelaporan progress untuk aktivitas ini.</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Daftar Kendala --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-amber-50/40 border border-amber-100 rounded-2xl p-4 space-y-2">
                        <span class="text-xs font-light text-amber-800 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Kendala Internal
                        </span>
                        <template x-if="kendalaInternal.length > 0">
                            <ul class="list-disc list-inside text-xs text-gray-600 space-y-1 font-light">
                                <template x-for="item in kendalaInternal">
                                    <li x-text="item"></li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="kendalaInternal.length === 0">
                            <p class="text-[11px] text-gray-500 font-light">Tidak ada kendala internal.</p>
                        </template>
                    </div>

                    <div class="bg-rose-50/40 border border-rose-100 rounded-2xl p-4 space-y-2">
                        <span class="text-xs font-light text-rose-800 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kendala Eksternal
                        </span>
                        <template x-if="kendalaEksternal.length > 0">
                            <ul class="list-disc list-inside text-xs text-gray-600 space-y-1 font-light">
                                <template x-for="item in kendalaEksternal">
                                    <li x-text="item"></li>
                                </template>
                            </ul>
                        </template>
                        <template x-if="kendalaEksternal.length === 0">
                            <p class="text-[11px] text-gray-500 font-light">Tidak ada kendala eksternal.</p>
                        </template>
                    </div>
                </div>

                {{-- Daftar Dokumen Pendukung (Teks Lihat Simpel) --}}
                <div class="space-y-2">
                    <span class="text-xs font-light text-gray-700 block">Daftar Dokumen Pendukung</span>
                    <div class="bg-gray-50/50 border border-purple-100/60 rounded-2xl p-4 max-h-48 overflow-y-auto custom-scrollbar">
                        <template x-if="dokumen.length > 0">
                            <div class="space-y-2">
                                <template x-for="doc in dokumen">
                                    <div class="flex items-center justify-between p-3 bg-white hover:bg-purple-50/40 border border-gray-200 hover:border-purple-200 rounded-xl transition-all text-xs group/doc">
                                        <div class="flex items-center gap-2.5 truncate">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                            <span class="font-light text-gray-700 truncate" x-text="typeof doc === 'object' ? (doc.nama_dokumen || doc.nama) : doc"></span>
                                        </div>
                                        <button type="button" 
                                                @click="
                                                    let fileUrl = typeof doc === 'object' ? (doc.url || ('{{ asset('storage') }}/' + doc.file_path)) : doc;
                                                    window.open(fileUrl, '_blank');
                                                " 
                                                class="text-xs font-light text-[#6E5BC3] hover:text-[#5C4AB5] hover:underline cursor-pointer transition-all shrink-0">
                                            Lihat
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="dokumen.length === 0">
                            <div class="flex flex-col items-center justify-center py-6 gap-2 text-center">
                                <div class="w-10 h-10 rounded-full bg-purple-50 border border-purple-100 flex items-center justify-center text-[#6E5BC3] shadow-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-500 font-light">Belum ada dokumen pendukung yang dilampirkan.</p>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            {{-- Footer Modal --}}
            <div class="px-4 py-3 sm:px-8 sm:py-4 border-t border-gray-100 flex items-center justify-end bg-gray-50/50">
                <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-light bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white transition-all cursor-pointer shadow-sm shadow-[#6E5BC3]/20">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>