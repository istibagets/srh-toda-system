<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Web App & Mobile Meta Tags (Android & iOS) -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="{{ \App\Support\SystemSettings::brandName() }}">
        <meta name="theme-color" content="#2563eb">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon & Touch Icon -->
        <link rel="icon" type="image/png" href="{{ srh_logo_url() }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ srh_logo_url() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Native App Touch & Scroll Engine */
            html, body {
                overscroll-behavior-y: contain;
                -webkit-tap-highlight-color: transparent;
                -webkit-touch-callout: none;
            }

            button, a, .select-none {
                user-select: none;
                -webkit-user-select: none;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100 min-h-[100dvh] flex flex-col justify-center items-center p-4 pt-safe pb-safe" style="padding-top: max(1rem, env(safe-area-inset-top, 0px)); padding-bottom: max(1rem, env(safe-area-inset-bottom, 0px));">
        <div class="w-full max-w-md my-auto flex flex-col items-center">
            <div class="mb-4">
                <a href="/" class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-white shadow-md p-2 hover:scale-105 transition-transform border border-slate-200/80" style="width: 96px; height: 96px; display: inline-flex; align-items: center; justify-content: center;">
                    <img src="{{ srh_logo_url() }}" alt="{{ \App\Support\SystemSettings::brandName() }}" width="84" height="84" class="w-full h-full object-contain" style="width: 84px; height: 84px; max-width: 84px; max-height: 84px; object-fit: contain;">
                </a>
            </div>

            <div class="w-full bg-white shadow-xl overflow-hidden rounded-2xl p-6 sm:p-8 border border-gray-100">
                {{ $slot }}
            </div>
        <script>
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]?.type === 'back_forward')) {
                    if (window.clearPageCache) window.clearPageCache();
                }
            });
        </script>
    </body>
</html>
