<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\HardwareCatalog;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuoteTemplate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::with('customer');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%$search%")
                  ->orWhere('customer_name', 'like', "%$search%")
                  ->orWhere('subject', 'like', "%$search%");
            });
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('quotation_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('quotation_date', '<=', $request->date_to);
        }

        $quotations = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $totals = [
            'count'          => Quotation::count(),
            'total_value'    => Quotation::sum('total_amount'),
            'accepted_count' => Quotation::where('status', 'accepted')->count(),
            'accepted_value' => Quotation::where('status', 'accepted')->sum('total_amount'),
        ];

        return view('quotations.index', compact('quotations', 'totals'));
    }

    public function create()
    {
        $customers    = Customer::orderBy('name')->get();
        $nextNumber   = Quotation::generateNumber();
        $defaultTerms = Setting::get('default_terms', '');
        $defaultFeatures = [];
        $defaultBenefits = [];
        $quotation   = null;
        $templates   = QuoteTemplate::orderBy('sort_order')->orderBy('id')->get();
        $hardware    = HardwareCatalog::active()->orderBy('category')->orderBy('name')->get(['id', 'name', 'description', 'category', 'unit_price']);
        return view('quotations.create', compact('customers', 'nextNumber', 'defaultTerms', 'quotation', 'defaultFeatures', 'defaultBenefits', 'templates', 'hardware'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_number' => 'required|unique:quotations,quotation_number',
            'quotation_date'   => 'required|date',
            'customer_name'    => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            $quoteType = in_array($request->quote_type, ['full_set', 'software_only', 'hardware_only'])
                ? $request->quote_type : 'full_set';

            $quotation = Quotation::create([
                'quotation_number'    => $request->quotation_number,
                'quotation_date'      => $request->quotation_date,
                'customer_id'         => $request->customer_id ?: null,
                'customer_name'       => $request->customer_name,
                'customer_address'    => $request->customer_address,
                'customer_contact'    => $request->customer_contact,
                'subject'             => $request->subject,
                'quote_type'          => $quoteType,
                'software_features'   => $this->filterEntries($request->software_features),
                'additional_benefits' => $this->filterEntries($request->additional_benefits),
                'tax_amount'          => $request->tax_amount ?? 0,
                'terms_conditions'    => $request->terms_conditions,
                'status'              => $request->status ?? 'draft',
            ]);

            $subtotal = 0;
            if ($request->items) {
                foreach ($request->items as $i => $item) {
                    if (empty($item['description'])) { continue; }
                    $total = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'item_number'  => $i + 1,
                        'description'  => $item['description'],
                        'quantity'     => $item['quantity'] ?? 1,
                        'unit_price'   => $item['unit_price'] ?? 0,
                        'total'        => $total,
                        'item_type'    => $item['item_type'] ?? 'hardware',
                    ]);
                    $subtotal += $total;
                }
            }

            $quotation->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal + ($request->tax_amount ?? 0),
            ]);
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('items', 'customer');
        $settings = Setting::pluck('value', 'key');
        return view('quotations.show', compact('quotation', 'settings'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $customers       = Customer::orderBy('name')->get();
        $defaultTerms    = Setting::get('default_terms', '');
        $defaultFeatures = [];
        $defaultBenefits = [];
        $templates       = QuoteTemplate::orderBy('sort_order')->orderBy('id')->get();
        $hardware        = HardwareCatalog::active()->orderBy('category')->orderBy('name')->get(['id', 'name', 'description', 'category', 'unit_price']);
        return view('quotations.edit', compact('quotation', 'customers', 'defaultTerms', 'defaultFeatures', 'defaultBenefits', 'templates', 'hardware'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'quotation_number' => 'required|unique:quotations,quotation_number,' . $quotation->id,
            'quotation_date'   => 'required|date',
            'customer_name'    => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            $quoteType = in_array($request->quote_type, ['full_set', 'software_only', 'hardware_only'])
                ? $request->quote_type : 'full_set';

            $quotation->update([
                'quotation_number'    => $request->quotation_number,
                'quotation_date'      => $request->quotation_date,
                'customer_id'         => $request->customer_id ?: null,
                'customer_name'       => $request->customer_name,
                'customer_address'    => $request->customer_address,
                'customer_contact'    => $request->customer_contact,
                'subject'             => $request->subject,
                'quote_type'          => $quoteType,
                'software_features'   => $this->filterEntries($request->software_features),
                'additional_benefits' => $this->filterEntries($request->additional_benefits),
                'tax_amount'          => $request->tax_amount ?? 0,
                'terms_conditions'    => $request->terms_conditions,
                'status'              => $request->status ?? 'draft',
            ]);

            $quotation->items()->delete();
            $subtotal = 0;
            if ($request->items) {
                foreach ($request->items as $i => $item) {
                    if (empty($item['description'])) { continue; }
                    $total = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0);
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'item_number'  => $i + 1,
                        'description'  => $item['description'],
                        'quantity'     => $item['quantity'] ?? 1,
                        'unit_price'   => $item['unit_price'] ?? 0,
                        'total'        => $total,
                        'item_type'    => $item['item_type'] ?? 'hardware',
                    ]);
                    $subtotal += $total;
                }
            }

            $quotation->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal + ($request->tax_amount ?? 0),
            ]);
        });

        return redirect()->route('quotations.show', $quotation)->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load('items');
        $settings = Setting::pluck('value', 'key');
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'settings'))
            ->setPaper('a4', 'portrait');
        return $pdf->download($quotation->quotation_number . '.pdf');
    }

    public function duplicate(Quotation $quotation)
    {
        $quotation->load('items');
        $newQuotation = $quotation->replicate();
        $newQuotation->quotation_number = Quotation::generateNumber();
        $newQuotation->quotation_date = now()->toDateString();
        $newQuotation->status = 'draft';
        $newQuotation->save();

        foreach ($quotation->items as $item) {
            $newItem = $item->replicate();
            $newItem->quotation_id = $newQuotation->id;
            $newItem->save();
        }

        return redirect()->route('quotations.edit', $newQuotation)->with('success', 'Quotation duplicated.');
    }

    public function convertToInvoice(Quotation $quotation)
    {
        $quotation->load('items');
        $customers = Customer::orderBy('name')->get();
        $nextNumber = \App\Models\Invoice::generateNumber();
        $settings = Setting::pluck('value', 'key');
        return view('invoices.create', compact('quotation', 'customers', 'nextNumber', 'settings'));
    }

    private function filterEntries(?array $entries): array
    {
        if (!$entries) { return []; }
        return array_values(array_filter($entries, fn($e) =>
            is_array($e) && isset($e['kind']) &&
            ($e['kind'] === 'space' || !empty(trim($e['text'] ?? '')))
        ));
    }
}
