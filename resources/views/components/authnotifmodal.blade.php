@php
    $isRegisterSuccess = session('register_success');
    $isAccountPending = session('account_pending');
    $showModal = $isRegisterSuccess || $isAccountPending;

    $title = $isRegisterSuccess ? 'Pendaftaran Berhasil!' : 'Hey, Tunggu Dulu!!';
    $message = $isRegisterSuccess 
        ? 'Akun Anda telah berhasil didaftarkan. Saat ini akun sedang menunggu proses verifikasi dan aktivasi oleh Administrator sebelum Anda dapat masuk.'
        : 'Akun Anda saat ini belum diaktivasi oleh Administrator. Mohon bersabar menunggu persetujuan atau hubungi admin unit kerja Anda agar akun segera diaktifkan.';
    $primaryBtnText = $isRegisterSuccess ? 'Siap, Saya Mengerti' : 'Baik, Saya Mengerti';

    // Format mailto langsung mengarahkan user menulis email ke admin
    $userName = session('registered_name') ?? session('pending_name') ?? '';
    $userEmail = session('registered_email') ?? old('email', '');
    $emailTo = 'admin@bps.go.id';
    $emailSubject = 'Permohonan Aktivasi Akun - Sistem Manajemen Proyek';
    $emailBody = "Halo Tim Administrator,\n\nSaya ingin mengajukan permohonan aktivasi akun saya di Sistem Manajemen Proyek:\n";
    if ($userName) {
        $emailBody .= "- Nama Lengkap: {$userName}\n";
    }
    if ($userEmail) {
        $emailBody .= "- Email Terdaftar: {$userEmail}\n";
    }
    $emailBody .= "\nMohon bantuannya untuk mengaktifkan akun dan menetapkan peran serta tim kerja saya.\n\nTerima kasih.";

    $mailtoUrl = "mailto:{$emailTo}?subject=" . rawurlencode($emailSubject) . "&body=" . rawurlencode($emailBody);
@endphp

