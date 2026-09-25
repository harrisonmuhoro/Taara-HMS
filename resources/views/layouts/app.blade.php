<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('grand-horizon-mark.svg') }}" type="image/svg+xml">
        <script>
            (() => {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', savedTheme ? savedTheme === 'dark' : prefersDark);
            })();
        </script>
        <title>{{ config('app.name', 'Taara HMS') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex h-screen overflow-hidden bg-paper font-sans antialiased text-ink dark:bg-paper-dark dark:text-[#F0E6D8]" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" class="no-print fixed inset-0 z-20 bg-ink/40 lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>
        <div class="no-print">
            @include('layouts.sidebar')
        </div>
        <div class="app-content-shell flex min-w-0 flex-1 flex-col overflow-hidden">
            <div class="no-print">
                @include('layouts.topbar')
            </div>
            <main class="app-content-main flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6">
                @isset($header)
                    <header class="mb-6">{{ $header }}</header>
                @endisset
                <div>{{ $slot }}</div>
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
