@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'loading' => false])

@php
$baseClasses = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-300 shadow-sm hover:shadow-md',
    'secondary' => 'bg-slate-600 text-white hover:bg-slate-700 focus:ring-slate-300 shadow-sm hover:shadow-md',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-300 shadow-sm hover:shadow-md',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-300 shadow-sm hover:shadow-md',
    'warning' => 'bg-amber-600 text-white hover:bg-amber-700 focus:ring-amber-300 shadow-sm hover:shadow-md',
    'outline' => 'border-2 border-slate-300 text-slate-700 hover:border-slate-400 hover:bg-slate-50 focus:ring-slate-300',
    'ghost' => 'text-slate-700 hover:bg-slate-100 focus:ring-slate-300',
];

$sizes = [
    'xs' => 'px-3 py-1.5 text-xs',
    'sm' => 'px-4 py-2 text-sm',
    'md' => 'px-5 py-2.5 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
];

$variantClass = $variants[$variant] ?? $variants['primary'];
$sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if($attributes->has('href'))
    <a {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass"]) }}>
        @if($loading)
            <i class="fas fa-spinner animate-spin"></i>
        @elseif($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass"]) }} {{ $loading ? 'disabled' : '' }}>
        @if($loading)
            <i class="fas fa-spinner animate-spin"></i>
        @elseif($icon)
            <i class="fas {{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
@endif
