<x-frontend-layout 
    :title="$category->name . ' Çiçek Modelleri ve Fiyatları - İstanbul Çiçek Evi'" 
    :description="$category->description ?: ($category->name . ' kategorisindeki en taze çiçekler ve özel tasarım aranjmanlar. İstanbul 39 ilçeye aynı gün VIP teslimat ve kapıda ödeme seçeneğiyle.')">
    <!-- Kategori Başlığı -->
    <div class="bg-gradient-to-r from-rose-700 via-pink-600 to-rose-800 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-white">{{ $category->name }}</h1>
            @if($category->description)
                <p class="mt-2 text-rose-100 max-w-3xl">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <!-- Ürün Listesi -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
                @forelse($products as $product)
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
                                 class="object-contain h-full w-full group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="flex items-center justify-center h-full w-full text-rose-300"><i class="fas fa-spa text-3xl sm:text-4xl"></i></div>
                        @endif
                    </a>

                    <div class="p-3 sm:p-4 flex-grow flex flex-col border-t border-gray-100 bg-gray-50/40 justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[9px] sm:text-[10px] text-rose-700 font-black uppercase tracking-wider truncate">{{ $product->category->name ?? '' }}</span>
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
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-dashed text-gray-400">
                    <i class="fas fa-spa text-4xl mb-2 text-rose-300"></i>
                    <h3 class="text-sm font-bold text-gray-700">Çiçek Bulunamadı</h3>
                    <p class="text-xs text-gray-400 mt-1">Bu kategoriye ait henüz aktif bir çiçek eklenmemiş.</p>
                </div>
                @endforelse
            </div>

            <!-- Sayfalama (Pagination) -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-frontend-layout>
