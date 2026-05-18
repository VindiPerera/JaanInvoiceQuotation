<x-guest-layout>
    <x-auth-session-status class="mb-4 text-sm text-green-600" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-medium text-gray-500 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', 'admin@jaan.lk') }}"
                required autofocus autocomplete="username"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 @error('email') border-red-400 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-medium text-gray-500 mb-1">Password</label>
            <input id="password" type="password" name="password"
                required autocomplete="current-password"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 @error('password') border-red-400 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember"
                    class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-400">
                Remember me
            </label>
        </div>

        <button type="submit"
            class="w-full bg-red-600 text-white text-sm font-semibold py-2.5 rounded-lg hover:bg-red-700 transition">
            Sign In
        </button>
    </form>
</x-guest-layout>
