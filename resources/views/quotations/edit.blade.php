@extends('layouts.app')
@section('title', 'Edit ' . $quotation->quotation_number)
@section('breadcrumb', 'Quotations / Edit')

@section('content')
<form method="POST" action="{{ route('quotations.update', $quotation) }}" x-data="quotationForm()">
@csrf @method('PUT')
@include('quotations._form', ['quotation' => $quotation])
</form>
@endsection

@push('scripts')
@include('quotations._form_scripts')
@endpush
