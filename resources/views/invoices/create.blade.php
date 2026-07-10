@extends('layouts.app')
@section('title', 'Create Invoice')
@section('breadcrumb', 'Invoices / New Invoice')

@section('content')
<form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
    @csrf
    @include('invoices._form', ['invoice' => null])
</form>
@endsection
