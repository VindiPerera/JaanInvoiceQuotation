<?php

namespace App\Http\Controllers;

use App\Models\HardwareCatalog;
use App\Models\QuoteTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $hardwareCatalog = HardwareCatalog::active()->orderBy('category')->orderBy('name')->get(['id', 'name', 'category', 'description', 'unit_price', 'warranty']);
        return view('quote-templates.form', compact('template', 'hardwareCatalog'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['key']               = $this->uniqueKey($request->input('name'));
        $data['hardware_items']    = $this->filterItems($request->input('hardware_items', []));
        $data['software_features'] = $this->filterEntries($request->input('software_features', []));

        QuoteTemplate::create($data);

        return redirect()->route('quote-templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(QuoteTemplate $quoteTemplate)
    {
        $template = $quoteTemplate;
        $hardwareCatalog = HardwareCatalog::active()->orderBy('category')->orderBy('name')->get(['id', 'name', 'category', 'description', 'unit_price', 'warranty']);
        return view('quote-templates.form', compact('template', 'hardwareCatalog'));
    }

    public function update(Request $request, QuoteTemplate $quoteTemplate)
    {
        $data = $this->validated($request);
        $data['hardware_items']    = $this->filterItems($request->input('hardware_items', []));
        $data['software_features'] = $this->filterEntries($request->input('software_features', []));

        $quoteTemplate->update($data);

        return redirect()->route('quote-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(QuoteTemplate $quoteTemplate)
    {
        $quoteTemplate->delete();
        return redirect()->route('quote-templates.index')->with('success', 'Template deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'               => 'required|string|max:255',
            'subtitle'           => 'nullable|string|max:255',
            'project_overview'   => 'nullable|string',
            'terms_conditions'   => 'nullable|string',
        ]);
    }

    private function uniqueKey(string $name): string
    {
        $base = Str::slug($name, '_');
        $key  = $base;
        $i    = 2;
        while (QuoteTemplate::where('key', $key)->exists()) {
            $key = $base . '_' . $i++;
        }
        return $key;
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
