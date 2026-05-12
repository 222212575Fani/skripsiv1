<div id="modalAktivasi" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModalAktivasi()"></div>

    <div class="relative bg-white w-full max-w-lg m-4 rounded-[32px] shadow-2xl p-10 transform transition-all duration-300">
        <h3 class="text-2xl font-black text-gray-900 mb-2">Konfirmasi Aktivasi</h3>
        <p class="text-sm text-gray-400 font-medium mb-8">Berikan otorisasi untuk pengguna ini.</p>

        <div class="bg-gray-50 rounded-2xl p-5 mb-8 border border-gray-100 space-y-2">
            <div class="flex justify-between"><span class="text-[10px] uppercase font-bold text-gray-400">Nama</span><span id="displayNama" class="text-sm font-bold text-gray-700"></span></div>
            <div class="flex justify-between"><span class="text-[10px] uppercase font-bold text-gray-400">NIP</span><span id="displayNip" class="text-sm font-bold text-gray-700 uppercase"></span></div>
        </div>

        <form action="{{ route('admin.aktivasi') }}" method="POST" class="space-y-6 text-left">
            @csrf
            <input type="hidden" name="user_id" id="inputUserId">
            
            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 ml-1">Pilih Peran (Role) <span class="text-red-500">*</span></label>
                <select name="id_role" required class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-4 focus:ring-[#5C46F5]/10 font-bold text-sm text-gray-700 cursor-pointer">
                    <option value="" disabled selected>-- Pilih Peran --</option>
                    @foreach($roles as $role)
                        {{-- Menggunakan id_role dan nama_role sesuai DB kamu --}}
                        <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] uppercase font-black text-gray-400 ml-1">Penempatan Tim (Opsional)</label>
                <select name="id_tim" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none focus:ring-4 focus:ring-[#5C46F5]/10 font-bold text-sm text-gray-700 cursor-pointer">
                    <option value="" selected>-- Belum Ada Tim --</option>
                    @foreach($tims as $tim)
                        {{-- Menggunakan id_tim dan nama_tim sesuai DB kamu --}}
                        <option value="{{ $tim->id_tim }}">{{ $tim->nama_tim }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-10">
                <button type="button" onclick="closeModalAktivasi()" class="px-6 py-4 bg-gray-100 text-gray-400 rounded-2xl font-black text-xs hover:bg-gray-200 transition-all uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-6 py-4 bg-[#5C46F5] text-white rounded-2xl font-black text-xs hover:bg-[#4A38D4] shadow-xl shadow-[#5C46F5]/20 transition-all uppercase tracking-widest">Aktifkan Sekarang</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalAktivasi(id, nama, nip) {
        document.getElementById('inputUserId').value = id;
        document.getElementById('displayNama').innerText = nama;
        document.getElementById('displayNip').innerText = nip;
        
        const modal = document.getElementById('modalAktivasi');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalAktivasi() {
        const modal = document.getElementById('modalAktivasi');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>