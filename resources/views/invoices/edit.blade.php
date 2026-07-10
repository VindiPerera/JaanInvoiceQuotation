@extends('layouts.app')
@section('title', 'Edit Invoice ' . $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number . ' / Edit')

@section('content')
<form method="POST" action="{{ route('invoices.update', $invoice) }}" x-data="invoiceForm()">
    @csrf @method('PUT')
    @include('invoices._form', ['invoice' => $invoice, 'quotation' => null])
</form>
@endsection
