<x-frontend-layout 
    :title="$districtName . ' Çiçek Siparişi & Aynı Gün Teslimat | İstanbul Çiçek Evi'"
    :description="$districtName . ' ilçesine en taze çiçekler, gül buketleri, kutuda güller ve orkideler aynı gün teslimat garantisi ve ücretsiz kart notuyla gönderilir.'"
    :keywords="$districtName . ' çiçek siparişi, ' . $districtName . ' çiçekçi, ' . $districtName . ' gül buketi, ' . $districtName . ' aynı gün çiçek'">

    <!-- Schema.org Florist Rich Snippet -->
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Florist',
            'name' => 'İstanbul Çiçek Evi - ' . $districtName . ' Çiçek Siparişi',
            'image' => !empty($siteSettings['logo']) ? asset('storage/' . $siteSettings['logo']) : asset('images/logo.png'),
            'telephone' => $siteSettings['phone'] ?? '0850 555 55 55',
            'email' => $siteSettings['email'] ?? 'bilgi@istanbulcicekevi.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $districtName,
                'addressRegion' => 'İstanbul',
                'addressCountry' => 'TR'
            ],
            'areaServed' => $districtName . ', İstanbul',
            'priceRange' => '₺₺',
            'url' => url()->current()
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <!-- Hero / Başlık Alanı -->
    <div class="bg-gradient-to-br from-rose-950 via-rose-900 to-pink-950 text-white py-14 sm:py-20 relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-rose-200/80 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Ana Sayfa</a>
                <i class="fas fa-chevron-right text-[10px] text-rose-400"></i>
                <a href="{{ route('districts.index') }}" class="hover:text-white transition-colors">Gönderim Bölgeleri</a>
                <i class="fas fa-chevron-right text-[10px] text-rose-400"></i>
                <span class="text-rose-300 font-bold">{{ $districtName }}</span>
            </nav>

            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 bg-rose-500/30 text-rose-200 text-xs font-black px-4 py-1.5 rounded-full border border-rose-400/30 mb-4 shadow-inner">
                    🌸 {{ $districtName }} ({{ $districtSide }} Yakası) Aynı Gün Teslimat
                </span>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-4">
                    {{ $districtName }} Çiçek Siparişi &amp; Özel Buket Tasarımları
                </h1>
                <p class="text-sm sm:text-base text-rose-100/90 leading-relaxed mb-8">
                    {{ $districtName }} ilçesindeki sevdikleriniz için mezattan günlük gelen en taze çiçekler, <strong>aynı gün randevulu teslimat</strong> ve <strong>ücretsiz şık kart notuyla</strong> kapıda!
                </p>

                <div class="flex flex-wrap gap-3">
                    <a href="#urunler" class="bg-white hover:bg-rose-50 text-rose-900 font-black px-6 py-3.5 rounded-2xl shadow-lg transition-all text-xs sm:text-sm flex items-center gap-2">
                        <i class="fas fa-spa text-rose-600"></i> Çiçekleri İncele
                    </a>
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text={{ urlencode('Merhaba, ' . $districtName . ' ilçesine çiçek siparişi vermek istiyorum.') }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black px-6 py-3.5 rounded-2xl shadow-lg transition-all text-xs sm:text-sm flex items-center gap-2">
                        <i class="fab fa-whatsapp text-base"></i> {{ $districtName }} WhatsApp Çiçekçi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Güven Unsurları (Modern 4'lü Kart Bandı) -->
    <div class="bg-white border-b border-gray-100 py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-rose-50/60 border border-rose-100/80 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-rose-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/20">
                        <i class="fas fa-clock text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight">Zaman Aralıklı Teslimat</div>
                        <div class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 leading-tight">{{ $districtName }} Özel Kurye</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100/80 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20">
                        <i class="fas fa-leaf text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight">%100 Taze Çiçek Garantisi</div>
                        <div class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 leading-tight">Günlük Mezat Seçimi</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-pink-50/60 border border-pink-100/80 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-pink-600 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-pink-500/20">
                        <i class="fas fa-envelope-open-text text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight">Ücretsiz Zarflı Not Kartı</div>
                        <div class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 leading-tight">Flora AI Duygusal Mesajlar</div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 sm:gap-3.5 p-3 sm:p-4 rounded-2xl bg-amber-50/60 border border-amber-100/80 hover:shadow-md transition-shadow">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-500 text-white rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md shadow-amber-500/20">
                        <i class="fas fa-heart text-base sm:text-xl"></i>
                    </div>
                    <div>
                        <div class="text-[11px] sm:text-xs font-black text-gray-900 leading-tight">Çikolata &amp; Peluş Ayıcık</div>
                        <div class="text-[9px] sm:text-[11px] text-gray-500 mt-0.5 leading-tight">Sepette Özel Tamamlayıcılar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Öne Çıkan Çiçekler -->
    <div id="urunler" class="bg-gray-50 py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-1">{{ $districtName }} Çiçek Koleksiyonu</span>
                <h2 class="text-xl sm:text-3xl font-black text-gray-900">{{ $districtName }} İçin En Çok Tercih Edilen Çiçekler</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Aynı gün teslimat ve ücretsiz özel kart notu dahil popüler buketlerimiz</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                @foreach($products as $product)
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

                    <!-- Görsel -->
                    <div class="aspect-square w-full overflow-hidden bg-white p-2.5 sm:p-4 relative flex items-center justify-center border-b border-gray-100">
                        <a href="{{ route('product.show', $product->slug) }}" class="w-full h-full flex items-center justify-center">
                            @if($product->image_path)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="flex items-center justify-center h-full w-full text-rose-300"><i class="fas fa-spa text-3xl sm:text-4xl"></i></div>
                            @endif
                        </a>
                    </div>

                    <!-- Bilgi -->
                    <div class="p-3 sm:p-4 flex flex-col flex-grow justify-between">
                        <div>
                            <div class="text-[10px] sm:text-xs text-gray-400 font-bold mb-1 uppercase tracking-wider">{{ $product->category->name ?? 'Çiçek' }}</div>
                            <h3 class="text-xs sm:text-sm font-black text-gray-900 group-hover:text-rose-600 transition-colors line-clamp-2 leading-snug mb-2">
                                <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
                            </h3>
                        </div>

                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                            <div>
                                @if($product->campaign_price && $product->campaign_price < $product->price)
                                    <div class="text-[10px] sm:text-xs text-gray-400 line-through">₺{{ number_format($product->price, 2, ',', '.') }}</div>
                                    <div class="text-sm sm:text-base font-black text-rose-600">₺{{ number_format($product->campaign_price, 2, ',', '.') }}</div>
                                @else
                                    <div class="text-sm sm:text-base font-black text-rose-600">₺{{ number_format($product->price, 2, ',', '.') }}</div>
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
                @endforeach
            </div>
        </div>
    </div>

    <!-- SEO Zengin Bilgilendirme Makalesi (İlçe Bazlı Çiçek Teslimatı) -->
    <div class="bg-white py-16 border-t border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-rose-50/40 rounded-3xl p-8 sm:p-12 border border-rose-100 shadow-sm">
                <div class="max-w-3xl mb-8">
                    <span class="text-xs font-extrabold text-rose-600 uppercase tracking-widest block mb-2">{{ $districtName }} Çiçekçi Rehberi</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight">
                        {{ $districtName }} İlçesinde Çiçek Siparişi Verirken Dikkat Edilmesi Gerekenler
                    </h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 text-xs sm:text-sm text-gray-700 leading-relaxed">
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-check-circle text-rose-600"></i> {{ $districtName }} Aynı Gün ve Zaman Dilimli Çiçek Teslimatı
                        </h3>
                        <p>
                            İstanbul'un en canlı ve nezih bölgelerinden <strong>{{ $districtName }}</strong> için oluşturduğunuz tüm çiçek siparişleri, mezattan taze temin edilen çiçeklerle siparişinize özel taze bağlanır. 09:00 - 13:00, 13:00 - 17:00 ve 17:00 - 21:00 zaman dilimlerinden birini seçerek teslimatı alıcının en müsait olduğu vakte planlayabilirsiniz.
                        </p>
                        <p>
                            Dilerseniz siparişinizi <strong>İsimsiz (Gizli Gönderim)</strong> olarak seçebilir, alıcıya sürpriz heyecanı yaşatabilirsiniz. Kart notunuz özenli zarflarla çiçeğin yanında teslim edilir.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <i class="fas fa-heart text-pink-600"></i> {{ $districtName }} Buket Tasarımları &amp; Tamamlayıcı Aksesuarlar
                        </h3>
                        <p>
                            Kırmızı güllerden zarafetin simgesi orkidelere, neşeli papatyalardan egzotik lilyum buketlerine kadar tüm çeşitlerimizde çiçek besini ve bakım talimatı birlikte sunulur.
                        </p>
                        <p>
                            Sipariş aşamasında çiçeklerinizin yanına <strong>lüks çikolata</strong>, <strong>sevimli peluş ayıcık</strong> veya <strong>özel tasarım cam vazo</strong> ekleyerek sürprizinizi unutulmaz kılabilirsiniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diğer İstanbul İlçeleri (Internal Linking & SEO Cloud) -->
    <div class="bg-gray-50 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-base font-black text-gray-900 mb-6 text-center">
                İstanbul'un Diğer Tüm İlçelerine de Aynı Gün Çiçek Gönderimi Yapıyoruz:
            </h3>
            <div class="flex flex-wrap justify-center gap-2 max-w-5xl mx-auto">
                @foreach($allDistricts as $dSlug => $dData)
                    <a href="{{ route('district.cicek', $dSlug) }}" 
                       class="text-xs px-3 py-1.5 rounded-xl border transition-all {{ $dSlug === $slug ? 'bg-rose-600 text-white font-bold border-rose-600 shadow' : 'bg-white text-gray-700 hover:bg-rose-50 hover:text-rose-600 border-gray-200' }}">
                        {{ $dData['name'] }} Çiçekçi
                    </a>
                @endforeach
            </div>
        </div>
    </div>

</x-frontend-layout>
