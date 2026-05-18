@extends('layouts.app')
@section('title', 'Edit ' . $invoice->invoice_number)
@section('breadcrumb', 'Invoices / Edit')

@section('content')
<form method="POST" action="{{ route('invoices.update', $invoice) }}" x-data="invoiceForm()">
@csrf @method('PUT')
@include('invoices._form', ['invoice' => $invoice, 'quotation' => null])
</form>
@endsection

@push('scripts')
@include('invoices._form_scripts')
@endpush
