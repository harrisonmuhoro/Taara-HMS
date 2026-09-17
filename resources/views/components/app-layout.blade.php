@props(['title' => null, 'header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            (() => {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', savedTheme ? savedTheme === 'dark' : prefersDark);
            })();
        </script>
        <title>{{ $title ? $title . ' · ' . config('app.name') : config('app.name', 'Hotel MS') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'DM Sans', sans-serif; }
            h1, h2, h3, h4, h5, h6 { font-family: 'Cormorant Garamond', serif; }
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.25); border-radius: 3px; }
            ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.45); }
            @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fade-in-down { animation: fadeInDown 0.4s ease both; }
            .animate-fade-in-up { animation: fadeInUp 0.4s ease both; }
        </style>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#0B0F1A] text-slate-900 dark:text-slate-100 overflow-hidden flex h-screen" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>
        
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-2/3 h-96 bg-brand-600/5 pointer-events-none"></div>
            
            @include('layouts.topbar')

            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                @isset($header)
                    <header class="mb-8 animate-fade-in-down">{{ $header }}</header>
                @endisset
                <div class="animate-fade-in-up">{{ $slot }}</div>
            </main>
        </div>
    </body>
</html>
