<!DOCTYPE html>
<html lang="tr" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta http-equiv="content-language" content="tr">
    <meta name="language" content="Turkish">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="HandheldFriendly" content="true">
    <meta name="MobileOptimized" content="width">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#e11d48">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $title ?? ($siteSettings['seo_title'] ?? config('app.name', 'İstanbul Çiçek Evi | Aynı Gün Taze Çiçek Siparişi'));
        $pageDescription = $description ?? ($siteSettings['seo_description'] ?? 'İstanbul genelinde en taze çiçekler, gül buketleri, saksı orkideleri, kapıda ödeme ve 90 dakikada VIP teslimat garantisi.');
        $pageKeywords = $keywords ?? ($siteSettings['seo_keywords'] ?? 'çiçek siparişi, istanbul çiçekçi, gül buketi, orkide siparişi, vip teslimat');
        $pageRobots = $robots ?? ($siteSettings['seo_robots'] ?? 'index, follow');
        $canonicalUrl = $canonical ?? url()->current();
        $ogImage = $image ?? (!empty($siteSettings['logo']) ? asset('storage/' . $siteSettings['logo']) : asset('images/logo.png'));
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Favicon & Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-title" content="İstanbul Çiçek Evi">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v={{ @filemtime(public_path('favicon.svg')) ?: 20261009 }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v={{ @filemtime(public_path('favicon-32x32.png')) ?: 20261009 }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v={{ @filemtime(public_path('favicon-16x16.png')) ?: 20261009 }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ @filemtime(public_path('favicon.ico')) ?: 20261009 }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ @filemtime(public_path('apple-touch-icon.png')) ?: 20261009 }}">

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:locale" content="tr_TR">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ config('app.name', 'İstanbul Çiçek Evi') }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <!-- Webmaster Doğrulama Kodları -->
    @if(!empty($siteSettings['google_verification']))
        <meta name="google-site-verification" content="{{ $siteSettings['google_verification'] }}">
    @endif
    @if(!empty($siteSettings['yandex_verification']))
        <meta name="yandex-verification" content="{{ $siteSettings['yandex_verification'] }}">
    @endif
    @if(!empty($siteSettings['bing_verification']))
        <meta name="msvalidate.01" content="{{ $siteSettings['bing_verification'] }}">
    @endif

    <!-- Fonts & Icons (0 Render-Blocking & Yerel Ultra Hızlı Türkçe Uyumlu Fontlar) -->
    <link rel="preload" href="/fonts/figtree-latin-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/figtree-latin-ext-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/figtree-latin-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/figtree-latin-ext-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/figtree-latin-700.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/figtree-latin-ext-700.woff2" as="font" type="font/woff2" crossorigin>
    
    <!-- FontAwesome 6 (Gecikmeli Yükleme - 0 Unused CSS Kilitlenmesi) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <style>
        body { font-family: 'Figtree', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    </style>

    <!-- Scripts & Minified Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Analytics (GA4) -->
    @if(!empty($siteSettings['google_analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSettings['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $siteSettings['google_analytics_id'] }}');
        </script>
    @endif

    <!-- Meta Pixel Code -->
    @if(!empty($siteSettings['meta_pixel_id']))
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $siteSettings['meta_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" class="hidden"
            src="https://www.facebook.com/tr?id={{ $siteSettings['meta_pixel_id'] }}&ev=PageView&noscript=1"
        /></noscript>
    @endif

    <!-- Özel Header Scriptleri -->
    @if(!empty($siteSettings['header_scripts']))
        {!! $siteSettings['header_scripts'] !!}
    @endif
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 flex flex-col min-h-screen overflow-x-hidden w-full max-w-full">
    @if(!empty($siteSettings['body_scripts']))
        {!! $siteSettings['body_scripts'] !!}
    @endif

    <!-- Header (Ana Menü) -->
    <header class="bg-white shadow-xs sticky top-0 z-40" x-data="{ mobileOpen: false }">
        <!-- 1. Ana Başlık & Aksiyon Satırı -->
        <div class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8 w-full max-w-full overflow-hidden">
            <div class="flex items-center justify-between h-18 sm:h-22 gap-2 sm:gap-6 py-1">
                <!-- Sol: Logo (Geniş, Ferah, Prestijli) -->
                <div class="flex-shrink-0 flex items-center min-w-0">
                    <a href="/" class="hover:opacity-95 transition-opacity block">
                        @if(!empty($siteSettings['logo']))
                            <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="İstanbul Çiçek Evi" class="h-11 sm:h-14 md:h-16 w-auto max-w-[210px] sm:max-w-[280px] md:max-w-none object-contain">
                        @else
                            <img src="{{ asset('images/logo.svg') }}?v={{ @filemtime(public_path('images/logo.svg')) ?: 20261009 }}" alt="İstanbul Çiçek Evi" class="h-11 sm:h-14 md:h-16 w-auto max-w-[210px] sm:max-w-[280px] md:max-w-none object-contain">
                        @endif
                    </a>
                </div>

                <!-- Orta: Arama Çubuğu (Masaüstü & Tablet) -->
                <div class="hidden md:flex flex-1 max-w-xl mx-2 lg:mx-6">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Çiçek ara... (Örn: 101 Kırmızı Gül, Beyaz Orkide, Lilyum...)"
                            style="padding-left: 3.25rem !important; padding-right: 7.25rem !important;"
                            class="header-search-input w-full pl-14 pr-28 py-3 rounded-full border border-gray-200 bg-gray-50/80 hover:bg-white text-sm sm:text-base font-medium shadow-2xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition-all">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-rose-600 transition-colors pointer-events-none flex items-center justify-center w-6 h-6">
                            <i class="fas fa-search text-base"></i>
                        </div>
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-sm px-5 py-2 rounded-full shadow-xs transition-all active:scale-95">
                            Ara
                        </button>
                    </form>
                </div>

                <!-- Sağ: Özel Aksiyonlar & Kullanıcı Alanı -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

                    <!-- Giriş & Üyelik / Müşteri Hesabı Butonları -->
                    @auth
                        @if(auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-2 sm:px-3.5 sm:py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-full text-xs sm:text-sm font-black transition-all shadow-xs">
                                <i class="fas fa-shield-alt text-amber-400 text-xs sm:text-sm"></i>
                                <span>Panel</span>
                            </a>
                        @else
                            <!-- Müşteri Açılır Menüsü (Dropdown) -->
                            <div class="relative" x-data="{ userMenu: false }" @click.away="userMenu = false">
                                <button type="button" @click="userMenu = !userMenu" 
                                        class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 sm:px-3.5 sm:py-2 bg-rose-50 hover:bg-rose-100 text-rose-900 rounded-full border border-rose-200/90 text-xs sm:text-sm font-black transition-all shadow-2xs cursor-pointer">
                                    <span class="w-6 h-6 rounded-full bg-gradient-to-tr from-rose-600 to-pink-600 text-white text-[11px] font-black flex items-center justify-center shadow-xs">
                                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                    <span class="hidden sm:inline max-w-[110px] truncate">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-[10px] text-rose-500 transition-transform duration-200" :class="{ 'rotate-180': userMenu }"></i>
                                </button>

                                <!-- Dropdown Menü -->
                                <div x-show="userMenu" x-transition x-cloak 
                                     class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 text-left">
                                    <div class="px-4 py-2.5 border-b border-gray-100 bg-rose-50/40 rounded-t-2xl">
                                        <div class="font-black text-xs text-gray-900 truncate">{{ auth()->user()->name }}</div>
                                        <div class="text-[11px] text-gray-500 truncate">{{ auth()->user()->phone ?? auth()->user()->email }}</div>
                                        <div class="mt-1 inline-flex items-center gap-1 text-[9px] font-black bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full uppercase">
                                            <i class="fas fa-user-check"></i> Müşteri Hesabı
                                        </div>
                                    </div>

                                    <div class="py-1">
                                        <a href="{{ route('customer.account') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                            <i class="fas fa-id-card text-rose-500 w-4 text-center"></i> Hesabım &amp; Profil
                                        </a>
                                        <a href="{{ route('cart.index') }}" class="flex items-center justify-between px-4 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                            <span class="flex items-center gap-2.5">
                                                <i class="fas fa-shopping-cart text-rose-500 w-4 text-center"></i> Aktif Sepetim
                                            </span>
                                            @php $headerCartQty = count(session()->get('cart', [])); @endphp
                                            @if($headerCartQty > 0)
                                                <span class="bg-rose-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ $headerCartQty }}</span>
                                            @endif
                                        </a>
                                        <a href="{{ route('customer.account') }}#siparislerim" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                            <i class="fas fa-box text-rose-500 w-4 text-center"></i> Siparişlerim &amp; Takip
                                        </a>
                                        <a href="{{ route('customer.account') }}#kuponlarim" class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                            <i class="fas fa-ticket-alt text-amber-500 w-4 text-center"></i> İndirim Kuponlarım
                                        </a>
                                    </div>

                                    <div class="pt-1 border-t border-gray-100">
                                        <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Güvenli Çıkış Yap
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Misafir (Giriş Yap & Üye Ol) -->
                        <div class="flex items-center gap-1 sm:gap-2">
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 sm:gap-1.5 px-2 py-1.5 sm:px-3.5 sm:py-2 text-xs font-bold text-gray-700 hover:text-rose-600 bg-gray-50 hover:bg-rose-50 border border-gray-200 rounded-full transition-all">
                                <i class="fas fa-user text-gray-400 text-xs"></i>
                                <span class="hidden sm:inline">Giriş Yap</span>
                                <span class="sm:hidden">Giriş</span>
                            </a>
                            <a href="{{ route('register') }}" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-black text-white bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 rounded-full shadow-xs transition-all active:scale-95">
                                <i class="fas fa-user-plus text-xs"></i>
                                <span>Üye Ol</span>
                            </a>
                        </div>
                    @endauth

                    <!-- Sepet İkonu -->
                    <a href="{{ route('cart.index') }}" aria-label="Alışveriş Sepetim" title="Alışveriş Sepeti" 
                       class="relative p-2 sm:p-3 bg-gray-50 hover:bg-rose-50 text-gray-700 hover:text-rose-600 rounded-full border border-gray-200 transition-all flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-sm sm:text-lg"></i>
                        @php $cartCount = count(session()->get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-black text-white bg-rose-600 rounded-full shadow-xs">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- 3 Çizgi Menü Butonu (Hem Masaüstü Hem Mobil) -->
                    <button type="button" @click="mobileOpen = !mobileOpen"
                        class="inline-flex items-center justify-center gap-1.5 p-2 sm:px-4 sm:py-2.5 rounded-full text-gray-800 hover:text-rose-600 bg-gray-50 hover:bg-rose-50 border border-gray-200 transition-all font-black text-xs sm:text-sm shadow-2xs focus:outline-none" aria-label="Menü">
                        <i x-show="!mobileOpen" class="fas fa-bars text-sm sm:text-base"></i>
                        <i x-show="mobileOpen" class="fas fa-times text-sm sm:text-base text-rose-600"></i>
                        <span class="hidden sm:inline font-black">Menü</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. KATEGORİLER (Zarif, Şık & Dengeli Menü) -->
        <nav class="border-t border-b border-gray-100 bg-white/95 backdrop-blur-md shadow-2xs overflow-hidden w-full">
            <div class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8">
                <div class="flex items-center justify-start md:justify-center gap-1.5 sm:gap-3 md:gap-5 lg:gap-7 overflow-x-auto no-scrollbar py-2 sm:py-2.5 scroll-smooth" style="overscroll-behavior-x: contain; -webkit-overflow-scrolling: touch;">
                    @php
                        $allHeaderCategories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
                    @endphp
                    @foreach($allHeaderCategories as $cat)
                        <a href="{{ route('category.show', $cat->slug) }}" 
                           class="flex-shrink-0 px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-xl text-gray-800 hover:text-rose-600 hover:bg-rose-50/80 text-xs sm:text-sm font-bold tracking-normal transition-all whitespace-nowrap">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <!-- 3. Mobilde Arama Çubuğu (md:hidden) -->
        <div class="md:hidden bg-gray-50 border-b border-gray-200/70 py-2.5 px-3 sm:px-4 shadow-inner">
            <form action="{{ route('products.index') }}" method="GET" class="w-full relative group">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Çiçek ara... (Örn: 101 Kırmızı Gül, Orkide...)"
                    style="padding-left: 2.75rem !important; padding-right: 5.5rem !important; font-size: 16px !important;"
                    class="header-search-input-mobile w-full pl-11 pr-24 py-2.5 rounded-xl border border-gray-300 bg-white text-base font-medium focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all box-border">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none flex items-center justify-center w-5 h-5">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 bg-gradient-to-r from-rose-600 to-pink-600 text-white font-black text-sm px-4 py-1.5 rounded-lg shadow-xs">
                    Ara
                </button>
            </form>
        </div>

        <!-- 4. 3 Çizgi Menü Çekmecesi (Masaüstü & Mobil Uyumlu) -->
        <div x-show="mobileOpen" x-transition x-cloak class="bg-white border-b border-gray-200 shadow-2xl relative z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                
                <!-- 👤 Müşteri Portalı / Oturum Alanı (Yiğit-Core Entegrasyonu) -->
                <div class="mb-5 pb-5 border-b border-gray-100">
                    @auth
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-rose-50 via-pink-50 to-purple-50 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-rose-600 text-white font-black text-base flex items-center justify-center shadow-xs">
                                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-black text-sm text-gray-900 flex items-center gap-2">
                                        {{ auth()->user()->name }}
                                        @if(auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))
                                            <span class="text-[10px] bg-slate-900 text-amber-400 px-2 py-0.5 rounded-full font-black">YÖNETİCİ</span>
                                        @else
                                            <span class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-black">MÜŞTERİ</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 font-medium">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-rose-200/50">
                                @if(auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))
                                    <a href="{{ route('dashboard') }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black transition-all">
                                        <i class="fas fa-shield-alt text-amber-400 mr-1"></i> Panel
                                    </a>
                                @endif
                                <a href="{{ route('cart.index') }}" class="px-3 py-1.5 bg-white hover:bg-rose-100 text-rose-800 border border-rose-300 rounded-xl text-xs font-black transition-all flex items-center gap-1.5">
                                    <i class="fas fa-shopping-cart text-rose-600"></i> Sepetim
                                    @php $drawerCartQty = count(session()->get('cart', [])); @endphp
                                    @if($drawerCartQty > 0)
                                        <span class="bg-rose-600 text-white text-[10px] font-black px-1.5 py-0.2 rounded-full">{{ $drawerCartQty }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('customer.account') }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black transition-all shadow-xs flex items-center gap-1.5">
                                    <i class="fas fa-ticket-alt"></i> Hesabım &amp; Kuponlarım
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-white hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-black transition-all cursor-pointer" title="Çıkış Yap">
                                        <i class="fas fa-sign-out-alt"></i> Çıkış
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-gradient-to-r from-rose-50/70 to-pink-50/50 border border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="font-black text-sm text-gray-900">🌸 İstanbul Çiçek Evi'ne Hoş Geldiniz</div>
                                <div class="text-xs text-gray-500 mt-0.5">Siparişlerinizi takip etmek ve indirim kuponlarınızı kullanmak için giriş yapın.</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="px-4 py-2 bg-white hover:bg-rose-50 text-gray-800 hover:text-rose-600 border border-gray-200 rounded-xl text-xs font-black transition-all shadow-2xs">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Giriş Yap
                                </a>
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-black transition-all shadow-xs">
                                    <i class="fas fa-user-plus mr-1"></i> Üye Ol
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Üst Özel Hizmet Kartları (Canlı Sipariş Takibi, Flora AI) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
                    <a href="{{ route('order.track') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition-all text-slate-900 group">
                        <div class="w-11 h-11 rounded-xl bg-slate-800 text-white flex items-center justify-center text-lg shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <div class="font-black text-sm text-slate-900">🚚 Canlı Sipariş Takibi</div>
                            <div class="text-xs text-slate-600">Kurye ve teslimat durumunu sorgula</div>
                        </div>
                    </a>

                    <a href="{{ url('/arimo') }}" class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-pink-50 hover:bg-pink-100/80 border border-pink-200 transition-all text-pink-950 group">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-pink-500 to-rose-600 text-white flex items-center justify-center text-lg shadow-sm group-hover:scale-105 transition-transform">
                            <span>🌸</span>
                        </div>
                        <div>
                            <div class="font-black text-sm text-pink-950">🤖 Flora AI Çiçek Danışmanı</div>
                            <div class="text-xs text-pink-800">Yapay zeka ile anında doğru çiçek seçimi</div>
                        </div>
                    </a>
                </div>

                <!-- Alt Hızlı Menü Bağlantıları -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-4 border-t border-gray-100 text-sm font-bold text-gray-700">
                    <a href="{{ route('districts.index') }}" class="p-2.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-rose-500"></i> 39 İlçe Teslimat
                    </a>
                    <a href="{{ route('blog.index') }}" class="p-2.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-book-open text-rose-500"></i> Çiçek Rehberi &amp; Blog
                    </a>
                    <a href="{{ route('contact') }}" class="p-2.5 rounded-xl hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-400"></i> İletişim &amp; Atölyemiz
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Ana İçerik -->
    <main class="flex-grow overflow-x-hidden w-full max-w-full">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-12">
        <div class="max-w-7xl mx-auto pt-14 pb-8 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand -->
                <div class="md:col-span-1">
                    @if(!empty($siteSettings['logo']))
                        <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="Logo" class="h-10 mb-4 brightness-0 invert">
                    @else
                        <img src="{{ asset('images/logo-white.svg') }}?v={{ @filemtime(public_path('images/logo-white.svg')) ?: 20261009 }}" alt="İstanbul Çiçek Evi" class="h-9 mb-4 object-contain">
                    @endif
                    <p class="text-gray-400 text-sm leading-relaxed">İstanbul'un her köşesine en taze çiçekler, özenle hazırlanan tasarım buketler ve kişiye özel kart notları ile aynı gün teslimat garantisi.</p>

                    <!-- Social Media -->
                    <div class="flex gap-3 mt-6">
                        <a href="{{ $siteSettings['facebook'] ?? '#' }}" target="_blank" aria-label="Facebook Sayfamız" title="Facebook" class="w-10 h-10 bg-rose-700 hover:bg-rose-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="{{ $siteSettings['instagram'] ?? '#' }}" target="_blank" aria-label="Instagram Sayfamız" title="Instagram" class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}" target="_blank" aria-label="WhatsApp Destek Hattı" title="WhatsApp" class="w-10 h-10 bg-green-600 hover:bg-green-500 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-whatsapp text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Hızlı Linkler -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-300 mb-5">Hızlı Linkler</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Ana Sayfa</a></li>
                        <li><a href="{{ route('districts.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Çiçek Gönderim Bölgeleri (39 İlçe)</a></li>
                        <li><a href="{{ route('order.track') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Çiçek Sipariş Takibi</a></li>
                        <li><a href="{{ route('arimo') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Flora AI Çiçek &amp; Not Asistanı</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Çiçek Bakım Rehberi</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> İletişim</a></li>
                        <li><a href="{{ route('social') }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> Sosyal Medya</a></li>
                    </ul>
                </div>

                <!-- Col 3: Kurumsal -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-300 mb-5">Kurumsal &amp; Hukuki</h3>
                    <ul class="space-y-3 text-sm">
                        @php $footerPages = \App\Models\Page::where('is_active', true)->get(); @endphp
                        @forelse($footerPages as $fpage)
                            <li><a href="{{ route('page.show', $fpage->slug) }}" class="text-gray-300 hover:text-white transition-colors flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-rose-500"></i> {{ $fpage->title }}</a></li>
                        @empty
                            <li class="text-gray-400 text-xs">Henüz sayfa eklenmedi.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Col 4: İletişim -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-gray-300 mb-5">İletişim &amp; Sipariş Hattı</h3>
                    <ul class="space-y-4 text-sm">
                        @if(!empty($siteSettings['phone']))
                        <li>
                            <a href="tel:{{ $siteSettings['phone'] }}" class="flex items-start gap-3 text-gray-300 hover:text-white transition-colors group">
                                <span class="w-8 h-8 bg-rose-950/80 group-hover:bg-rose-700 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors"><i class="fas fa-phone text-xs text-rose-400 group-hover:text-white"></i></span>
                                <span>{{ $siteSettings['phone'] }}</span>
                            </a>
                        </li>
                        @endif
                        @if(!empty($siteSettings['email']))
                        <li>
                            <!--email_off-->
                            <a href="mailto:{{ $siteSettings['email'] }}" class="flex items-start gap-3 text-gray-300 hover:text-white transition-colors group">
                                <span class="w-8 h-8 bg-rose-950/80 group-hover:bg-rose-700 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors"><i class="fas fa-envelope text-xs text-rose-400 group-hover:text-white"></i></span>
                                <span>{{ $siteSettings['email'] }}</span>
                            </a>
                            <!--/email_off-->
                        </li>
                        @endif
                        @if(!empty($siteSettings['address']))
                        <li class="flex items-start gap-3 text-gray-300">
                            <span class="w-8 h-8 bg-rose-950/80 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fas fa-map-marker-alt text-xs text-rose-400"></i></span>
                            <span>{{ $siteSettings['address'] }}</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- SEO Popüler Aramalar & Anahtar Kelime Bulutu (Google Bot & Kullanıcı Hızlı Arama) -->
            @php
                $rawFooterTags = $siteSettings['seo_footer_tags'] ?? 'istanbul çiçek siparişi, aynı gün çiçek teslimatı, gül buketi, orkide siparişi, sevgiliye çiçek, doğum günü çiçeği, kadıköy çiçekçi, beşiktaş çiçekçi, üsküdar çiçekçi, nişantaşı çiçekçi, şakayık buketi, teraryum, saksı bitkisi, kutuda güller, tasarım aranjman, düğün çelengi';
                $footerTags = array_filter(array_map('trim', explode(',', $rawFooterTags)));
            @endphp
            @if(count($footerTags) > 0)
                <div class="border-t border-gray-800/80 pt-8 pb-4 mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-300 mb-3 flex items-center gap-2">
                        <i class="fas fa-search text-rose-500 text-[10px]"></i> Popüler Aramalar &amp; Hizmetlerimiz:
                    </h4>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($footerTags as $ftag)
                            <a href="{{ route('products.index', ['search' => $ftag]) }}" 
                               class="text-[11px] text-gray-300 hover:text-white bg-gray-800/80 hover:bg-rose-600 px-2.5 py-1 rounded-md transition-all duration-150 border border-gray-700 hover:border-rose-500">
                                {{ $ftag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs sm:text-sm text-gray-300">
                <span>&copy; {{ date('Y') }} İstanbul Çiçek Evi. Tüm hakları saklıdır.</span>
                <div class="flex gap-1 items-center text-gray-300 font-medium">
                    <i class="fas fa-heart text-rose-400 mr-1"></i>
                    Taze Çiçek Garantisi
                    <span class="mx-2">·</span>
                    <i class="fas fa-truck text-rose-400 mr-1"></i>
                    Aynı Gün Teslimat
                    <span class="mx-2">·</span>
                    <i class="fas fa-envelope-open-text text-rose-400 mr-1"></i>
                    Ücretsiz Kart Notu
                </div>
            </div>
        </div>
    </footer>

    <!-- Yüzen İletişim Butonları & En Üste Çık Butonu -->
    @if(!request()->routeIs('arimo'))
        <div x-data="{ showTopBtn: false }" 
             @scroll.window="showTopBtn = (window.pageYOffset > 350)"
             class="fixed bottom-4 right-3 sm:bottom-6 sm:right-6 flex flex-col space-y-2.5 sm:space-y-3 z-50">
            
            <!-- 🚀 En Üste Çık Butonu (Aşağı kaydırınca görünür) -->
            <button type="button"
                    x-show="showTopBtn"
                    x-cloak
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-75"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-75"
                    @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                    title="Sayfa Başına Dön"
                    aria-label="Sayfa Başına Dön"
                    class="bg-white hover:bg-rose-50 text-gray-700 hover:text-rose-600 rounded-full shadow-2xl transition-all flex items-center justify-center border border-gray-200 w-11 h-11 sm:w-14 sm:h-14 cursor-pointer transform hover:-translate-y-1 active:scale-90 group relative">
                <i class="fas fa-arrow-up text-base sm:text-xl font-black"></i>
                <span class="absolute right-full mr-4 bg-white text-gray-800 text-xs font-bold px-3 py-1.5 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap">
                    En Üste Çık
                </span>
            </button>

            <!-- Telefon Butonu -->
            <a href="tel:{{ $siteSettings['phone'] ?? '0212 555 12 34' }}" 
               aria-label="Telefonla Hemen Ara"
               title="Telefonla Hemen Ara"
               class="bg-rose-600 text-white p-2.5 sm:p-4 rounded-full shadow-lg hover:bg-rose-700 transition-colors flex items-center justify-center group relative w-11 h-11 sm:w-14 sm:h-14">
                <i class="fas fa-phone-alt text-lg sm:text-2xl"></i>
                <span class="absolute right-full mr-4 bg-white text-gray-800 text-xs sm:text-sm font-semibold px-4 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap">
                    Hemen Ara
                </span>
            </a>
        </div>
    @endif

    <!-- 🌸 FLORA YAPAY ZEKA ÇİÇEK & KART DANIŞMANI BUTONU -->
    @if(!request()->routeIs('arimo'))
        <div class="fixed bottom-4 left-3 sm:bottom-6 sm:left-6 z-40">
            <a href="{{ url('/arimo') }}" 
               aria-label="Flora Yapay Zeka Çiçek & Kart Danışmanı" 
               title="Flora AI Çiçek Danışmanı"
               class="group flex items-center gap-2 sm:gap-3 bg-gradient-to-r from-rose-950 via-pink-950 to-slate-950 text-white p-2 sm:px-4 sm:py-3 rounded-2xl shadow-2xl hover:shadow-rose-500/30 hover:scale-105 transition-all duration-300 border border-rose-400/40">
                <div class="relative">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-pink-600 flex items-center justify-center text-lg sm:text-xl shadow-md">
                        🌸
                    </div>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse"></span>
                </div>
                <div class="flex flex-col text-left">
                    <div class="flex items-center gap-1.5">
                        <span class="font-black text-xs sm:text-sm text-white tracking-wide">Flora AI</span>
                        <span class="text-[9px] bg-rose-500/20 text-rose-300 font-bold px-1.5 py-0.5 rounded-md border border-rose-400/30">Çiçek &amp; Kart</span>
                    </div>
                    <span class="hidden sm:block text-[10px] text-rose-200/90 font-medium whitespace-nowrap">
                        Kart notu &amp; çiçek önerisi 💌
                    </span>
                </div>
            </a>
        </div>
    @endif


    <!-- 🍪 KVKK ÇEREZ AÇIK RIZA BANNERI & İSPAT KÜTÜĞÜ -->
    <x-cookie-banner />

</body>
</html>
