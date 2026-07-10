@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('breadcrumb', 'Quotations / ' . $quotation->quotation_number)

@section('header-actions')
    <x-button href="{{ route('quotations.pdf', $quotation) }}" variant="danger" icon="fa-file-pdf">
        Download PDF
    </x-button>
    <x-button href="{{ route('quotations.edit', $quotation) }}" variant="secondary" icon="fa-pencil">
        Edit
    </x-button>
    <x-button href="{{ route('quotations.convert', $quotation) }}" variant="success" icon="fa-arrow-right-arrow-left">
        Convert to Invoice
    </x-button>
@endsection

@section('content')
<div class="space-y-6 max-w-4xl">
    {{-- Quotation Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card
            title="Quotation Total"
            value="LKR {{ number_format($quotation->total_amount) }}"
            color="blue"
        />
        <x-stat-card
            title="Status"
            value="{{ ucfirst($quotation->status) }}"
            color="indigo"
        />
        <x-stat-card
            title="Date"
            value="{{ $quotation->quotation_date->format('d M Y') }}"
            color="slate"
        />
    </div>

    {{-- Quotation Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Bill To --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Bill To</h3>
            <div class="space-y-2">
                <p class="text-sm text-slate-600"><span class="font-semibold text-slate-900">{{ $quotation->customer_name }}</span></p>
                @if($quotation->customer_address)
                    <p class="text-sm text-slate-600">{{ $quotation->customer_address }}</p>
                @endif
                @if($quotation->customer_contact)
                    <p class="text-sm text-slate-600">{{ $quotation->customer_contact }}</p>
                @endif
            </div>
        </div>

        {{-- Quotation Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Quotation Details</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600">Quotation Number</span>
                    <span class="font-semibold text-slate-900">{{ $quotation->quotation_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Quotation Date</span>
                    <span class="font-semibold text-slate-900">{{ $quotation->quotation_date->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">Status</span>
                    <span class="font-bold">
                        @if($quotation->status === 'accepted')
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Accepted</span>
                        @elseif($quotation->status === 'rejected')
                            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs">Rejected</span>
                        @elseif($quotation->status === 'sent')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Sent</span>
                        @elseif($quotation->status === 'finalized')
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">Finalized</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-xs">Draft</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Overview --}}
    @if($quotation->project_overview)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Project Overview</h3>
        <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $quotation->project_overview }}</p>
    </div>
    @endif

    {{-- Line Items Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900">Line Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-700">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-700">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-700">Unit Price</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-700">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotation->items->where('is_hidden', false) as $item)
                    <tr class="border-b border-slate-200 hover:bg-slate-50">
                        <td class="px-6 py-3 font-semibold text-slate-900">{{ $item->item_number }}</td>
                        <td class="px-6 py-3">
                            <div class="font-medium text-slate-900">{{ $item->item_name }}</div>
                            @if($item->description)
                                <div class="text-xs text-slate-500">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">{{ number_format((float)$item->quantity, 0) }}</td>
                        <td class="px-6 py-3 text-right">LKR {{ number_format((float)$item->unit_price) }}</td>
                        <td class="px-6 py-3 text-right font-bold text-slate-900">LKR {{ number_format((float)$item->total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No items to display</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="bg-slate-50 border-t border-slate-200 p-6">
            <div class="flex justify-end max-w-sm space-y-3">
                <div class="w-full">
                    <div class="flex justify-between pt-2">
                        <span class="font-bold text-slate-900 text-lg">Total (LKR)</span>
                        <span class="font-bold text-blue-600 text-lg">{{ number_format($quotation->total_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terms and Conditions --}}
    @if($quotation->terms_and_conditions)
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Terms & Conditions</h3>
        <p class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{{ $quotation->terms_and_conditions }}</p>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex gap-3">
        <x-button href="{{ route('quotations.index') }}" variant="outline" icon="fa-arrow-left">
            Back to Quotations
        </x-button>
        <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" class="inline" onsubmit="return confirm('Delete this quotation?');">
            @csrf @method('DELETE')
            <x-button type="submit" variant="danger" icon="fa-trash">
                Delete Quotation
            </x-button>
        </form>
    </div>
</div>
@endsection
