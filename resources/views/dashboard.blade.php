<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-900 leading-tight flex items-center gap-2">
                    <span>🌸</span> {{ __('Çiçekçi Yönetim Paneli & Canlı Atölye') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">İstanbul Çiçek Evi günlük sipariş, teslimat ve çiçek stok durumu</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="/" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-2xs border border-gray-200 transition-colors">
                    <i class="fas fa-store text-rose-500"></i> Canlı Mağaza
                    <i class="fas fa-external-link-alt text-[9px] text-gray-400"></i>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                    <i class="fas fa-shopping-bag"></i> Siparişler
                </a>
                <a href="{{ route('admin.products.create') }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-3.5 rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition-colors">
                    <i class="fas fa-plus text-amber-400"></i> Yeni Çiçek Ekle
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- 1. TIKLANABİLİR İSTATİSTİK KARTLARI (4'LÜ IZGARA) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- 1. Bugünkü Siparişler -->
                <a href="{{ route('admin.orders.index', ['tab' => 'today']) }}" 
                    class="group bg-white hover:bg-rose-50/40 rounded-2xl p-5 border border-gray-100 hover:border-rose-300 shadow-2xs transition-all block">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black text-gray-400 group-hover:text-rose-600 uppercase tracking-wider transition-colors">
                            Bugünkü Siparişler
                        </div>
                        <div class="w-10 h-10 bg-rose-50 group-hover:bg-rose-600 text-rose-600 group-hover:text-white rounded-xl flex items-center justify-center transition-colors">
                            <i class="fas fa-calendar-day text-base"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-3xl font-black text-gray-900 group-hover:text-rose-600 transition-colors">{{ $todayOrders }}</div>
                    <div class="mt-1 text-[11px] text-rose-600 font-bold flex items-center gap-1">
                        <span>Bugün gelenler</span>
                        <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>
                
                <!-- 2. Hazırlanacak & Bekleyen Çiçekler -->
                <a href="{{ route('admin.orders.index', ['tab' => 'active']) }}" 
                    class="group bg-white hover:bg-amber-50/40 rounded-2xl p-5 border border-gray-100 hover:border-amber-300 shadow-2xs transition-all block">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black text-gray-400 group-hover:text-amber-600 uppercase tracking-wider transition-colors">
                            Hazırlanacak Çiçekler
                        </div>
                        <div class="w-10 h-10 bg-amber-50 group-hover:bg-amber-600 text-amber-600 group-hover:text-white rounded-xl flex items-center justify-center transition-colors">
                            <i class="fas fa-spa text-base"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-3xl font-black text-amber-600">{{ $pendingOrders }}</div>
                    <div class="mt-1 text-[11px] text-amber-600 font-bold flex items-center gap-1">
                        <span>Atölyede hazırlananlar</span>
                        <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- 3. Yayındaki Çiçek & Buket Sayısı -->
                <a href="{{ route('admin.products.index') }}" 
                    class="group bg-white hover:bg-pink-50/40 rounded-2xl p-5 border border-gray-100 hover:border-pink-300 shadow-2xs transition-all block">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black text-gray-400 group-hover:text-pink-600 uppercase tracking-wider transition-colors">
                            Yayındaki Çiçekler
                        </div>
                        <div class="w-10 h-10 bg-pink-50 group-hover:bg-pink-600 text-pink-600 group-hover:text-white rounded-xl flex items-center justify-center transition-colors">
                            <i class="fas fa-box text-base"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-3xl font-black text-pink-600">{{ $totalProducts }}</div>
                    <div class="mt-1 text-[11px] text-pink-600 font-bold flex items-center gap-1">
                        <span>Kataloğu yönet</span>
                        <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- 4. Kayıtlı Müşteriler -->
                <a href="{{ route('admin.customers.index') }}" 
                    class="group bg-white hover:bg-blue-50/40 rounded-2xl p-5 border border-gray-100 hover:border-blue-300 shadow-2xs transition-all block">
                    <div class="flex items-center justify-between">
                        <div class="text-[11px] font-black text-gray-400 group-hover:text-blue-600 uppercase tracking-wider transition-colors">
                            Kayıtlı Müşteriler
                        </div>
                        <div class="w-10 h-10 bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white rounded-xl flex items-center justify-center transition-colors">
                            <i class="fas fa-users text-base"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-3xl font-black text-blue-600">{{ $totalCustomers }}</div>
                    <div class="mt-1 text-[11px] text-blue-600 font-bold flex items-center gap-1">
                        <span>Müşteri listesi &amp; kuponlar</span>
                        <i class="fas fa-arrow-right text-[9px] group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

            </div>

            <!-- 🕒 2. GÜNÜN TESLİMAT SAAT DİLİMLERİ PROGRAMI -->
            <div class="bg-gradient-to-r from-rose-900 via-rose-800 to-pink-900 rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                    <i class="fas fa-clock text-[130px]"></i>
                </div>
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider">
                            <i class="fas fa-truck text-amber-300"></i> Kurye &amp; Atölye Zaman Tablosu
                        </div>
                        <h3 class="text-lg font-black mt-2">Bugünkü Teslimat Saat Dilimleri</h3>
                        <p class="text-xs text-rose-100/80 mt-0.5">İstanbul genelindeki aktif siparişlerin saatlik teslimat hedefleri</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                            <div class="text-[10px] font-bold text-rose-200">10:00 - 14:00</div>
                            <div class="text-xl font-black text-white mt-1">{{ $slot10_14 }} <span class="text-[10px] font-normal text-rose-200">Adet</span></div>
                            <span class="text-[9px] text-rose-200/80 block mt-0.5">Öğle Teslimatı</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                            <div class="text-[10px] font-bold text-rose-200">14:00 - 18:00</div>
                            <div class="text-xl font-black text-white mt-1">{{ $slot14_18 }} <span class="text-[10px] font-normal text-rose-200">Adet</span></div>
                            <span class="text-[9px] text-rose-200/80 block mt-0.5">Öğleden Sonra</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                            <div class="text-[10px] font-bold text-rose-200">18:00 - 22:00</div>
                            <div class="text-xl font-black text-white mt-1">{{ $slot18_22 }} <span class="text-[10px] font-normal text-rose-200">Adet</span></div>
                            <span class="text-[9px] text-rose-200/80 block mt-0.5">Akşam Teslimatı</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-center">
                            <div class="text-[10px] font-bold text-amber-300">VIP / Özel Saat</div>
                            <div class="text-xl font-black text-amber-300 mt-1">{{ $slotVip }} <span class="text-[10px] font-normal text-amber-200">Adet</span></div>
                            <span class="text-[9px] text-amber-200/80 block mt-0.5">VIP Kurye</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ⚠️ 3. KRİTİK ÇİÇEK STOKLARI UYARISI (VARSA) -->
            @if($lowStockCount > 0)
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-base shadow-xs shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-rose-950 text-xs sm:text-sm">Kritik Stok Uyarısı ({{ $lowStockCount }} Çiçek 5 Adet ve Altında)</h4>
                            <p class="text-[11px] text-rose-700 mt-0.5">
                                {{ $lowStockProducts->pluck('name')->implode(', ') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('admin.pricing.bulk') }}" class="px-3 py-1.5 bg-white hover:bg-rose-100 text-rose-800 text-xs font-bold rounded-xl border border-rose-300 transition-colors">
                            Toplu Fiyat &amp; Stok
                        </a>
                        <a href="{{ route('admin.flower_studio.daily_stock') }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl transition-colors shadow-xs">
                            Mezat Stok Masası
                        </a>
                    </div>
                </div>
            @endif

            <!-- ⚡ 4. HIZLI EYLEMLER & YÖNETİM KISAYOLLARI (6'LI DENGELİ IZGARA) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ route('admin.products.create') }}" class="group p-4 bg-white hover:bg-rose-50/60 rounded-2xl border border-gray-100 hover:border-rose-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-rose-600 transition-colors">Yeni Çiçek</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">Buket &amp; Aranjman</span>
                </a>

                <a href="{{ route('admin.pricing.bulk') }}" class="group p-4 bg-white hover:bg-amber-50/60 rounded-2xl border border-gray-100 hover:border-amber-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-percent"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-amber-600 transition-colors">Toplu Fiyat</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">Zam &amp; İndirim</span>
                </a>

                <a href="{{ route('admin.orders.index', ['tab' => 'active']) }}" class="group p-4 bg-white hover:bg-purple-50/60 rounded-2xl border border-gray-100 hover:border-purple-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-purple-600 transition-colors">Aktif Siparişler</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">{{ $pendingOrders }} Bekliyor</span>
                </a>

                <a href="{{ route('admin.flower_studio.daily_stock') }}" class="group p-4 bg-white hover:bg-pink-50/60 rounded-2xl border border-gray-100 hover:border-pink-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 group-hover:bg-pink-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-palette"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-pink-600 transition-colors">Tasarım Masası</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">{{ $customStudioCount }} Malzeme</span>
                </a>

                <a href="{{ route('admin.regions.index') }}" class="group p-4 bg-white hover:bg-emerald-50/60 rounded-2xl border border-gray-100 hover:border-emerald-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-emerald-600 transition-colors">İlçe &amp; Teslimat</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">39 İlçe Tarifesi</span>
                </a>

                <a href="{{ route('admin.customers.index') }}" class="group p-4 bg-white hover:bg-blue-50/60 rounded-2xl border border-gray-100 hover:border-blue-300 shadow-2xs transition-all flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center text-base transition-colors mb-2">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-xs font-black text-gray-900 group-hover:text-blue-600 transition-colors">Müşteriler</span>
                    <span class="text-[10px] text-gray-400 mt-0.5">{{ $totalCustomers }} Kayıtlı</span>
                </a>
            </div>

            <!-- 5. BİLDİRİMLER (ONAY BEKLEYEN DEĞERLENDİRME & MESAJLAR) -->
            @if($pendingReviewsCount > 0 || $unreadMessagesCount > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-base shadow-xs">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-900 text-xs sm:text-sm">Bekleyen Müşteri Bildirimleri</h4>
                            <p class="text-xs text-amber-700 mt-0.5">
                                @if($pendingReviewsCount > 0)
                                    <span>• <strong>{{ $pendingReviewsCount }}</strong> yeni çiçek yorumu onay bekliyor.</span>
                                @endif
                                @if($unreadMessagesCount > 0)
                                    <span class="ml-2">• <strong>{{ $unreadMessagesCount }}</strong> yeni müşteri mesajı var.</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if($pendingReviewsCount > 0)
                            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                Yorumları Onayla
                            </a>
                        @endif
                        @if($unreadMessagesCount > 0)
                            <a href="{{ route('admin.contact-messages.index') }}" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                Mesajları Oku
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 6. CANLI GRAFİKLER BÖLÜMÜ (2 DENGELİ SÜTUN) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 1. Sipariş Durum Dağılımı (Doughnut Chart) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-black text-gray-900 text-base">Sipariş Durumları</h3>
                            <p class="text-xs text-gray-400">Atölye ve kurye süreç dağılımı</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs text-rose-600 hover:underline font-bold">
                            Tüm Siparişler &rarr;
                        </a>
                    </div>
                    <div class="h-64 flex items-center justify-center">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>

                <!-- 2. En Çok Çiçek Gönderilen İlçeler Grafiği (Bar Chart) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-black text-gray-900 text-base">En Çok Çiçek Gönderilen İlçeler</h3>
                            <p class="text-xs text-gray-400">İstanbul genelinde kurye teslimat yoğunluğu</p>
                        </div>
                        <a href="{{ route('admin.regions.index') }}" class="text-xs text-rose-600 hover:underline font-bold">
                            Tüm İlçeler &rarr;
                        </a>
                    </div>
                    <div class="h-64">
                        <canvas id="regionOrdersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 7. TABLOLAR: SON SİPARİŞLER (KART NOTLU) & POPÜLER ÇİÇEKLER -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Sol: Son Gelen Çiçek Siparişleri Listesi -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-black text-gray-900 text-sm flex items-center gap-2">
                            <i class="fas fa-shopping-bag text-rose-600"></i> Son Gelen Çiçek Siparişleri
                        </h3>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs text-rose-600 hover:underline font-bold">Tümünü Gör &rarr;</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <li class="p-4 hover:bg-gray-50/60 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            <i class="fas fa-receipt"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="font-mono font-black text-xs text-rose-600 hover:underline">
                                                    {{ $order->order_number }}
                                                </a>
                                                <span class="inline-block px-2 py-0.5 text-[9px] font-black rounded-full bg-gray-100 text-gray-700">
                                                    {{ $order->status }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-0.5">
                                                Alıcı: <strong>{{ $order->recipient_name ?: $order->customer_name }}</strong> 
                                                • 📍 {{ $order->region->name ?? 'İstanbul' }}
                                                @if($order->delivery_time_slot)
                                                    • <span class="text-rose-600 font-bold">🕒 {{ $order->delivery_time_slot }}</span>
                                                @endif
                                            </p>

                                            <!-- 💌 Zarflı Müşteri Kart Notu Önizlemesi -->
                                            @if(!empty($order->card_note))
                                                <div class="text-[11px] text-pink-900 bg-pink-50/80 border border-pink-100 rounded-lg px-2.5 py-1 mt-1.5 font-medium flex items-center gap-1.5">
                                                    <i class="fas fa-envelope-open-text text-pink-600 shrink-0 text-xs"></i>
                                                    <span class="truncate">"{{ Str::limit($order->card_note, 45) }}"</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0 flex flex-col items-end gap-1.5">
                                        <p class="font-black text-sm text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }} TL</p>
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-[10px] font-bold transition-colors" title="Kurye Fişi Yazdır">
                                                <i class="fas fa-print"></i> Fiş
                                            </a>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-[10px] font-black transition-colors">
                                                Detay &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="p-8 text-gray-400 text-center text-sm">Henüz sipariş kaydı bulunmuyor.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Sağ: Popüler & Son Eklenen Çiçekler -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-black text-gray-900 text-sm flex items-center gap-2">
                            <i class="fas fa-spa text-pink-600"></i> Popüler Çiçek Kataloğu &amp; Stok
                        </h3>
                        <a href="{{ route('admin.products.index') }}" class="text-xs text-rose-600 hover:underline font-bold">Kataloğu Yönet &rarr;</a>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse($popularProducts as $prod)
                            <li class="p-3.5 hover:bg-gray-50/60 transition-colors flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-11 h-11 rounded-xl bg-gray-50 border border-gray-100 p-1 shrink-0 flex items-center justify-center overflow-hidden">
                                        @if($prod->image_path)
                                            <img src="{{ $prod->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-contain">
                                        @else
                                            <i class="fas fa-spa text-rose-300 text-base"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.products.edit', $prod) }}" class="font-bold text-xs sm:text-sm text-gray-900 hover:text-rose-600 transition-colors truncate block">
                                            {{ $prod->name }}
                                        </a>
                                        <span class="text-[10px] text-gray-400 font-medium">
                                            {{ $prod->category->name ?? 'Kategori Yok' }} • Stok: <strong class="{{ $prod->stock < 5 ? 'text-red-600 font-black' : 'text-emerald-600' }}">{{ $prod->stock }} Adet</strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="font-black text-sm text-rose-600 font-mono">
                                        {{ number_format($prod->campaign_price ?: $prod->price, 0, ',', '.') }} TL
                                    </div>
                                    <a href="{{ route('admin.products.edit', $prod) }}" class="text-[10px] text-gray-400 hover:text-rose-600 font-bold transition-colors">
                                        Düzenle &rarr;
                                    </a>
                                </div>
                            </li>
                        @empty
                            <li class="p-8 text-gray-400 text-center text-sm">Henüz çiçek kaydı bulunmuyor.</li>
                        @endforelse
                    </ul>
                </div>

            </div>

        </div>
    </div>

    <!-- Chart.js Scriptleri -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Sipariş Durumları Grafiği (Doughnut)
            const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($orderStatuses) !!},
                    datasets: [{
                        data: {!! json_encode($orderStatusCounts) !!},
                        backgroundColor: [
                            '#f43f5e', // Yeni (Gül Pembesi)
                            '#f59e0b', // Hazırlanıyor (Turuncu)
                            '#8b5cf6', // Kuryede (Mor)
                            '#10b981', // Teslim Edildi (Yeşil)
                            '#ef4444'  // İptal (Kırmızı)
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    },
                    cutout: '70%'
                }
            });

            // 2. İlçe Bazlı Çiçek Siparişleri (Bar)
            const ctxRegion = document.getElementById('regionOrdersChart').getContext('2d');
            new Chart(ctxRegion, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($regionLabels) !!},
                    datasets: [{
                        label: 'Çiçek Siparişi Sayısı',
                        data: {!! json_encode($regionCounts) !!},
                        backgroundColor: '#fb7185',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
