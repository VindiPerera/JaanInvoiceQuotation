<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Customer;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $stats = [
            'quotations_month' => Quotation::whereMonth('quotation_date', $now->month)
                ->whereYear('quotation_date', $now->year)->count(),
            'invoices_month' => Invoice::whereMonth('invoice_date', $now->month)
                ->whereYear('invoice_date', $now->year)->count(),
            'revenue_month' => Invoice::whereMonth('invoice_date', $now->month)
                ->whereYear('invoice_date', $now->year)->sum('total_amount'),
            'pending_invoices' => Invoice::where('payment_status', 'pending')->count(),
            'outstanding' => Invoice::whereIn('payment_status', ['pending', 'partial'])->sum('balance'),
            'customers_total' => Customer::count(),
        ];

        $recent_quotations = Quotation::with('customer')
            ->orderBy('created_at', 'desc')->limit(10)->get();

        $recent_invoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')->limit(10)->get();

        $monthly_revenue = Invoice::selectRaw('MONTH(invoice_date) as month, SUM(total_amount) as total')
            ->whereYear('invoice_date', $now->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chart_labels = [];
        $chart_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $chart_labels[] = Carbon::create()->month($m)->format('M');
            $chart_data[] = $monthly_revenue[$m] ?? 0;
        }

        return view('dashboard', compact('stats', 'recent_quotations', 'recent_invoices', 'chart_labels', 'chart_data'));
    }
}
