@props(['label', 'value', 'color' => 'indigo'])

@php
    $colorClasses = [
        'indigo' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
        'emerald' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
        'amber' => 'bg-amber-50 text-amber-600 border-amber-100',
    ][$color] ?? 'bg-gray-50 text-gray-600 border-gray-100';
@endphp

<div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $label }}</span>
    <div class="flex items-baseline justify-between mt-4">
        <span class="text-3xl font-black text-gray-900 tracking-tight">{{ $value }}</span>
        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $colorClasses }}">
            Aktif
        </span>
    </div>
</div>