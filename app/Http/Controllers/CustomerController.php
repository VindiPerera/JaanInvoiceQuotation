<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['quotations', 'invoices']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('contact', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $customers = $query->orderBy('name')->paginate(25)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'required|string|max:50',
        ]);

        $customer = Customer::create($request->only('name', 'address', 'contact', 'email', 'notes'));

        if ($request->expectsJson()) {
            return response()->json(['customer' => $customer]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer added.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['quotations', 'invoices']);
        return view('customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'contact' => 'required|string|max:50',
        ]);
        $customer->update($request->only('name', 'address', 'contact', 'email', 'notes'));

        if ($request->expectsJson()) {
            return response()->json(['customer' => $customer]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->quotations()->count() > 0 || $customer->invoices()->count() > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer with existing quotations or invoices.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
