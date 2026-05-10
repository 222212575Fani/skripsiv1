<div class="relative overflow-hidden rounded-[22px] bg-gradient-to-br from-[#8FD0FF] via-[#4D5BFF] to-[#D9E8F7]">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_22%_35%,rgba(34,0,255,0.88),transparent_38%),radial-gradient(circle_at_60%_18%,rgba(173,106,255,0.55),transparent_30%),radial-gradient(circle_at_85%_75%,rgba(255,255,255,0.5),transparent_35%)]"></div>

    <div class="relative h-full flex flex-col justify-between p-8 md:p-10 text-white">
        <div class="text-[54px] leading-none font-bold opacity-95">*</div>

        <div class="max-w-[300px]">
            <p class="text-[16px] text-white/90 mb-4">{{ $greeting ?? 'Selamat Datang!' }}</p>
            <h2 class="text-[26px] md:text-[34px] font-extrabold leading-[1.15]">
                {{ $title }}
            </h2>
        </div>

        <div class="text-[14px] text-white/90">
            © 2026 Direktorat Sistem Informasi Statistik
        </div>
    </div>
</div>