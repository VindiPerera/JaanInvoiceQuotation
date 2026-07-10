@extends('layouts.app')
@section('title', 'Quotations')
@section('breadcrumb', 'Create and manage quotations for your customers')

@section('header-actions')
    <x-button href="{{ route('quotations.create') }}" variant="primary" icon="fa-plus">
        New Quotation
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
            title="Total Quotations"
            value="{{ number_format($totals['count']) }}"
            icon="fa-file-lines"
            color="blue"
        />

        <x-stat-card
            title="Total Value"
            value="LKR {{ number_format($totals['total_value']) }}"
            icon="fa-chart-bar"
            color="indigo"
        />

        <x-stat-card
            title="Accepted"
            value="{{ number_format($totals['accepted_count']) }}"
            icon="fa-handshake"
            color="green"
        />

        <x-stat-card
            title="Accepted Value"
            value="LKR {{ number_format($totals['accepted_value']) }}"
            icon="fa-check-circle"
            color="green"
        />
    </div>

    {{-- Quotations Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        {{-- Filters --}}
        <div class="border-b border-slate-200 bg-slate-50 p-6">
            <form method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by number, customer, or subject…"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div class="w-full lg:w-48">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="finalized" {{ request('status') === 'finalized' ? 'selected' : '' }}>Finalized</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                    @if(request()->hasAny(['search','status','date_from','date_to']))
                        <x-button href="{{ route('quotations.index') }}" variant="outline" icon="fa-times">Clear</x-button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Quotation #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition group">
                        <td class="px-6 py-4">
                            <a href="{{ route('quotations.show', $q) }}" class="font-bold text-blue-600 hover:text-blue-700 transition">{{ $q->quotation_number }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $q->quotation_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $q->customer_name }}</td>
                        <td class="px-6 py-4 text-slate-600 max-w-xs truncate" title="{{ $q->subject }}">{{ $q->subject }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">LKR {{ number_format($q->total_amount) }}</td>
                        <td class="px-6 py-4">
                            @if($q->status === 'accepted')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-300">
                                    <i class="fas fa-check-circle"></i> Accepted
                                </span>
                            @elseif($q->status === 'rejected')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-300">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @elseif($q->status === 'sent')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full border border-blue-300">
                                    <i class="fas fa-paper-plane"></i> Sent
                                </span>
                            @elseif($q->status === 'finalized')
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-full border border-purple-300">
                                    <i class="fas fa-check"></i> Finalized
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-full border border-slate-300">
                                    <i class="fas fa-file"></i> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('quotations.show', $q) }}" title="View" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('quotations.edit', $q) }}" title="Edit" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                    <i class="fas fa-pencil text-sm"></i>
                                </a>
                                <a href="{{ route('quotations.pdf', $q) }}" title="Download PDF" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-file-pdf text-sm"></i>
                                </a>
                                <a href="{{ route('quotations.convert', $q) }}" title="Convert to Invoice" class="p-2 text-slate-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition">
                                    <i class="fas fa-arrow-right-arrow-left text-sm"></i>
                                </a>
                                <form method="POST" action="{{ route('quotations.duplicate', $q) }}" class="inline">
                                    @csrf
                                    <button type="submit" title="Duplicate" class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <i class="fas fa-copy text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-slate-300 mb-4"></i>
                                <p class="text-slate-500 font-medium mb-4">No quotations found</p>
                                <x-button href="{{ route('quotations.create') }}" variant="primary" icon="fa-plus">
                                    Create Your First Quotation
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
            {{ $quotations->links() }}
        </div>
    </div>
</div>
@endsection
