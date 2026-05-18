@extends('layouts.app')
@section('title', 'Dashboard')

@section('header-actions')
    <a href="{{ route('quotations.create') }}" class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-plus"></i> New Quotation
    </a>
    <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
        <i class="fa-solid fa-plus"></i> New Invoice
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Quotations (Month)</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['quotations_month'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Invoices (Month)</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['invoices_month'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Revenue (Month)</p>
            <p class="text-2xl font-bold text-red-600 mt-1">LKR {{ number_format($stats['revenue_month']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Pending Invoices</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ $stats['pending_invoices'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Outstanding</p>
            <p class="text-2xl font-bold text-red-500 mt-1">LKR {{ number_format($stats['outstanding']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Customers</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['customers_total'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Monthly Revenue {{ date('Y') }}</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Quick Actions</h3>
            <div class="space-y-2">
                @foreach([
                    ['route' => 'quotations.create', 'icon' => 'fa-file-invoice', 'title' => 'New Quotation', 'sub' => 'Create a quotation'],
                    ['route' => 'invoices.create',   'icon' => 'fa-receipt',      'title' => 'New Invoice',   'sub' => 'Create an invoice'],
                    ['route' => 'customers.index',   'icon' => 'fa-users',        'title' => 'Customers',     'sub' => 'Manage customers'],
                    ['route' => 'reports.daily',     'icon' => 'fa-chart-bar',    'title' => 'Daily Report',  'sub' => "View today's summary"],
                ] as $action)
                <a href="{{ route($action['route']) }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-red-50 hover:border-red-200 transition">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fa-solid {{ $action['icon'] }} text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $action['title'] }}</p>
                        <p class="text-xs text-gray-500">{{ $action['sub'] }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Recent Quotations</h3>
                <a href="{{ route('quotations.index') }}" class="text-xs text-red-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recent_quotations as $q)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <a href="{{ route('quotations.show', $q) }}" class="text-sm font-medium text-gray-800 hover:text-red-600">{{ $q->quotation_number }}</a>
                        <p class="text-xs text-gray-500">{{ $q->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">LKR {{ number_format($q->total_amount) }}</p>
                        <span @class([
                            'text-xs px-2 py-0.5 rounded-full',
                            'bg-green-100 text-green-700'  => $q->status === 'accepted',
                            'bg-red-100 text-red-700'      => $q->status === 'rejected',
                            'bg-blue-100 text-blue-700'    => $q->status === 'sent',
                            'bg-purple-100 text-purple-700'=> $q->status === 'finalized',
                            'bg-gray-100 text-gray-600'    => $q->status === 'draft',
                        ])>{{ ucfirst($q->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No quotations yet</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Recent Invoices</h3>
                <a href="{{ route('invoices.index') }}" class="text-xs text-red-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recent_invoices as $inv)
                <div class="flex items-center justify-between px-5 py-3">
                    <div>
                        <a href="{{ route('invoices.show', $inv) }}" class="text-sm font-medium text-gray-800 hover:text-red-600">{{ $inv->invoice_number }}</a>
                        <p class="text-xs text-gray-500">{{ $inv->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">LKR {{ number_format($inv->total_amount) }}</p>
                        <span @class([
                            'text-xs px-2 py-0.5 rounded-full',
                            'bg-green-100 text-green-700'  => $inv->payment_status === 'paid',
                            'bg-orange-100 text-orange-700'=> $inv->payment_status === 'partial',
                            'bg-red-100 text-red-700'      => $inv->payment_status === 'pending',
                        ])>{{ ucfirst($inv->payment_status) }}</span>
                    </div>
                </div>
                @empty
                <p class="px-5 py-6 text-sm text-gray-400 text-center">No invoices yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"
    integrity="sha512-ZaHWOuHhECOlBxf5jmBOHH4MNimLSDt/XVEsYBXmGHLHzrT5Ak3kZHUQ0xNWpJF1M3l4pJ72pHNQR3uNalMw=="
    crossorigin="anonymous"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: @json($chart_labels),
        datasets: [{
            label: 'Revenue (LKR)',
            data: @json($chart_data),
            backgroundColor: 'rgba(220,38,38,0.12)',
            borderColor: 'rgba(220,38,38,0.8)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { callback: v => 'LKR ' + Number(v).toLocaleString() } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
