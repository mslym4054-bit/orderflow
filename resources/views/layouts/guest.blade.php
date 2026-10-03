<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'OrderFlow') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center bg-base pt-6 sm:pt-0">
            <div class="mb-4 flex items-center gap-2">
                <svg class="w-10 h-10 text-neon" viewBox="0 0 32 32" fill="none">
                    <path d="M4 22c4 0 4-6 8-6s4 6 8 6 4-6 8-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 10l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-ink font-bold text-2xl">OrderFlow</span>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-surface border border-line rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
