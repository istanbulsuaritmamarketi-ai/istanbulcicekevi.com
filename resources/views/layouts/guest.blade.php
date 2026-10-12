<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'İstanbul Çiçek Evi') }}</title>

        <!-- Favicon & Touch Icons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ @filemtime(public_path('favicon.svg')) ?: 20261009 }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v={{ @filemtime(public_path('favicon-32x32.png')) ?: 20261009 }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v={{ @filemtime(public_path('favicon-16x16.png')) ?: 20261009 }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ @filemtime(public_path('favicon.ico')) ?: 20261009 }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ @filemtime(public_path('apple-touch-icon.png')) ?: 20261009 }}">

        <!-- Fonts & Icons (Yerel & Asenkron Türkçe Uyumlu) -->
        <link rel="preload" href="/fonts/figtree-latin-400.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/figtree-latin-ext-400.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/figtree-latin-700.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/fonts/figtree-latin-ext-700.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-8 px-4 sm:px-6 bg-gradient-to-b from-rose-50/60 via-pink-50/30 to-white">
            <div class="mb-6">
                <a href="/" class="hover:opacity-90 transition-opacity">
                    <x-application-logo />
                </a>
            </div>

            <div class="w-full sm:max-w-md p-6 sm:p-8 bg-white shadow-xl border border-gray-200/80 rounded-3xl overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
