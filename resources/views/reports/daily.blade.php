@extends('layouts.app')
@section('title', 'Daily Cash Summary')
@section('breadcrumb', 'Reports / Daily Summary')

@section('header-actions')
    <a href="{{ route('reports.daily.pdf', request()->query()) }}"
       class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-file-pdf"></i> Export PDF
    </a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Date filters --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from', $dateFrom->toDateString()) }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to', $dateTo->toDateString()) }}"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
            {{-- Quick selectors --}}
            <div class="flex gap-2 flex-wrap">
                @php
                    $today = now()->toDateString();
                    $yesterday = now()->subDay()->toDateString();
                    $weekStart = now()->startOfWeek()->toDateString();
                    $monthStart = now()->startOfMonth()->toDateString();
                @endphp
                <a href="{{ route('reports.daily', ['date_from' => $today, 'date_to' => $today]) }}"
                   class="px-3 py-2 text-xs {{ request('date_from') === $today && request('date_to') === $today ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} rounded-lg transition">Today</a>
                <a href="{{ route('reports.daily', ['date_from' => $yesterday, 'date_to' => $yesterday]) }}"
                   class="px-3 py-2 text-xs {{ request('date_from') === $yesterday && request('date_to') === $yesterday ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} rounded-lg transition">Yesterday</a>
                <a href="{{ route('reports.daily', ['date_from' => $weekStart, 'date_to' => $today]) }}"
                   class="px-3 py-2 text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg transition">This Week</a>
                <a href="{{ route('reports.daily', ['date_from' => $monthStart, 'date_to' => $today]) }}"
                   class="px-3 py-2 text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg transition">This Month</a>
            </div>
        </form>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Invoices</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $count }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Revenue</p>
            <p class="text-2xl font-bold text-red-600 mt-1">LKR {{ number_format($totalAmount) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Collected</p>
            <p class="text-2xl font-bold text-green-600 mt-1">LKR {{ number_format($totalPaid) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Outstanding</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">LKR {{ number_format($outstanding) }}</p>
        </div>
    </div>

    {{-- Invoices table --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-700">
                Invoices from {{ $dateFrom->format('d M Y') }}
                @if($dateFrom->toDateString() !== $dateTo->toDateString())
                    to {{ $dateTo->format('d M Y') }}
                @endif
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Invoice #</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Amount</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Paid</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Balance</th>
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
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
                        <td class="px-5 py-3 text-right {{ $inv->balance > 0 ? 'text-red-500 font-medium' : 'text-gray-400' }}">{{ number_format($inv->balance) }}</td>
                        <td class="px-5 py-3">
                            <span @class([
                                'text-xs px-2.5 py-1 rounded-full font-medium',
                                'bg-green-100 text-green-700'   => $inv->payment_status === 'paid',
                                'bg-orange-100 text-orange-700' => $inv->payment_status === 'partial',
                                'bg-red-100 text-red-700'       => $inv->payment_status === 'pending',
                            ])>{{ ucfirst($inv->payment_status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-chart-bar text-3xl mb-2 block"></i>
                            No invoices found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($invoices->count())
                <tfoot>
                    <tr class="bg-gray-50 border-t-2 border-gray-200 font-semibold">
                        <td colspan="3" class="px-5 py-3 text-gray-700">Total ({{ $count }} invoices)</td>
                        <td class="px-5 py-3 text-right text-gray-900">LKR {{ number_format($totalAmount) }}</td>
                        <td class="px-5 py-3 text-right text-green-700">LKR {{ number_format($totalPaid) }}</td>
                        <td class="px-5 py-3 text-right text-red-600">LKR {{ number_format($outstanding) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
