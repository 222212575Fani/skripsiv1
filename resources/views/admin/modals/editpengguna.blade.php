@php
    // Mencari ID Role untuk peran Ketua Tim secara dinamis dari database
    $idRoleKetua = $roles->first(function($r) {
        return stripos($r->nama_role, 'ketua') !== false;
    })?->id_role ?? '';
@endphp

<div x-data="{ 
        open: false, 
        id: '', 
        nama: '', 
        nip: '', 
        status: '', 
        role: '', 
        tim: '',
        initialStatus: '',
        ketuaRoleId: '{{ $idRoleKetua }}'
     }" 
     @open-modal-edit-pengguna.window="
        open = true; 
        id = $event.detail.id; 
        nama = $event.detail.nama; 
        nip = $event.detail.nip; 
        status = $event.detail.status; 
        initialStatus = $event.detail.status; 
        role = $event.detail.role ?? ''; 
        tim = $event.detail.tim ?? '';
     " 
     @close-modal-edit-pengguna.window="open = false"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[999] overflow-y-auto" 
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-2xl transform overflow-hidden rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-500 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Edit Data Pengguna</h3>
                        <p class="text-xs font-medium text-gray-400">Ubah hak akses akun, NIP, serta penempatan tim kerja.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pengguna.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id_pengguna" x-model="id">
                <input type="hidden" name="nip" x-model="nip">
                <input type="hidden" name="nama" x-model="nama">

                <div class="p-8 space-y-5">
                    
                    {{-- Grid 2 Kolom (Nama Lengkap & NIP) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" x-model="nama" readonly 
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-xs font-medium cursor-not-allowed outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">NIP (18 Digit)</label>
                            <input type="text" x-model="nip" readonly 
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-500 text-xs font-medium cursor-not-allowed outline-none">
                        </div>
                    </div>

                    {{-- Grid 3 Kolom (Status Akun, Peran, Tim Kerja) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Akun</label>
                            <div class="relative">
                                <select name="status_akun" x-model="status" required 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer appearance-none">
                                    <option value="aktif">Aktif</option>
                                    <option value="pending">Pending</option>
                                    <option value="nonaktif">Non-Aktif</option>
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Peran (Role)</label>
                            <div class="relative">
                                <select name="id_role" x-model="role" 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer appearance-none">
                                    <option value="">Pilih Peran</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id_role }}">{{ $r->nama_role }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">Tim Kerja</label>
                            <div class="relative">
                                <select name="id_tim" x-model="tim" 
                                    class="w-full px-4 py-2.5 pr-10 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#5C46F5]/20 focus:border-[#5C46F5] outline-none text-xs font-medium text-gray-700 cursor-pointer appearance-none">
                                    <option value="">Pilih Tim Kerja</option>
                                    @foreach($tims as $t)
                                        <option value="{{ $t->id_tim }}" 
                                            {{-- Dinonaktifkan HANYA JIKA: Peran yang dipilih adalah Ketua Tim, tim sudah ada ketua, dan bukan akun ini ketuanya --}}
                                            :disabled="String(role) === String(ketuaRoleId) && {{ $t->sudah_punya_ketua ? 'true' : 'false' }} && String(id) !== '{{ $t->id_ketua_tim }}'"
                                            :class="(String(role) === String(ketuaRoleId) && {{ $t->sudah_punya_ketua ? 'true' : 'false' }} && String(id) !== '{{ $t->id_ketua_tim }}') ? 'text-gray-300 bg-gray-50' : 'text-gray-700'"
                                            x-text="(String(role) === String(ketuaRoleId) && {{ $t->sudah_punya_ketua ? 'true' : 'false' }} && String(id) !== '{{ $t->id_ketua_tim }}') ? '{{ $t->nama_tim }} (Sudah punya ketua)' : '{{ $t->nama_tim }}'">
                                            {{ $t->nama_tim }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Peringatan konfirmasi status --}}
                    <div x-show="status === 'nonaktif' && initialStatus !== 'nonaktif'" 
                         x-transition 
                         class="p-3 bg-red-50 text-red-600 text-xs font-medium rounded-xl border border-red-100 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Perhatian: Akun akan dinonaktifkan. Pengguna tidak dapat mengakses sistem.</span>
                    </div>

                </div>

                {{-- Footer Action --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <button type="button" @click="open = false" 
                        class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold text-xs hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-6 py-2.5 bg-[#5C46F5] text-white rounded-xl font-bold text-xs hover:bg-[#4A38D4] shadow-md shadow-[#5C46F5]/20 transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>