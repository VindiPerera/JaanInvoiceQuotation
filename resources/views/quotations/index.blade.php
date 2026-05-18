@extends('layouts.app')
@section('title', 'Quotations')
@section('breadcrumb', 'Manage all quotations')

@section('header-actions')
    <a href="{{ route('quotations.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-plus"></i> New Quotation
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 p-4 border-b border-gray-100">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search number, customer, subject…"
            class="flex-1 min-w-48 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
        <select name="status" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
            <option value="">All Statuses</option>
            @foreach(['draft','finalized','sent','accepted','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition">Filter</button>
        @if(request()->hasAny(['search','status','date_from','date_to']))
            <a href="{{ route('quotations.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">Clear</a>
        @endif
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Number</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Customer</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Subject</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-right">Total</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($quotations as $q)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <a href="{{ route('quotations.show', $q) }}" class="font-semibold text-red-600 hover:underline">{{ $q->quotation_number }}</a>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $q->quotation_date->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $q->customer_name }}</td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $q->subject }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">LKR {{ number_format($q->total_amount) }}</td>
                    <td class="px-5 py-3">
                        <span @class([
                            'text-xs px-2.5 py-1 rounded-full font-medium',
                            'bg-green-100 text-green-700'  => $q->status === 'accepted',
                            'bg-red-100 text-red-700'      => $q->status === 'rejected',
                            'bg-blue-100 text-blue-700'    => $q->status === 'sent',
                            'bg-purple-100 text-purple-700'=> $q->status === 'finalized',
                            'bg-gray-100 text-gray-600'    => $q->status === 'draft',
                        ])>{{ ucfirst($q->status) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('quotations.show', $q) }}" title="View" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('quotations.edit', $q) }}" title="Edit" class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-pencil"></i></a>
                            <a href="{{ route('quotations.pdf', $q) }}" title="Download PDF" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-file-pdf"></i></a>
                            <a href="{{ route('quotations.convert', $q) }}" title="Convert to Invoice" class="text-gray-400 hover:text-green-600"><i class="fa-solid fa-arrow-right-arrow-left"></i></a>
                            <form method="POST" action="{{ route('quotations.duplicate', $q) }}" class="inline">
                                @csrf
                                <button type="submit" title="Duplicate" class="text-gray-400 hover:text-purple-600"><i class="fa-solid fa-copy"></i></button>
                            </form>
                            <form method="POST" action="{{ route('quotations.destroy', $q) }}" class="inline" onsubmit="return confirm('Delete this quotation?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Delete" class="text-gray-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-file-invoice text-3xl mb-2 block"></i>
                        No quotations found. <a href="{{ route('quotations.create') }}" class="text-red-600 hover:underline">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-5 py-4 border-t border-gray-100">
        {{ $quotations->links() }}
    </div>
</div>
@endsection
