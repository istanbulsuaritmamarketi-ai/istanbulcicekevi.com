<x-frontend-layout 
    :title="$product->seo_title ?: ($product->name . ' - İstanbul Çiçek Evi')" 
    :description="$product->seo_description ?: Str::limit(strip_tags($product->short_description ?: $product->description), 155)"
    :image="$product->image_url"
    ogType="product">
    <!-- Google Rich Snippet (Schema.org JSON-LD) -->
    @php
        $productSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_url,
            'description' => $product->seo_description ?: Str::limit(strip_tags($product->short_description ?: $product->description), 160),
            'sku' => 'PRD-' . $product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => config('app.name', 'İstanbul Çiçek Evi')
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product->slug),
                'priceCurrency' => 'TRY',
                'price' => (string) $product->price,
                'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition'
            ]
        ];

        if ($product->review_count > 0) {
            $productSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string) $product->average_rating,
                'reviewCount' => (string) $product->review_count,
                'bestRating' => '5',
                'worstRating' => '1'
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-3.5 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs sm:text-sm text-gray-500 font-medium overflow-x-auto whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-rose-600">Ana Sayfa</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('products.index') }}" class="hover:text-rose-600">Ürünler</a>
                <span class="mx-2 text-gray-300">/</span>
                <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-rose-600">{{ $product->category->name }}</a>
                <span class="mx-2 text-gray-300">/</span>
                <span class="text-gray-900 truncate font-semibold">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Ürün Detay Üst Alanı (Çoklu Galeri + Fiyat & Aksiyonlar) -->
    <div class="bg-white py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('review_success'))
                <div class="mb-8 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-sm font-bold text-green-800">{{ session('review_success') }}</p>
                    </div>
                </div>
            @endif

            <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-start">
                
                <!-- 1. İnteraktif Ürün Görseli & Slider Galeri -->
                @php
                    $allImages = [];
                    if ($product->image_path) {
                        $allImages[] = $product->image_url;
                    }
                    foreach ($product->images as $galImg) {
                        $allImages[] = $galImg->image_url;
                    }
                @endphp
                <div class="lg:col-span-6 mb-8 lg:mb-0" 
                     x-data="{
                        images: {{ json_encode($allImages) }},
                        currentIndex: 0,
                        touchStartX: 0,
                        touchEndX: 0,
                        get activeImg() {
                            return this.images[this.currentIndex] || '';
                        },
                        next() {
                            if (this.images.length > 1) {
                                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                            }
                        },
                        prev() {
                            if (this.images.length > 1) {
                                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                            }
                        },
                        goTo(index) {
                            this.currentIndex = index;
                        },
                        handleTouchStart(e) {
                            this.touchStartX = e.changedTouches[0].screenX;
                        },
                        handleTouchEnd(e) {
                            this.touchEndX = e.changedTouches[0].screenX;
                            this.handleSwipe();
                        },
                        handleSwipe() {
                            const diff = this.touchStartX - this.touchEndX;
                            if (Math.abs(diff) > 40) {
                                if (diff > 0) {
                                    this.next(); // Sola kaydırma -> Sonraki görsel
                                } else {
                                    this.prev(); // Sağa kaydırma -> Önceki görsel
                                }
                            }
                        }
                     }"
                     @keydown.window.arrow-right="next()"
                     @keydown.window.arrow-left="prev()">

                    <!-- Ürün Rozetleri (Görseli Asla Kapatmayacak Şekilde Görselin Üzerinde Yatay Şerit) -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if($product->custom_badge)
                            <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-2xs flex items-center gap-1.5">
                                <i class="fas fa-fire text-[11px]"></i> {{ $product->custom_badge }}
                            </span>
                        @endif

                        @if($product->in_stock)
                            <span class="bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-2xs flex items-center gap-1.5">
                                <i class="fas fa-check-circle text-[11px]"></i> Stokta Var
                            </span>
                        @else
                            <span class="bg-rose-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-2xs">
                                Tükendi
                            </span>
                        @endif

                        @if($product->has_free_installation)
                            <span class="bg-rose-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-2xs flex items-center gap-1.5">
                                <i class="fas fa-truck-fast text-[11px]"></i> Aynı Gün VIP Teslimat
                            </span>
                        @endif
                    </div>

                    <!-- Büyük Ana Görsel Kutusu (Tamamen Net & Görseli Kapatmayan Alan) -->
                    <div class="relative bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/80 flex items-center justify-center overflow-hidden shadow-sm group aspect-square select-none"
                         @touchstart="handleTouchStart($event)"
                         @touchend="handleTouchEnd($event)">

                        <template x-if="activeImg">
                            <img :src="activeImg" :key="currentIndex" alt="{{ $product->name }}"
                                class="max-h-full max-w-full object-contain transition-all duration-300 group-hover:scale-105">
                        </template>
                        <template x-if="!activeImg">
                            <div class="py-24 text-center">
                                <i class="fas fa-spa text-6xl text-rose-300 mb-3"></i>
                                <p class="text-xs text-gray-400 font-medium">Görsel Yüklenmedi</p>
                            </div>
                        </template>

                        <!-- Sol Ok Butonu (<) -->
                        <template x-if="images.length > 1">
                            <button type="button" @click.stop="prev()" aria-label="Önceki Fotoğraf"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-lg border border-gray-200 flex items-center justify-center transition-all opacity-80 hover:opacity-100 hover:scale-110 active:scale-95 z-20">
                                <i class="fas fa-chevron-left text-sm"></i>
                            </button>
                        </template>

                        <!-- Sağ Ok Butonu (>) -->
                        <template x-if="images.length > 1">
                            <button type="button" @click.stop="next()" aria-label="Sonraki Fotoğraf"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-lg border border-gray-200 flex items-center justify-center transition-all opacity-80 hover:opacity-100 hover:scale-110 active:scale-95 z-20">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </button>
                        </template>

                        <!-- Fotoğraf Sayacı Rozeti (Örn: 1 / 4) -->
                        <template x-if="images.length > 1">
                            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-3 py-1 rounded-full z-10">
                                <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                            </div>
                        </template>

                        <!-- Mobil Sağa Sola Kaydırma İpucu -->
                        <template x-if="images.length > 1">
                            <div class="sm:hidden absolute bottom-4 left-4 bg-white/80 backdrop-blur-sm text-gray-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-gray-200 shadow-sm flex items-center gap-1">
                                <i class="fas fa-arrows-alt-h text-rose-600"></i> Kaydırın
                            </div>
                        </template>
                    </div>

                    <!-- Galeri Küçük Görseller (Thumbnails) -->
                    <template x-if="images.length > 1">
                        <div class="flex items-center gap-3 mt-4 overflow-x-auto pb-2">
                            <template x-for="(imgUrl, idx) in images" :key="idx">
                                <button type="button" @click="goTo(idx)"
                                    :class="currentIndex === idx ? 'border-rose-600 ring-2 ring-rose-400 shadow-md' : 'border-gray-200 hover:border-rose-400 opacity-70 hover:opacity-100'"
                                    class="w-16 h-16 rounded-2xl border-2 bg-white p-1 flex-shrink-0 overflow-hidden flex items-center justify-center transition-all">
                                    <img :src="imgUrl" :alt="'Görsel ' + (idx + 1)" class="max-h-full max-w-full object-contain mix-blend-multiply">
                                </button>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- 2. Ürün Bilgileri & Satın Alma Kutusu -->
                <div class="lg:col-span-6 flex flex-col justify-between">
                    <div>
                        <!-- Kategori & Değerlendirme Özeti -->
                        <div class="flex items-center justify-between gap-4 mb-3">
                            <span class="text-xs font-extrabold text-rose-600 bg-rose-50 px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $product->category->name }}
                            </span>
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
                                <div class="flex text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= round($product->average_rating) ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="text-gray-900 font-bold">{{ $product->average_rating }}</span>
                                <span class="text-gray-400">({{ $product->review_count }} Değerlendirme)</span>
                            </div>
                        </div>

                        <!-- Ürün Başlığı -->
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight mb-4">
                            {{ $product->name }}
                        </h1>

                        <!-- Kısa Açıklama -->
                        @if($product->short_description)
                            @php
                                $isShortDescLong = mb_strlen($product->short_description) > 160;
                            @endphp
                            <div x-data="{ expanded: false }" class="relative mb-6">
                                <div class="text-gray-600 text-sm leading-relaxed transition-all relative"
                                     :style="expanded ? 'max-height: none;' : '{{ $isShortDescLong ? "max-height: 70px; overflow: hidden;" : "" }}'">
                                    {{ $product->short_description }}
                                </div>
                                @if($isShortDescLong)
                                    <div x-show="!expanded" class="absolute bottom-5 left-0 right-0 h-10 bg-gradient-to-t from-white via-white/80 to-transparent pointer-events-none"></div>
                                    <button type="button" @click="expanded = !expanded" class="text-rose-600 hover:text-rose-800 text-xs font-bold mt-1 inline-flex items-center gap-1 cursor-pointer">
                                        <span x-text="expanded ? 'Daha az göster' : 'Devamını oku...'"></span>
                                        <i class="fas" :class="expanded ? 'fa-chevron-up text-[10px]' : 'fa-chevron-down text-[10px]'"></i>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- 🛡️ 4 Temel Güven Rozeti (Kompakt Mini Şerit) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-rose-50/80 border border-rose-100">
                                <i class="fas fa-hand-holding-usd text-rose-600 text-sm flex-shrink-0"></i>
                                <div class="leading-tight">
                                    <span class="text-[11px] font-bold block text-gray-900">Kapıda Ödeme</span>
                                    <span class="text-[9px] text-gray-500 block">Nakit / POS</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-emerald-50/80 border border-emerald-100">
                                <i class="fas fa-truck-fast text-emerald-600 text-sm flex-shrink-0"></i>
                                <div class="leading-tight">
                                    <span class="text-[11px] font-bold block text-gray-900">Aynı Gün Teslimat</span>
                                    <span class="text-[9px] text-gray-500 block">VIP Çiçek Kuryesi</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-pink-50/80 border border-pink-100">
                                <i class="fas fa-spa text-pink-600 text-sm flex-shrink-0"></i>
                                <div class="leading-tight">
                                    <span class="text-[11px] font-bold block text-gray-900">%100 Taze Çiçek</span>
                                    <span class="text-[9px] text-gray-500 block">Günlük Mezat</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-2 rounded-xl bg-purple-50/80 border border-purple-100">
                                <i class="fas fa-headset text-purple-600 text-sm flex-shrink-0"></i>
                                <div class="leading-tight">
                                    <span class="text-[11px] font-bold block text-gray-900">7/24 Destek</span>
                                    <span class="text-[9px] text-gray-500 block">WhatsApp &amp; Çağrı</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fiyat Alanı -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 mb-6 flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs text-gray-500 font-bold block">
                                        {{ $product->has_free_installation ? 'Teslimat Dahil Özel Fiyat:' : 'Özel Satış Fiyatı:' }}
                                    </span>
                                    @if($product->discount_percent)
                                        <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                            %{{ $product->discount_percent }} İNDİRİM
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-baseline gap-3">
                                    @if($product->campaign_price && $product->campaign_price < $product->price)
                                        <div class="text-3xl sm:text-4xl font-black text-rose-600">
                                            {{ number_format($product->campaign_price, 0, ',', '.') }} TL
                                        </div>
                                        <div class="text-lg sm:text-xl font-bold text-gray-400 line-through">
                                            {{ number_format($product->price, 0, ',', '.') }} TL
                                        </div>
                                    @else
                                        <div class="text-3xl sm:text-4xl font-extrabold text-rose-600">
                                            {{ number_format($product->price, 0, ',', '.') }} TL
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[11px] text-gray-400 font-medium block mt-1">
                                    KDV dahildir. {{ $product->has_free_installation ? 'İstanbul 39 ilçeye aynı gün teslimat dahildir.' : '' }}
                                </span>
                            </div>
                            @if($product->has_cash_on_delivery)
                                <div class="text-right hidden sm:block">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100">
                                        <i class="fas fa-hand-holding-usd"></i> Kapıda Ödeme
                                    </span>
                                </div>
                            @endif
                        </div>


                        <!-- Aksiyon Butonları (Sepete Ekle & Adet Seçimi & WhatsApp) -->
                        <div class="space-y-3 mb-8" x-data="{ qty: 1 }">
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                                @csrf
                                
                                <!-- Adet Seçici -->
                                <div class="flex items-center border border-gray-300 rounded-2xl bg-gray-50 overflow-hidden shadow-sm h-14 w-full sm:w-36 flex-shrink-0">
                                    <button type="button" @click="if(qty > 1) qty--" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" name="quantity" x-model="qty" min="1" max="99" class="w-full h-full text-center font-extrabold text-gray-900 border-0 bg-white focus:ring-0 text-base">
                                    <button type="button" @click="qty++" class="w-10 h-full flex items-center justify-center text-gray-600 hover:bg-gray-200">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <button type="submit" @if(!$product->in_stock) disabled @endif
                                    class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-3 {{ !$product->in_stock ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    <i class="fas fa-cart-plus text-lg"></i>
                                    <span>{{ $product->in_stock ? 'SEPETE EKLE & SİPARİŞ VER' : 'STOKTA BULUNMUYOR' }}</span>
                                </button>
                            </form>

                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905555555555') }}?text={{ urlencode('Merhaba, ' . $product->name . ' ürünü hakkında bilgi almak ve sipariş vermek istiyorum.') }}"
                                target="_blank"
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold text-sm sm:text-base py-3.5 px-6 rounded-2xl shadow transition-all duration-200 flex items-center justify-center gap-2">
                                <i class="fab fa-whatsapp text-xl"></i>
                                <span>WhatsApp'tan Uzmanımıza Danışın</span>
                            </a>

                            <!-- ⚡ SIFIR RİSK BİLGİLENDİRMESİ -->
                            <div class="p-3 bg-gradient-to-r from-emerald-50 via-teal-50 to-rose-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-xs">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <div class="text-[11px] sm:text-xs text-emerald-950 font-bold leading-snug">
                                    <span class="text-emerald-700 font-black">⚡ Sıfır Risk:</span> Kayıt olmadan &amp; kart bilginizi girmeden sipariş verin, ödemeyi kapıda nakit veya POS ile yapın!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- TAB MENÜ ALANI (Açıklama, Özellikler, Yorumlar, Teslimat) -->
    <div class="bg-gray-50 py-12 border-t border-gray-200" x-data="{ activeTab: 'description' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Tab Başlıkları -->
            <div class="flex border-b border-gray-200 overflow-x-auto space-x-2 sm:space-x-8 bg-white p-2 sm:p-3 rounded-2xl shadow-sm mb-8">
                <button @click="activeTab = 'description'"
                    :class="activeTab === 'description' ? 'border-rose-600 text-rose-600 font-bold bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 rounded-xl text-sm sm:text-base transition-all whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-align-left"></i> Çiçek Açıklaması
                </button>

                @if($product->technical_features)
                    <button @click="activeTab = 'specs'"
                        :class="activeTab === 'specs' ? 'border-rose-600 text-rose-600 font-bold bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                        class="py-3 px-4 rounded-xl text-sm sm:text-base transition-all whitespace-nowrap flex items-center gap-2">
                        <i class="fas fa-spa"></i> Çiçek &amp; Aranjman Özellikleri
                    </button>
                @endif

                <button @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews' ? 'border-rose-600 text-rose-600 font-bold bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 rounded-xl text-sm sm:text-base transition-all whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-star text-amber-400"></i> Değerlendirmeler ({{ $product->review_count }})
                </button>

                <button @click="activeTab = 'shipping'"
                    :class="activeTab === 'shipping' ? 'border-rose-600 text-rose-600 font-bold bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 font-medium'"
                    class="py-3 px-4 rounded-xl text-sm sm:text-base transition-all whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-truck-fast"></i> Teslimat &amp; Bakım Rehberi
                </button>
            </div>

            <!-- Tab İçerikleri -->
            <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-gray-100">
                
                <!-- 1. Ürün Açıklaması (Zengin HTML Desteği & Devamını Oku) -->
                @php
                    $isDescLong = mb_strlen(strip_tags($product->description)) > 100;
                @endphp
                <div x-show="activeTab === 'description'" 
                     x-data="{ expanded: false }"
                     class="relative">
                    
                    <div :style="expanded ? 'max-height: none;' : 'max-height: 120px; overflow: hidden;'"
                         class="product-description-view text-slate-700 leading-relaxed text-sm sm:text-base transition-all duration-300 relative">
                        @if(Str::contains($product->description, ['<p>', '<div>', '<br>', '<ul>', '<ol>', '<h1>', '<h2>', '<h3>']))
                            {!! $product->description !!}
                        @else
                            {!! nl2br(e($product->description)) !!}
                        @endif
                    </div>

                    @if($isDescLong)
                        <!-- Gradient Fade Effect -->
                        <div x-show="!expanded" 
                             class="absolute bottom-10 left-0 right-0 h-24 bg-gradient-to-t from-white via-white/95 to-transparent pointer-events-none">
                        </div>

                        <!-- Daha Fazlasını Gör Butonu -->
                        <div class="pt-3 text-center relative z-10">
                            <button type="button" 
                                    @click="expanded = !expanded" 
                                    class="inline-flex items-center gap-2 px-6 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-black text-xs sm:text-sm rounded-full border border-rose-200 shadow-2xs transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                <span x-text="expanded ? 'Daha Az Göster' : 'Tüm Açıklamayı Göster (Devamını Oku)'"></span>
                                <i class="fas" :class="expanded ? 'fa-chevron-up text-xs' : 'fa-chevron-down text-xs'"></i>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- 2. Teknik Özellikler (Zengin HTML Desteği & Devamını Oku) -->
                @if($product->technical_features)
                    @php
                        $isSpecsLong = mb_strlen(strip_tags($product->technical_features)) > 100;
                    @endphp
                    <div x-show="activeTab === 'specs'" x-cloak 
                         x-data="{ expanded: false }"
                         class="relative">
                        
                        <div :style="expanded ? 'max-height: none;' : 'max-height: 120px; overflow: hidden;'"
                             class="bg-gray-50/90 p-6 rounded-3xl border border-gray-100 product-description-view text-slate-700 text-sm leading-loose transition-all duration-300 relative">
                            @if(Str::contains($product->technical_features, ['<p>', '<div>', '<br>', '<ul>', '<ol>', '<li>']))
                                {!! $product->technical_features !!}
                            @else
                                {!! nl2br(e($product->technical_features)) !!}
                            @endif
                        </div>

                        @if($isSpecsLong)
                            <!-- Gradient Fade Effect -->
                            <div x-show="!expanded" 
                                 class="absolute bottom-10 left-0 right-0 h-24 bg-gradient-to-t from-gray-50/90 via-gray-50/80 to-transparent pointer-events-none rounded-b-3xl">
                            </div>

                            <!-- Daha Fazlasını Gör Butonu -->
                            <div class="pt-3 text-center relative z-10">
                                <button type="button" 
                                        @click="expanded = !expanded" 
                                        class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-black text-xs sm:text-sm rounded-full border border-gray-200 shadow-2xs transition-all hover:scale-105 active:scale-95 cursor-pointer">
                                    <span x-text="expanded ? 'Daha Az Göster' : 'Tüm Özellikleri Göster'"></span>
                                    <i class="fas" :class="expanded ? 'fa-chevron-up text-xs' : 'fa-chevron-down text-xs'"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- 3. YORUMLAR VE DEĞERLENDİRME TABI -->
                <div x-show="activeTab === 'reviews'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        
                        <!-- Sol: Puan Özeti & Onaylı Yorumlar Listesi -->
                        <div class="lg:col-span-7">
                            <div class="flex items-center gap-4 p-6 bg-rose-50/60 rounded-2xl border border-rose-100 mb-8">
                                <div class="text-4xl font-extrabold text-rose-600">{{ $product->average_rating }}</div>
                                <div>
                                    <div class="flex text-amber-400 text-base mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($product->average_rating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-gray-600 font-semibold">Toplam {{ $product->review_count }} gerçek müşteri değerlendirmesi</p>
                                </div>
                            </div>

                            <!-- Yorum Listesi -->
                            <div class="space-y-6">
                                @forelse($product->approvedReviews as $review)
                                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold text-xs">
                                                    {{ strtoupper(substr($review->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-sm text-gray-900">{{ $review->name }}</h4>
                                                    <span class="text-[10px] text-gray-400">{{ $review->created_at->translatedFormat('d F Y') }}</span>
                                                </div>
                                            </div>
                                            <div class="flex text-amber-400 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed mt-3">
                                            {{ $review->comment }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-10 bg-gray-50 rounded-2xl p-6 text-gray-500">
                                        <i class="far fa-comment-dots text-3xl text-gray-300 mb-2"></i>
                                        <p class="text-sm font-semibold">Bu ürün için henüz yorum yapılmamış.</p>
                                        <p class="text-xs text-gray-400 mt-1">İlk değerlendirmeyi siz yapın, diğer kullanıcılara fikir verin!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Sağ: Yeni Yorum Yapma Formu -->
                        <div class="lg:col-span-5 bg-gray-50 p-6 sm:p-8 rounded-2xl border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Ürünü Değerlendirin</h3>
                            <p class="text-xs text-gray-500 mb-6">Deneyiminizi paylaşarak diğer müşterilere yardımcı olun.</p>

                            <form action="{{ route('reviews.store', $product) }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Puanınız *</label>
                                    <select name="rating" required class="w-full rounded-xl border-gray-300 text-sm focus:border-rose-500 focus:ring-rose-500 font-bold text-amber-600">
                                        <option value="5">⭐⭐⭐⭐⭐ (5/5 - Çok Memnun Kaldım)</option>
                                        <option value="4">⭐⭐⭐⭐ (4/5 - İyi / Başarılı)</option>
                                        <option value="3">⭐⭐⭐ (3/5 - Orta / Standart)</option>
                                        <option value="2">⭐⭐ (2/5 - Beklediğim Gibi Değil)</option>
                                        <option value="1">⭐ (1/5 - Memnun Kalmadım)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Adınız Soyadınız *</label>
                                    <input type="text" name="name" required placeholder="Örn: Ahmet Yılmaz"
                                        class="w-full rounded-xl border-gray-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">E-posta Adresiniz (Yayınlanmaz)</label>
                                    <input type="email" name="email" placeholder="ornek@mail.com"
                                        class="w-full rounded-xl border-gray-300 text-sm focus:border-rose-500 focus:ring-rose-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Yorumunuz *</label>
                                    <textarea name="comment" rows="4" required placeholder="Çiçeklerin tazeliği, teslimat hızı ve paketleme deneyiminiz hakkında bilgi verin..."
                                        class="w-full rounded-xl border-gray-300 text-sm focus:border-rose-500 focus:ring-rose-500"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl text-sm shadow transition-colors cursor-pointer">
                                    Değerlendirmeyi Gönder
                                </button>
                                <p class="text-[10px] text-gray-400 text-center">Yorumlar onaylandıktan sonra yayına alınır.</p>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- 4. Teslimat & Bakım Rehberi -->
                <div x-show="activeTab === 'shipping'" x-cloak class="space-y-6 text-sm text-gray-700 leading-relaxed">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-6 bg-rose-50/50 rounded-2xl border border-rose-100">
                            <h4 class="font-bold text-rose-900 text-base mb-2 flex items-center gap-2">
                                <i class="fas fa-truck-fast text-rose-600"></i> İstanbul İçi VIP Kurye &amp; Saatli Teslimat
                            </h4>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                İstanbul genelindeki 39 ilçeye özel çiçek kuryelerimizle, koruyucu su hazneli ambalajında ve seçtiğiniz zaman aralığında teslimat yapıyoruz. Alıcı adreste bulunmazsa sizinle iletişime geçilerek alternatif teslimat sağlanır.
                            </p>
                        </div>
                        <div class="p-6 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                            <h4 class="font-bold text-emerald-900 text-base mb-2 flex items-center gap-2">
                                <i class="fas fa-leaf text-emerald-600"></i> %100 Tazelik Garantisi &amp; Bakım Önerisi
                            </h4>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Tüm çiçeklerimiz günlük mezattan temin edilir ve usta çiçekçilerimiz tarafından özenle tanzim edilir. Çiçeklerinizi teslim aldığınızda saplarını 45° açıyla kesip temiz vazoya oda sıcaklığında su koyarak tazeliğini uzun süre koruyabilirsiniz.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-frontend-layout>
