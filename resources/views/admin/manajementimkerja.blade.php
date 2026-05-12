<x-layoutadmin title="Manajemen Tim Kerja">
    <div class="flex flex-col gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Tim Kerja</h1>
            <p class="text-sm text-gray-500 mt-1 font-medium">Pengelolaan tim untuk SIS PROJECT.</p>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mt-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Tim</h3>
            <ul class="mt-4 list-disc pl-5 text-gray-600">
                @forelse($tims as $tim)
                    <li>{{ $tim->nama_tim }}</li>
                @empty
                    <li class="italic text-gray-400">Belum ada tim kerja yang terdaftar.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-layoutadmin>