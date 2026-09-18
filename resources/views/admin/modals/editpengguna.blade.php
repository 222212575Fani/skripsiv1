@php
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
        roleName: 'Pilih Peran',
        openRoleDropdown: false,
        tim: '', 
        timName: 'Pilih Tim Kerja',
        openTimDropdown: false,
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
        
        let selectedRole = document.querySelector(`[data-role-id='${role}']`);
        roleName = selectedRole ? selectedRole.dataset.roleName : 'Pilih Peran';

        let selectedTim = document.querySelector(`[data-tim-id='${tim}']`);
        timName = selectedTim ? selectedTim.dataset.timName : 'Pilih Tim Kerja';
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
    
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-[1.5px] transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        {{-- max-w-2xl diperlebar menjadi max-w-3xl agar ruang input lebih longgar --}}
        <div @click.away="open = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="relative w-full max-w-3xl transform overflow-visible rounded-[24px] bg-white p-0 text-left shadow-[0_25px_80px_-15px_rgba(0,0,0,0.15)] transition-all border border-gray-100">
            
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-purple-50 border border-purple-100 rounded-xl flex items-center justify-center text-[#6E5BC3] shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Edit Data Pengguna</h3>
                        <p class="text-xs font-medium text-gray-400">Ubah hak akses akun, NIP, serta penempatan tim kerja.</p>
                    </div>
                </div>
                <button type="button" @click="open = false" class="p-2 text-gray-300 hover:text-gray-500 hover:bg-gray-50 rounded-full transition-all cursor-pointer">
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
                    
                    @if(session('error'))
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-start gap-3 animate-fade-in">
                            <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0 mt-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-rose-900">Perubahan Gagal</h4>
                                <p class="text-xs text-rose-700 mt-0.5 font-medium leading-relaxed">
                                    {{ session('error') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Baris 1: Nama & NIP --}}
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

                    {{-- Baris 2: Status Akun & Peran (Role) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Status Akun --}}
                        <div class="relative" x-data="{ openStatus: false }">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Status Akun</label>
                            <input type="hidden" name="status_akun" x-model="status" required>

                            <button @click="openStatus = !openStatus; openRoleDropdown = false; openTimDropdown = false;" @click.outside="openStatus = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="status.charAt(0).toUpperCase() + status.slice(1)" class="text-gray-700 font-normal"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openStatus ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openStatus" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1">
                                <button type="button" @click="status = 'aktif'; openStatus = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Aktif</button>
                                <button type="button" @click="status = 'pending'; openStatus = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pending</button>
                                <button type="button" @click="status = 'nonaktif'; openStatus = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Non-Aktif</button>
                            </div>
                        </div>

                        {{-- Peran (Role) --}}
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-700 mb-2">Peran (Role)</label>
                            <input type="hidden" name="id_role" x-model="role">

                            <button @click="openRoleDropdown = !openRoleDropdown; openStatus = false; openTimDropdown = false;" @click.outside="openRoleDropdown = false" type="button" 
                                class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                                <span x-text="roleName" :class="role === '' ? 'text-gray-400 font-normal' : 'text-gray-700 font-normal'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openRoleDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openRoleDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1 max-h-36 overflow-y-auto">
                                <button type="button" @click="role = ''; roleName = 'Pilih Peran'; openRoleDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pilih Peran</button>
                                @foreach($roles as $r)
                                    @if(strtolower($r->nama_role) !== 'admin')
                                        <button type="button" 
                                            data-role-id="{{ $r->id_role }}" 
                                            data-role-name="{{ $r->nama_role }}"
                                            @click="role = '{{ $r->id_role }}'; roleName = '{{ $r->nama_role }}'; openRoleDropdown = false;"
                                            class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">
                                            {{ $r->nama_role }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Tim Kerja (Dibuat Full Width / Satu Baris Sendiri agar Luas) --}}
                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 mb-2">Tim Kerja</label>
                        <input type="hidden" name="id_tim" x-model="tim">

                        <button @click="openTimDropdown = !openTimDropdown; openStatus = false; openRoleDropdown = false;" @click.outside="openTimDropdown = false" type="button" 
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-white hover:bg-[#F8F7FF] border border-purple-200 hover:border-[#6E5BC3] rounded-xl text-xs font-medium transition-all cursor-pointer">
                            <span x-text="timName" :class="tim === '' ? 'text-gray-400 font-normal' : 'text-gray-700 font-normal'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6E5BC3] transition-transform duration-200" :class="openTimDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openTimDropdown" x-cloak class="absolute left-0 mt-2 w-full bg-white border border-purple-100 rounded-[20px] shadow-xl p-2 z-50 space-y-1 max-h-36 overflow-y-auto">
                            <button type="button" @click="tim = ''; timName = 'Pilih Tim Kerja'; openTimDropdown = false;" class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-400 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">Pilih Tim Kerja</button>
                            @foreach($tims as $t)
                                <button type="button" 
                                    data-tim-id="{{ $t->id_tim }}"
                                    data-tim-name="{{ $t->nama_tim }}"
                                    @click="tim = '{{ $t->id_tim }}'; timName = '{{ $t->nama_tim }}'; openTimDropdown = false;"
                                    class="w-full text-left px-3 py-2 rounded-xl text-xs text-gray-700 hover:bg-purple-50 hover:text-[#6E5BC3] font-normal transition-all cursor-pointer">
                                    {{ $t->nama_tim }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="status === 'nonaktif' && initialStatus !== 'nonaktif'" 
                         x-transition 
                         class="p-3 bg-red-50 text-red-600 text-xs font-medium rounded-xl border border-red-100 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Perhatian: Akun akan dinonaktifkan. Pengguna tidak dapat mengakses sistem.</span>
                    </div>

                </div>

                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50/50">
                    <x-button type="button" @click="open = false" color="bg-rose-500 hover:bg-rose-600 text-white" shadow="shadow-md shadow-rose-500/20">
                        Batal
                    </x-button>
                    <x-button type="submit" color="bg-[#6E5BC3] hover:bg-[#5C4AB5] text-white" shadow="shadow-md shadow-[#6E5BC3]/20">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>