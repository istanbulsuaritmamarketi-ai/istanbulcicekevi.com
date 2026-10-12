<x-frontend-layout title="Hesabım, Sepetim & Siparişlerim - İstanbul Çiçek Evi">
    <div class="bg-gray-50/70 min-h-screen py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Üst Karşılama Başlığı -->
            <div class="bg-gradient-to-r from-rose-700 via-pink-600 to-rose-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
                    <i class="fas fa-spa text-[170px]"></i>
                </div>
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold tracking-wide">
                            <i class="fas fa-user-check"></i> Müşteri Portalı
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black">
                            Hoş Geldiniz, {{ $user->name }} 👋
                        </h1>
                        <p class="text-rose-100 text-xs sm:text-sm max-w-xl leading-relaxed">
                            Aktif alışveriş sepetinizi, çiçek siparişlerinizin kurye teslimat durumunu ve adınıza tanımlanan özel indirim kuponlarını buradan tek panelden yönetebilirsiniz.
                        </p>

                        <!-- Hızlı Metrik Hapları -->
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <a href="#aktif-sepetim" class="inline-flex items-center gap-1.5 bg-black/20 hover:bg-black/30 backdrop-blur-xs px-3 py-1 rounded-full text-[11px] font-bold text-white transition-colors">
                                <i class="fas fa-shopping-cart text-rose-300"></i> Sepet: <strong>{{ count($cart) }} Ürün</strong>
                            </a>
                            <a href="#siparislerim" class="inline-flex items-center gap-1.5 bg-black/20 hover:bg-black/30 backdrop-blur-xs px-3 py-1 rounded-full text-[11px] font-bold text-white transition-colors">
                                <i class="fas fa-box text-rose-300"></i> Sipariş: <strong>{{ $orders->count() }} Adet</strong>
                            </a>
                            <a href="#kuponlarim" class="inline-flex items-center gap-1.5 bg-black/20 hover:bg-black/30 backdrop-blur-xs px-3 py-1 rounded-full text-[11px] font-bold text-white transition-colors">
                                <i class="fas fa-ticket-alt text-amber-300"></i> Kupon: <strong>{{ $userCoupons->count() }} Aktif</strong>
                            </a>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
                        <a href="{{ route('products.index') }}" class="bg-white text-rose-800 hover:bg-rose-50 font-black text-xs px-5 py-3 rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-spa text-rose-600"></i> Çiçekleri İncele
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto bg-rose-900/60 hover:bg-rose-900 text-white font-bold text-xs px-4 py-3 rounded-2xl border border-white/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bildirimler -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-xs">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center gap-2.5 shadow-xs">
                    <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Sol Kolon: Aktif Sepetim + Kuponlarım + Siparişlerim -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- 🛒 1. AKTİF SEPET TAKİBİ KARTI -->
                    <div id="aktif-sepetim" class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                                        <i class="fas fa-shopping-cart"></i>
                                    </span>
                                    Aktif Alışveriş Sepetim
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ count($cart) > 0 ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ count($cart) }} Ürün
                                    </span>
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Şu anda sepetinizde bekleyen ürünler ve sipariş özeti</p>
                            </div>
                            @if(count($cart) > 0)
                                <a href="{{ route('cart.index') }}" class="text-xs font-black text-rose-600 hover:text-rose-700 flex items-center gap-1">
                                    Sepete Git &rarr;
                                </a>
                            @endif
                        </div>

                        @if(empty($cart))
                            <div class="text-center py-8 space-y-3 bg-rose-50/30 rounded-2xl border border-dashed border-rose-200">
                                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-lg">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-gray-700">Sepetinizde henüz ürün bulunmuyor.</p>
                                    <p class="text-[11px] text-gray-400">Taze çiçek buketlerimizi, vazo tasarımlarımızı inceleyip hemen sepetinize ekleyebilirsiniz.</p>
                                </div>
                                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-xs transition-colors">
                                    <i class="fas fa-spa"></i> Çiçekleri Keşfet
                                </a>
                            </div>
                        @else
                            <div class="space-y-3">
                                <div class="divide-y divide-gray-100">
                                    @foreach($cart as $itemKey => $item)
                                        <div class="py-3 flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                    @if(!empty($item['image_url']))
                                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain">
                                                    @else
                                                        <i class="fas fa-spa text-gray-400 text-sm"></i>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-xs text-gray-900 truncate">{{ $item['name'] }}</div>
                                                    <div class="text-[11px] text-gray-500 font-medium">
                                                        {{ $item['quantity'] }} Adet &times; {{ number_format($item['price'], 2, ',', '.') }} TL
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <div class="text-xs font-black text-rose-600">
                                                    {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }} TL
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Sepet Özeti & Hızlı Sipariş Butonu -->
                                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-rose-50/50 p-4 rounded-2xl">
                                    <div>
                                        <div class="text-[11px] text-gray-500 font-medium">Toplam Sepet Tutarı:</div>
                                        <div class="text-lg font-black text-gray-900">{{ number_format($cartSubtotal, 2, ',', '.') }} TL</div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('cart.index') }}" class="px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-800 text-xs font-bold rounded-xl transition-all shadow-2xs">
                                            Sepeti Düzenle
                                        </a>
                                        <a href="{{ route('checkout') }}" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                                            <span>Siparişi Tamamla</span>
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- 📦 2. GEÇMİŞ VE AKTİF SİPARİŞLERİM -->
                    <div id="siparislerim" class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                                        <i class="fas fa-box"></i>
                                    </span>
                                    Siparişlerim ({{ $orders->count() }})
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Verdiğiniz çiçek siparişleri ve canlı kurye teslimat durumları</p>
                            </div>
                            <a href="{{ route('order.track') }}" class="text-xs font-bold text-gray-600 hover:text-rose-600 flex items-center gap-1">
                                <i class="fas fa-search"></i> Sipariş Sorgula
                            </a>
                        </div>

                        @if($orders->isEmpty())
                            <div class="text-center py-10 space-y-3 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mx-auto text-lg">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-600">Henüz kayıtlı bir siparişiniz bulunmuyor.</p>
                                <a href="{{ route('products.index') }}" class="inline-block text-xs text-rose-600 font-bold hover:underline">
                                    Taze çiçek ve özel aranjmanları inceleyin &rarr;
                                </a>
                            </div>
                        @else
                            <div class="divide-y divide-gray-100 space-y-4">
                                @foreach($orders as $order)
                                    <div class="pt-4 first:pt-0 space-y-3">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-sm text-gray-900 font-mono">{{ $order->order_number }}</span>
                                                @php
                                                    $statusColors = match($order->status) {
                                                        'Teslim Edildi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'Kuryede', 'Dağıtımda' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        'Hazırlanıyor' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'İptal Edildi' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                        default => 'bg-gray-50 text-gray-700 border-gray-200'
                                                    };
                                                @endphp
                                                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full border {{ $statusColors }}">
                                                    {{ $order->status }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="text-gray-400 font-medium">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                                                <span class="font-black text-rose-600 text-sm ml-2">{{ number_format($order->total_amount, 2, ',', '.') }} TL</span>
                                            </div>
                                        </div>

                                        <!-- Sipariş Detayları & İlçe -->
                                        <div class="bg-gray-50/70 p-3.5 rounded-2xl text-xs space-y-2">
                                            <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-gray-600">
                                                <div>
                                                    <i class="fas fa-map-marker-alt text-rose-500 mr-1"></i>
                                                    Teslimat: <strong>{{ $order->region->name ?? 'İstanbul' }}</strong>
                                                    @if($order->delivery_date || $order->delivery_time_slot)
                                                        • 🕒 {{ $order->delivery_date }} {{ $order->delivery_time_slot }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                                    Alıcı: <strong>{{ $order->recipient_name ?: $order->customer_name }}</strong>
                                                </div>
                                            </div>

                                            @if($order->items && $order->items->count() > 0)
                                                <div class="text-[11px] text-gray-700 pt-1 border-t border-gray-200/60">
                                                    <strong>İçerik:</strong> 
                                                    {{ $order->items->map(fn($it) => ($it->product->name ?? $it->custom_title ?? 'Çiçek Tasarımı') . ' (' . $it->quantity . ' Adet)')->implode(', ') }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Aksiyon Butonu: Canlı Takip -->
                                        <div class="flex items-center justify-end gap-2 pt-1">
                                            <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-2xs">
                                                <i class="fas fa-truck text-rose-400 text-xs"></i> Canlı Kurye Takibi &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- 🎟️ 3. KUPONLARIM VE İNDİRİMLERİM KARTI -->
                    <div id="kuponlarim" class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    Tanımlı İndirim Kuponlarım ({{ $userCoupons->count() }})
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Çiçek siparişlerinizde tek tıkla sepete tanımlayabileceğiniz indirimleriniz</p>
                            </div>
                        </div>

                        @if($userCoupons->isEmpty())
                            <div class="text-center py-8 space-y-3 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-lg">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-600">Henüz hesabınıza tanımlı bir kupon bulunmuyor.</p>
                                <p class="text-[11px] text-gray-400">Yeni kampanyalarımızda ve özel günlerde hesabınıza özel kuponlar yüklenecektir.</p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($userCoupons as $uc)
                                    @php
                                        $cp = $uc->coupon;
                                        $isExpired = $cp->expires_at && $cp->expires_at->isPast();
                                        $sourceText = match($uc->source) {
                                            'welcome' => '🎉 Hoş Geldin Kuponu',
                                            'spin_wheel' => '🎡 Şans Çarkı Kuponu',
                                            default => '🏷️ Özel İndirim Kuponu'
                                        };
                                    @endphp
                                    <div class="relative overflow-hidden rounded-2xl border {{ $uc->is_used || $isExpired ? 'border-gray-200 bg-gray-50 opacity-70' : 'border-rose-200 bg-gradient-to-br from-rose-50/60 via-white to-pink-50/40' }} p-5 flex flex-col justify-between shadow-2xs space-y-4">
                                        
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="text-[10px] font-black uppercase tracking-wider {{ $uc->is_used ? 'bg-gray-200 text-gray-700' : 'bg-rose-100 text-rose-800' }} px-2.5 py-0.5 rounded-full">
                                                    {{ $sourceText }}
                                                </span>
                                                @if($uc->is_used)
                                                    <span class="text-[10px] font-bold text-gray-500">Kullanıldı</span>
                                                @elseif($isExpired)
                                                    <span class="text-[10px] font-bold text-red-600">Süresi Doldu</span>
                                                @else
                                                    <span class="text-[10px] font-black text-emerald-600 flex items-center gap-1">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Aktif
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xl font-black text-gray-900 mt-1">
                                                @if($cp->type === 'percent')
                                                    %{{ number_format($cp->value, 0) }} İndirim
                                                @else
                                                    {{ number_format($cp->value, 0, ',', '.') }} TL İndirim
                                                @endif
                                            </div>

                                            <div class="text-[11px] text-gray-600 mt-1 space-y-0.5">
                                                @if($cp->min_order_amount > 0)
                                                    <div>• Min. Sepet Tutarı: <strong>{{ number_format($cp->min_order_amount, 0, ',', '.') }} TL</strong></div>
                                                @else
                                                    <div>• Alt sepet limiti yok</div>
                                                @endif
                                                @if($cp->expires_at)
                                                    <div>• Son Kullanım: {{ $cp->expires_at->format('d.m.Y') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Kupon Kodu & Aksiyonlar -->
                                        <div class="pt-3 border-t border-rose-100/60 flex items-center justify-between gap-2"
                                             x-data="{ copied: false }">
                                            <div class="bg-white px-3 py-1.5 rounded-xl border border-dashed border-rose-300 font-mono font-black text-xs text-rose-900 tracking-wider">
                                                {{ $cp->code }}
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <button type="button" 
                                                        @click="navigator.clipboard.writeText('{{ $cp->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                        class="px-2.5 py-1.5 text-xs font-bold bg-white hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-700 transition-colors cursor-pointer"
                                                        title="Kodu Kopyala">
                                                    <i class="fas" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                                                    <span x-text="copied ? 'Kopyalandı' : 'Kopyala'"></span>
                                                </button>

                                                @if(!$uc->is_used && !$isExpired)
                                                    <a href="{{ route('customer.coupons.apply', $cp->code) }}" 
                                                       class="px-3 py-1.5 text-xs font-black bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition-colors whitespace-nowrap">
                                                        Sepette Kullan &rarr;
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Sağ Kolon: Profil & Şifre Güncelleme & Çıkış -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-6">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                                <i class="fas fa-id-card text-rose-600"></i> Hesap &amp; Profil Bilgilerim
                            </h3>
                        </div>

                        <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Adınız Soyadınız</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full text-xs font-bold py-2.5 px-3 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Kayıtlı Telefon Numaranız</label>
                                <input type="text" value="{{ $user->phone ?? 'Belirtilmemiş' }}" disabled
                                       class="w-full text-xs font-bold py-2.5 px-3 border border-gray-200 bg-gray-50 text-gray-500 rounded-xl cursor-not-allowed">
                                <p class="text-[10px] text-gray-400 mt-1">Giriş yaparken bu telefon numaranızı kullanırsınız.</p>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <h4 class="text-xs font-black text-gray-800 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-lock text-gray-400"></i> Şifre Değiştir (İsteğe Bağlı)
                                </h4>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Mevcut Şifre</label>
                                        <input type="password" name="current_password"
                                               class="w-full text-xs py-2 px-3 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Yeni Şifre</label>
                                        <input type="password" name="new_password"
                                               class="w-full text-xs py-2 px-3 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white text-xs font-black py-3 rounded-xl shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fas fa-save"></i> Bilgilerimi Güncelle
                            </button>
                        </form>

                        <!-- Güvenli Çıkış Yap Butonu -->
                        <div class="pt-3 border-t border-gray-100">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold py-2.5 rounded-xl border border-rose-200 transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i class="fas fa-sign-out-alt"></i> Güvenli Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Müşteri Hizmetleri İletişim Kutusu -->
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50/60 rounded-3xl p-5 border border-rose-100 space-y-3">
                        <div class="flex items-center gap-2 text-rose-950 font-black text-xs">
                            <i class="fas fa-headset text-rose-600 text-sm"></i> Yardıma mı İhtiyacınız Var?
                        </div>
                        <p class="text-[11px] text-rose-800 leading-relaxed">
                            Aynı gün teslimat, sipariş saat dilimi, özel çiçek aranjmanı veya kart notunuzla ilgili tüm sorularınız için atölye çiçek uzmanlarımıza 7/24 WhatsApp hattımızdan ulaşabilirsiniz.
                        </p>
                        <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20%C4%B0stanbul%20%C3%87i%C3%A7ek%20Evi%20hesab%C4%B1mla%20ilgili%20destek%20almak%20istiyorum." 
                           target="_blank" 
                           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition-colors w-full justify-center">
                            <i class="fab fa-whatsapp text-sm"></i> WhatsApp Çiçek Destek Hattı
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-frontend-layout>