@if($showModal)
<div x-data="{ open: true }" 
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[1000] pointer-events-none flex items-start justify-end p-3.5 sm:p-6"
     style="display: none;">
    
    {{-- Backdrop Sangat Halus (Bisa diklik untuk menutup secara instan) --}}
    <div class="fixed inset-0 bg-gray-950/15 backdrop-blur-[0.5px] transition-opacity pointer-events-auto" 
         @click="open = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Kartu Modal Melayang di Pojok Kanan Atas (Slide-in dari Kanan Atas) --}}
    <div @click.outside="open = false"
         class="relative w-full max-w-[420px] sm:max-w-[480px] md:max-w-[520px] bg-gradient-to-r from-[#6E5BC3] via-[#7B66DC] to-[#8C7AE6] rounded-2xl sm:rounded-[22px] p-4 sm:p-5 shadow-[0_20px_50px_-10px_rgba(110,91,195,0.45)] border border-white/25 pointer-events-auto transform transition-all overflow-hidden"
         x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-400 transform"
         x-transition:enter-start="opacity-0 translate-x-12 -translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-x-0 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-x-0 scale-100"
         x-transition:leave-end="opacity-0 translate-x-10 scale-95">
        
        {{-- Tombol Tutup (X) Bundar di Pojok Kanan Atas --}}
        <button type="button" 
                @click="open = false" 
                class="absolute top-3.5 right-3.5 sm:top-4 sm:right-4 w-7 h-7 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center transition-all cursor-pointer shrink-0 z-10"
                title="Tutup">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-center gap-3.5 sm:gap-4 min-w-0 pr-6 sm:pr-7">
            
            {{-- Grafis Megafon Vektor Keren di Sisi Kiri --}}
            <div class="shrink-0 flex items-center justify-center">
                <svg viewBox="0 0 160 130" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-20 sm:w-24 sm:h-24 select-none drop-shadow-md">
                    <!-- Gelombang Suara / Sound Waves -->
                    <path d="M124 28 C136 42 138 58 130 74" stroke="white" stroke-width="3.5" stroke-linecap="round" opacity="0.9"/>
                    <path d="M138 20 C154 40 156 68 144 92" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.6"/>
                    <path d="M112 16 L120 8" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.85"/>
                    <path d="M128 86 L136 94" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.85"/>
                    <path d="M142 54 L152 52" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity="0.7"/>

                    <!-- Corong Megafon (Biru Cerah) -->
                    <polygon points="58,55 116,24 116,84 58,63" fill="#3B82F6"/>
                    <!-- Highlight Bagian Atas Corong -->
                    <polygon points="58,55 116,24 116,38 58,58" fill="#60A5FA"/>
                    <!-- Shading Bagian Bawah Corong -->
                    <polygon points="58,61 116,74 116,84 58,63" fill="#2563EB"/>

                    <!-- Lingkar Dalam Corong Megafon (Kuning Emas) -->
                    <ellipse cx="116" cy="54" rx="8" ry="30" fill="#F59E0B"/>
                    <ellipse cx="118" cy="54" rx="5" ry="24" fill="#D97706"/>
                    <ellipse cx="119" cy="54" rx="3" ry="18" fill="#B45309"/>
                    <!-- Highlight Bibir Corong -->
                    <path d="M116 28 C119 36 120 46 120 54 C120 62 119 72 116 80" stroke="#FEF08A" stroke-width="2" stroke-linecap="round"/>

                    <!-- Silinder Belakang Megafon -->
                    <g transform="rotate(-8 48 58)">
                        <rect x="36" y="50" width="22" height="17" rx="3.5" fill="#F1F5F9"/>
                        <ellipse cx="37" cy="58.5" rx="3.5" ry="8.5" fill="#CBD5E1"/>
                        <rect x="44" y="50" width="5" height="17" fill="#2563EB"/>
                    </g>

                    <!-- Gagang Megafon -->
                    <path d="M57 65 L51 92 L44 90 L49 63 Z" fill="#1D4ED8"/>

                    <!-- Lengan & Tangan Jas Hitam -->
                    <!-- Jas Hitam -->
                    <path d="M4 122 L34 92 L48 106 L18 136 Z" fill="#1E293B"/>
                    <!-- Manset Kemeja Putih -->
                    <path d="M32 90 L40 82 L50 92 L42 100 Z" fill="#FFFFFF"/>
                    <!-- Telapak Tangan & Ibu Jari -->
                    <path d="M39 81 C39 81 44 71 52 73 C57 74 56 81 56 81 L44 91 Z" fill="#FDBA74"/>
                    <!-- Jari-jari menggenggam gagang -->
                    <rect x="47" y="70" width="13" height="6" rx="3" fill="#FDBA74" transform="rotate(-15 47 70)"/>
                    <rect x="45" y="77" width="13" height="6" rx="3" fill="#FDBA74" transform="rotate(-15 45 77)"/>
                    <rect x="43" y="84" width="12" height="6" rx="3" fill="#FDBA74" transform="rotate(-15 43 84)"/>
                </svg>
            </div>

            {{-- Bagian Teks & Tombol di Sisi Kanan --}}
            <div class="flex-1 min-w-0 text-left">
                <h3 class="text-base sm:text-lg font-black text-white tracking-tight leading-snug">
                    {{ $title }}
                </h3>
                
                <p class="mt-1 text-xs text-purple-100/90 leading-relaxed font-medium">
                    {{ $message }}
                </p>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 mt-3.5">
                    {{-- Tombol Utama Putih --}}
                    <button type="button" 
                            @click="open = false" 
                            class="px-3.5 sm:px-4 py-1.5 sm:py-2 bg-white hover:bg-purple-50 text-[#6E5BC3] text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer hover:scale-[1.02] active:scale-[0.98]">
                        {{ $primaryBtnText }}
                    </button>

                    {{-- Tombol Sekunder: Langsung Membuka Aplikasi Email untuk Menulis Inbox --}}
                    <a href="{{ $mailtoUrl }}" 
                       target="_blank" 
                       class="px-3 sm:px-3.5 py-1.5 sm:py-2 border border-white/60 hover:bg-white/15 text-white text-xs font-semibold rounded-xl transition-all cursor-pointer flex items-center gap-1.5 hover:scale-[1.02] active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>Hubungi Admin</span>
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
@endif
