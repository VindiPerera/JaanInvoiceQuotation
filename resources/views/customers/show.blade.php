@extends('layouts.app')
@section('title', $customer->name)
@section('breadcrumb', 'Customers / ' . $customer->name)

@section('header-actions')
    <a href="{{ route('quotations.create') }}?customer_id={{ $customer->id }}"
       class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-file-invoice"></i> New Quotation
    </a>
    <a href="{{ route('invoices.create') }}?customer_id={{ $customer->id }}"
       class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-receipt"></i> New Invoice
    </a>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl">

    {{-- Customer info card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 font-bold text-lg">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $customer->name }}</h2>
                    @if($customer->contact)
                        <p class="text-sm text-gray-500"><i class="fa-solid fa-phone text-xs mr-1"></i>{{ $customer->contact }}</p>
                    @endif
                    @if($customer->email)
                        <p class="text-sm text-gray-500"><i class="fa-solid fa-envelope text-xs mr-1"></i>{{ $customer->email }}</p>
                    @endif
                    @if($customer->address)
                        <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-location-dot text-xs mr-1"></i>{{ $customer->address }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-center px-4 py-2 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-900">{{ $customer->quotations->count() }}</p>
                    <p class="text-xs text-gray-500">Quotations</p>
                </div>
                <div class="text-center px-4 py-2 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-900">{{ $customer->invoices->count() }}</p>
                    <p class="text-xs text-gray-500">Invoices</p>
                </div>
                <div class="text-center px-4 py-2 bg-red-50 rounded-lg">
                    <p class="text-xl font-bold text-red-600">LKR {{ number_format($customer->invoices->sum('total_amount')) }}</p>
                    <p class="text-xs text-gray-500">Total Revenue</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quotations --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Quotations</h3>
            <a href="{{ route('quotations.index') }}?customer={{ $customer->name }}" class="text-xs text-red-600 hover:underline">View all</a>
        </div>
        @if($customer->quotations->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Number</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Subject</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customer->quotations->sortByDesc('created_at') as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('quotations.show', $q) }}" class="font-semibold text-red-600 hover:underline">{{ $q->quotation_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $q->quotation_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $q->subject ?: '—' }}</td>
                        <td class="px-5 py-3 text-right font-semibold">LKR {{ number_format($q->total_amount) }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'text-xs px-2.5 py-1 rounded-full font-medium',
                                'bg-green-100 text-green-700'   => $q->status === 'accepted',
                                'bg-red-100 text-red-700'       => $q->status === 'rejected',
                                'bg-blue-100 text-blue-700'     => $q->status === 'sent',
                                'bg-purple-100 text-purple-700' => $q->status === 'finalized',
                                'bg-gray-100 text-gray-600'     => $q->status === 'draft',
                            ])>{{ ucfirst($q->status) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('quotations.pdf', $q) }}" class="text-gray-400 hover:text-red-600 text-xs">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-5 py-6 text-sm text-gray-400 text-center">No quotations for this customer.</p>
        @endif
    </div>

    {{-- Invoices --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Invoices</h3>
            <a href="{{ route('invoices.index') }}?customer={{ $customer->name }}" class="text-xs text-red-600 hover:underline">View all</a>
        </div>
        @if($customer->invoices->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Number</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Balance</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($customer->invoices->sortByDesc('created_at') as $inv)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-semibold text-red-600 hover:underline">{{ $inv->invoice_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right font-semibold">LKR {{ number_format($inv->total_amount) }}</td>
                        <td class="px-5 py-3 text-right {{ $inv->balance > 0 ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                            LKR {{ number_format($inv->balance) }}
                        </td>
                        <td class="px-5 py-3">
                            <span @class([
                                'text-xs px-2.5 py-1 rounded-full font-medium',
                                'bg-green-100 text-green-700'   => $inv->payment_status === 'paid',
                                'bg-orange-100 text-orange-700' => $inv->payment_status === 'partial',
                                'bg-red-100 text-red-700'       => $inv->payment_status === 'pending',
                            ])>{{ ucfirst($inv->payment_status) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('invoices.pdf', $inv) }}" class="text-gray-400 hover:text-red-600 text-xs">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-5 py-6 text-sm text-gray-400 text-center">No invoices for this customer.</p>
        @endif
    </div>

    <div>
        <a href="{{ route('customers.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to customers
        </a>
    </div>
</div>
@endsection
