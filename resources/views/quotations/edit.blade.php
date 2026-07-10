@extends('layouts.app')
@section('title', 'Edit Quotation ' . $quotation->quotation_number)
@section('breadcrumb', 'Quotations / ' . $quotation->quotation_number . ' / Edit')

@section('content')
<form method="POST" action="{{ route('quotations.update', $quotation) }}" x-data="quotationForm()">
    @csrf @method('PUT')
    @include('quotations._form', ['quotation' => $quotation])
</form>
@endsection
