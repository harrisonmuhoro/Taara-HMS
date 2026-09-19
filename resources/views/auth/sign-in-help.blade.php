<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign-in help · {{ config('app.name', 'Grand Horizon') }}</title>
    <link rel="icon" href="{{ asset('grand-horizon-mark.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-paper font-sans text-ink">
    <main class="mx-auto flex min-h-screen w-full max-w-2xl items-center justify-center p-6 sm:p-10">
        <section class="w-full rounded-2xl border border-[#D9CFC0] bg-white/80 p-6 shadow-sm sm:p-10">
            <div class="flex items-center gap-3">
                <img src="{{ asset('grand-horizon-mark.svg') }}" alt="Grand Horizon" class="h-11 w-11 rounded-xl">
                <div>
                    <p class="font-display text-2xl text-ink">Grand Horizon</p>
                    <p class="text-sm text-ink-muted">Nairobi · Property desk</p>
                </div>
            </div>

            <div class="mt-8">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#256D6A]">Sign-in help</p>
                <h1 class="mt-2 text-2xl font-semibold text-ink">Get back to your hotel desk</h1>
                <p class="mt-2 text-sm leading-6 text-ink-muted">Follow these steps to sign in securely to Grand Horizon.</p>
            </div>

            <ol class="mt-8 space-y-5">
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#256D6A] text-sm font-semibold text-white">1</span>
                    <div>
                        <h2 class="font-semibold text-ink">Use your hotel email</h2>
                        <p class="mt-1 text-sm leading-6 text-ink-muted">Enter the work email issued to you by the hotel administrator.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#256D6A] text-sm font-semibold text-white">2</span>
                    <div>
                        <h2 class="font-semibold text-ink">Enter your password</h2>
                        <p class="mt-1 text-sm leading-6 text-ink-muted">Type your account password. Use the eye icon if you need to check what you entered.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#256D6A] text-sm font-semibold text-white">3</span>
                    <div>
                        <h2 class="font-semibold text-ink">Select “Stay signed in” only on a trusted desk</h2>
                        <p class="mt-1 text-sm leading-6 text-ink-muted">Leave it unchecked on shared or public computers.</p>
                    </div>
                </li>
                <li class="flex gap-4">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#256D6A] text-sm font-semibold text-white">4</span>
                    <div>
                        <h2 class="font-semibold text-ink">Select Sign in</h2>
                        <p class="mt-1 text-sm leading-6 text-ink-muted">If your account is active, you will be taken to your hotel desk.</p>
                    </div>
                </li>
            </ol>

            <div class="mt-8 border-t border-[#D9CFC0] pt-6">
                <p class="text-sm text-ink-muted">Still unable to access your account?</p>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mt-3 inline-flex font-semibold text-[#256D6A] hover:text-[#1F5957]">Forgot password? Reset it here <span aria-hidden="true" class="ml-1">→</span></a>
                @endif
            </div>

            <a href="{{ route('login') }}" class="mt-8 inline-flex text-sm font-semibold text-ink-muted hover:text-ink">← Back to sign in</a>
        </section>
    </main>
</body>
</html>
