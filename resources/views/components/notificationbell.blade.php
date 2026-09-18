@props(['white' => false])

@php
    $unreadCountInitial = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $initialNotifications = auth()->check() ? auth()->user()->notifications()->latest()->take(15)->get()->map(function($n) {
        return [
            'id' => $n->id,
            'title' => $n->data['name'] ?? 'Notifikasi',
            'message' => $n->data['message'] ?? '',
            'time' => $n->created_at->diffForHumans(),
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
         class="absolute right-0 mt-3 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50 text-left">
        
        {{-- Header Panel --}}
        <div class="p-4 border-b border-gray-50 bg-gray-50/60 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Notifikasi</span>
                <span x-show="unreadCount > 0" x-text="`${unreadCount} Baru`" 
                      class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-full">
                </span>
            </div>
            <span class="text-[11px] text-gray-400 font-medium">Terbaru</span>
        </div>

        {{-- Isi Daftar Notifikasi --}}
        <div class="p-3 max-h-80 overflow-y-auto space-y-2 custom-scrollbar">
            <template x-if="notifications.length > 0">
                <div class="space-y-2">
                    <template x-for="item in notifications" :key="item.id">
                        <div class="p-3 rounded-xl transition-all flex flex-col gap-1 border"
                             :class="item.is_unread ? 'bg-purple-50/70 border-purple-200/70 shadow-2xs' : 'bg-gray-50/40 border-gray-100/80'">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span x-show="item.is_unread" class="w-1.5 h-1.5 rounded-full bg-[#6E5BC3] inline-block shrink-0"></span>
                                    <p class="text-xs font-bold text-gray-800" x-text="item.title"></p>
                                </div>
                                <span class="text-[9px] text-gray-400 shrink-0 font-medium" x-text="item.time"></span>
                            </div>
                            <p class="text-[11px] text-gray-600 leading-relaxed pl-3" x-text="item.message"></p>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="notifications.length === 0">
                <div class="py-8 text-center text-xs text-gray-400 italic">
                    Belum ada notifikasi masuk.
                </div>
            </template>
        </div>
    </div>
</div>

