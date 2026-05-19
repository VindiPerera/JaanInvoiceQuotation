@extends('layouts.app')
@section('title', 'Settings')
@section('breadcrumb', 'System settings')

@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="max-w-3xl space-y-6">
@csrf

    {{-- Company --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Company Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Company Name</label>
                <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Phone</label>
                <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Website</label>
                <input type="text" name="company_website" value="{{ $settings['company_website'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Address</label>
                <textarea name="company_address" rows="2"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">{{ $settings['company_address'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    {{-- Company Logo --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-1">Company Logo</h2>
        <p class="text-xs text-gray-400 mb-4">Appears in PDF headers. PNG with transparent background recommended. Max 2 MB.</p>

        @if(!empty($settings['company_logo']) && file_exists(public_path($settings['company_logo'])))
        <div class="flex items-center gap-4 mb-4">
            <img src="{{ asset($settings['company_logo']) }}" alt="Company logo"
                 class="h-14 object-contain border border-gray-100 rounded p-2 bg-gray-50">
            <span class="text-xs text-gray-400">Current logo</span>
        </div>
        @endif

        <input type="file" name="company_logo" accept="image/png,image/jpeg,image/gif,image/svg+xml"
            class="block text-sm text-gray-500
                   file:mr-3 file:py-1.5 file:px-4 file:rounded file:border-0
                   file:text-sm file:font-semibold file:bg-red-50 file:text-red-700
                   hover:file:bg-red-100 cursor-pointer">
    </div>

    {{-- Bank / Payment --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Bank / Payment Details</h2>
        <p class="text-xs text-gray-400 mb-4">These details appear on every invoice PDF.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Bank Name</label>
                <input type="text" name="bank_name" value="{{ $settings['bank_name'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Branch</label>
                <input type="text" name="bank_branch" value="{{ $settings['bank_branch'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Account Name</label>
                <input type="text" name="bank_account_name" value="{{ $settings['bank_account_name'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Account Number</label>
                <input type="text" name="bank_account_number" value="{{ $settings['bank_account_number'] ?? '' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
        </div>
    </div>

    {{-- Document numbering --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Document Settings</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Quotation Prefix</label>
                <input type="text" name="quotation_prefix" value="{{ $settings['quotation_prefix'] ?? 'QT-' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" value="{{ $settings['invoice_prefix'] ?? 'INV-' }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Default Tax Rate (%)</label>
                <input type="number" name="default_tax_rate" value="{{ $settings['default_tax_rate'] ?? '0' }}"
                    min="0" step="0.01"
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
            </div>
        </div>
    </div>

    {{-- Default Terms --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Default Terms & Conditions</h2>
        <p class="text-xs text-gray-400 mb-3">Pre-filled on every new quotation.</p>
        <textarea name="default_terms" rows="10"
            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 font-mono">{{ $settings['default_terms'] ?? '' }}</textarea>
    </div>

    <div>
        <button type="submit" class="px-6 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition">
            Save Settings
        </button>
    </div>
</form>
@endsection
