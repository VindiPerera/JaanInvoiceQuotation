@extends('layouts.app')
@section('title', 'New Quotation')
@section('breadcrumb', 'Quotations / Create')

@section('content')
<form method="POST" action="{{ route('quotations.store') }}" x-data="quotationForm()">
@csrf
@include('quotations._form', ['quotation' => null])
</form>
@endsection

@push('scripts')
@include('quotations._form_scripts')
@endpush
