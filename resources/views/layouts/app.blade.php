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
    <body class="font-sans antialiased text-gray-900 bg-gray-100 flex h-screen overflow-hidden" x-data="{ open: false }">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="open" @click="open = false" class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            
            <!-- Header (Top bar just for Logout/Profile usually, but here we can just put the Page Heading) -->
            <header class="bg-white shadow z-10 sticky top-0">
                <div class="flex justify-between items-center py-4 px-6 lg:px-8">
                    @isset($header)
                        <div class="font-bold text-gray-800">
                            {{ $header }}
                        </div>
                    @endisset
                    <!-- Mobile Hamburger -->
                    <div class="-mr-2 flex items-center lg:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
