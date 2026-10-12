<!-- Sidebar -->
<aside :class="{'translate-x-0': open, '-translate-x-full': !open}" 
       class="bg-slate-950 text-white w-64 space-y-3 py-5 px-3 fixed inset-y-0 left-0 lg:static lg:translate-x-0 transition-transform duration-200 ease-in-out z-40 flex flex-col border-r border-rose-900/30 shadow-2xl lg:shadow-none shrink-0">
    
    <style>
        .custom-admin-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-admin-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-admin-scrollbar::-webkit-scrollbar-thumb { background: rgba(244, 63, 94, 0.25); border-radius: 4px; }
        .custom-admin-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(244, 63, 94, 0.5); }
    </style>

    <!-- Logo & Mağaza Kısayolu -->
    <div class="px-2 mb-1 space-y-2.5">
        <a href="{{ route('dashboard') }}" class="flex items-center hover:opacity-95 transition-opacity">
            <img src="{{ asset('images/logo-white.svg') }}?v={{ @filemtime(public_path('images/logo-white.svg')) ?: 20261009 }}" alt="İstanbul Çiçek Evi" class="h-9 w-auto max-w-[190px] object-contain">
        </a>
        <a href="/" target="_blank" class="w-full py-1.5 px-3 bg-white/5 hover:bg-white/10 text-rose-200 hover:text-white rounded-xl text-xs font-bold transition-all flex items-center justify-between border border-rose-900/40 shadow-2xs">
            <span class="flex items-center gap-1.5">
                <i class="fas fa-store text-rose-400 text-xs"></i>
                <span>Canlı Mağaza</span>
            </span>
            <i class="fas fa-external-link-alt text-[10px] text-rose-300/60"></i>
        </a>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 overflow-y-auto space-y-1 pr-1 custom-admin-scrollbar">
        
        <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="fas fa-chart-line">
            Panel &amp; İstatistikler
        </x-sidebar-link>

        <div class="px-3 py-1.5 mt-3 text-[10px] font-black text-rose-400 uppercase tracking-wider">Sipariş &amp; Müşteri</div>
        
        <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')" icon="fas fa-shopping-bag">
            Çiçek Siparişleri
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')" icon="fas fa-users">
            Müşteriler
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.reviews.index')" :active="request()->routeIs('admin.reviews.*')" icon="fas fa-star text-amber-400">
            Yorumlar
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.contact-messages.index')" :active="request()->routeIs('admin.contact-messages.*')" icon="fas fa-envelope text-sky-400">
            Gelen Mesajlar
        </x-sidebar-link>
        
        <div class="px-3 py-1.5 mt-3 text-[10px] font-black text-rose-400 uppercase tracking-wider">Katalog, Mezat &amp; Fiyat</div>

        <x-sidebar-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')" icon="fas fa-spa">
            Çiçek &amp; Buketler
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.flower_studio.daily_stock')" :active="request()->routeIs('admin.flower_studio.*')" icon="fas fa-palette text-pink-400">
            Tasarım Masası
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="fas fa-tags">
            Kategoriler
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.pricing.bulk')" :active="request()->routeIs('admin.pricing.*')" icon="fas fa-percent text-amber-400">
            Toplu Fiyat
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.regions.index')" :active="request()->routeIs('admin.regions.*')" icon="fas fa-map-marker-alt text-emerald-400">
            İlçe &amp; Teslimat
        </x-sidebar-link>

        <div class="px-3 py-1.5 mt-3 text-[10px] font-black text-rose-400 uppercase tracking-wider">İçerik &amp; Sistem</div>

        <x-sidebar-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')" icon="fas fa-newspaper text-purple-400">
            Rehber &amp; Blog
        </x-sidebar-link>

        <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.index')" icon="fas fa-cog">
            Site &amp; WhatsApp Ayarları
        </x-sidebar-link>

        @can('manage-users')
        <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="fas fa-user-shield">
            Yöneticiler
        </x-sidebar-link>
        @endcan
        
    </nav>

    <!-- User Profile & Logout (Çiçekçi Teması - Gül / Rose) -->
    <div class="p-3 bg-rose-950/80 border border-rose-900/60 rounded-2xl mx-0.5 shadow-inner">
        <div class="flex items-center justify-between gap-2">
            <div class="min-w-0 flex-1">
                <p class="text-xs font-black text-rose-100 truncate flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    <span class="truncate">{{ Auth::user()->name }}</span>
                </p>
                <span class="text-[9px] font-semibold text-rose-300/70 block uppercase tracking-wider">Yönetici Paneli</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                @csrf
                <button type="submit" 
                        class="text-xs font-bold bg-rose-900/80 hover:bg-rose-800 text-rose-200 hover:text-white px-2.5 py-1.5 rounded-xl border border-rose-800/80 transition-all flex items-center gap-1 cursor-pointer"
                        title="Güvenli Çıkış">
                    <i class="fas fa-sign-out-alt text-[10px]"></i> Çıkış
                </button>
            </form>
        </div>
    </div>
</aside>
