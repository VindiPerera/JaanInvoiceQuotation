<?php

namespace App\Http\Controllers;

use App\Models\HardwareCatalog;
use Illuminate\Http\Request;

class HardwareCatalogController extends Controller
{
    public function index()
    {
        $items = HardwareCatalog::orderBy('category')->orderBy('name')->get();
        return view('hardware-catalog.index', compact('items'));
    }

    public function create()
    {
        $item = null;
        return view('hardware-catalog.form', compact('item'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'nullable|string|max:100',
            'description'=> 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'warranty'   => 'nullable|string|max:255',
            'is_active'  => 'boolean',
        ]);

        HardwareCatalog::create([
            'name'        => $request->name,
            'category'    => $request->category,
            'description' => $request->description,
            'unit_price'  => $request->unit_price,
            'warranty'    => $request->warranty,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('hardware-catalog.index')->with('success', 'Item added to catalog.');
    }

    public function edit(HardwareCatalog $hardwareCatalog)
    {
        $item = $hardwareCatalog;
        return view('hardware-catalog.form', compact('item'));
    }

    public function update(Request $request, HardwareCatalog $hardwareCatalog)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit_price'  => 'required|numeric|min:0',
            'warranty'    => 'nullable|string|max:255',
            'is_active'   => 'boolean',
        ]);

        $hardwareCatalog->update([
            'name'        => $request->name,
            'category'    => $request->category,
            'description' => $request->description,
            'unit_price'  => $request->unit_price,
            'warranty'    => $request->warranty,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('hardware-catalog.index')->with('success', 'Item updated.');
    }

    public function destroy(HardwareCatalog $hardwareCatalog)
    {
        $hardwareCatalog->delete();
        return redirect()->route('hardware-catalog.index')->with('success', 'Item deleted.');
    }

    public function apiList()
    {
        $items = HardwareCatalog::active()
            ->orderBy('category')
            ->orderBy('name')
            ->get(['id', 'name', 'category', 'description', 'unit_price', 'warranty']);

        return response()->json($items);
    }
}
