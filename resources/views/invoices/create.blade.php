@extends('layouts.app')
@section('title', 'New Invoice')
@section('breadcrumb', 'Invoices / Create')

@section('content')
<form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
@csrf
@include('invoices._form', ['invoice' => null])
</form>
@endsection

@push('scripts')
@include('invoices._form_scripts')
@endpush
