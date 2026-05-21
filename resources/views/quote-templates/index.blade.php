@extends('layouts.app')
@section('title', 'Quote Templates')
@section('breadcrumb', 'Manage quote templates for quotations')

@section('header-actions')
    <a href="{{ route('quote-templates.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-plus"></i> New Template
    </a>
@endsection

@section('content')

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

<div class="space-y-3">
    @forelse($templates as $template)
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                    <i class="fa-solid {{ $template->icon ?: 'fa-file-alt' }} text-red-600 text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-gray-800">{{ $template->name }}</h3>
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded font-mono">{{ $template->key }}</span>
                    </div>
                    @if($template->subtitle)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $template->subtitle }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 mt-1.5 text-xs text-gray-400">
                        @if($template->hardware_items && count($template->hardware_items))
                        <span><i class="fa-solid fa-microchip mr-1"></i>{{ count($template->hardware_items) }} hardware item(s)</span>
                        @endif
                        @if($template->software_features && count($template->software_features))
                        <span><i class="fa-solid fa-check mr-1"></i>{{ count($template->software_features) }} feature row(s)</span>
                        @endif
                        @if($template->additional_benefits && count($template->additional_benefits))
                        <span><i class="fa-solid fa-star mr-1"></i>{{ count($template->additional_benefits) }} benefit row(s)</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('quote-templates.edit', $template) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <i class="fa-solid fa-pencil text-xs"></i> Edit
                </a>
                <form method="POST" action="{{ route('quote-templates.destroy', $template) }}"
                      onsubmit="return confirm('Delete this template? Existing quotations using it will not be affected.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                        <i class="fa-solid fa-trash text-xs"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <i class="fa-solid fa-layer-group text-gray-200 text-4xl mb-3"></i>
        <p class="text-gray-500 text-sm">No templates yet. Create one to get started.</p>
        <a href="{{ route('quote-templates.create') }}" class="mt-4 inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
            <i class="fa-solid fa-plus"></i> Create Template
        </a>
    </div>
    @endforelse
</div>
@endsection
