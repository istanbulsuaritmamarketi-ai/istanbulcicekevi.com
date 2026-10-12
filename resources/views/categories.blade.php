<x-frontend-layout title="Tüm Çiçek Kategorileri | İstanbul Çiçek Evi" description="Gül buketleri, vazoda taze çiçekler, kutuda tasarım çiçekler ve özel aranjmanlar. İstanbul geneli aynı gün VIP teslimat.">
    <!-- Breadcrumb & Header -->
    <div class="bg-gradient-to-r from-rose-700 via-pink-700 to-rose-900 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-rose-800">
        <div class="max-w-7xl mx-auto text-center">
            <span class="inline-flex items-center gap-1.5 bg-white/10 text-rose-200 text-xs font-black px-3.5 py-1.5 rounded-full border border-white/20 mb-3 shadow-inner">
                <i class="fas fa-spa"></i> Taze Çiçek Koleksiyonu
            </span>
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                Tüm Çiçek Kategorilerimiz
            </h1>
            <p class="text-xs sm:text-sm text-rose-100 max-w-2xl mx-auto">
                Sevdiklerinize en güzel sürprizi yapmak için günlük taze çiçeklerle hazırladığımız özel buket ve aranjmanları keşfedin.
            </p>
        </div>
    </div>

    <!-- Kategoriler Listesi -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group block p-5 sm:p-7 bg-white border border-gray-200/80 rounded-3xl shadow-2xs hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 text-center relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto mb-4 bg-gradient-to-br from-rose-50 to-pink-50 text-rose-600 rounded-2xl flex items-center justify-center p-3 group-hover:scale-105 transition-all shadow-xs overflow-hidden">
                            @if($category->image_path)
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" width="64" height="64" loading="lazy" class="w-full h-full object-contain">
                            @else
                                <i class="fas fa-spa text-2xl sm:text-3xl text-rose-500 group-hover:scale-110 transition-transform"></i>
                            @endif
                        </div>
                        <h2 class="font-extrabold text-sm sm:text-base text-gray-900 group-hover:text-rose-600 transition-colors">
                            {{ $category->name }}
                        </h2>
                        @if($category->description)
                            <p class="text-[11px] text-gray-400 mt-1 line-clamp-2 leading-relaxed">
                                {{ $category->description }}
                            </p>
                        @endif
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-bold text-gray-500 group-hover:text-rose-600 transition-colors">
                        <span>{{ $category->products_count ?? 0 }} Çiçek</span>
                        <span class="inline-flex items-center gap-1 text-rose-600">
                            İncele <i class="fas fa-chevron-right text-[9px] group-hover:translate-x-0.5 transition-transform"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center text-gray-400 py-12">
                    Henüz kategori tanımlanmamış.
                </div>
            @endforelse
        </div>

        <!-- Tüm Ürünler Butonu -->
        <div class="mt-12 text-center">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs sm:text-sm px-8 py-3.5 rounded-2xl shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95">
                <i class="fas fa-spa"></i> Tüm Çiçek Kataloğunu İncele (Buketler &amp; Tasarımlar)
            </a>
        </div>
    </div>
</x-frontend-layout>
