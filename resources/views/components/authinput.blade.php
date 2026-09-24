@props(['label', 'type' => 'text', 'id', 'name', 'placeholder', 'value' => '', 'required' => false])

<div class="mb-3.5 sm:mb-4">
    <label for="{{ $id }}" class="block text-xs sm:text-[13px] font-bold text-slate-700 mb-1.5">
        {{ $label }}
    </label>

    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'w-full h-[42px] sm:h-[44px] rounded-lg sm:rounded-[10px] border border-[#D7D7D7] bg-white pl-11 pr-11 text-xs sm:text-[13px] font-light text-slate-800 placeholder:text-gray-400 placeholder:font-light outline-none focus:ring-2 focus:ring-[#604EE6]/20 focus:border-[#604EE6] transition-all']) }}
            @if($required) required @endif
        >

        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
            {{ $icon }}
        </span>

        @if($type === 'password')
            <button 
                type="button" 
                onclick="togglePassword('{{ $id }}', this)"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#604EE6] transition p-1 cursor-pointer focus:outline-none"
                tabindex="-1"
                title="Lihat / Sembunyikan Password"
            >
                <svg class="eye-open w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="eye-closed w-[17px] h-[17px] hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
            </button>
        @endif
    </div>

    @isset($hint)
        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
            {{ $hint }}
        </div>
    @endisset

    @isset($error)
        {{ $error }}
    @else
        @error($name)
            <div class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-rose-600">
                <svg class="w-3.5 h-3.5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $message }}</span>
            </div>
        @enderror
    @endisset
</div>