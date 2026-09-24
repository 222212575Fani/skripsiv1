<div class="relative overflow-hidden rounded-xl sm:rounded-2xl bg-linear-to-br from-[#8FD0FF] via-[#4D5BFF] to-[#D9E8F7]">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_35%,rgba(34,0,255,0.88),transparent_38%),radial-gradient(circle_at_60%_18%,rgba(173,106,255,0.55),transparent_30%),radial-gradient(circle_at_85%_75%,rgba(255,255,255,0.5),transparent_35%)]"></div>

    <div class="relative h-full flex flex-col justify-between gap-6 md:gap-0 p-6 sm:p-7 md:p-8 text-white">
        <div class="text-[36px] md:text-[42px] leading-none font-bold opacity-95">*</div>

        <div class="max-w-[280px]">
            <p class="text-xs sm:text-sm text-white/90 mb-2 sm:mb-3">{{ $greeting ?? 'Selamat Datang!' }}</p>
            <h2 class="text-xl sm:text-2xl md:text-[26px] font-extrabold leading-snug">
                {{ $title }}
            </h2>
        </div>

        <div class="hidden md:block text-xs text-white/85">
            © 2026 Direktorat Sistem Informasi Statistik
        </div>
    </div>
</div>