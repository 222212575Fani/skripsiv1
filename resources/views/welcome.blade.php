<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROXIS - Pengelolaan & Pemantauan Proyek Terintegrasi | BPS</title>

    <!-- Favicon Logo BPS -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_bps.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo_bps.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_bps.png') }}">

    <!-- Google Fonts: Mulish -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300..1000;1,300..1000&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Mulish', sans-serif;
        }

        /* Scrollbar Ungu Khas PROXIS */
        * {
            scrollbar-width: thin;
            scrollbar-color: #8C78E6 #F1EEFF;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #F1EEFF;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #8C78E6;
            border-radius: 9999px;
            border: 1.5px solid #F1EEFF;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #604EE6;
        }
    </style>
</head>
<body class="bg-[#FAF9FE] text-slate-800 antialiased selection:bg-[#604EE6] selection:text-white" 
      x-data="{ 
          mobileMenu: false, 
          activeSection: 'beranda',
          init() {
              const sections = ['beranda', 'tentang', 'alur-akses', 'faq'];
              const updateActive = () => {
                  const scrollPos = window.scrollY + 200;
                  for (let i = sections.length - 1; i >= 0; i--) {
                      const el = document.getElementById(sections[i]);
                      if (el && scrollPos >= el.offsetTop) {
                          this.activeSection = sections[i];
                          break;
                      }
                  }
              };
              window.addEventListener('scroll', updateActive, { passive: true });
              updateActive();
          }
      }">

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-[#604EE6]/90 backdrop-blur-md border-b border-white/10 py-3 sm:py-3.5 transition-all">
        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                
                <!-- Brand / Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo_bps.png') }}" alt="Logo BPS" class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-105 filter drop-shadow-xs">
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-white tracking-tight">PROXIS</span>
                        <span class="text-[10px] font-semibold text-white/80 uppercase tracking-wider hidden sm:block">Direktorat Sistem Informasi Statistik</span>
                    </div>
                </a>

                <!-- Desktop Menu Navigasi (Center Floating Pill) -->
                <nav class="hidden lg:flex items-center bg-white/95 backdrop-blur-md p-1.5 rounded-full shadow-lg shadow-black/5 border border-white/60">
                    <a href="#beranda" 
                       :class="activeSection === 'beranda' ? 'bg-[#604EE6] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100/70'"
                       class="px-5 py-1.5 rounded-full text-xs sm:text-[13px] transition-all">Beranda</a>
                    <a href="#tentang" 
                       :class="activeSection === 'tentang' ? 'bg-[#604EE6] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100/70'"
                       class="px-4 py-1.5 rounded-full text-xs sm:text-[13px] transition-all">Tentang PROXIS</a>
                    <a href="#alur-akses" 
                       :class="activeSection === 'alur-akses' ? 'bg-[#604EE6] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100/70'"
                       class="px-4 py-1.5 rounded-full text-xs sm:text-[13px] transition-all">Alur Akses Pegawai</a>
                    <a href="#faq" 
                       :class="activeSection === 'faq' ? 'bg-[#604EE6] text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100/70'"
                       class="px-4 py-1.5 rounded-full text-xs sm:text-[13px] transition-all">Bantuan & FAQ</a>
                </nav>

                <!-- Action Buttons Desktop (Right Floating Pill - Hanya di layar besar lg agar tidak tabrakan dengan hamburger) -->
                <div class="hidden lg:flex items-center bg-white/95 backdrop-blur-md p-1.5 rounded-full shadow-lg shadow-black/5 border border-white/60">
                    @auth
                        @php
                            $role = auth()->user()->role?->nama_role;
                            $dashboardRoute = match ($role) {
                                'Admin' => route('admin.manajemenpengguna'),
                                'Direktur' => route('direktur.dashboard'),
                                'Ketua Tim' => route('ketuatim.dashboard'),
                                'Anggota' => route('anggota.proyekaktivitas'),
                                default => route('login'),
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-2 px-5 py-1.5 rounded-full bg-[#604EE6] hover:bg-[#503ED8] text-white text-xs sm:text-[13px] font-bold shadow-xs transition-all">
                            <span>Buka Dashboard</span>
                            <span class="px-2 py-0.5 rounded-full bg-white/20 text-[10px] font-semibold">{{ $role }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-full text-slate-700 hover:text-slate-950 text-xs sm:text-[13px] font-bold transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4.5 py-1.5 rounded-full bg-[#18181B] hover:bg-black text-white text-xs sm:text-[13px] font-bold shadow-xs transition-all">
                            <span>Daftar Akun</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button (Muncul di layar < lg) -->
                <div class="flex items-center lg:hidden">
                    <button @click="mobileMenu = !mobileMenu" 
                            type="button" 
                            class="w-10 h-10 rounded-full bg-white/20 hover:bg-white/30 text-white backdrop-blur-md border border-white/30 transition-all focus:outline-none shadow-xs flex items-center justify-center"
                            aria-label="Navigasi Menu">
                        <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu (Layar < lg) -->
        <div x-show="mobileMenu" 
             x-cloak 
             @click.away="mobileMenu = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 pt-3 pb-2">
            <div class="bg-white/95 backdrop-blur-2xl border border-white/60 rounded-3xl p-5 shadow-2xl space-y-4">
                <nav class="flex flex-col space-y-1.5 text-sm font-semibold">
                    <a @click="mobileMenu = false; activeSection = 'beranda'" href="#beranda" 
                       :class="activeSection === 'beranda' ? 'bg-purple-50 text-[#604EE6] font-bold' : 'text-slate-700 hover:text-[#604EE6] hover:bg-purple-50/70 font-semibold'"
                       class="group flex items-center justify-between px-4 py-3 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span :class="activeSection === 'beranda' ? 'bg-purple-100 text-[#604EE6]' : 'bg-slate-100 text-slate-500 group-hover:bg-purple-100 group-hover:text-[#604EE6]'"
                                  class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </span>
                            <span :class="activeSection === 'beranda' ? 'text-[#604EE6]' : 'group-hover:text-[#604EE6]'" class="transition-colors">Beranda</span>
                        </div>
                        <svg :class="activeSection === 'beranda' ? 'text-[#604EE6] translate-x-0.5' : 'text-slate-400 group-hover:text-[#604EE6] group-hover:translate-x-0.5'"
                             class="w-4 h-4 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a @click="mobileMenu = false; activeSection = 'tentang'" href="#tentang" 
                       :class="activeSection === 'tentang' ? 'bg-purple-50 text-[#604EE6] font-bold' : 'text-slate-700 hover:text-[#604EE6] hover:bg-purple-50/70 font-semibold'"
                       class="group flex items-center justify-between px-4 py-3 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span :class="activeSection === 'tentang' ? 'bg-purple-100 text-[#604EE6]' : 'bg-slate-100 text-slate-500 group-hover:bg-purple-100 group-hover:text-[#604EE6]'"
                                  class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span :class="activeSection === 'tentang' ? 'text-[#604EE6]' : 'group-hover:text-[#604EE6]'" class="transition-colors">Tentang PROXIS</span>
                        </div>
                        <svg :class="activeSection === 'tentang' ? 'text-[#604EE6] translate-x-0.5' : 'text-slate-400 group-hover:text-[#604EE6] group-hover:translate-x-0.5'"
                             class="w-4 h-4 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a @click="mobileMenu = false; activeSection = 'alur-akses'" href="#alur-akses" 
                       :class="activeSection === 'alur-akses' ? 'bg-purple-50 text-[#604EE6] font-bold' : 'text-slate-700 hover:text-[#604EE6] hover:bg-purple-50/70 font-semibold'"
                       class="group flex items-center justify-between px-4 py-3 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span :class="activeSection === 'alur-akses' ? 'bg-purple-100 text-[#604EE6]' : 'bg-slate-100 text-slate-500 group-hover:bg-purple-100 group-hover:text-[#604EE6]'"
                                  class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <span :class="activeSection === 'alur-akses' ? 'text-[#604EE6]' : 'group-hover:text-[#604EE6]'" class="transition-colors">Alur Akses Pegawai</span>
                        </div>
                        <svg :class="activeSection === 'alur-akses' ? 'text-[#604EE6] translate-x-0.5' : 'text-slate-400 group-hover:text-[#604EE6] group-hover:translate-x-0.5'"
                             class="w-4 h-4 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a @click="mobileMenu = false; activeSection = 'faq'" href="#faq" 
                       :class="activeSection === 'faq' ? 'bg-purple-50 text-[#604EE6] font-bold' : 'text-slate-700 hover:text-[#604EE6] hover:bg-purple-50/70 font-semibold'"
                       class="group flex items-center justify-between px-4 py-3 rounded-2xl transition-all">
                        <div class="flex items-center gap-3">
                            <span :class="activeSection === 'faq' ? 'bg-purple-100 text-[#604EE6]' : 'bg-slate-100 text-slate-500 group-hover:bg-purple-100 group-hover:text-[#604EE6]'"
                                  class="w-8 h-8 rounded-xl flex items-center justify-center transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span :class="activeSection === 'faq' ? 'text-[#604EE6]' : 'group-hover:text-[#604EE6]'" class="transition-colors">Bantuan & FAQ</span>
                        </div>
                        <svg :class="activeSection === 'faq' ? 'text-[#604EE6] translate-x-0.5' : 'text-slate-400 group-hover:text-[#604EE6] group-hover:translate-x-0.5'"
                             class="w-4 h-4 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </nav>

                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-[#604EE6] hover:bg-[#503ED8] text-white font-bold text-sm shadow-md transition-all">
                            <span>Buka Dashboard</span>
                            <span class="px-2 py-0.5 rounded-full bg-white/20 text-[10px]">{{ $role }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-white hover:bg-purple-50 text-[#604EE6] border border-purple-200 font-extrabold text-sm shadow-xs transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk ke Sistem</span>
                        </a>
                        <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-[#18181B] hover:bg-black text-white font-bold text-sm shadow-md transition-all">
                            <span>Daftar Akun Baru</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="beranda" class="relative bg-gradient-to-b from-[#604EE6] via-[#6E5DE7] to-[#7F6FF5] text-white pt-12 pb-20 md:pt-18 md:pb-28 overflow-hidden">
        <!-- Ambient Radial Glow Highlights -->
        <div class="absolute -top-32 right-1/4 w-[600px] h-[600px] bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/4 -left-20 w-[500px] h-[500px] bg-purple-300/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-[450px] h-[450px] bg-indigo-300/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                
                <!-- Badge Tag Pill (Finseer style) -->
                <div class="inline-flex items-center gap-2 p-1 pl-1.5 pr-4 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white shadow-xs mb-8">
                    <span class="px-3 py-1 rounded-full bg-white text-[#604EE6] text-[11px] font-black uppercase tracking-wider shadow-2xs">Portal Resmi</span>
                    <span class="text-xs sm:text-sm font-semibold text-white/95">Direktorat Sistem Informasi Statistik</span>
                </div>

                <!-- Headline -->
                <h1 class="text-4xl sm:text-5xl lg:text-[58px] font-black text-white tracking-tight leading-[1.14]">
                    Pengelolaan dan Pemantauan 
                    <span class="block mt-1">
                        Proyek Terintegrasi
                    </span>
                </h1>

                <!-- Subheadline -->
                <p class="mt-6 text-base sm:text-lg text-white/85 leading-relaxed font-normal max-w-2xl mx-auto">
                    Mendukung koordinasi lintas tim kerja dan transparansi pelaporan progres proyek statistik secara terstruktur, terukur, dan akuntabel di lingkungan BPS.
                </p>

                <!-- Finseer-style Action Buttons -->
                <div class="mt-9 flex flex-col sm:flex-row items-center justify-center gap-3.5 sm:gap-4">
                    @auth
                        <a href="{{ $dashboardRoute }}" class="group w-full sm:w-auto inline-flex items-center justify-between sm:justify-center gap-3.5 pl-7 pr-2.5 py-2.5 rounded-full bg-white hover:bg-slate-50 text-slate-900 font-extrabold text-sm sm:text-base shadow-[0_10px_25px_rgba(0,0,0,0.18)] hover:shadow-[0_14px_30px_rgba(0,0,0,0.25)] hover:scale-[1.02] active:scale-[0.99] transition-all">
                            <span>Menuju Dashboard Anda</span>
                            <span class="w-9 h-9 rounded-full bg-[#604EE6] group-hover:bg-[#503ED8] text-white flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:rotate-45">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7V17"/>
                                </svg>
                            </span>
                        </a>
                        <a href="#alur-akses" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-bold text-sm sm:text-base shadow-xs hover:scale-[1.02] active:scale-[0.99] transition-all">
                            <span>Pelajari Alur Akses</span>
                        </a>
                    @else
                        <!-- Primary: White Pill with Purple Arrow Circle -->
                        <a href="{{ route('login') }}" class="group w-full sm:w-auto inline-flex items-center justify-between sm:justify-center gap-3.5 pl-7 pr-2.5 py-2.5 rounded-full bg-white hover:bg-slate-50 text-slate-900 font-extrabold text-sm sm:text-base shadow-[0_10px_25px_rgba(0,0,0,0.18)] hover:shadow-[0_14px_30px_rgba(0,0,0,0.25)] hover:scale-[1.02] active:scale-[0.99] transition-all">
                            <span>Masuk ke Sistem</span>
                            <span class="w-9 h-9 rounded-full bg-[#604EE6] group-hover:bg-[#503ED8] text-white flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:rotate-45">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7M17 7H7M17 7V17"/>
                                </svg>
                            </span>
                        </a>
                        
                        <!-- Secondary: Translucent Glass Pill -->
                        <a href="#alur-akses" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-bold text-sm sm:text-base shadow-xs hover:scale-[1.02] active:scale-[0.99] transition-all">
                            <span>Pelajari Alur Akses</span>
                        </a>
                    @endauth
                </div>
            </div>

        </div>
    </section>

    <!-- SECTION: TENTANG PROXIS -->
    <section id="tentang" class="py-16 sm:py-24 bg-white border-y border-purple-100">
        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            
            <!-- Makna Singkatan PROXIS -->
            <div>
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-100 text-[#604EE6] text-xs font-bold uppercase tracking-wider mb-3">
                        Mengenal PROXIS
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Makna Singkatan PROXIS
                    </h2>
                    <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed">
                        Nama <strong>PROXIS</strong> mencerminkan integrasi pengelolaan tugas, eksekusi kolaboratif, serta sistem informasi statistik yang terpadu.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card PRO -->
                    <div class="p-7 rounded-2xl bg-gradient-to-br from-[#FAF9FE] to-white border border-purple-100/90 hover:shadow-lg transition-all duration-300 relative group overflow-hidden flex flex-col justify-between">
                        <div class="absolute -right-3 -bottom-3 text-7xl font-black text-purple-100/40 select-none pointer-events-none group-hover:scale-105 transition-transform">
                            PRO
                        </div>
                        <div>
                            <div class="w-13 h-13 rounded-2xl bg-purple-100 text-[#604EE6] flex items-center justify-center font-black text-xl mb-5 shadow-xs">
                                PRO
                            </div>
                            <h3 class="text-xl font-black text-slate-800 mb-2">Proyek / Project</h3>
                            <p class="text-sm text-slate-600 leading-relaxed relative z-10 text-justify">
                                Berfokus pada pengelolaan dan pencapaian target seluruh proyek-proyek statistik agar terstruktur, terarah, dan akuntabel.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-[#604EE6]">
                            <span>PRO (Project)</span>
                        </div>
                    </div>

                    <!-- Card X -->
                    <div class="p-7 rounded-2xl bg-gradient-to-br from-[#FAF9FE] to-white border border-purple-100/90 hover:shadow-lg transition-all duration-300 relative group overflow-hidden flex flex-col justify-between">
                        <div class="absolute -right-2 -bottom-3 text-8xl font-black text-blue-100/40 select-none pointer-events-none group-hover:scale-105 transition-transform">
                            X
                        </div>
                        <div>
                            <div class="w-13 h-13 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-2xl mb-5 shadow-xs">
                                X
                            </div>
                            <h3 class="text-xl font-black text-slate-800 mb-2">eXchange & eXecution</h3>
                            <p class="text-sm text-slate-600 leading-relaxed relative z-10 text-justify">
                                Mengakomodasi <em>eXchange</em> (pertukaran informasi dan koordinasi), <em>eXecution</em> (eksekusi rencana secara nyata), serta konektivitas antar pihak yang terlibat.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-blue-600">
                            <span>X (eXchange, eXecution, & Konektivitas)</span>
                        </div>
                    </div>

                    <!-- Card IS -->
                    <div class="p-7 rounded-2xl bg-gradient-to-br from-[#FAF9FE] to-white border border-purple-100/90 hover:shadow-lg transition-all duration-300 relative group overflow-hidden flex flex-col justify-between">
                        <div class="absolute -right-3 -bottom-3 text-7xl font-black text-indigo-100/40 select-none pointer-events-none group-hover:scale-105 transition-transform">
                            IS
                        </div>
                        <div>
                            <div class="w-13 h-13 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xl mb-5 shadow-xs">
                                IS
                            </div>
                            <h3 class="text-xl font-black text-slate-800 mb-2">Information System</h3>
                            <p class="text-sm text-slate-600 leading-relaxed relative z-10 text-justify">
                                Sistem Informasi Statistik terintegrasi sebagai landasan data yang transparan, aman, dan mudah diakses untuk mendukung pengambilan keputusan.
                            </p>
                        </div>
                        <div class="mt-6 flex items-center gap-2 text-xs font-bold text-indigo-600">
                            <span>IS (Information System)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nilai yang Dibawa PROXIS -->
            <div class="pt-10 border-t border-purple-100">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-100 text-[#604EE6] text-xs font-bold uppercase tracking-wider mb-3">
                        Nilai Utama
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Nilai yang Dibawa PROXIS
                    </h2>
                    <p class="mt-2 text-slate-600 text-sm sm:text-base leading-relaxed">
                        Prinsip kerja yang memandu jalannya setiap proyek dari tahap awal hingga evaluasi capaian.
                    </p>
                </div>

                <!-- 5 Value Cards (P - R - O - X - IS) -->
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 sm:gap-5">
                    
                    <!-- P — Plan -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-[#FAF9FE] border border-purple-100/90 hover:bg-white hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 lg:flex-col lg:items-start lg:gap-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:flex-col lg:items-start lg:gap-0">
                            <div class="w-11 h-11 rounded-xl bg-purple-100 text-[#604EE6] flex items-center justify-center font-black text-lg shrink-0 lg:mb-4">
                                P
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 mb-1 lg:mb-2">
                                    Plan
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed text-justify">
                                    Mendorong setiap proyek dimulai dengan perencanaan yang jelas, terstruktur, dan terukur.
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 lg:mt-5 pt-3 sm:pt-0 lg:pt-3 border-t sm:border-t-0 lg:border-t border-purple-100/70 text-[11px] font-bold text-[#604EE6] shrink-0">
                            <span class="inline-block sm:px-3 sm:py-1 sm:rounded-full sm:bg-purple-100/60 lg:p-0 lg:bg-transparent">Perencanaan Jelas</span>
                        </div>
                    </div>

                    <!-- R — Run -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-[#FAF9FE] border border-purple-100/90 hover:bg-white hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 lg:flex-col lg:items-start lg:gap-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:flex-col lg:items-start lg:gap-0">
                            <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-black text-lg shrink-0 lg:mb-4">
                                R
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 mb-1 lg:mb-2">
                                    Run
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed text-justify">
                                    Mengawal pelaksanaan proyek agar berjalan sesuai rencana, target, jadwal, dan sumber daya yang tersedia.
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 lg:mt-5 pt-3 sm:pt-0 lg:pt-3 border-t sm:border-t-0 lg:border-t border-purple-100/70 text-[11px] font-bold text-blue-600 shrink-0">
                            <span class="inline-block sm:px-3 sm:py-1 sm:rounded-full sm:bg-blue-100/60 lg:p-0 lg:bg-transparent">Pelaksanaan Tepat</span>
                        </div>
                    </div>

                    <!-- O — Observe -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-[#FAF9FE] border border-purple-100/90 hover:bg-white hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 lg:flex-col lg:items-start lg:gap-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:flex-col lg:items-start lg:gap-0">
                            <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-lg shrink-0 lg:mb-4">
                                O
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 mb-1 lg:mb-2">
                                    Observe
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed text-justify">
                                    Memastikan perkembangan proyek dapat dipantau secara berkala melalui informasi yang aktual dan terintegrasi.
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 lg:mt-5 pt-3 sm:pt-0 lg:pt-3 border-t sm:border-t-0 lg:border-t border-purple-100/70 text-[11px] font-bold text-indigo-600 shrink-0">
                            <span class="inline-block sm:px-3 sm:py-1 sm:rounded-full sm:bg-indigo-100/60 lg:p-0 lg:bg-transparent">Pemantauan Berkala</span>
                        </div>
                    </div>

                    <!-- X — eXchange -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-[#FAF9FE] border border-purple-100/90 hover:bg-white hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 lg:flex-col lg:items-start lg:gap-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:flex-col lg:items-start lg:gap-0">
                            <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center font-black text-lg shrink-0 lg:mb-4">
                                X
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 mb-1 lg:mb-2">
                                    eXchange
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed text-justify">
                                    Menjadi titik penghubung pertukaran informasi, koordinasi, dan kolaborasi antar pihak yang terlibat dalam proyek.
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 lg:mt-5 pt-3 sm:pt-0 lg:pt-3 border-t sm:border-t-0 lg:border-t border-purple-100/70 text-[11px] font-bold text-violet-600 shrink-0">
                            <span class="inline-block sm:px-3 sm:py-1 sm:rounded-full sm:bg-violet-100/60 lg:p-0 lg:bg-transparent">Kolaborasi Lintas Tim</span>
                        </div>
                    </div>

                    <!-- IS — Information System -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-[#FAF9FE] border border-purple-100/90 hover:bg-white hover:shadow-md transition-all flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 lg:flex-col lg:items-start lg:gap-0">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:flex-col lg:items-start lg:gap-0">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-lg shrink-0 lg:mb-4">
                                IS
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-800 mb-1 lg:mb-2">
                                    Information System
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed text-justify">
                                    Menghadirkan sistem informasi yang andal, transparan, dan terintegrasi untuk mendukung seluruh aktivitas pengelolaan proyek.
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0 lg:mt-5 pt-3 sm:pt-0 lg:pt-3 border-t sm:border-t-0 lg:border-t border-purple-100/70 text-[11px] font-bold text-emerald-600 shrink-0">
                            <span class="inline-block sm:px-3 sm:py-1 sm:rounded-full sm:bg-emerald-100/60 lg:p-0 lg:bg-transparent">Sistem Informasi Terpadu</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- SECTION: ALUR AKSES & AKTIVASI PEGAWAI -->
    <section id="alur-akses" class="py-16 sm:py-24 bg-[#FAF9FE]">
        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-100 text-[#604EE6] text-xs font-bold uppercase tracking-wider mb-3">
                    Tata Kelola & Alur Akses
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Alur Akses Pegawai ke Dalam Sistem
                </h2>
                <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed">
                    4 langkah sederhana bagi pegawai untuk mulai berkolaborasi dan mengelola proyek di lingkungan Direktorat Sistem Informasi Statistik.
                </p>
            </div>

            <!-- Steps Grid (4 Steps) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-6 border border-purple-100/90 shadow-xs hover:shadow-lg transition-all duration-300 relative group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-purple-50 text-[#604EE6] flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Registrasi Akun Pegawai</h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify">
                            Pegawai mendaftarkan diri dengan mengisi 18 digit NIP resmi dan alamat email resmi kantor.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-6 border border-purple-100/90 shadow-xs hover:shadow-lg transition-all duration-300 relative group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Verifikasi & Aktivasi</h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify">
                            Admin memeriksa kesesuaian data kepegawaian, mengaktifkan akun, serta memberikan hak akses pengguna.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-6 border border-purple-100/90 shadow-xs hover:shadow-lg transition-all duration-300 relative group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Penempatan Tim & Proyek</h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify">
                            Pegawai dialokasikan ke dalam Tim Kerja dan Proyek Statistik sesuai SK atau penugasan kerja yang telah ditetapkan.
                        </p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-2xl p-6 border border-purple-100/90 shadow-xs hover:shadow-lg transition-all duration-300 relative group flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Pelaporan & Pemantauan</h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify">
                            Pegawai melaporkan progres aktivitas mingguan, dan pimpinan memantau capaian proyek secara transparan dan terukur.
                        </p>
                    </div>
                </div>

            </div>



        </div>
    </section>

    <!-- SECTION: BANTUAN & FAQ -->
    <section id="faq" class="py-16 sm:py-24 bg-white border-t border-purple-100">
        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-purple-100 text-[#604EE6] text-xs font-bold uppercase tracking-wider mb-3">
                    Bantuan & Dukungan
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="mt-3 text-slate-600 text-sm sm:text-base leading-relaxed">
                    Informasi penting seputar akses sistem, persyaratan pendaftaran, dan aktivasi akun pegawai.
                </p>
            </div>

            <!-- Accordion List with Alpine.js -->
            <div class="max-w-4xl xl:max-w-5xl mx-auto space-y-3.5" x-data="{ active: null }">
                
                <!-- FAQ Item 1 -->
                <div class="border border-purple-100 rounded-2xl overflow-hidden bg-[#FAF9FE] transition-colors">
                    <button @click="active = (active === 1 ? null : 1)" class="w-full px-6 py-4.5 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:text-[#604EE6] transition">
                        <span class="text-sm sm:text-base">Apakah saya wajib menggunakan email @bps.go.id saat mendaftar?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-[#604EE6]': active === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="active === 1" x-collapse class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/60 bg-white">
                        Ya. Demi integritas dan keamanan data pegawai, sistem PROXIS hanya menerima pendaftaran akun baru yang menggunakan alamat email resmi.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border border-purple-100 rounded-2xl overflow-hidden bg-[#FAF9FE] transition-colors">
                    <button @click="active = (active === 2 ? null : 2)" class="w-full px-6 py-4.5 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:text-[#604EE6] transition">
                        <span class="text-sm sm:text-base">Mengapa akun saya belum bisa langsung digunakan setelah pendaftaran berhasil?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-[#604EE6]': active === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="active === 2" x-collapse class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/60 bg-white">
                        Sesuai tata kelola keamanan internal, setiap akun baru memiliki status awal <em>Pending Verification</em>. Admin akan memvalidasi keabsahan NIP dan menetapkan peran pengguna sebelum akses login dibuka.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border border-purple-100 rounded-2xl overflow-hidden bg-[#FAF9FE] transition-colors">
                    <button @click="active = (active === 3 ? null : 3)" class="w-full px-6 py-4.5 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:text-[#604EE6] transition">
                        <span class="text-sm sm:text-base">Berapa lama waktu proses aktivasi akun oleh Admin?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-[#604EE6]': active === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="active === 3" x-collapse class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/60 bg-white">
                        Verifikasi data akun umumnya dilakukan dalam kurun waktu 1x24 jam pada hari dan jam kerja (Senin – Jumat, pukul 08.00 – 16.00 WIB). Apabila terdapat kebutuhan mendesak untuk penugasan proyek, pegawai dapat mengonfirmasikan kepada Admin melalui narahubung resmi.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border border-purple-100 rounded-2xl overflow-hidden bg-[#FAF9FE] transition-colors">
                    <button @click="active = (active === 4 ? null : 4)" class="w-full px-6 py-4.5 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:text-[#604EE6] transition">
                        <span class="text-sm sm:text-base">Bagaimana jika saya lupa kata sandi atau mengalami kendala login?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-[#604EE6]': active === 4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="active === 4" x-collapse class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/60 bg-white">
                        Pegawai dapat menghubungi Admin secara langsung atau melalui Helpdesk dengan menyebutkan NIP dan email resmi BPS. Admin akan melakukan verifikasi identitas dan membantu reset kata sandi akun Anda secara aman.
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="border border-purple-100 rounded-2xl overflow-hidden bg-[#FAF9FE] transition-colors">
                    <button @click="active = (active === 5 ? null : 5)" class="w-full px-6 py-4.5 text-left font-bold text-slate-800 flex items-center justify-between gap-4 hover:text-[#604EE6] transition">
                        <span class="text-sm sm:text-base">Apakah sistem PROXIS dapat diakses melalui ponsel (smartphone)?</span>
                        <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="{ 'rotate-180 text-[#604EE6]': active === 5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="active === 5" x-collapse class="px-6 pb-5 pt-1 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/60 bg-white">
                        Ya. Tampilan antarmuka PROXIS telah dioptimalkan secara responsif, sehingga Anda dapat memantau status proyek, melihat riwayat pelaporan, dan menginput kemajuan aktivitas dengan nyaman langsung dari browser ponsel Anda.
                    </div>
                </div>

            </div>

            <!-- Helpdesk Contact Card -->
            <div class="mt-14 max-w-5xl xl:max-w-6xl 2xl:max-w-[1360px] mx-auto rounded-[28px] sm:rounded-3xl p-8 sm:p-10 bg-gradient-to-r from-[#604EE6] via-[#6E5DE7] to-[#7F6FF5] text-white shadow-[0_20px_45px_rgba(96,78,230,0.28)] flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider mb-2">
                        Helpdesk SIS
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black">Butuh Bantuan Lebih Lanjut?</h3>
                    <p class="text-white/85 text-xs sm:text-sm mt-1 max-w-lg leading-relaxed">
                        Admin siap membantu kendala akses akun, penugasan tim kerja, maupun kendala operasional sistem lainnya.
                    </p>
                </div>
                <div class="shrink-0 w-full sm:w-auto">
                    <a href="mailto:sis@bps.go.id" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3 rounded-full bg-white hover:bg-slate-50 text-[#604EE6] font-extrabold text-sm shadow-md transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Hubungi Helpdesk SIS</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-[#FAF9FE] text-slate-700 pt-16 pb-12 border-t border-purple-100">
        <div class="max-w-7xl xl:max-w-[1400px] 2xl:max-w-[1480px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-purple-100">
                
                <!-- Col 1: Brand & Office -->
                <div class="md:col-span-12 lg:col-span-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo_bps.png') }}" alt="Logo BPS" class="h-10 w-auto object-contain">
                        <div>
                            <span class="text-2xl font-black text-[#604EE6] tracking-tight">PROXIS</span>
                            <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Direktorat Sistem Informasi Statistik</div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-lg text-justify">
                        Sistem pengelolaan dan pemantauan proyek statistik terpadu di lingkungan Direktorat Sistem Informasi Statistik, Badan Pusat Statistik Republik Indonesia.
                    </p>
                    <div class="text-xs text-slate-600 space-y-2.5">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-[#604EE6] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <a href="https://www.google.com/maps/search/?api=1&query=Badan+Pusat+Statistik+RI,+Jl.+Dr.+Sutomo+No.+6-8,+Jakarta+Pusat" 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="hover:text-[#604EE6] hover:underline transition-colors"
                               title="Buka lokasi Kantor Pusat BPS RI di Google Maps">
                                Gedung 1 Lantai 3, Jl. Dr. Sutomo No. 6-8, Pasar Baru, Jakarta Pusat 10710
                            </a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#604EE6] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:sis@bps.go.id" class="text-[13px] hover:text-[#604EE6] hover:underline transition-colors tracking-wide" title="Kirim email ke Helpdesk SIS">
                                sis@bps.go.id
                            </a>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-[#604EE6] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:+62213841195" class="hover:text-[#604EE6] hover:underline transition-colors" title="Hubungi saluran telepon BPS RI">
                                (021) 3841195
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div class="md:col-span-6 lg:col-span-3 lg:pl-10 space-y-3">
                    <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">Navigasi Halaman</div>
                    <ul class="space-y-2.5 text-xs text-slate-600">
                        <li><a href="#beranda" class="hover:text-[#604EE6] transition-colors">Beranda</a></li>
                        <li><a href="#tentang" class="hover:text-[#604EE6] transition-colors">Tentang PROXIS</a></li>
                        <li><a href="#alur-akses" class="hover:text-[#604EE6] transition-colors">Alur Akses Pegawai</a></li>
                        <li><a href="#faq" class="hover:text-[#604EE6] transition-colors">Bantuan & FAQ</a></li>
                    </ul>
                </div>

                <!-- Col 3: Akses Sistem & Tautan Resmi -->
                <div class="md:col-span-6 lg:col-span-4 lg:pl-10 space-y-3">
                    <div class="text-xs font-bold text-slate-900 uppercase tracking-wider">Akses & Tautan Terkait</div>
                    <ul class="space-y-2.5 text-xs text-slate-600">
                        <li><a href="{{ route('login') }}" class="hover:text-[#604EE6] transition-colors">Masuk ke Sistem</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-[#604EE6] transition-colors">Pendaftaran Akun Pegawai</a></li>
                        <li><a href="https://www.bps.go.id" target="_blank" rel="noopener noreferrer" class="hover:text-[#604EE6] transition-colors">
                            Portal Resmi BPS RI
                        </a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 text-xs text-slate-500 text-center sm:text-left">
                © 2026 Direktorat Sistem Informasi Statistik. Hak Cipta Dilindungi Undang-Undang.
            </div>

        </div>
    </footer>

</body>
</html>
