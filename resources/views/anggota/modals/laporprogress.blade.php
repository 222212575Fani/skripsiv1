<div x-data="{ 
    open: false, 
    id: '', 
    nama: '', 
    progressSebelumnya: 0,
    progressTambahan: 0,
    files: [],
    isUploading: false,
    uploadProgress: 0,
    
    get totalProgress() {
        let total = parseFloat(this.progressSebelumnya) + parseFloat(this.progressTambahan || 0);
        return total > 100 ? 100 : (total < 0 ? 0 : total.toFixed(2));
    },

    handleFileChange(event) {
        const input = event.target;
        const uploadedFiles = Array.from(input.files);
        
        uploadedFiles.forEach(file => {
            let fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            let fileObj = {
                file: file,
                name: file.name,
                sizeText: fileSizeMB + ' MB / ' + fileSizeMB + ' MB',
                type: file.name.split('.').pop().toLowerCase(),
                progress: 0,
                status: 'uploading'
            };
            
            this.files.push(fileObj);
            
            let index = this.files.length - 1;
            let interval = setInterval(() => {
                if (this.files[index].progress < 100) {
                    this.files[index].progress += 20;
                } else {
                    clearInterval(interval);
                    this.files[index].status = 'completed';
                }
            }, 150);
        });

        this.updateInputFiles(input);
    },

    removeFile(index) {
        this.files.splice(index, 1);
        const input = document.getElementById('dokumen_input');
        this.updateInputFiles(input);
    },

    updateInputFiles(inputElement) {
        if (!inputElement) return;
        let dataTransfer = new DataTransfer();
        this.files.forEach(item => {
            dataTransfer.items.add(item.file);
        });
        inputElement.files = dataTransfer.files;
    }
}"
    @open-modal-lapor-progress.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        progressSebelumnya = parseFloat($event.detail.progress) || 0; 
        progressTambahan = 0; 
        files = []; 
        let docInput = document.getElementById('dokumen_input');
        if(docInput) docInput.value = '';
    "
    @close-modal-lapor-progress.window="open = false"
    x-show="open" x-cloak class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">

    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px]" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4">
        <div @click.away="open = false" class="relative w-full max-w-xl rounded-xl sm:rounded-2xl bg-white text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-7 sm:py-5 border-b border-gray-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-purple-50 flex items-center justify-center text-[#6E5BC3] shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 truncate">Laporkan Progress Aktivitas</h3>
                        <p class="text-[10px] sm:text-[11px] font-normal text-gray-400 truncate max-w-xs" x-text="nama"></p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-1.5 sm:p-2 text-gray-300 hover:text-gray-500 rounded-full transition-all cursor-pointer shrink-0">✕</button>
            </div>

            <form :action="'{{ url('anggota/aktivitas') }}/' + id + '/progress'" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="custom-scrollbar p-4 sm:p-7 space-y-4 max-h-[70vh] overflow-y-auto">
                    
                    {{-- INPUT PROGRESS --}}
                    <div class="bg-purple-50/40 border border-purple-100 rounded-2xl p-4 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Progress Sebelumnya:</span>
                            <span class="font-bold text-[#6E5BC3]" x-text="progressSebelumnya + '%'"></span>
                        </div>

                        <div>
                            <label class="block text-xs font-normal text-gray-700 mb-1.5">Tambahan Progress Baru (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="progress_minggu_berjalan_tambahan" x-model.number="progressTambahan" min="0" :max="100 - progressSebelumnya" step="0.01" required
                                placeholder="Contoh: 30"
                                class="w-full px-4 py-2.5 bg-white border border-purple-200 rounded-xl focus:ring-2 focus:ring-[#6E5BC3]/20 focus:border-[#6E5BC3] outline-none text-xs font-light text-gray-800 placeholder:text-gray-400 placeholder:font-light">
                        </div>

                        <input type="hidden" name="progress_minggu_berjalan" :value="totalProgress">

                        <div class="flex items-center justify-between pt-2 border-t border-purple-100 text-xs">
                            <span class="font-bold text-gray-700">Total Progress Menjadi:</span>
                            <span class="px-2.5 py-1 rounded-lg bg-[#6E5BC3] text-white font-extrabold text-xs shadow-xs" x-text="totalProgress + '%'"></span>
                        </div>
                    </div>

                    {{-- Uraian Progress --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Uraian Progress</label>
                        <textarea name="uraian_progress" rows="3" maxlength="2000" placeholder="Jelaskan pekerjaan yang telah diselesaikan pada periode ini..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>

                    {{-- Kendala Internal & Eksternal --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Kendala Internal</label>
                        <textarea name="kendala_internal" rows="2" maxlength="2000" placeholder="Hambatan dari dalam tim..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Kendala Eksternal</label>
                        <textarea name="kendala_eksternal" rows="2" maxlength="2000" placeholder="Hambatan dari luar tim/pihak lain..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] outline-none text-xs font-light text-gray-700 placeholder:text-gray-400 placeholder:font-light resize-none"></textarea>
                    </div>

                    {{-- Area Upload Multi-File dengan Progress Bar & Ikon Sampah --}}
                    <div>
                        <label class="block text-xs font-normal text-gray-700 mb-2">Dokumen Pendukung (Bisa Banyak File)</label>
                        
                        <div class="relative border-2 border-dashed border-purple-200 hover:border-[#6E5BC3] rounded-2xl p-6 bg-purple-50/20 hover:bg-purple-50/50 transition-all text-center cursor-pointer group">
                            <input type="file" id="dokumen_input" name="dokumen_pendukung[]" multiple accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx" @change="handleFileChange($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="px-4 py-2 bg-white border border-purple-100 rounded-xl shadow-xs text-[#6E5BC3] font-bold text-xs flex items-center gap-2 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload
                                </div>
                                <div class="text-xs text-gray-600 font-medium">Pilih file atau seret & lepas di sini</div>
                                <p class="text-[10px] text-gray-400">Mendukung PDF, IMG (JPG, PNG), Dokumen (DOC, XLS) hingga 5.0 MB</p>
                            </div>
                        </div>

                        {{-- Daftar List File dengan Progress Bar, Status & Link Preview --}}
                        <div class="mt-3 space-y-3">
                            <template x-for="(fileItem, index) in files" :key="index">
                                <div class="p-3.5 bg-gray-50/80 border border-gray-100 rounded-2xl space-y-2">
                                    <div class="flex items-center justify-between">
                                        
                                        {{-- Bisa diklik untuk preview dokumen di tab baru --}}
                                        <a :href="URL.createObjectURL(fileItem.file)" target="_blank" 
                                           class="flex items-center gap-3 overflow-hidden group/link cursor-pointer text-left">
                                            <div class="w-10 h-10 rounded-xl bg-purple-100 text-[#6E5BC3] flex items-center justify-center font-bold text-[10px] uppercase shrink-0 shadow-xs" x-text="fileItem.type"></div>
                                            <div class="truncate">
                                                <p class="text-xs font-bold text-gray-800 truncate group-hover/link:text-[#6E5BC3] group-hover/link:underline transition-all" x-text="fileItem.name"></p>
                                                <p class="text-[10px] text-gray-400 font-medium" x-text="fileItem.sizeText + ' • ' + (fileItem.status === 'completed' ? '✅ Completed (Klik untuk buka)' : '⏳ Uploading...')"></p>
                                            </div>
                                        </a>
                                        
                                        {{-- Tombol Hapus (Ikon Tong Sampah jika Completed, Silang jika belum) --}}
                                        <button type="button" @click="removeFile(index)" class="p-2 text-gray-400 hover:text-rose-500 rounded-xl hover:bg-white transition-all cursor-pointer">
                                            <svg x-show="fileItem.status === 'completed'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <svg x-show="fileItem.status !== 'completed'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Progress Bar Animasi --}}
                                    <div class="w-full bg-purple-50 border border-purple-100 rounded-full h-2 overflow-hidden shadow-2xs">
                                        <div class="bg-[#604EE6] h-full rounded-full transition-all duration-300" :style="'width: ' + (fileItem.progress || 0) + '%'"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                <div class="px-4 py-3 sm:px-7 sm:py-4 border-t border-gray-100 flex justify-end gap-2.5 sm:gap-3 bg-gray-50/60 rounded-b-xl sm:rounded-b-2xl">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">Batal</x-button>
                    <x-button type="submit" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">Kirim Laporan</x-button>
                </div>
            </form>
        </div>
    </div>
</div>