@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('breadcrumb', 'Quotations / ' . $quotation->quotation_number)

@section('header-actions')
    <a href="{{ route('quotations.pdf', $quotation) }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-file-pdf"></i> Download PDF
    </a>
    <a href="{{ route('quotations.edit', $quotation) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-pencil"></i> Edit
    </a>
    <a href="{{ route('quotations.convert', $quotation) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Convert to Invoice
    </a>
@endsection

@section('content')
<div class="max-w-4xl space-y-6">

    {{-- Preview card --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        {{-- Header stripe --}}
        <div class="bg-red-600 h-2"></div>
        <div class="p-8">
            {{-- Logo row --}}
            <div class="flex items-start justify-between mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-10 h-10 bg-red-600 rounded flex items-center justify-center">
                            <span class="text-white font-black text-sm">JN</span>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-lg leading-none">JAAN</p>
                            <p class="font-bold text-gray-900 text-sm leading-none">Network</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">{{ $settings['company_address'] ?? '' }}</p>
                    <p class="text-xs text-gray-500">{{ $settings['company_website'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_phone'] ?? '' }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-gray-900 text-white text-xs px-3 py-1 rounded mb-2 inline-block">
                        Quotation No: {{ $quotation->quotation_number }}
                    </div>
                    <p class="text-4xl font-black text-gray-900">QUOTATION</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-6">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Prepared For</p>
                    <p class="font-bold text-gray-900">{{ $quotation->customer_name }}</p>
                    @if($quotation->customer_address)
                        <p class="text-sm text-gray-600">{{ $quotation->customer_address }}</p>
                    @endif
                    @if($quotation->customer_contact)
                        <p class="text-sm text-gray-600">{{ $quotation->customer_contact }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Prepared Date</p>
                    <p class="font-semibold text-gray-900">{{ $quotation->quotation_date->format('d/m/Y') }}</p>
                    @if($quotation->subject)
                        <p class="text-sm text-gray-600 mt-2">{{ $quotation->subject }}</p>
                    @endif
                </div>
            </div>

            @if($quotation->items->count())
            <div class="mb-6">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-red-600 text-white">
                            <th class="px-4 py-2 text-left font-semibold">Item</th>
                            <th class="px-4 py-2 text-left font-semibold">Description</th>
                            <th class="px-4 py-2 text-center font-semibold w-16">Qty</th>
                            <th class="px-4 py-2 text-right font-semibold w-28">Price</th>
                            <th class="px-4 py-2 text-right font-semibold w-28">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $i => $item)
                        <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="px-4 py-2 text-gray-600">{{ $item->item_number }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ $item->description }}</td>
                            <td class="px-4 py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right text-gray-600">{{ number_format($item->unit_price) }}</td>
                            <td class="px-4 py-2 text-right font-medium text-gray-800">{{ number_format($item->total) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @if($quotation->tax_amount > 0)
                        <tr class="border-t border-gray-200">
                            <td colspan="4" class="px-4 py-2 text-right text-gray-600 font-medium">Subtotal</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ number_format($quotation->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-gray-600 font-medium">Tax</td>
                            <td class="px-4 py-2 text-right font-semibold">{{ number_format($quotation->tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="bg-gray-100">
                            <td colspan="4" class="px-4 py-3 text-right font-bold text-gray-900">LKR TOTAL</td>
                            <td class="px-4 py-3 text-right font-black text-red-600 text-base">{{ number_format($quotation->total_amount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif

            @if(!empty($quotation->software_features))
            <div class="mb-4">
                <h3 class="font-bold text-red-600 text-sm uppercase tracking-wide mb-2">Software Features</h3>
                <div class="space-y-0.5">
                    @foreach($quotation->software_features as $f)
                        @php $kind = is_array($f) ? ($f['kind'] ?? 'item') : 'item'; $text = is_array($f) ? ($f['text'] ?? '') : $f; @endphp
                        @if($kind === 'space')
                            <div class="py-1"><div class="border-t border-dashed border-gray-200"></div></div>
                        @elseif($kind === 'heading')
                            <div class="font-semibold text-gray-800 text-sm pt-1">{{ $text }}</div>
                        @else
                            <div class="flex items-start gap-2 text-sm text-gray-700 pl-2">
                                <i class="fa-solid fa-check text-red-500 mt-0.5 shrink-0 text-xs"></i>{{ $text }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($quotation->additional_benefits))
            <div class="mb-4">
                <h3 class="font-bold text-red-600 text-sm uppercase tracking-wide mb-2">Additional Benefits</h3>
                <div class="space-y-0.5">
                    @foreach($quotation->additional_benefits as $b)
                        @php $kind = is_array($b) ? ($b['kind'] ?? 'item') : 'item'; $text = is_array($b) ? ($b['text'] ?? '') : $b; @endphp
                        @if($kind === 'space')
                            <div class="py-1"><div class="border-t border-dashed border-gray-200"></div></div>
                        @elseif($kind === 'heading')
                            <div class="font-semibold text-gray-800 text-sm pt-1">{{ $text }}</div>
                        @else
                            <div class="flex items-start gap-2 text-sm text-gray-700 pl-2">
                                <i class="fa-solid fa-circle text-red-400 text-xs mt-1.5 shrink-0"></i>{{ $text }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($quotation->terms_conditions)
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="font-bold text-gray-800 text-sm mb-2">Terms & Conditions</h3>
                <pre class="text-xs text-gray-600 whitespace-pre-wrap font-sans">{{ $quotation->terms_conditions }}</pre>
            </div>
            @endif
        </div>
        <div class="bg-red-600 h-2"></div>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('quotations.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to list
        </a>
        <form method="POST" action="{{ route('quotations.destroy', $quotation) }}" onsubmit="return confirm('Delete this quotation?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700">
                <i class="fa-solid fa-trash mr-1"></i> Delete
            </button>
        </form>
    </div>
</div>
@endsection
