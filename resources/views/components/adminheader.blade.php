@props(['title'])

<header class="h-20 bg-white flex items-center justify-between px-10 border-b border-gray-100 shadow-sm relative z-30">
    <h2 class="text-xl font-extrabold text-[#5C46F5]">{{ $title }}</h2>
    
    <div class="relative">
        <button id="profileBtn" class="flex items-center gap-4 pl-6 border-l border-gray-200 focus:outline-none hover:opacity-80 transition-opacity">
            <div class="text-right">
                <p class="text-sm font-bold text-gray-900 leading-tight">{{ Auth::user()->nama ?? 'Admin' }}</p>
                <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest text-right mt-0.5">
                    {{ Auth::user()->role->nama_role ?? 'Administrator' }}
                </p>
            </div>
            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold shadow-lg shadow-[#5C46F5]/20">
                {{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 1)) }}
            </div>
        </button>

        <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transition-all transform origin-top-right">
            <div class="p-5 flex items-center gap-4 border-b border-gray-50 bg-gray-50/50">
                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#5C46F5] to-[#8FD0FF] flex items-center justify-center text-white font-bold text-lg shadow-md">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-extrabold text-gray-900 leading-tight">{{ Auth::user()->nama ?? 'Admin' }}</p>
                    <p class="text-[10px] font-bold text-[#5C46F5] uppercase tracking-widest mt-0.5">{{ Auth::user()->role->nama_role ?? 'Administrator' }}</p>
                </div>
            </div>
            <div class="p-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 hover:text-red-600 rounded-xl transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout Sistem
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => { 
                profileDropdown.classList.toggle('hidden'); 
                e.stopPropagation(); 
            });
            document.addEventListener('click', (e) => { 
                if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                    profileDropdown.classList.add('hidden'); 
                }
            });
        }
    });
</script>
@endpush