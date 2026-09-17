<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Authentication &middot; {{ config('app.name', 'Grand Horizon PMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-stretch font-sans antialiased">

    {{-- Left Panel: Operational Context --}}
    <div class="hidden lg:flex lg:w-5/12 bg-slate-900 border-r border-slate-800 flex-col justify-between p-12 relative">
        {{-- Header / Brand --}}
        <div>
            <div class="flex items-center gap-3 mb-8">
                <div class="w-10 h-10 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold text-lg tracking-tight">
                    GH
                </div>
                <div>
                    <span class="block text-white font-semibold text-lg leading-tight tracking-tight">Grand Horizon Hotel</span>
                    <span class="text-xs text-slate-400 font-mono">Property Management System</span>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-slate-800 text-slate-300 text-xs font-mono border border-slate-700/60 mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Terminal Active &middot; Nairobi Main Branch
            </div>
        </div>

        {{-- Operational Guidance --}}
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight mb-2">Staff Access Portal</h2>
                <p class="text-sm text-slate-400 leading-relaxed">
                    Authorized access point for Front Desk, Night Audit, Housekeeping Supervisors, and Hotel Accounting. Please sign in with your assigned staff credentials.
                </p>
            </div>

            <div class="border-t border-slate-800 pt-6 space-y-3">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>Audit & Compliance</span>
                    <span class="text-slate-300 font-mono">ISO-27001 / PCI-DSS</span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>Active Shift</span>
                    <span class="text-slate-300 font-mono">{{ date('D, d M Y') }} &middot; Shift A</span>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span>System Status</span>
                    <span class="text-emerald-400 font-medium">All Modules Online</span>
                </div>
            </div>
        </div>

        {{-- Footer note --}}
        <div class="text-xs text-slate-500 font-mono">
            Property ID: GH-NBI-01 &middot; Build {{ app()->version() }}
        </div>
    </div>

    {{-- Right Panel: Login Form --}}
    <div class="flex-1 flex items-center justify-center p-8 sm:p-12 bg-slate-950">
        <div class="w-full max-w-md">

            {{-- Mobile Brand Header --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold text-lg">GH</div>
                <div>
                    <span class="font-bold text-lg text-white">Grand Horizon Hotel</span>
                    <span class="block text-xs text-slate-400 font-mono">PMS Staff Portal</span>
                </div>
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-white tracking-tight">Staff Sign In</h1>
                <p class="text-sm text-slate-400 mt-1">Enter your work email and password to access your station.</p>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Staff Email / ID</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="staff@example.test"
                        class="block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors @error('email') border-red-500 focus:ring-red-500 @enderror"
                    >
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Reset password</a>
                        @endif
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="block w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-colors @error('password') border-red-500 focus:ring-red-500 @enderror"
                    >
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                </div>

                {{-- Remember Me & Terminal Session --}}
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-700 bg-slate-900 text-brand-600 focus:ring-brand-500 focus:ring-offset-slate-950"
                        >
                        <span class="text-xs text-slate-400">Keep station active</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-medium rounded-lg text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 focus:ring-offset-slate-950"
                >
                    Sign In to Portal
                </button>
            </form>

            
        </div>
    </div>
</body>
</html>
