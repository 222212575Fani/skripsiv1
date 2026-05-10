@props(['label', 'type' => 'text', 'id', 'name', 'placeholder', 'value' => ''])

<div class="mb-6">
    <label for="{{ $id }}" class="block text-[14px] font-semibold text-[#232323] mb-2">
        {{ $label }}
    </label>

    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => 'w-full h-[46px] rounded-[10px] border border-[#D7D7D7] bg-white pl-12 pr-11 text-[14px] text-[#333] placeholder:text-[#A0A0A0] outline-none focus:ring-2 focus:ring-[#6B56FF]/20 focus:border-[#6B56FF]']) }}
            required
        >

        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#9B9B9B]">
            {{ $icon }}
        </span>

        @if($type === 'password')
            <button 
                type="button" 
                onclick="togglePassword('{{ $id }}', this)"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#9B9B9B] hover:text-[#6B56FF] transition"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-[17px] h-[17px]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        @endif
    </div>
</div>