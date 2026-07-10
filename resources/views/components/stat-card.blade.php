@props(['title', 'value', 'change' => null, 'icon' => null, 'color' => 'blue'])

@php
$colorClasses = [
    'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon-bg' => 'bg-blue-100', 'border' => 'border-blue-200'],
    'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon-bg' => 'bg-green-100', 'border' => 'border-green-200'],
    'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon-bg' => 'bg-amber-100', 'border' => 'border-amber-200'],
    'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon-bg' => 'bg-red-100', 'border' => 'border-red-200'],
    'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'icon-bg' => 'bg-indigo-100', 'border' => 'border-indigo-200'],
];
$colors = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs hover:shadow-md transition-all duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-slate-600 mb-1">{{ $title }}</p>
            <p class="text-3xl font-bold text-slate-900 mb-2">{{ $value }}</p>
            @if($change)
                <div class="flex items-center gap-1 text-xs font-semibold {{ strpos($change, '-') === false ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas {{ strpos($change, '-') === false ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ $change }}
                </div>
            @endif
        </div>
        @if($icon)
            <div class="w-14 h-14 {{ $colors['icon-bg'] }} rounded-xl flex items-center justify-center {{ $colors['text'] }} text-xl">
                <i class="fas {{ $icon }}"></i>
            </div>
        @endif
    </div>
</div>
