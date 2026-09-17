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

        <title>{{ config('app.name', 'Hotel Management System') }}</title>

        <!-- Modern Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'DM Sans', sans-serif; }
            h1, h2, h3, h4, h5, h6 { font-family: 'Cormorant Garamond', serif; }
            
            /* Custom Scrollbar for a premium feel */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: rgba(15, 23, 42, 0.1); 
            }
            ::-webkit-scrollbar-thumb {
                background: rgba(148, 163, 184, 0.3); 
                border-radius: 4px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: rgba(148, 163, 184, 0.5); 
            }
        </style>
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50 dark:bg-slate-900 dark:text-slate-100 overflow-hidden flex h-screen" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/80 backdrop-blur-sm lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="sidebarOpen = false"></div>

        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden relative w-full">
            
            <!-- Top Navigation Bar -->
            @include('layouts.topbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                <!-- Page Heading -->
                @isset($header)
                    <header class="mb-8 flex items-center justify-between animate-fade-in-down">
                        {{ $header }}
                    </header>
                @endisset

                <div class="animate-fade-in-up">
                    {{ $slot }}
                </div>
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
