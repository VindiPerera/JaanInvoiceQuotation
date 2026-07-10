<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'JAAN') }} – @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <style>
        :root {
            --primary: #2563EB;
            --secondary: #6366F1;
            --success: #22C55E;
            --warning: #F59E0B;
            --danger: #EF4444;
            --bg: #F8FAFC;
            --surface: #FFFFFF;
            --text-primary: #0F172A;
            --text-secondary: #64748B;
            --border: #E2E8F0;
        }

        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg);
            color: var(--text-primary);
        }
    </style>
</head>
<body class="font-sans antialiased">

<div class="flex h-screen bg-slate-50">
    {{-- Modern Sidebar --}}
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col shrink-0 shadow-sm lg:relative fixed left-0 top-0 h-full z-40 lg:z-auto" id="sidebar">
        {{-- Logo Section --}}
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg">
                    J
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900">JAAN</h1>
                    <p class="text-xs text-slate-500">Invoice Suite</p>
                </div>
            </div>
            <button class="lg:hidden p-2 hover:bg-slate-100 rounded-lg transition" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <i class="fas fa-times text-slate-600"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">
            {{-- Main Section --}}
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            {{-- Documents Section --}}
            <div>
                <h3 class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Documents</h3>
                <div class="space-y-1">
                    <a href="{{ route('quotations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('quotations.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-file-lines w-5"></i>
                        <span>Quotations</span>
                    </a>
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-receipt w-5"></i>
                        <span>Invoices</span>
                    </a>
                </div>
            </div>

            {{-- Management Section --}}
            <div class="pt-6 border-t border-slate-200">
                <h3 class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Management</h3>
                <div class="space-y-1">
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-people-group w-5"></i>
                        <span>Customers</span>
                    </a>
                </div>
            </div>

            {{-- Admin Section --}}
            @if(auth()->user()->is_admin)
            <div class="pt-6 border-t border-slate-200">
                <h3 class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Administration</h3>
                <div class="space-y-1">
                    <a href="{{ route('quote-templates.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('quote-templates.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-layer-group w-5"></i>
                        <span>Templates</span>
                    </a>
                    <a href="{{ route('hardware-catalog.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('hardware-catalog.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-microchip w-5"></i>
                        <span>Catalog</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium text-sm transition {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        <i class="fas fa-cog w-5"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>

        {{-- User Profile Card --}}
        <div class="p-6 border-t border-slate-200 bg-gradient-to-b from-slate-50 to-white">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium text-sm rounded-lg transition duration-200">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
        {{-- Top Navigation --}}
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center gap-4">
                <button class="lg:hidden p-2 hover:bg-slate-100 rounded-lg transition" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                    <i class="fas fa-bars text-slate-700 text-lg"></i>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">@yield('title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                        <p class="text-xs text-slate-500 mt-0.5">@yield('breadcrumb')</p>
                    @endif
                </div>
            </div>

            {{-- Header Actions --}}
            <div class="flex items-center gap-4">
                @yield('header-actions')
            </div>
        </header>

        {{-- Notifications --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3.5 rounded-xl shadow-sm mb-4" x-data="{ show: true }" x-show="show" x-transition>
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                    <button class="ml-auto p-1 hover:bg-emerald-100 rounded transition" @click="show=false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-xl shadow-sm mb-4" x-data="{ show: true }" x-show="show" x-transition>
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                    <button class="ml-auto p-1 hover:bg-red-100 rounded transition" @click="show=false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        </div>

        {{-- Main Content Area --}}
        <main class="flex-1 overflow-y-auto px-6 pb-6">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
