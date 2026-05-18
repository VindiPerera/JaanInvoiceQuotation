@extends('layouts.app')
@section('title', 'Customers')
@section('breadcrumb', 'Manage customers')

@section('header-actions')
    <button onclick="document.getElementById('addCustomerModal').classList.remove('hidden')"
        class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-red-700 transition">
        <i class="fa-solid fa-plus"></i> Add Customer
    </button>
@endsection

@section('content')
<div class="bg-white rounded-xl border border-gray-200">
    <form method="GET" class="flex gap-3 p-4 border-b border-gray-100">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, contact, email…"
            class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-300">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700 transition">Search</button>
        @if(request('search'))
            <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200 transition">Clear</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Address</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Quotations</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Invoices</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($customers as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $c->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $c->contact ?: '—' }}</td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $c->address ?: '—' }}</td>
                    <td class="px-5 py-3 text-center text-gray-700">{{ $c->quotations_count }}</td>
                    <td class="px-5 py-3 text-center text-gray-700">{{ $c->invoices_count }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('customers.show', $c) }}" title="View" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-eye"></i></a>
                            <button onclick="openEdit({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->address) }}', '{{ addslashes($c->contact) }}', '{{ addslashes($c->email) }}')"
                                class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-pencil"></i></button>
                            <form method="POST" action="{{ route('customers.destroy', $c) }}" onsubmit="return confirm('Delete customer?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-users text-3xl mb-2 block"></i>
                        No customers yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">{{ $customers->links() }}</div>
</div>

{{-- Add Customer Modal --}}
<div id="addCustomerModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Add Customer</h3>
            <button onclick="document.getElementById('addCustomerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Name *</label>
                <input type="text" name="name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                <textarea name="address" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Contact</label>
                    <input type="text" name="contact" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                    <input type="email" name="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-red-700 transition">Add Customer</button>
                <button type="button" onclick="document.getElementById('addCustomerModal').classList.add('hidden')"
                    class="flex-1 bg-white border border-gray-200 text-gray-600 text-sm py-2.5 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Customer Modal --}}
<div id="editCustomerModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900">Edit Customer</h3>
            <button onclick="document.getElementById('editCustomerModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editCustomerForm" method="POST" class="space-y-3">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Name *</label>
                <input type="text" id="edit_name" name="name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                <textarea id="edit_address" name="address" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Contact</label>
                    <input type="text" id="edit_contact" name="contact" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                    <input type="email" id="edit_email" name="email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-red-700 transition">Save Changes</button>
                <button type="button" onclick="document.getElementById('editCustomerModal').classList.add('hidden')"
                    class="flex-1 bg-white border border-gray-200 text-gray-600 text-sm py-2.5 rounded-lg hover:bg-gray-50 transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEdit(id, name, address, contact, email) {
    document.getElementById('editCustomerForm').action = `/customers/${id}`;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_contact').value = contact;
    document.getElementById('edit_email').value = email;
    document.getElementById('editCustomerModal').classList.remove('hidden');
}
</script>
@endsection
