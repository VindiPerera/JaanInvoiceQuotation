@props(['label', 'name', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'error' => null])

@php
$hasError = $errors->has($name) || $error;
$errorMessage = $errors->first($name) ?? $error;
@endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold text-slate-700">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-4 py-2.5 border rounded-lg transition ' . ($hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-blue-500 focus:ring-blue-200') . ' focus:outline-none focus:ring-2']) }}
    >

    @if($hasError)
        <p class="text-sm text-red-600 font-medium flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errorMessage }}
        </p>
    @endif
</div>
