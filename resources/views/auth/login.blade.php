<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · {{ config('app.name', 'Grand Horizon') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen bg-paper font-sans antialiased text-ink">

    @php
        $localHour = now()->timezone(config('app.timezone'))->hour;
        $greeting = $localHour < 12 ? 'Good morning' : ($localHour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp

    <div class="relative hidden flex-col justify-between overflow-hidden bg-brand-700 bg-cover bg-center p-10 text-[#FFF8F2] lg:flex lg:w-[42%]" style="background-image: url('{{ asset('images/login-lobby.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-br from-[#4A1F12]/95 via-[#6F321D]/85 to-[#263528]/90"></div>
        <div class="relative flex items-center gap-3">
            <img src="{{ asset('grand-horizon-mark.svg') }}" alt="Grand Horizon" class="h-11 w-11 rounded-xl shadow-lg">
            <div>
                <p class="font-display text-3xl">Grand Horizon</p>
                <p class="mt-1 text-sm text-brand-100">Nairobi · Property desk</p>
            </div>
        </div>
        <div class="relative max-w-sm">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-brand-100">Your shift, at a glance</p>
            <p class="mt-4 text-xl font-semibold leading-snug">Sign in to work today’s arrivals, rooms, and bills.</p>
            <div class="mt-6 grid grid-cols-2 gap-3 text-sm text-brand-100">
                <div class="rounded-xl border border-white/15 bg-black/10 px-3 py-3">Arrivals & check-ins</div>
                <div class="rounded-xl border border-white/15 bg-black/10 px-3 py-3">Room status</div>
                <div class="rounded-xl border border-white/15 bg-black/10 px-3 py-3">Guest billing</div>
                <div class="rounded-xl border border-white/15 bg-black/10 px-3 py-3">Daily operations</div>
            </div>
            <p class="mt-3 text-sm text-brand-100">{{ now()->timezone(config('app.timezone'))->format('l, j F Y') }}</p>
        </div>
        <p class="relative text-xs text-brand-200">Staff only. Ask the duty manager if you need an account.</p>
    </div>

    <div class="flex flex-1 items-center justify-center p-6 sm:p-10">
        <div class="w-full max-w-sm">
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <img src="{{ asset('grand-horizon-mark.svg') }}" alt="Grand Horizon" class="h-10 w-10 rounded-lg">
                <div>
                    <p class="font-display text-2xl text-ink">Grand Horizon</p>
                    <p class="text-sm text-ink-muted">Nairobi · Property desk</p>
                </div>
            </div>

            <div class="rounded-2xl border border-[#D9CFC0] bg-white/70 p-6 shadow-sm sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#256D6A]">Welcome back</p>
            <h1 class="mt-2 text-2xl font-semibold text-ink">{{ $greeting }}, welcome back</h1>
            <p class="mt-1 text-sm text-ink-muted">Use the email issued by the hotel to access your desk.</p>

            <x-auth-session-status class="mt-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5" x-data="{ showPassword: false }">
                @csrf
                <div>
                    <label for="email" class="form-label">Work email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input @error('email') form-input-error @enderror">
                    <x-input-error :messages="$errors->get('email')" class="form-error" />
                </div>
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="form-label mb-0">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Reset password</a>
                        @endif
                    </div>
                    <div class="relative">
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" class="form-input pr-16 @error('password') form-input-error @enderror">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-[#256D6A] hover:text-[#1F5957]" :aria-label="showPassword ? 'Hide password' : 'Show password'">
                            <svg x-show="!showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.046.14.046.288 0  .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.126 19 12 19c1.364 0 2.652-.23 3.83-.65M6.228 6.228A10.45 10.45 0 0112 5c4.874 0 8.774 2.662 10.066 7a10.523 10.523 0 01-4.132 5.411M6.228 6.228L3 3m3.228 3.228l3.18 3.18m5.364 5.364L21 21m-6.228-6.228a3 3 0 01-4.244-4.244" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="form-error" />
                </div>
                <label for="remember_me" class="inline-flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-[#D9CFC0] text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-ink-muted">Stay signed in on this desk</span>
                </label>
                <button type="submit" class="btn-sign-in w-full justify-center py-2.5">Sign in</button>
            </form>
            <p class="mt-6 text-center text-xs text-ink-muted">Your access is protected. Only use this desk on a trusted device.</p>
            @if (Route::has('auth.help'))
                <p class="mt-3 text-center text-sm text-ink-muted">
                    Need help signing in?
                    <a href="{{ route('auth.help') }}" class="font-semibold text-[#256D6A] hover:text-[#1F5957]">Get support</a>
                </p>
            @endif
            </div>
        </div>
    </div>
</body>
</html>
