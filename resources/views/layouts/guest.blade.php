<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('taara-hms-mark.svg') }}" type="image/svg+xml">
        <title>{{ config('app.name', 'Taara HMS') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600&family=Source+Serif+4:opsz,wght@8..60,600&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-paper font-sans text-ink antialiased">
        <div class="flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <a href="/" class="font-display text-2xl">Taara HMS</a>
            <div class="mt-6 w-full overflow-hidden rounded border border-[#D9CFC0] bg-surface px-6 py-4 sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
