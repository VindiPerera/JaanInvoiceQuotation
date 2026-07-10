@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Welcome back! Here\'s your business overview')

@section('header-actions')
    <x-button href="{{ route('quotations.create') }}" variant="primary" icon="fa-plus">
        New Quotation
    </x-button>
    <x-button href="{{ route('invoices.create') }}" variant="secondary" icon="fa-plus">
        New Invoice
    </x-button>
@endsection

@section('content')
<div class="space-y-8">

    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <x-stat-card
            title="Quotations (Month)"
            value="{{ $stats['quotations_month'] }}"
            icon="fa-file-lines"
            color="blue"
        />
        <x-stat-card
            title="Invoices (Month)"
            value="{{ $stats['invoices_month'] }}"
            icon="fa-receipt"
            color="indigo"
        />
        <x-stat-card
            title="Revenue (Month)"
            value="LKR {{ number_format($stats['revenue_month']) }}"
            icon="fa-chart-line"
            color="green"
        />
        <x-stat-card
            title="Pending"
            value="{{ $stats['pending_invoices'] }}"
            icon="fa-hourglass-half"
            color="amber"
        />
        <x-stat-card
            title="Outstanding"
            value="LKR {{ number_format($stats['outstanding']) }}"
            icon="fa-exclamation-circle"
            color="red"
        />
        <x-stat-card
            title="Customers"
            value="{{ $stats['customers_total'] }}"
            icon="fa-people-group"
            color="slate"
        />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Revenue Chart --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <div class="mb-6 pb-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-900">Monthly Revenue {{ date('Y') }}</h3>
                <p class="text-sm text-slate-500 mt-1">Year-to-date performance</p>
            </div>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @foreach([
                    ['route' => 'quotations.create', 'icon' => 'fa-file-lines', 'title' => 'New Quotation', 'sub' => 'Create quotation', 'color' => 'bg-blue-100 text-blue-600'],
                    ['route' => 'invoices.create',   'icon' => 'fa-receipt',     'title' => 'New Invoice',   'sub' => 'Create invoice', 'color' => 'bg-indigo-100 text-indigo-600'],
                    ['route' => 'customers.index',   'icon' => 'fa-people-group', 'title' => 'Customers',     'sub' => 'Manage customers', 'color' => 'bg-green-100 text-green-600'],
                ] as $action)
                <a href="{{ route($action['route']) }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition">
                    <div class="w-10 h-10 {{ $action['color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $action['icon'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $action['title'] }}</p>
                        <p class="text-xs text-slate-500">{{ $action['sub'] }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-slate-300 ml-auto"></i>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Recent Quotations --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900">Recent Quotations</h3>
                <x-button href="{{ route('quotations.index') }}" variant="ghost" size="sm">
                    View All
                </x-button>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recent_quotations as $q)
                <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                    <div>
                        <a href="{{ route('quotations.show', $q) }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">{{ $q->quotation_number }}</a>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $q->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">LKR {{ number_format($q->total_amount) }}</p>
                        <span @class([
                            'text-xs font-bold px-2.5 py-1 rounded-full mt-1 inline-block border',
                            'bg-green-100 text-green-700 border-green-300'  => $q->status === 'accepted',
                            'bg-red-100 text-red-700 border-red-300'      => $q->status === 'rejected',
                            'bg-blue-100 text-blue-700 border-blue-300'    => $q->status === 'sent',
                            'bg-purple-100 text-purple-700 border-purple-300'=> $q->status === 'finalized',
                            'bg-slate-100 text-slate-700 border-slate-300'    => $q->status === 'draft',
                        ])>{{ ucfirst($q->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="px-6 py-8 text-sm text-slate-500 text-center">No quotations yet. <a href="{{ route('quotations.create') }}" class="text-blue-600 hover:underline">Create one</a></p>
                @endforelse
            </div>
        </div>

        {{-- Recent Invoices --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-900">Recent Invoices</h3>
                <x-button href="{{ route('invoices.index') }}" variant="ghost" size="sm">
                    View All
                </x-button>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recent_invoices as $inv)
                <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                    <div>
                        <a href="{{ route('invoices.show', $inv) }}" class="text-sm font-bold text-blue-600 hover:text-blue-700">{{ $inv->invoice_number }}</a>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $inv->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-slate-900">LKR {{ number_format($inv->total_amount) }}</p>
                        <span @class([
                            'text-xs font-bold px-2.5 py-1 rounded-full mt-1 inline-block border',
                            'bg-green-100 text-green-700 border-green-300'  => $inv->payment_status === 'paid',
                            'bg-amber-100 text-amber-700 border-amber-300'=> $inv->payment_status === 'partial',
                            'bg-red-100 text-red-700 border-red-300'      => $inv->payment_status === 'pending',
                        ])>{{ ucfirst($inv->payment_status) }}</span>
                    </div>
                </div>
                @empty
                <p class="px-6 py-8 text-sm text-slate-500 text-center">No invoices yet. <a href="{{ route('invoices.create') }}" class="text-blue-600 hover:underline">Create one</a></p>
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
            backgroundColor: 'rgba(37,99,235,0.1)',
            borderColor: 'rgba(37,99,235,0.8)',
            borderWidth: 2,
            borderRadius: 8,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            filler: { propagate: true }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#f1f5f9', drawBorder: false },
                ticks: {
                    callback: v => 'LKR ' + Number(v).toLocaleString(),
                    color: '#64748b',
                    font: { size: 12 }
                },
                border: { display: false }
            },
            x: {
                grid: { display: false, drawBorder: false },
                ticks: { color: '#64748b', font: { size: 12 } }
            }
        }
    }
});
</script>
@endpush
