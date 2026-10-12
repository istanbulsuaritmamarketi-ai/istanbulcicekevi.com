<x-frontend-layout 
    title="Tüm Çiçek Çeşitleri ve Buket Fiyatları - İstanbul Çiçek Evi" 
    description="Taze gül buketleri, orkide, saksı çiçekleri, aranjmanlar ve çelenkler. İstanbul içi aynı gün teslimat ve kapıda ödeme fırsatıyla sipariş verin.">
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Başlık + Arama -->
            <div class="mb-6">
                <h1 class="text-2xl font-extrabold text-gray-900 mb-4">
                    @if(request('search'))
                        "<span class="text-rose-600">{{ request('search') }}</span>" için arama sonuçları
                    @elseif(request('category'))
                        {{ \App\Models\Category::where('slug', request('category'))->value('name') ?? 'Ürünler' }}
                    @else
                        Tüm Çiçekler &amp; Buketler
                    @endif
                    <span class="text-base font-normal text-gray-400 ml-2">({{ $products->total() }} ürün)</span>
                </h1>

                <!-- Arama Çubuğu -->
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-2">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                    <div class="relative flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Çiçek, buket veya aranjman ara..."
                            class="w-full pl-4 pr-10 py-3 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:border-rose-400 shadow-sm">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    @if(request('search') || request('category') || request('sort') || request('min_price') || request('max_price'))
                        <a href="{{ route('products.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
                            <i class="fas fa-times mr-1"></i> Temizle
                        </a>
                    @endif
                </form>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Sidebar Filtreler -->
                <aside class="w-full lg:w-64 flex-shrink-0">
                    <form action="{{ route('products.index') }}" method="GET" id="filter-form">
                        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                        
                        <!-- Kategoriler -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
                            <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">Kategoriler</h3>
                            <ul class="space-y-2">
                                <li>
                                    <a href="{{ route('products.index', array_merge(request()->except('category'), [])) }}"
                                       class="flex items-center justify-between text-sm {{ !request('category') ? 'text-rose-600 font-bold' : 'text-gray-600 hover:text-rose-600' }}">
                                        <span>Tümü</span>
                                        <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ \App\Models\Product::where('is_active', true)->count() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $cat)
                                <li>
                                    <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                                       class="flex items-center justify-between text-sm {{ request('category') === $cat->slug ? 'text-rose-600 font-bold' : 'text-gray-600 hover:text-rose-600' }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $cat->products()->where('is_active', true)->count() }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Fiyat Aralığı -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-4">
                            <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">Fiyat Aralığı</h3>
                            <div class="flex gap-2 items-center">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-rose-400">
                                <span class="text-gray-400">—</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0"
                                    class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-rose-400">
                            </div>
                            <button type="submit" class="mt-3 w-full bg-rose-600 hover:bg-rose-700 text-white text-sm font-medium py-2 rounded-lg transition-colors cursor-pointer">
                                Uygula
                            </button>
                        </div>

                        <!-- Sıralama -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                            <h3 class="font-bold text-gray-900 mb-3 text-sm uppercase tracking-wider">Sıralama</h3>
                            <div class="space-y-2">
                                @foreach(['newest' => 'En Yeni', 'price_asc' => 'Fiyat: Düşükten Yükseğe', 'price_desc' => 'Fiyat: Yüksekten Düşüğe', 'name' => 'İsme Göre (A-Z)'] as $val => $label)
                                <a href="{{ route('products.index', array_merge(request()->except('sort'), ['sort' => $val])) }}"
                                   class="flex items-center gap-2 text-sm {{ request('sort', 'newest') === $val ? 'text-rose-600 font-semibold' : 'text-gray-600 hover:text-rose-600' }}">
                                    <i class="fas fa-circle text-{{ request('sort', 'newest') === $val ? 'rose-500' : 'gray-200' }} text-xs"></i>
                                    {{ $label }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </aside>

                <!-- Ürün Listesi -->
                <div class="flex-1">
                    @if($products->count() > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                            @foreach($products as $product)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-all duration-300 flex flex-col justify-between">
                                    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-square w-full bg-white p-2.5 sm:p-4 overflow-hidden flex items-center justify-center">
                                        @php
                                            $prodImg = $product->image_path ?: $product->image;
                                        @endphp
                                        @if($prodImg)
                                            <img src="{{ $product->image_url }}" 
                                                 alt="{{ $product->name }}"
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-rose-300">
                                                <i class="fas fa-spa text-4xl"></i>
                                            </div>
                                        @endif
                                        <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                                            @if($product->created_at->gt(now()->subDays(14)))
                                                <span class="bg-rose-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">YENİ</span>
                                            @endif
                                        </div>
                                        @if($product->discount_percent)
                                            <span class="absolute top-2 right-2 bg-gradient-to-r from-rose-600 to-red-600 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full shadow">
                                                %{{ $product->discount_percent }} İNDİRİM
                                            </span>
                                        @endif
                                    </a>
                                    <div class="p-2.5 sm:p-3 flex-grow flex flex-col justify-between">
                                        <h3 class="text-xs sm:text-sm font-semibold text-gray-800 mb-1 line-clamp-2 leading-tight">
                                            <a href="{{ route('product.show', $product->slug) }}" class="hover:text-rose-600">{{ $product->name }}</a>
                                        </h3>
                                        <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-100">
                                            <div>
                                                @if($product->campaign_price)
                                                    <span class="text-sm sm:text-base font-bold text-red-600">{{ number_format($product->campaign_price, 0, ',', '.') }} ₺</span>
                                                    <span class="text-[10px] sm:text-xs text-gray-400 line-through block sm:inline sm:ml-1">{{ number_format($product->price, 0, ',', '.') }} ₺</span>
                                                @else
                                                    <span class="text-sm sm:text-base font-bold text-rose-600">{{ number_format($product->price, 0, ',', '.') }} ₺</span>
                                                @endif
                                            </div>
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-8 h-8 sm:w-9 sm:h-9 bg-rose-600 hover:bg-rose-700 text-white rounded-xl sm:rounded-full flex items-center justify-center transition-colors shadow-sm cursor-pointer" title="Sepete Ekle">
                                                    <i class="fas fa-cart-plus text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Sayfalama -->
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-20">
                            <i class="fas fa-search text-5xl text-gray-200 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-500 mb-2">Sonuç Bulunamadı</h3>
                            <p class="text-gray-400 mb-6">Arama kriterlerinizi değiştirip tekrar deneyin.</p>
                            <a href="{{ route('products.index') }}" class="bg-rose-600 text-white px-6 py-3 rounded-lg hover:bg-rose-700 transition-colors">
                                Tüm Çiçekleri Gör
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-frontend-layout>
