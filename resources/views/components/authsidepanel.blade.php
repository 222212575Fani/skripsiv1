<div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-linear-to-br from-[#6D5CE8] via-[#604EE6] to-[#513FE0]">
    <div class="relative h-full flex flex-col justify-between gap-6 md:gap-0 p-6 sm:p-7 md:p-8 text-white">
        <div></div>

        <div class="max-w-[280px]">
            <p class="text-sm sm:text-base md:text-[17px] font-normal text-white/90 mb-2 sm:mb-2.5">{{ $greeting ?? 'Selamat Datang!' }}</p>
            <h2 class="text-xl sm:text-2xl md:text-[26px] font-extrabold leading-snug">
                {{ $title }}
            </h2>
        </div>

        <div class="hidden md:block text-xs text-white/85">
            © 2026 Direktorat Sistem Informasi Statistik
        </div>
    </div>
</div>