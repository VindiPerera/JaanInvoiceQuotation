<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : $date->copy()->startOfDay();
        $dateTo   = $request->date_to   ? Carbon::parse($request->date_to)->endOfDay()   : $date->copy()->endOfDay();

        $query = Invoice::with('items')
            ->whereBetween('invoice_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        $invoices    = $query->orderBy('invoice_date', 'desc')->get();
        $totalAmount = $invoices->sum('total_amount');
        $totalPaid   = $invoices->sum('paid_amount');
        $outstanding = $invoices->sum('balance');
        $count       = $invoices->count();

        $settings = Setting::pluck('value', 'key');

        return view('reports.daily', compact(
            'invoices', 'totalAmount', 'totalPaid', 'outstanding',
            'count', 'date', 'dateFrom', 'dateTo', 'settings'
        ));
    }

    public function dailyPdf(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::today();
        $dateTo   = $request->date_to   ? Carbon::parse($request->date_to)->endOfDay() : Carbon::today()->endOfDay();

        $invoices    = Invoice::whereBetween('invoice_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                              ->orderBy('invoice_date', 'desc')->get();
        $totalAmount = $invoices->sum('total_amount');
        $totalPaid   = $invoices->sum('paid_amount');
        $outstanding = $invoices->sum('balance');
        $count       = $invoices->count();
        $settings    = Setting::pluck('value', 'key');

        $pdf = Pdf::loadView('reports.daily_pdf', compact(
            'invoices', 'totalAmount', 'totalPaid', 'outstanding',
            'count', 'dateFrom', 'dateTo', 'settings'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('daily-report-' . $dateFrom->format('Y-m-d') . '.pdf');
    }
}
