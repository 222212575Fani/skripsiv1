<div x-data="{ open: false, id: '', nama: '', progress: 0 }"
    @open-modal-lapor-progress.window="open = true; id = $event.detail.id; nama = $event.detail.nama; progress = $event.detail.progress"
    @close-modal-lapor-progress.window="open = false"
    x-show="open" x-cloak class="fixed inset-0 z-[999] overflow-y-auto" style="display: none;">

    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px]" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" class="relative w-full max-w-2xl rounded-[24px] bg-white text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden">
            
            <div class="flex items-center justify-between px-7 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Laporkan Progress Aktivitas</h3>
                    <p class="text-xs font-normal text-gray-400 mt-1" x-text="nama"></p>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full transition-all cursor-pointer">✕</button>
            </div>

            <form :action="'{{ url('anggota/aktivitas') }}/' + id + '/progress'" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-7 space-y-4 max-h-[70vh] overflow-y-auto">
                    
                    {{-- Progress --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Progress saat ini (%) <span class="text-red-500">*</span></label>
                        <input type="number" name="progress_minggu_berjalan" x-model="progress" min="0" max="100" step="0.01" required
                            class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-normal text-gray-700">
                    </div>

                    {{-- Uraian Progress --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Uraian Progress</label>
                        <textarea name="uraian_progress" rows="3" placeholder="Jelaskan pekerjaan yang telah diselesaikan..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                    </div>

                    {{-- Kendala Internal & Eksternal (2 Kolom) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Kendala Internal</label>
                            <textarea name="kendala_internal" rows="2" placeholder="Hambatan dari dalam tim..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Kendala Eksternal</label>
                            <textarea name="kendala_eksternal" rows="2" placeholder="Hambatan dari luar tim/pihak lain..." class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-normal text-gray-700 resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Upload Dokumen Pendukung --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-2">Dokumen Pendukung (Opsional)</label>
                        <input type="file" name="dokumen_pendukung" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-[#5C46F5] hover:file:bg-purple-100 cursor-pointer">
                        <p class="mt-1 text-[10px] text-gray-400">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, PNG, JPG (Maks. 5MB).</p>
                    </div>

                </div>

                <div class="px-7 py-4 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/50">
                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-500 hover:bg-rose-600 text-white transition-all cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#5C46F5] hover:bg-[#4A38D4] text-white transition-all cursor-pointer">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>