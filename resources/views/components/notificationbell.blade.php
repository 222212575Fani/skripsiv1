@props(['white' => false])

@php
    $unreadCountInitial = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $initialNotifications = auth()->check() ? auth()->user()->notifications()->latest()->take(20)->get()->map(function($n) {
        return [
            'id' => $n->id,
            'category' => $n->data['category'] ?? 'PROXIS',
            'title' => $n->data['name'] ?? 'Notifikasi',
            'message' => $n->data['message'] ?? '',
            'time' => $n->created_at->locale('id')->diffForHumans(),
            'is_unread' => is_null($n->read_at),
        ];
    })->values()->all() : [];
@endphp

<div class="relative" 
     x-data="{ 
        openNotif: false, 
        hasUnread: {{ $unreadCountInitial > 0 ? 'true' : 'false' }},
        unreadCount: {{ $unreadCountInitial }},
        notifications: {{ Js::from($initialNotifications) }},
        pollingTimer: null,

        getIconType(title) {
            const t = (title || '').toLowerCase();
            if (t.includes('selesai') || t.includes('disetujui') || t.includes('sukses') || t.includes('berhasil') || t.includes('completed')) return 'check';
            if (t.includes('peringatan') || t.includes('ditolak') || t.includes('batal') || t.includes('darurat') || t.includes('suspended')) return 'warning';
            if (t.includes('pengguna') || t.includes('akun') || t.includes('registrasi') || t.includes('aktivasi') || t.includes('user') || t.includes('anggota')) return 'user';
            if (t.includes('tenggat') || t.includes('jadwal') || t.includes('waktu') || t.includes('tanggal') || t.includes('event')) return 'calendar';
            if (t.includes('aktivitas') || t.includes('tugas') || t.includes('proyek') || t.includes('dokumen') || t.includes('task')) return 'task';
            return 'bell';
        },

        formatTitle(title) {
            if (!title) return '';
            return title.replace(/:\s*$/, '').trim();
        },

        init() {
            // Polling background berkala setiap 30 detik agar server tetap ringan & cepat
            this.pollingTimer = setInterval(() => {
                if (!document.hidden) {
                    this.fetchNotifications();
                }
            }, 30000);

            // Cek otomatis saat pengguna kembali membuka tab ini
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.fetchNotifications();
                }
            });
        },

        fetchNotifications() {
            fetch('{{ route('notifications.check') }}', {
                headers: { 
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.unreadCount = data.unread_count;
                this.hasUnread = data.has_unread;
                if (data.notifications) {
                    this.notifications = data.notifications;
                }
            })
            .catch(err => console.error('Error fetching notifications:', err));
        },

        markAsRead() {
            this.openNotif = !this.openNotif;
            // Ketika lonceng dibuka, hilangkan dot merah secara instan dan update status di database
            if (this.openNotif && this.hasUnread) {
                this.hasUnread = false;
                this.unreadCount = 0;
                this.notifications = this.notifications.map(n => ({ ...n, is_unread: false }));

                fetch('{{ route('notifications.readAll') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).catch(err => console.error('Error mark as read:', err));
            }
        }
     }">
    
    {{-- Tombol Lonceng Notifikasi --}}
    <button type="button" @click="markAsRead()" 
            class="focus:outline-none flex items-center justify-center relative group cursor-pointer p-1.5 rounded-full transition-colors {{ $white ? 'hover:bg-white/10' : 'hover:bg-purple-50/60' }}" 
            title="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6 transition-colors {{ $white ? 'text-white group-hover:text-white/80' : 'text-[#6E5BC3] group-hover:text-[#5C4AB5]' }}" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        {{-- DOT MERAH AKTIF (Muncul saat ada notif baru, hilang saat dibuka) --}}
        <span x-show="hasUnread" x-cloak 
              class="absolute top-1 right-1 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 {{ $white ? 'ring-[#6E5BC3]' : 'ring-white' }} animate-pulse">
        </span>
    </button>

    {{-- Dropdown Panel Daftar Notifikasi --}}
    <div x-show="openNotif" x-cloak @click.outside="openNotif = false" x-transition 
         class="fixed sm:absolute top-16 sm:top-auto right-3 sm:right-0 mt-2 sm:mt-3 w-[calc(100vw-1.5rem)] max-w-xs sm:max-w-none sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 text-left">
        
        {{-- Header Panel (Sesuai Referensi Gambar) --}}
        <div class="px-5 py-3.5 border-b border-gray-100 bg-white flex items-center justify-between">
            <div>
                <h3 class="text-[13.5px] font-bold text-gray-900 leading-snug">Notifikasi</h3>
                <p class="text-[11px] text-gray-500 font-normal mt-0.5">
                    <span x-show="unreadCount > 0" x-text="`Anda memiliki ${unreadCount} notifikasi baru.`"></span>
                    <span x-show="unreadCount === 0">Semua notifikasi telah dibaca.</span>
                </p>
            </div>
            <div class="text-gray-400 p-1" title="Kotak Masuk">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
        </div>

        {{-- Isi Daftar Notifikasi Sesuai Desain Timeline --}}
        <div class="max-h-[380px] overflow-y-auto custom-scrollbar p-2">
            <template x-if="notifications.length > 0">
                <div>
                    <template x-for="(item, index) in notifications" :key="item.id">
                        <div class="relative px-3 py-2.5 rounded-xl transition-colors flex items-start gap-3 hover:bg-slate-50/80 cursor-pointer"
                             :class="item.is_unread ? 'bg-[#F8F7FF]/60' : 'bg-transparent'">
                            
                            {{-- Kolom Kiri: Ikon Melingkar + Garis Timeline Penghubung --}}
                            <div class="relative flex flex-col items-center shrink-0 self-stretch pt-0.5">
                                {{-- Lingkaran Ikon (Border abu halus, background putih) --}}
                                <div class="w-8 h-8 rounded-full border border-gray-200 bg-white flex items-center justify-center shrink-0 z-10 shadow-xs">
                                    {{-- Checkmark / Selesai (Emerald) --}}
                                    <template x-if="getIconType(item.title) === 'check'">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>

                                    {{-- Kalender / Jadwal (Biru) --}}
                                    <template x-if="getIconType(item.title) === 'calendar'">
                                        <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </template>

                                    {{-- Peringatan / Ditolak / Suspended (Kuning / Oranye) --}}
                                    <template x-if="getIconType(item.title) === 'warning'">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </template>

                                    {{-- Pengguna / Registrasi Akun (Indigo / Ungu) --}}
                                    <template x-if="getIconType(item.title) === 'user'">
                                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </template>

                                    {{-- Dokumen / Tugas / Aktivitas (Teal) --}}
                                    <template x-if="getIconType(item.title) === 'task'">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </template>

                                    {{-- Lonceng / Notifikasi Umum (Amber) --}}
                                    <template x-if="getIconType(item.title) === 'bell'">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </template>
                                </div>

                                {{-- Garis Penghubung Vertikal (Timeline) Antar Lingkaran --}}
                                <div x-show="index < notifications.length - 1" 
                                     class="w-px flex-1 border-l border-dashed border-gray-200 mt-1 mb-0">
                                </div>
                            </div>

                            {{-- Kolom Kanan: Konten Notifikasi --}}
                            <div class="flex-1 min-w-0 pb-1 pt-0.5">
                                {{-- Baris Atas: Kategori (PROXIS Tidak Miring) & Waktu di Pojok Kanan --}}
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        {{-- Kategori (TIDAK Miring / Non-italic, Ukuran Pas) --}}
                                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider not-italic select-none block leading-none mb-1" 
                                              x-text="item.category || 'PROXIS'"></span>
                                        {{-- Judul Notifikasi (TANPA Titik Dua, Ukuran Ringkas Semibold) --}}
                                        <h4 class="text-[12.5px] font-semibold text-gray-800 leading-snug wrap-break-word" 
                                            x-text="formatTitle(item.title)"></h4>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0 pt-0.5">
                                        <span class="text-[11px] text-gray-400 font-normal select-none whitespace-nowrap" 
                                              x-text="item.time"></span>
                                        {{-- Indikator Belum Dibaca --}}
                                        <span x-show="item.is_unread" class="w-1.5 h-1.5 rounded-full bg-[#604EE6] shrink-0" title="Belum dibaca"></span>
                                    </div>
                                </div>

                                {{-- Pesan Notifikasi (Ukuran Proporsional & Rapi) --}}
                                <p class="text-[11.5px] text-gray-500 font-normal leading-relaxed mt-1 wrap-break-word" 
                                   x-text="item.message"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="notifications.length === 0">
                <div class="py-10 px-4 text-center flex flex-col items-center justify-center gap-2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="text-xs font-medium">Belum ada notifikasi masuk</span>
                </div>
            </template>
        </div>
    </div>
</div>

