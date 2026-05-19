<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JAAN Invoice') }} – @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-60 bg-white border-r border-gray-200 flex flex-col shrink-0">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
            <img src="{{ asset('images/company_logo.jpg') }}" alt="JAAN Network" class="h-10 w-auto object-contain">
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-gauge-high w-4"></i> Dashboard
            </a>
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Documents</p>
            </div>
            <a href="{{ route('quotations.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('quotations.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-file-invoice w-4"></i> Quotations
            </a>
            <a href="{{ route('invoices.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('invoices.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-receipt w-4"></i> Invoices
            </a>
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</p>
            </div>
            <a href="{{ route('customers.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('customers.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-users w-4"></i> Customers
            </a>
            <a href="{{ route('reports.daily') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-chart-bar w-4"></i> Reports
            </a>
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">System</p>
            </div>
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('settings.*') ? 'bg-red-50 text-red-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fa-solid fa-gear w-4"></i> Settings
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-gray-200">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center">
                    <span class="text-red-600 font-bold text-xs">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition">
                    <i class="fa-solid fa-right-from-bracket w-4"></i> Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <p class="text-xs text-gray-400 mt-0.5">@yield('breadcrumb')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        @if(session('success'))
            <div class="mx-6 mt-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-2.5 rounded-lg" x-data="{ show: true }" x-show="show">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
                <button class="ml-auto text-green-400 hover:text-green-600" @click="show=false"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-2.5 rounded-lg" x-data="{ show: true }" x-show="show">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
                <button class="ml-auto text-red-400 hover:text-red-600" @click="show=false"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
