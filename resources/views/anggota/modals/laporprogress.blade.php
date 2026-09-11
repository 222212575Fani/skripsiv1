<div x-data="{ 
    open: false, 
    id: '', 
    nama: '', 
    progressSebelumnya: 0,
    progressTambahan: 0,
    files: [],
    get totalProgress() {
        let total = parseFloat(this.progressSebelumnya) + parseFloat(this.progressTambahan || 0);
        return total > 100 ? 100 : (total < 0 ? 0 : total.toFixed(2));
    },
    handleFileChange(event) {
        const uploadedFiles = Array.from(event.target.files);
        uploadedFiles.forEach(file => {
            this.files.push({
                file: file,
                name: file.name,
                size: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                type: file.name.split('.').pop().toLowerCase()
            });
        });
        event.target.value = '';
    },
    removeFile(index) {
        this.files.splice(index, 1);
    }
}"
    @open-modal-lapor-progress.window="open = true; id = $event.detail.id; nama = $event.detail.nama; progressSebelumnya = parseFloat($event.detail.progress) || 0; progressTambahan = 0; files = []"
    @close-modal-lapor-progress.window="open = false"
    x-show="open" x-cloak class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">

    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px]" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" class="relative w-full max-w-xl rounded-[28px] bg-white text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 flex items-center justify-center text-[#6E5BC3]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Laporkan Progress Aktivitas</h3>
                        <p class="text-[11px] font-normal text-gray-400 truncate max-w-xs" x-text="nama"></p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full transition-all cursor-pointer">✕</button>
            </div>

            <form :action="'{{ url('anggota/aktivitas') }}/' + id + '/progress'" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-7 space-y-4 max-h-[70vh] overflow-y-auto">
                    
                    {{-- INPUT PROGRESS DENGAN DESAIN INFORMASI TAMBAHAN --}}
                    <div class="bg-purple-50/40 border border-purple-100 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Progress Sebelumnya:</span>
                            <span class="font-bold text-[#6E5BC3]" x-text="progressBeforeFormatted = progressSebelumnya + '%'"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Tambahan Progress Baru (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="progress_minggu_berjalan_tambahan" x-model.number="progressTambahan" min="0" :max="100 - progressSebelumnya" step="0.01" required
                                placeholder="Contoh: 30"
                                class="w-full px-4 py-2.5 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-bold text-gray-800">
                        </div>

                        {{-- Input tersembunyi yang membawa nilai total final untuk dikirim ke Controller --}}
                        <input type="hidden" name="progress_minggu_berjalan" :value="totalProgress">

                        <div class="flex items-center justify-between pt-2 border-t border-purple-100 text-xs">
                            <span class="font-bold text-gray-700">Total Progress Menjadi:</span>
                            <span class="px-2.5 py-1 rounded-lg bg-[#6E5BC3] text-white font-extrabold text-xs shadow-xs" x-text="totalProgress + '%'"></span>
                        </div>
                    </div>

                    {{-- Uraian Progress --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Uraian Progress</label>
                        <textarea name="uraian_progress" rows="3" placeholder="Jelaskan pekerjaan yang telah diselesaikan pada periode ini..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                    </div>

                    {{-- Kendala Internal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Kendala Internal</label>
                        <textarea name="kendala_internal" rows="2" placeholder="Hambatan dari dalam tim..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                    </div>

                    {{-- Kendala Eksternal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Kendala Eksternal</label>
                        <textarea name="kendala_eksternal" rows="2" placeholder="Hambatan dari luar tim/pihak lain..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                    </div>

                    {{-- Area Upload Multi-File --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Dokumen Pendukung (Bisa Banyak File)</label>
                        
                        <div class="relative border-2 border-dashed border-purple-200 hover:border-[#6E5BC3] rounded-2xl p-6 bg-purple-50/20 hover:bg-purple-50/50 transition-all text-center cursor-pointer group">
                            <input type="file" name="dokumen_pendukung[]" multiple @change="handleFileChange($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="px-4 py-2 bg-white border border-purple-100 rounded-xl shadow-xs text-[#6E5BC3] font-bold text-xs flex items-center gap-2 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload
                                </div>
                                <div class="text-xs text-gray-600 font-medium">
                                    Choose a file or drag & drop it here
                                </div>
                                <p class="text-[10px] text-gray-400">Maximum 5.0 MB file size per file</p>
                            </div>
                        </div>

                        {{-- Daftar List File yang Dipilih --}}
                        <div class="mt-3 space-y-2">
                            <template x-for="(fileItem, index) in files" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded-2xl">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-[#6E5BC3] flex items-center justify-center font-bold text-[10px] uppercase shrink-0 shadow-xs" x-text="fileItem.type"></div>
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-gray-800 truncate" x-text="fileItem.name"></p>
                                            <p class="text-[10px] text-gray-400" x-text="fileItem.size + ' • Ready to upload'"></p>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="p-2 text-gray-400 hover:text-rose-500 rounded-xl hover:bg-white transition-all cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                <div class="px-7 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/50">
                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-gray-100 hover:bg-gray-200 text-gray-600 transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white transition-all cursor-pointer shadow-sm shadow-[#6E5BC3]/30">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>