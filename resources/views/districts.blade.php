<x-frontend-layout title="İstanbul Çiçek Gönderim Bölgelerimiz (39 İlçe) | İstanbul Çiçek Evi" description="İstanbul genelinde 39 ilçeye aynı gün teslimat, taze çiçek garantisi ve özel tasarım aranjmanlar sunuyoruz.">
    <!-- Breadcrumb & Başlık -->
    <div class="bg-gradient-to-r from-rose-900 via-pink-900 to-slate-900 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-rose-800/40">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-flex items-center gap-1.5 bg-rose-500/20 text-rose-300 text-xs font-black px-3.5 py-1.5 rounded-full border border-rose-400/30 mb-3 shadow-inner">
                <i class="fas fa-motorcycle text-rose-400"></i> Hızlı Özel Kurye Teslimatı
            </span>
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                İstanbul'un 39 İlçesine Aynı Gün Çiçek Teslimatı
            </h1>
            <p class="text-xs sm:text-sm text-rose-100/90 max-w-2xl mx-auto">
                Bulunduğunuz veya çiçeği göndermek istediğiniz ilçeyi seçerek bölgenize özel aynı gün randevulu teslimat seçeneklerini ve taze buketleri keşfedin.
            </p>
        </div>
    </div>

    <!-- İlçe Listeleri -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        
        <!-- Anadolu Yakası -->
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-5 border-b border-gray-200 pb-3">
                <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm font-black">
                    <i class="fas fa-heart"></i>
                </span>
                <h2 class="text-base sm:text-lg font-black text-gray-900">
                    Anadolu Yakası Çiçek Siparişi (14 İlçe)
                </h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($districts as $dSlug => $dData)
                    @if($dData['side'] === 'Anadolu')
                        <a href="{{ route('district.cicek', $dSlug) }}" 
                           class="p-3.5 rounded-2xl bg-white border border-gray-200 hover:border-rose-500 hover:bg-rose-50/50 transition-all text-center group shadow-2xs transform hover:-translate-y-0.5 active:scale-95 flex flex-col items-center justify-center">
                            <i class="fas fa-spa text-rose-500 group-hover:scale-110 transition-transform mb-1 text-base"></i>
                            <span class="text-xs font-black text-gray-900 group-hover:text-rose-600">{{ $dData['name'] }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5">Çiçek Siparişi</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Avrupa Yakası -->
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-5 border-b border-gray-200 pb-3">
                <span class="w-8 h-8 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center text-sm font-black">
                    <i class="fas fa-heart"></i>
                </span>
                <h2 class="text-base sm:text-lg font-black text-gray-900">
                    Avrupa Yakası Çiçek Siparişi (25 İlçe)
                </h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($districts as $dSlug => $dData)
                    @if($dData['side'] === 'Avrupa')
                        <a href="{{ route('district.cicek', $dSlug) }}" 
                           class="p-3.5 rounded-2xl bg-white border border-gray-200 hover:border-pink-500 hover:bg-pink-50/50 transition-all text-center group shadow-2xs transform hover:-translate-y-0.5 active:scale-95 flex flex-col items-center justify-center">
                            <i class="fas fa-spa text-pink-500 group-hover:scale-110 transition-transform mb-1 text-base"></i>
                            <span class="text-xs font-black text-gray-900 group-hover:text-pink-600">{{ $dData['name'] }}</span>
                            <span class="text-[10px] text-gray-400 mt-0.5">Çiçek Siparişi</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Alt Sipariş Çağrısı Kutusu -->
        <div class="bg-gradient-to-r from-rose-600 via-pink-600 to-rose-700 text-white rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-5 shadow-lg">
            <div class="text-center sm:text-left">
                <h3 class="text-lg sm:text-xl font-black">
                    Tüm İlçelere Aynı Gün Taze Çiçek &amp; Özel Not Kartı
                </h3>
                <p class="text-xs sm:text-sm text-rose-100 mt-1 max-w-xl leading-relaxed">
                    Sevdikleriniz için siparişinizi verin, dilediğiniz teslimat saatinde özel çiçek kuryemiz güvenle ulaştırsın!
                </p>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <a href="{{ route('products.index') }}" class="bg-white text-rose-700 hover:bg-rose-50 font-black text-xs sm:text-sm px-5 py-3 rounded-2xl shadow transition-all transform active:scale-95 flex items-center gap-2">
                    <i class="fas fa-shopping-bag"></i> Çiçekleri İncele
                </a>
                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20çiçek%20siparişi%20vermek%20istiyorum." target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs sm:text-sm px-5 py-3 rounded-2xl shadow transition-all transform active:scale-95 flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i> WhatsApp Sipariş
                </a>
            </div>
        </div>

    </div>
</x-frontend-layout>
