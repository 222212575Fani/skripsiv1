<div x-data="{ 
        open: false, 
        id: '', 
        nama: '', 
        nip: '', 
        status: '', 
        role: '', 
        tim: '' 
     }" 
     @open-modal-edit-pengguna.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        nip = $event.detail.nip; 
        status = $event.detail.status; 
        role = $event.detail.role; 
        tim = $event.detail.tim;
     " 
     @close-modal-edit-pengguna.window="open = false"
     x-show="open" 
     class="fixed inset-0 z-[999] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[30px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.2)] transition-all border border-gray-100">
            
            <div class="flex items-start justify-between p-8 pb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#5C46F5]/10 rounded-full flex items-center justify-center text-[#5C46F5]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-[900] text-gray-900 tracking-tight">Edit Data Pengguna</h3>
                        <p class="text-sm font-medium text-gray-400">Ubah hak akses akun, NIP, serta penempatan tim kerja.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('admin.pengguna.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id_pengguna" x-model="id">

                <div class="p-8 pt-4 space-y-5">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600">Nama Lengkap</label>
                            <input type="text" name="nama" x-model="nama" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#5C46F5] focus:ring-1 focus:ring-[#5C46F5] outline-none text-sm font-bold transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600">NIP (18 Digit)</label>
                            <input type="text" name="nip" x-model="nip" required maxlength="18" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#5C46F5] focus:ring-1 focus:ring-[#5C46F5] outline-none text-sm font-bold transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 pt-2">
                        
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600">Status Akun</label>
                            <select name="status_akun" x-model="status" required class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer transition-all">
                                <option value="aktif">Aktif</option>
                                <option value="pending">Pending</option>
                                <option value="nonaktif">Non-Aktif</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600">Peran (Role)</label>
                            <select name="id_role" x-model="role" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer transition-all">
                                <option value="">-- Tanpa Peran --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-600">Tim Kerja</label>
                            <select name="id_tim" x-model="tim" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-[#5C46F5] outline-none text-sm font-bold cursor-pointer transition-all">
                                <option value="">Tanpa Tim (Kosong)</option>
                                @foreach($tims as $t)
                                    <option value="{{ $t->id_tim }}">{{ $t->nama_tim }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="p-8 border-t border-gray-50 flex items-center justify-end gap-3">
                    <button type="button" @click="open = false" class="px-6 py-3 bg-white border border-gray-200 text-red-500 rounded-full font-bold text-xs uppercase hover:bg-red-50 transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-8 py-3 bg-[#5C46F5] text-white rounded-full font-bold text-xs uppercase hover:bg-[#4A38D4] shadow-lg shadow-[#5C46F5]/20 active:scale-[0.98] transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>