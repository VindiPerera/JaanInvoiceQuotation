@extends('layouts.app')
@section('title', 'Invoices')
@section('breadcrumb', 'Manage and track all your invoices')

@section('header-actions')
    <x-button href="{{ route('invoices.create') }}" variant="primary" icon="fa-plus">
        New Invoice
    </x-button>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Summary Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php $dateLabel = request('date_from') || request('date_to')
            ? trim((request('date_from') ? date('d M Y', strtotime(request('date_from'))) : '') . ' – ' . (request('date_to') ? date('d M Y', strtotime(request('date_to'))) : ''))
            : 'all time'; @endphp

        <x-stat-card
            title="Total Invoices"
            value="{{ number_format($totals['count']) }}"
            icon="fa-file-invoice"
            color="blue"
        />

        <x-stat-card
            title="Total Revenue"
            value="LKR {{ number_format($totals['total_amount']) }}"
            icon="fa-chart-line"
            color="indigo"
        />

        <x-stat-card
            title="Amount Collected"
            value="LKR {{ number_format($totals['total_paid']) }}"
            icon="fa-check-circle"
            color="green"
        />

        <x-stat-card
            title="Outstanding"
            value="LKR {{ number_format($totals['total_balance']) }}"
            icon="fa-clock"
            color="amber"
        />
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        {{-- Filters --}}
        <div class="border-b border-slate-200 bg-slate-50 p-6">
            <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by invoice number or customer…"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="w-full lg:w-48">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="payment_status" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="w-full lg:w-40">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="w-full lg:w-40">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="flex gap-3">
                    <x-button type="submit" variant="primary" icon="fa-filter">Search</x-button>
                    @if(request()->hasAny(['search','payment_status','date_from','date_to']))
                        <x-button href="{{ route('invoices.index') }}" variant="outline" icon="fa-times">Clear</x-button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition group">
                        <td class="px-6 py-4">
                            <a href="{{ route('invoices.show', $inv) }}" class="font-bold text-blue-600 hover:text-blue-700 transition">{{ $inv->invoice_number }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $inv->customer_name }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">LKR {{ number_format($inv->total_amount) }}</td>
                        <td class="px-6 py-4 text-right text-green-600 font-semibold">LKR {{ number_format($inv->paid_amount) }}</td>
                        <td class="px-6 py-4 text-right font-bold {{ $inv->balance > 0 ? 'text-red-600' : 'text-slate-500' }}">
                            {{ $inv->balance > 0 ? 'LKR ' . number_format($inv->balance) : '—' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($inv->payment_status === 'paid')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-300">
                                    <i class="fas fa-check-circle"></i> Paid
                                </span>
                            @elseif($inv->payment_status === 'partial')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full border border-amber-300">
                                    <i class="fas fa-hourglass-half"></i> Partial
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-300">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('invoices.show', $inv) }}" title="View" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $inv) }}" title="Edit" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-pencil text-sm"></i>
                                </a>
                                <a href="{{ route('invoices.pdf', $inv) }}" title="Download PDF" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-file-pdf text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('invoices.duplicate', $inv) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Duplicate" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('invoices.destroy', $inv) }}" class="inline" onsubmit="return confirm('Delete this invoice?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-4"></i>
                                <p class="text-slate-500 font-medium mb-4">No invoices found</p>
                                <x-button href="{{ route('invoices.create') }}" variant="primary" icon="fa-plus">
                                    Create Your First Invoice
                                </x-button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
