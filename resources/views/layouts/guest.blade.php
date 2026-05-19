<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JAAN Invoice') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-sm">
            {{-- Logo --}}
            <div class="flex flex-col items-center mb-8">
                <img src="{{ asset('images/company_logo.jpg') }}" alt="JAAN Network" class="h-20 w-auto object-contain mb-3">
                <p class="text-sm text-gray-500 mt-0.5">Invoice & Quotation System</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
