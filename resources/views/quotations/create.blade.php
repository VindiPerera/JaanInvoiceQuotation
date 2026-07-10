@extends('layouts.app')
@section('title', 'Create Quotation')
@section('breadcrumb', 'Quotations / New')

@section('content')
<form method="POST" action="{{ route('quotations.store') }}" x-data="quotationForm()">
    @csrf
    @include('quotations._form', ['quotation' => null])
</form>
@endsection
