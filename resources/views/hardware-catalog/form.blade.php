@extends('layouts.app')
@section('title', $item ? 'Edit Hardware Item' : 'Add Hardware Item')
@section('breadcrumb', $item ? 'Edit: ' . $item->name : 'Add a new hardware item to the catalog')

@section('content')

<form method="POST"
      action="{{ $item ? route('hardware-catalog.update', $item) : route('hardware-catalog.store') }}"
      class="max-w-2xl space-y-6">
    @csrf
    @if($item) @method('PUT') @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Item Details</h2>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Item Name *</label>
            <input type="text" name="name" value="{{ old('name', $item?->name) }}" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                placeholder="e.g. Core i5 Desktop PC">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Description <span class="text-gray-400 font-normal">(shown in quotation)</span></label>
            <textarea name="description" rows="4"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 leading-snug"
                placeholder="• Spec one&#10;• Spec two">{{ old('description', $item?->description) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Unit Price (LKR) *</label>
            <input type="number" name="unit_price" value="{{ old('unit_price', $item?->unit_price ?? '0') }}"
                step="0.01" min="0" required
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Warranty <span class="text-gray-400 font-normal">(optional, e.g. "1 Year")</span></label>
            <input type="text" name="warranty" value="{{ old('warranty', $item?->warranty) }}"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"
                placeholder="e.g. 1 Year, 2 Years, Lifetime">
        </div>

        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" id="is_active" name="is_active" value="1"
                {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}
                class="rounded border-gray-300 text-red-600 focus:ring-red-300">
            <label for="is_active" class="text-sm text-gray-700">Active (visible in template dropdown)</label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            {{ $item ? 'Update Item' : 'Save Item' }}
        </button>
        <a href="{{ route('hardware-catalog.index') }}"
           class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            Cancel
        </a>
    </div>
</form>

@endsection
