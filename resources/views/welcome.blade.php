<x-frontend-layout>
    
    <!-- ⚡ 3 ÖZEL KAMPANYA & VİTRİN KUTUSU (Zengin Banner Düzeni) -->
    <div class="bg-white py-4 sm:py-6 px-3 sm:px-6 lg:px-8 border-b border-rose-100/70 relative z-20">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-5">
                
                <!-- 1. Kutu: Bugün Fiyatı Düşenler (Kırmızı Gradient Banner) -->
                <a href="#urunler" class="group relative overflow-hidden rounded-3xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 p-4 sm:p-5 text-white shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-between gap-3 border border-red-500/30">
                    <!-- Sol: Beyaz Çerçeveli Ürün Önizlemeleri & % İndirim Rozeti -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-13 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1.5 shadow-md flex flex-col items-center justify-center flex-shrink-0 text-center border border-white/40">
                            <span class="text-2xl sm:text-3xl font-black text-red-600 leading-none">%</span>
                            <span class="text-[9px] font-black text-rose-700 uppercase tracking-tighter mt-1">İNDİRİM</span>
                        </div>
                        <div class="w-14 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/40 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/products/6.png') }}" alt="Fırsat Çiçeği" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <!-- Sağ: Metinler & Buton -->
                    <div class="flex-1 min-w-0 text-right sm:text-left flex flex-col justify-center items-end sm:items-start">
                        <span class="inline-flex items-center gap-1 text-[10px] sm:text-[11px] font-extrabold text-amber-200 uppercase tracking-wider mb-1">
                            <i class="fas fa-bolt text-amber-300"></i> Fırsatları Kaçırma
                        </span>
                        <h3 class="text-base sm:text-lg lg:text-xl font-black text-white leading-tight drop-shadow-xs">
                            Bugün Fiyatı Düşenler
                        </h3>
                        <span class="mt-2 inline-flex items-center gap-1 bg-white text-rose-700 hover:bg-rose-50 text-xs sm:text-sm font-black px-3.5 py-1.5 rounded-xl shadow-xs transition-colors">
                            İncele <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>

                <!-- 2. Kutu: Orkide & Saksı Çiçekleri (Petrol / Cyan Gradient Banner) -->
                <a href="{{ route('category.show', 'orkide-ve-saksi-cicekleri') }}" class="group relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 via-cyan-800 to-sky-900 p-4 sm:p-5 text-white shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-between gap-3 border border-teal-500/30">
                    <!-- Sol: 2 Adet Beyaz Çerçeveli Orkide Görseli -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-14 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/40 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/products/5.png') }}" alt="Beyaz Orkide" class="w-full h-full object-contain">
                        </div>
                        <div class="w-14 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/40 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/products/9.png') }}" alt="Mavi Orkide" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <!-- Sağ: Rozet, Başlık ve Alt Başlık -->
                    <div class="flex-1 min-w-0 text-right sm:text-left flex flex-col justify-center items-end sm:items-start">
                        <span class="inline-block bg-black/30 backdrop-blur-xs text-white text-[9px] sm:text-[10px] font-extrabold px-2.5 py-0.5 rounded-full mb-1 border border-white/20">
                            Taze Saksı Bitkileri
                        </span>
                        <h3 class="text-base sm:text-lg lg:text-xl font-black text-white leading-tight drop-shadow-xs">
                            En Trend Çiçekler
                        </h3>
                        <p class="text-[11px] sm:text-xs text-cyan-100 font-medium mt-0.5 line-clamp-1">
                            Orkide &amp; Saksı • Avantajlı Fiyatlar
                        </p>
                    </div>
                </a>

                <!-- 3. Kutu: Açılış & Düğün Çelengi (Terracotta / Turuncu Gradient Banner) -->
                <a href="{{ route('category.show', 'acilis-dugun-ve-celenk') }}" class="group relative overflow-hidden rounded-3xl bg-gradient-to-r from-orange-600 via-amber-600 to-rose-700 p-4 sm:p-5 text-white shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-between gap-3 border border-orange-500/30">
                    <!-- Sol: 2 Adet Beyaz Çerçeveli Çelenk / Tören Görseli -->
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-14 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/40 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/products/1.png') }}" alt="Açılış Sepeti" class="w-full h-full object-contain">
                        </div>
                        <div class="w-14 h-18 sm:w-16 sm:h-22 rounded-2xl bg-white p-1 shadow-md flex items-center justify-center overflow-hidden flex-shrink-0 border border-white/40 group-hover:scale-105 transition-transform">
                            <img src="{{ asset('images/products/11.png') }}" alt="Özel Çiçek" class="w-full h-full object-contain">
                        </div>
                    </div>
                    <!-- Sağ: Rozet, Başlık ve Alt Başlık -->
                    <div class="flex-1 min-w-0 text-right sm:text-left flex flex-col justify-center items-end sm:items-start">
                        <span class="inline-block bg-black/30 backdrop-blur-xs text-white text-[9px] sm:text-[10px] font-extrabold px-2.5 py-0.5 rounded-full mb-1 border border-white/20">
                            Aynı Gün Tören Teslimi
                        </span>
                        <h3 class="text-base sm:text-lg lg:text-xl font-black text-white leading-tight drop-shadow-xs">
                            Açılış &amp; Düğün Çelengi
                        </h3>
                        <p class="text-[11px] sm:text-xs text-orange-100 font-medium mt-0.5 line-clamp-1">
                            Kurumsal İsim Şeritli • Keşfet
                        </p>
                    </div>
                </a>

            </div>
        </div>
    </div>

    <!-- 💬 TEK BUTON: ÇİÇEK DANIŞMA -->
    <div class="py-2.5 sm:py-3.5 px-3 sm:px-6 lg:px-8 bg-rose-50/70 border-b border-rose-100 flex items-center justify-center">
        <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20%C3%B6zel%20g%C3%BCn%20i%C3%A7in%20%C3%A7i%C3%A7ek%20sipari%C5%9Fi%20vermek%20istiyorum.%20Yard%C4%B1mc%C4%B1%20olabilir%20misiniz?" 
           target="_blank" 
           class="inline-flex max-w-full items-center justify-center gap-2 sm:gap-3 px-4 sm:px-6 py-2.5 sm:py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs hover:shadow transition-all transform hover:-translate-y-0.5 active:scale-95 text-center group">
            <i class="fab fa-whatsapp text-lg sm:text-xl text-emerald-200 group-hover:text-white transition-colors flex-shrink-0"></i>
            <span class="text-xs sm:text-sm md:text-base font-black tracking-tight leading-snug">
                Özel aranjman veya çiçek seçimi için WhatsApp çiçek uzmanımıza anında danışın
            </span>
            <i class="fas fa-arrow-right text-xs opacity-80 group-hover:translate-x-1 transition-transform flex-shrink-0"></i>
        </a>
    </div>

    <!-- 🔥 4 TEMEL FIRSAT ÜRÜNÜ (GÜNÜN ÇİÇEK FIRSATLARI) -->
    @if(isset($topDealProducts) && count($topDealProducts) > 0)
    <div class="bg-gray-50/70 pt-8 pb-10 px-4 sm:px-6 lg:px-8 border-b border-gray-100 relative">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-6 pb-3 border-b border-gray-200/80 text-center sm:text-left">
                <div>
                    <span class="bg-rose-50 text-rose-600 border border-rose-200 text-[10px] sm:text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider inline-flex items-center gap-1.5 mb-2 shadow-2xs">
                        <i class="fas fa-fire text-rose-500"></i> Günün Çiçek Fırsatları
                    </span>
                    <h2 class="text-xl sm:text-3xl font-black text-gray-900 tracking-tight">
                        En Popüler 4 Tasarım Buket
                    </h2>
                </div>
                <div class="text-xs text-gray-500 font-medium flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold"><i class="fas fa-check-circle"></i> Taze Çiçek Garantisi</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 text-rose-600 font-bold"><i class="fas fa-envelope"></i> Ücretsiz Kart Notu</span>
                </div>
            </div>

            <!-- 4'lü Fırsat Ürünü Grid (Mobilde İkili, Masaüstünde 4'lü) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
                @foreach($topDealProducts as $deal)
                    <div class="bg-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 text-gray-900 border border-gray-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group transform hover:-translate-y-1">
                        
                        <!-- Rozetler -->
                        <div class="relative">
                            <div class="absolute top-0 left-0 flex flex-col gap-1 z-10">
                                @if($deal->discount_percent)
                                    <span class="bg-gradient-to-r from-rose-600 to-red-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                        %{{ $deal->discount_percent }} İNDİRİM
                                    </span>
                                @elseif($deal->custom_badge)
                                    <span class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                        {{ $deal->custom_badge }}
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('product.show', $deal->slug) }}" class="block aspect-square w-full bg-white rounded-2xl p-2 sm:p-3 overflow-hidden flex items-center justify-center border border-gray-100">
                                @if($deal->image_path)
                                    <img src="{{ $deal->image_url }}" 
                                         alt="{{ $deal->name }}" 
                                         loading="lazy" 
                                         class="object-contain h-full w-full group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <i class="fas fa-spa text-4xl text-rose-300"></i>
                                @endif
                            </a>
                        </div>

                        <!-- Ürün Detayları -->
                        <div class="mt-3 flex flex-col justify-between flex-grow">
                            <div>
                                <span class="text-[9px] font-black text-rose-600 uppercase tracking-wider block mb-1">
                                    {{ $deal->category->name ?? 'Tasarım Çiçek' }}
                                </span>
                                <a href="{{ route('product.show', $deal->slug) }}" class="hover:text-rose-600 transition-colors">
                                    <h3 class="text-xs sm:text-sm font-black text-gray-900 line-clamp-2 leading-snug">{{ $deal->name }}</h3>
                                </a>
                            </div>

                            <!-- Fiyat & Sipariş Butonu -->
                            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <div class="text-sm sm:text-base font-black text-rose-600 leading-none">
                                        {{ number_format($deal->campaign_price ?: $deal->price, 0, ',', '.') }} ₺
                                    </div>
                                    @if($deal->campaign_price && $deal->campaign_price < $deal->price)
                                        <div class="text-[10px] text-gray-400 line-through mt-0.5">
                                            {{ number_format($deal->price, 0, ',', '.') }} ₺
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('product.show', $deal->slug) }}" class="bg-rose-600 hover:bg-rose-700 text-white font-black text-[11px] px-3 py-1.5 rounded-xl shadow transition-all active:scale-95 flex items-center gap-1">
                                    İncele <i class="fas fa-chevron-right text-[8px]"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- 🏷️ 3 KATEGORİ KUTUCUĞU (2 ADMİN SEÇİMİ + 1 DİĞER KATEGORİLER) -->
    <div class="bg-white py-7 sm:py-9 px-4 sm:px-6 lg:px-8 border-b border-gray-100">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between gap-3 mb-5 pb-2 border-b border-gray-100">
                <div>
                    <span class="text-[11px] font-black text-rose-600 uppercase tracking-widest block mb-0.5">Hızlı Keşfet</span>
                    <h2 class="text-lg sm:text-2xl font-black text-gray-900">Öne Çıkan Çiçek Koleksiyonları</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-3.5 py-1.5 rounded-xl transition-colors">
                    Tüm Kategoriler <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                
                <!-- 1. Kategori Kutusu -->
                @if($homeCat1)
                <a href="{{ route('category.show', $homeCat1->slug) }}" class="group p-4 sm:p-5 rounded-2xl bg-white border border-gray-200/80 hover:border-rose-400 hover:shadow-md transition-all flex items-center gap-3.5 transform hover:-translate-y-0.5 active:scale-95 shadow-2xs">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-rose-50 to-pink-50 text-rose-600 rounded-2xl flex items-center justify-center flex-shrink-0 border border-rose-100 group-hover:scale-105 transition-transform overflow-hidden p-2">
                        @if($homeCat1->image_path)
                            <img src="{{ $homeCat1->image_url }}" alt="{{ $homeCat1->name }}" class="w-full h-full object-contain">
                        @else
                            <i class="fas fa-heart text-xl sm:text-2xl text-rose-600"></i>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <span class="text-xs sm:text-sm font-black text-gray-900 group-hover:text-rose-600 transition-colors block line-clamp-1">{{ $homeCat1->name }}</span>
                        <span class="text-[10px] sm:text-[11px] text-gray-500 mt-0.5 block">Buketleri İncele <i class="fas fa-chevron-right text-[8px]"></i></span>
                    </div>
                </a>
                @endif

                <!-- 2. Kategori Kutusu -->
                @if($homeCat2)
                <a href="{{ route('category.show', $homeCat2->slug) }}" class="group p-4 sm:p-5 rounded-2xl bg-white border border-gray-200/80 hover:border-pink-400 hover:shadow-md transition-all flex items-center gap-3.5 transform hover:-translate-y-0.5 active:scale-95 shadow-2xs">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-50 to-purple-50 text-pink-600 rounded-2xl flex items-center justify-center flex-shrink-0 border border-pink-100 group-hover:scale-105 transition-transform overflow-hidden p-2">
                        @if($homeCat2->image_path)
                            <img src="{{ $homeCat2->image_url }}" alt="{{ $homeCat2->name }}" class="w-full h-full object-contain">
                        @else
                            <i class="fas fa-spa text-xl sm:text-2xl text-pink-600"></i>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <span class="text-xs sm:text-sm font-black text-gray-900 group-hover:text-pink-600 transition-colors block line-clamp-1">{{ $homeCat2->name }}</span>
                        <span class="text-[10px] sm:text-[11px] text-gray-500 mt-0.5 block">Çiçekleri İncele <i class="fas fa-chevron-right text-[8px]"></i></span>
                    </div>
                </a>
                @endif

                <!-- 3. Kutu: Diğer Kategoriler (Tümünü İncele) -->
                <a href="{{ route('categories.index') }}" class="group p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-rose-600 to-pink-700 text-white hover:from-rose-700 hover:to-pink-800 hover:shadow-md transition-all flex items-center gap-3.5 transform hover:-translate-y-0.5 active:scale-95 shadow-2xs">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 text-white rounded-2xl flex items-center justify-center flex-shrink-0 border border-white/20 group-hover:scale-105 transition-transform">
                        <i class="fas fa-th-large text-xl sm:text-2xl text-pink-200"></i>
                    </div>
                    <div class="flex-grow">
                        <span class="text-xs sm:text-sm font-black text-white block">Tüm Çiçek Çeşitleri</span>
                        <span class="text-[10px] sm:text-[11px] text-rose-100 mt-0.5 block font-bold">Kategorilere Göz At <i class="fas fa-arrow-right text-[8px]"></i></span>
                    </div>
                </a>

            </div>
        </div>
    </div>

    <!-- 1. Öne Çıkan Çiçekler (Modern E-Ticaret Kartları - Mobilde İkili & Kaydırılabilir / Grid) -->
    <div id="urunler" class="bg-white py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-4 border-b border-gray-100 text-center sm:text-left">
                <div>
                    <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-1">Taptaze &amp; Özel Tasarım</span>
                    <h2 class="text-xl sm:text-3xl font-black text-gray-900">En Çok Tercih Edilen Çiçekler ve Buketler</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-xl transition-colors">
                    Tüm Çiçekleri Gör ({{ count($featuredProducts) }}+) <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                @forelse($featuredProducts as $product)
                <div class="group relative border border-gray-200/80 rounded-2xl sm:rounded-3xl overflow-hidden bg-white shadow-2xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col justify-between">
                    
                    <!-- Rozetler -->
                    <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex flex-col gap-1 z-10">
                        @if($product->discount_percent)
                            <div class="bg-gradient-to-r from-rose-600 to-red-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                %{{ $product->discount_percent }} İNDİRİM
                            </div>
                        @elseif($product->custom_badge)
                            <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                {{ $product->custom_badge }}
                            </div>
                        @endif
                    </div>

                    <a href="{{ route('product.show', $product->slug) }}" class="block aspect-square w-full bg-white p-2.5 sm:p-4 overflow-hidden relative flex items-center justify-center">
                        @if($product->image_path)
                            <img src="{{ $product->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy" 
                                 decoding="async" 
                                 class="object-contain h-full w-full rounded-2xl group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex items-center justify-center h-full w-full text-rose-300">
                                <i class="fas fa-spa text-3xl sm:text-4xl"></i>
                            </div>
                        @endif
                    </a>

                    <div class="p-3 sm:p-4 flex-grow flex flex-col border-t border-gray-100 bg-gray-50/40 justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[9px] sm:text-[10px] text-rose-700 font-black uppercase tracking-wider truncate">{{ $product->category->name ?? 'Çiçek' }}</span>
                                <div class="flex text-amber-400 text-[8px] sm:text-[10px]">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>

                            <a href="{{ route('product.show', $product->slug) }}">
                                <h3 class="text-xs sm:text-sm font-black text-gray-900 mb-1 group-hover:text-rose-600 transition-colors line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            </a>
                        </div>
                        
                        <div class="flex items-center justify-between mt-2 pt-2 sm:pt-3 border-t border-gray-200/60">
                            <div>
                                @if($product->campaign_price && $product->campaign_price < $product->price)
                                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:gap-1.5">
                                        <span class="text-base sm:text-xl font-black text-rose-600 font-mono">{{ number_format($product->campaign_price, 0, ',', '.') }} TL</span>
                                        <span class="text-[10px] sm:text-xs font-bold text-gray-400 line-through">{{ number_format($product->price, 0, ',', '.') }} TL</span>
                                    </div>
                                @else
                                    <span class="text-base sm:text-xl font-black text-rose-600 font-mono">{{ number_format($product->price, 0, ',', '.') }} TL</span>
                                @endif
                            </div>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white w-8 h-8 sm:w-auto sm:p-2 sm:px-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-1 text-xs active:scale-95 cursor-pointer" title="Sepete Ekle">
                                    <i class="fas fa-cart-plus text-xs"></i> <span class="hidden sm:inline">Ekle</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center text-gray-500 py-8">
                    Henüz çiçek eklenmemiş.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SEO BİLGİLENDİRME & TANITIM REHBERİ -->
    <section class="bg-gray-50 py-16 border-t border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-gray-200/80">
                <div class="max-w-3xl mb-8">
                    <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-2">İstanbul Çiçek Siparişi &amp; Teslimat Rehberi</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">
                        İstanbul'un Her Noktasına Aynı Gün Taze Çiçek, Özel Not Kartı ve Güvenli Teslimat
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 text-xs sm:text-sm text-gray-700 leading-relaxed">
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-check-circle text-rose-600"></i> Neden İstanbul Çiçek Evi?
                        </h3>
                        <p>
                            Çiçeklerimiz mezattan her sabah taze seçilir, usta çiçek tasarımcılarımız tarafından özenle bağlanır ve serin araçlarımızla alıcısına tam vaktinde ulaştırılır. İster sevgilinize 101 kırmızı gül, ister yeni iş tebriki için zarif beyaz bir orkide gönderin; çiçeklerinizin ilk günkü tazeliği korunur.
                        </p>
                        <p>
                            Ücretsiz kişiselleştirilmiş kart notu ve Flora AI yapay zeka asistanımız ile duygu yüklü mesajınızı şık bir zarfla çiçeğinizin yanına iliştiriyoruz.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-heart text-pink-600"></i> Zaman Dilimli Teslimat &amp; Gizli Gönderici Seçeneği
                        </h3>
                        <p>
                            Alıcının adresinde bulunacağı en uygun vakti seçebilmeniz için gün içerisinde 4 farklı teslimat penceresi sunuyoruz. 
                        </p>
                        <p>
                            Sürprizinizi daha da heyecanlı kılmak isterseniz <strong>"İsimsiz (Gizli Gönderim)"</strong> seçeneğini tercih edebilirsiniz. Kuryemiz dahil hiç kimse gönderici ismini alıcıya açıklamaz; yalnızca kart notunuz teslim edilir.
                        </p>
                    </div>
                </div>

                <!-- 3 Sütunlu Avantaj Kartları -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10 pt-8 border-t border-gray-100">
                    <div class="p-5 bg-rose-50/50 rounded-2xl border border-rose-100">
                        <h4 class="font-bold text-rose-900 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-clock text-rose-600"></i> Zaman Dilimli Teslimat
                        </h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            İstanbul genelindeki 39 ilçede kuryelerimiz siparişlerinizi belirlediğiniz gün ve saat aralığında güvenle adrese ulaştırır.
                        </p>
                    </div>
                    <div class="p-5 bg-emerald-50/50 rounded-2xl border border-emerald-100">
                        <h4 class="font-bold text-emerald-900 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-leaf text-emerald-600"></i> %100 Taze Çiçek Garantisi
                        </h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Günlük taze mezat çiçekleri kullanılır. Vazoda 7-10 gün canlılık garantisi ve ücretsiz çiçek besini ile gönderilir.
                        </p>
                    </div>
                    <div class="p-5 bg-purple-50/50 rounded-2xl border border-purple-100">
                        <h4 class="font-bold text-purple-900 text-sm mb-2 flex items-center gap-2">
                            <i class="fas fa-envelope text-purple-600"></i> Ücretsiz Şık Not Kartı
                        </h4>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Mühürlü zarfta size özel mesajınız. Dilerseniz Flora AI'nin hazırladığı hazır duygusal not şablonlarını kullanabilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Sık Sorulan Sorular (Dinamik Veritabanı Accordion) -->
    @if(isset($faqs) && $faqs->count() > 0)
    <div class="bg-gray-50 py-16 border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-1">Merak Edilenler</span>
                <h2 class="text-2xl sm:text-3xl font-black text-gray-900">Sıkça Sorulan Sorular</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Çiçek teslimatı, kart notu ve sipariş süreçleri hakkında detaylı yanıtlar</p>
            </div>
            
            <div class="space-y-4">
                @foreach($faqs as $faq)
                <details class="group bg-white rounded-2xl p-5 border border-gray-200 shadow-sm [&_summary::-webkit-details-marker]:hidden" {{ $loop->first ? 'open' : '' }}>
                    <summary class="flex justify-between items-center font-bold text-sm sm:text-base text-gray-900 cursor-pointer list-none">
                        <span>{{ $faq->question }}</span>
                        <span class="transition group-open:rotate-180 text-rose-600">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </summary>
                    <div class="text-xs sm:text-sm text-gray-600 mt-3 leading-relaxed border-t pt-3">
                        {!! nl2br(e($faq->answer)) !!}
                    </div>
                </details>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Google FAQPage JSON-LD Rich Snippet (Veritabanından Dinamik) -->
    @php
        $faqSchemaList = [];
        foreach($faqs as $faqItem) {
            $faqSchemaList[] = [
                '@type' => 'Question',
                'name' => $faqItem->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faqItem->answer)
                ]
            ];
        }
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqSchemaList
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif

</x-frontend-layout>
