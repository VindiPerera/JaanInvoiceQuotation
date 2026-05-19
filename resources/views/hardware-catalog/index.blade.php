@extends('layouts.app')
@section('title', 'Hardware Catalog')
@section('breadcrumb', 'Manage hardware items and prices')

@section('content')

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-xl border border-gray-200">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-gray-800">Hardware Items</h2>
        <a href="{{ route('hardware-catalog.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            <i class="fa-solid fa-plus"></i> Add Item
        </a>
    </div>

    @if($items->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="fa-solid fa-microchip text-4xl mb-3"></i>
        <p class="text-sm">No hardware items yet. <a href="{{ route('hardware-catalog.create') }}" class="text-red-600 hover:underline">Add the first item.</a></p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">Unit Price</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wide">Active</th>
                    <th class="px-6 py-3 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($items as $item)
                <tr class="hover:bg-gray-50 transition {{ $item->is_active ? '' : 'opacity-50' }}">
                    <td class="px-6 py-3 font-medium text-gray-800">{{ $item->name }}</td>
                    <td class="px-6 py-3 text-gray-500">
                        @if($item->category)
                        <span class="inline-block bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $item->category }}</span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-gray-500 max-w-xs truncate">{{ $item->description ?? '—' }}</td>
                    <td class="px-6 py-3 text-right font-medium text-gray-800">
                        LKR {{ number_format($item->unit_price, 2) }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        @if($item->is_active)
                        <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                        @else
                        <span class="inline-block w-2 h-2 rounded-full bg-gray-300"></span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('hardware-catalog.edit', $item) }}"
                               class="text-gray-400 hover:text-red-600 transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                            <form method="POST" action="{{ route('hardware-catalog.destroy', $item) }}"
                                  onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-300 hover:text-red-600 transition" title="Delete">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
