<?php

namespace App\Http\Controllers;

use App\Models\QuoteTemplate;
use Illuminate\Http\Request;

class QuoteTemplateController extends Controller
{
    public function index()
    {
        $templates = QuoteTemplate::orderBy('sort_order')->orderBy('id')->get();
        return view('quote-templates.index', compact('templates'));
    }

    public function create()
    {
        $template = null;
        return view('quote-templates.form', compact('template'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['hardware_items']      = $this->filterItems($request->hardware_items);
        $data['software_features']   = $this->filterEntries($request->software_features);
        $data['additional_benefits'] = $this->filterEntries($request->additional_benefits);

        QuoteTemplate::create($data);

        return redirect()->route('quote-templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(QuoteTemplate $quoteTemplate)
    {
        $template = $quoteTemplate;
        return view('quote-templates.form', compact('template'));
    }

    public function update(Request $request, QuoteTemplate $quoteTemplate)
    {
        $data = $this->validated($request, $quoteTemplate->id);
        $data['hardware_items']      = $this->filterItems($request->hardware_items);
        $data['software_features']   = $this->filterEntries($request->software_features);
        $data['additional_benefits'] = $this->filterEntries($request->additional_benefits);

        $quoteTemplate->update($data);

        return redirect()->route('quote-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(QuoteTemplate $quoteTemplate)
    {
        $quoteTemplate->delete();
        return redirect()->route('quote-templates.index')->with('success', 'Template deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'key'              => 'required|string|max:100|alpha_dash|unique:quote_templates,key' . ($ignoreId ? ",$ignoreId" : ''),
            'icon'             => 'nullable|string|max:100',
            'subtitle'         => 'nullable|string|max:255',
            'terms_conditions' => 'nullable|string',
            'sort_order'       => 'nullable|integer|min:0',
        ]);
    }

    private function filterItems(?array $items): array
    {
        if (!$items) { return []; }
        return array_values(array_filter($items, fn($i) =>
            is_array($i) && !empty(trim($i['description'] ?? ''))
        ));
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
