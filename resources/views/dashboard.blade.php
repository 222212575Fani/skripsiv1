<x-layoutadmin :title="$title">
    <div class="space-y-8">
        {{-- Header dengan sapaan personal --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-black text-gray-900">Dashboard Monitoring</h1>
                <p class="text-sm text-gray-500 font-medium">Selamat datang kembali, {{ auth()->user()->nama }}</p>
            </div>
        </div>

        {{-- Logika Tampilan Berdasarkan Role (yang dikirim dari route) --}}
        @if($role == 'direktur')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-stat-card label="Total Tim Aktif" value="12" color="indigo" />
                <x-stat-card label="Progres Keseluruhan" value="78%" color="emerald" />
                <x-stat-card label="Target Bulanan" value="95%" color="amber" />
            </div>
            
            <div class="mt-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h2 class="font-bold text-gray-800 mb-4">Pantauan Seluruh Progres Tim</h2>
                </div>
        @elseif($role == 'ketuatim')
            <div class="bg-gradient-to-r from-[#5C46F5] to-indigo-600 p-6 rounded-2xl text-white">
                <h2 class="font-bold text-lg">Tim: {{ auth()->user()->nama_tim }}</h2>
                <p class="text-indigo-100 text-sm">Ketua: {{ auth()->user()->nama }}</p>
            </div>

            <div class="mt-6 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h2 class="font-bold text-gray-800 mb-4">Detail Tugas Tim</h2>
                </div>
        @endif
    </div>
</x-layoutadmin>