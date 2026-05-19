@extends('layouts.app')
@section('title', 'Invoices')
@section('breadcrumb', 'Manage all invoices')

@section('header-actions')
    <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-plus"></i> New Invoice
    </a>
@endsection

@section('content')
<div class="space-y-4">

    {{-- Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totals['count']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">all time</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">LKR {{ number_format($totals['total_amount']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">invoiced amount</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 border-l-4 border-l-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Revenue Collected</p>
            <p class="text-2xl font-bold text-green-600 mt-1">LKR {{ number_format($totals['total_paid']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">payments received</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 border-l-4 border-l-red-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Outstanding</p>
            <p class="text-2xl font-bold text-red-500 mt-1">LKR {{ number_format($totals['total_balance']) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">balance due</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200">
        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3 p-4 border-b border-gray-100">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice number or customer…"
                class="flex-1 min-w-48 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
            <select name="payment_status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
                <option value="">All Statuses</option>
                @foreach(['pending','partial','paid'] as $s)
                    <option value="{{ $s }}" {{ request('payment_status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition">Filter</button>
            @if(request()->hasAny(['search','payment_status','date_from','date_to']))
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Number</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Amount</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Paid</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Balance</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-semibold text-red-600 hover:underline">{{ $inv->invoice_number }}</a>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-800">{{ $inv->customer_name }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">{{ number_format($inv->total_amount) }}</td>
                        <td class="px-5 py-3 text-right text-green-600">{{ number_format($inv->paid_amount) }}</td>
                        <td class="px-5 py-3 text-right {{ $inv->balance > 0 ? 'text-red-500 font-medium' : 'text-gray-500' }}">{{ number_format($inv->balance) }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'text-xs px-2.5 py-1 rounded-full font-medium',
                                'bg-green-100 text-green-700'  => $inv->payment_status === 'paid',
                                'bg-orange-100 text-orange-700'=> $inv->payment_status === 'partial',
                                'bg-red-100 text-red-700'      => $inv->payment_status === 'pending',
                            ])>{{ ucfirst($inv->payment_status) }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('invoices.show', $inv) }}" title="View" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-eye"></i></a>
                                <a href="{{ route('invoices.edit', $inv) }}" title="Edit" class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-pencil"></i></a>
                                <a href="{{ route('invoices.pdf', $inv) }}" title="PDF" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-file-pdf"></i></a>
                                <form method="POST" action="{{ route('invoices.duplicate', $inv) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Duplicate" class="text-gray-400 hover:text-purple-600"><i class="fa-solid fa-copy"></i></button>
                                </form>
                                <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="inline" onsubmit="return confirm('Delete this invoice?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-receipt text-3xl mb-2 block"></i>
                            No invoices found. <a href="{{ route('invoices.create') }}" class="text-red-600 hover:underline">Create one</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
