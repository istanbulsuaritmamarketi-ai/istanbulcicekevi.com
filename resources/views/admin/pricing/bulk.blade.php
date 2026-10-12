<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-rose-600 font-bold mb-1">
                    <a href="{{ route('admin.products.index') }}" class="hover:underline">Çiçek Kataloğu</a>
                    <span>/</span>
                    <span class="text-gray-500">Fiyat &amp; Gelir Yönetimi</span>
                </div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <span class="p-2 bg-amber-50 text-amber-600 rounded-2xl border border-amber-200/60 text-lg">
                        <i class="fas fa-tags"></i>
                    </span>
                    Toplu Fiyat &amp; İndirim / Zam Yönetim Paneli
                </h2>
                <p class="text-xs text-gray-500 mt-1">Tüm çiçeklerde veya seçili kategorilerde tek tıkla toplu zam yapın, indirim tanımlayın veya kampanya fiyatı oluşturun.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.index') }}" 
                   class="bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs py-2.5 px-4 rounded-xl border border-gray-200 shadow-2xs transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Ürün Listesine Dön
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-2xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="font-bold text-sm text-emerald-900">İşlem Başarılı!</div>
                        <div class="text-xs text-emerald-700">{{ session('success') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-2xl shadow-sm">
                <div class="font-bold text-sm text-rose-900 mb-1 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Lütfen formu kontrol edin:
                </div>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="bulkPricing({{ json_encode($products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->price,
            'campaign_price' => $p->campaign_price ? (float) $p->campaign_price : null,
            'category_id' => $p->category_id,
            'category_name' => $p->category ? $p->category->name : 'Kategorisiz',
            'image_url' => $p->image_url,
         ])) }})">

        <!-- 1. ÜST İSTATİSTİK KARTLARI -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-spa"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400">Aktif Çiçekler</div>
                    <div class="text-xl font-black text-gray-900 mt-0.5">{{ $activeProducts }} Adet</div>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-percentage"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400">Kampanyalı Çiçekler</div>
                    <div class="text-xl font-black text-amber-600 mt-0.5">{{ $campaignProducts }} Adet</div>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400">Ortalama Fiyat</div>
                    <div class="text-xl font-black text-emerald-600 mt-0.5">{{ number_format($avgPrice, 0, ',', '.') }} ₺</div>
                </div>
            </div>

            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-100 shadow-2xs flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-gray-400">Kategori Sayısı</div>
                    <div class="text-xl font-black text-purple-600 mt-0.5">{{ $categories->count() }} Kategori</div>
                </div>
            </div>
        </div>

        <!-- 2. ANA İŞLEM FORMU (KONTROL PANELİ) -->
        <form action="{{ route('admin.pricing.bulk.apply') }}" method="POST" @submit.prevent="submitForm">
            @csrf

            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-6 sm:p-8 mb-8">
                
                <div class="border-b border-gray-100 pb-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-black text-lg text-gray-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xs">1</span>
                            Fiyat Güncelleme Parametreleri
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Hangi işlem yapılacağını, oranını ve hedeflenen ürün grubunu seçin</p>
                    </div>

                    <!-- Canlı Değişim Özeti Rozeti -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold shadow-2xs"
                         :class="{
                            'bg-emerald-50 text-emerald-700 border border-emerald-200': actionType === 'markup',
                            'bg-rose-50 text-rose-700 border border-rose-200': actionType === 'discount',
                            'bg-purple-50 text-purple-700 border border-purple-200': actionType === 'set_campaign',
                            'bg-gray-100 text-gray-700 border border-gray-200': actionType === 'clear_campaign',
                            'bg-blue-50 text-blue-700 border border-blue-200': actionType === 'set_fixed'
                         }">
                        <span class="w-2 h-2 rounded-full"
                              :class="{
                                'bg-emerald-500': actionType === 'markup',
                                'bg-rose-500': actionType === 'discount',
                                'bg-purple-500': actionType === 'set_campaign',
                                'bg-gray-400': actionType === 'clear_campaign',
                                'bg-blue-500': actionType === 'set_fixed'
                              }"></span>
                        <span x-text="getSummaryText()"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Sol Kolon: İşlem Türü & Kapsam (7 Kolon) -->
                    <div class="lg:col-span-7 space-y-6">
                        
                        <!-- A. İşlem Türü -->
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2.5">
                                A. Yapılacak İşlem Türü
                            </label>
                            <input type="hidden" name="action_type" :value="actionType">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                
                                <!-- Zam / Fiyat Artışı -->
                                <button type="button" 
                                        @click="actionType = 'markup'; if(amount == 0) amount = 10;"
                                        class="p-3.5 rounded-2xl border text-left transition-all relative overflow-hidden"
                                        :class="actionType === 'markup' 
                                            ? 'border-emerald-500 bg-emerald-50/50 text-emerald-950 shadow-xs ring-2 ring-emerald-500/20' 
                                            : 'border-gray-200 hover:border-gray-300 text-gray-700 bg-white'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                                            <i class="fas fa-arrow-trend-up"></i>
                                        </span>
                                        <i x-show="actionType === 'markup'" class="fas fa-check-circle text-emerald-600 text-sm"></i>
                                    </div>
                                    <div class="font-extrabold text-xs">Zam / Fiyat Artışı</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">Fiyatları yukarı çekin (+)</div>
                                </button>

                                <!-- İndirim / Fiyat Düşürme -->
                                <button type="button" 
                                        @click="actionType = 'discount'; if(amount == 0) amount = 15;"
                                        class="p-3.5 rounded-2xl border text-left transition-all relative overflow-hidden"
                                        :class="actionType === 'discount' 
                                            ? 'border-rose-500 bg-rose-50/50 text-rose-950 shadow-xs ring-2 ring-rose-500/20' 
                                            : 'border-gray-200 hover:border-gray-300 text-gray-700 bg-white'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-sm">
                                            <i class="fas fa-arrow-trend-down"></i>
                                        </span>
                                        <i x-show="actionType === 'discount'" class="fas fa-check-circle text-rose-600 text-sm"></i>
                                    </div>
                                    <div class="font-extrabold text-xs">Fiyat İndirimi</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">Fiyatları aşağı çekin (-)</div>
                                </button>

                                <!-- Kampanya Fiyatı Tanımla -->
                                <button type="button" 
                                        @click="actionType = 'set_campaign'; if(amount == 0) amount = 20;"
                                        class="p-3.5 rounded-2xl border text-left transition-all relative overflow-hidden"
                                        :class="actionType === 'set_campaign' 
                                            ? 'border-purple-500 bg-purple-50/50 text-purple-950 shadow-xs ring-2 ring-purple-500/20' 
                                            : 'border-gray-200 hover:border-gray-300 text-gray-700 bg-white'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-sm">
                                            <i class="fas fa-fire"></i>
                                        </span>
                                        <i x-show="actionType === 'set_campaign'" class="fas fa-check-circle text-purple-600 text-sm"></i>
                                    </div>
                                    <div class="font-extrabold text-xs">Kampanya Tanımla</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">İndirimli etiket fiyatı ekler</div>
                                </button>

                                <!-- Kampanyaları Kaldır -->
                                <button type="button" 
                                        @click="actionType = 'clear_campaign'"
                                        class="p-3.5 rounded-2xl border text-left transition-all relative overflow-hidden"
                                        :class="actionType === 'clear_campaign' 
                                            ? 'border-gray-600 bg-gray-50 text-gray-950 shadow-xs ring-2 ring-gray-400/20' 
                                            : 'border-gray-200 hover:border-gray-300 text-gray-700 bg-white'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-8 h-8 rounded-xl bg-gray-200 text-gray-700 flex items-center justify-center text-sm">
                                            <i class="fas fa-times-circle"></i>
                                        </span>
                                        <i x-show="actionType === 'clear_campaign'" class="fas fa-check-circle text-gray-700 text-sm"></i>
                                    </div>
                                    <div class="font-extrabold text-xs">Kampanyaları Sıfırla</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">Sadece normal fiyata döner</div>
                                </button>

                                <!-- Sabit Fiyat Belirle -->
                                <button type="button" 
                                        @click="actionType = 'set_fixed'; valueType = 'fixed';"
                                        class="p-3.5 rounded-2xl border text-left transition-all relative overflow-hidden"
                                        :class="actionType === 'set_fixed' 
                                            ? 'border-blue-500 bg-blue-50/50 text-blue-950 shadow-xs ring-2 ring-blue-500/20' 
                                            : 'border-gray-200 hover:border-gray-300 text-gray-700 bg-white'">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <i x-show="actionType === 'set_fixed'" class="fas fa-check-circle text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="font-extrabold text-xs">Tek Sabit Fiyat Ata</div>
                                    <div class="text-[10px] text-gray-500 mt-0.5">Tümüne tek bir fiyat verir</div>
                                </button>
                            </div>
                        </div>

                        <!-- B. Kapsam (Hangi Çiçeklere Uygulansın?) -->
                        <div>
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2.5">
                                B. Hedef Kapsam (Hangi Çiçekler?)
                            </label>
                            <input type="hidden" name="scope" :value="scope">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                
                                <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="scope === 'all' ? 'border-amber-500 bg-amber-50/40 text-amber-950' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <input type="radio" value="all" x-model="scope" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <div class="font-bold text-xs">Tüm Çiçekler</div>
                                        <div class="text-[10px] text-gray-500">{{ $activeProducts }} ürünün tümü</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="scope === 'category' ? 'border-amber-500 bg-amber-50/40 text-amber-950' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <input type="radio" value="category" x-model="scope" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <div class="font-bold text-xs">Kategoriye Göre</div>
                                        <div class="text-[10px] text-gray-500">Sadece seçilen kategori</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3.5 rounded-2xl border cursor-pointer transition-all"
                                       :class="scope === 'selected' ? 'border-amber-500 bg-amber-50/40 text-amber-950' : 'border-gray-200 text-gray-700 hover:bg-gray-50'">
                                    <input type="radio" value="selected" x-model="scope" class="text-amber-600 focus:ring-amber-500">
                                    <div>
                                        <div class="font-bold text-xs">Seçili Çiçekler</div>
                                        <div class="text-[10px] text-gray-500"><span x-text="selectedIds.length"></span> adet seçildi</div>
                                    </div>
                                </label>
                            </div>

                            <!-- Kategori Seçim Kutusu (Kapsam Kategori ise Görünür) -->
                            <div x-show="scope === 'category'" x-transition class="mt-3 p-4 bg-gray-50 rounded-2xl border border-gray-200">
                                <label for="category_id" class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Kategori Seçin:
                                </label>
                                <select name="category_id" id="category_id" x-model="selectedCategory"
                                        class="w-full text-xs font-bold rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-2.5">
                                    <option value="">-- Bir Kategori Seçiniz --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }} ({{ $cat->products->count() }} Çiçek)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Sağ Kolon: Oran/Tutar & Yuvarlama (5 Kolon) -->
                    <div class="lg:col-span-5 space-y-6 lg:border-l lg:border-gray-100 lg:pl-8">
                        
                        <!-- C. Değer & Tür (Tutar veya Yüzde) -->
                        <div x-show="actionType !== 'clear_campaign'">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2.5">
                                C. Değişim Değeri &amp; Birimi
                            </label>

                            <div class="space-y-3">
                                <!-- Birim Seçimi: % veya ₺ -->
                                <div class="flex items-center gap-2" x-show="actionType !== 'set_fixed'">
                                    <button type="button" 
                                            @click="valueType = 'percent'"
                                            class="flex-1 py-2 px-3 rounded-xl font-bold text-xs transition-colors flex items-center justify-center gap-1.5"
                                            :class="valueType === 'percent' ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                        <i class="fas fa-percent"></i> Yüzdesel (%)
                                    </button>
                                    <button type="button" 
                                            @click="valueType = 'fixed'"
                                            class="flex-1 py-2 px-3 rounded-xl font-bold text-xs transition-colors flex items-center justify-center gap-1.5"
                                            :class="valueType === 'fixed' ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                        <i class="fas fa-lira-sign"></i> Sabit Tutar (₺)
                                    </button>
                                </div>
                                <input type="hidden" name="value_type" :value="valueType">

                                <!-- Değer Giriş Kutusu -->
                                <div class="relative">
                                    <input type="number" 
                                           name="amount" 
                                           x-model.number="amount" 
                                           step="any" 
                                           min="0.01" 
                                           class="w-full text-xl font-black rounded-2xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-3 pl-4 pr-12 text-gray-900"
                                           :placeholder="valueType === 'percent' ? 'Örn: 15' : 'Örn: 100'">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-black text-gray-400 text-lg"
                                          x-text="valueType === 'percent' ? '%' : '₺'"></span>
                                </div>

                                <!-- Hızlı Tıklama Butonları -->
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Hızlı Seçim:</div>
                                    <div class="flex flex-wrap gap-1.5" x-show="valueType === 'percent'">
                                        <template x-for="p in [5, 10, 15, 20, 25, 30, 40, 50]">
                                            <button type="button" 
                                                    @click="amount = p"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors"
                                                    :class="amount === p ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                                    x-text="'%' + p"></button>
                                        </template>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5" x-show="valueType === 'fixed'">
                                        <template x-for="f in [25, 50, 100, 150, 200, 250, 500, 1000]">
                                            <button type="button" 
                                                    @click="amount = f"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors"
                                                    :class="amount === f ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                                    x-text="f + ' ₺'"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- D. Hedef Alan (Hangi Fiyat Değişecek?) -->
                        <div x-show="actionType === 'markup' || actionType === 'discount' || actionType === 'set_fixed'">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                                D. Hedef Fiyat Alanı
                            </label>
                            <input type="hidden" name="target_field" :value="targetField">
                            <div class="grid grid-cols-3 gap-2">
                                <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all"
                                       :class="targetField === 'price' ? 'border-amber-500 bg-amber-50 text-amber-950 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50 text-xs'">
                                    <input type="radio" value="price" x-model="targetField" class="sr-only">
                                    <div>Satış Fiyatı</div>
                                    <div class="text-[10px] text-gray-400">Normal</div>
                                </label>
                                <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all"
                                       :class="targetField === 'campaign_price' ? 'border-amber-500 bg-amber-50 text-amber-950 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50 text-xs'">
                                    <input type="radio" value="campaign_price" x-model="targetField" class="sr-only">
                                    <div>Kampanya Fiyatı</div>
                                    <div class="text-[10px] text-gray-400">İndirimli</div>
                                </label>
                                <label class="p-2.5 rounded-xl border text-center cursor-pointer transition-all"
                                       :class="targetField === 'both' ? 'border-amber-500 bg-amber-50 text-amber-950 font-bold' : 'border-gray-200 text-gray-600 hover:bg-gray-50 text-xs'">
                                    <input type="radio" value="both" x-model="targetField" class="sr-only">
                                    <div>Her İkisi Birden</div>
                                    <div class="text-[10px] text-gray-400">Eşzamanlı</div>
                                </label>
                            </div>
                        </div>

                        <!-- E. Yuvarlama / Kuruş Kuralı -->
                        <div x-show="actionType !== 'clear_campaign'">
                            <label class="block text-xs font-black text-gray-700 uppercase tracking-wider mb-2">
                                E. Yuvarlama Kuralı
                            </label>
                            <select name="rounding" x-model="rounding" 
                                    class="w-full text-xs font-bold rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 py-2.5">
                                <option value="integer">🎯 En Yakın Tam Sayıya Yuvarla (Örn: 749.60 ₺ ➔ 750 ₺)</option>
                                <option value="psychological_9">🏷️ Psikolojik 9 ile Biten (Örn: 752 ₺ ➔ 749 ₺)</option>
                                <option value="psychological_90">✨ Psikolojik .90 Kuruş (Örn: 750 ₺ ➔ 749.90 ₺)</option>
                                <option value="ten">🔢 En Yakın Onluğa Yuvarla (Örn: 743 ₺ ➔ 740 ₺)</option>
                                <option value="none">⚙️ Kuruşlu Bırak (Virgülden sonra 2 hane)</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Alt Aksiyon Barı -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-gray-500 flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-500 text-sm"></i>
                        <span>Aşağıdaki canlı önizleme tablosunda <strong class="text-gray-900" x-text="targetProductsCount"></strong> adet ürünün yeni fiyatları anlık simüle edilmektedir.</span>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="submit" 
                                :disabled="targetProductsCount === 0"
                                class="w-full sm:w-auto px-6 py-3.5 rounded-2xl font-black text-sm text-white shadow-md transition-all flex items-center justify-center gap-2"
                                :class="targetProductsCount > 0 
                                    ? 'bg-gradient-to-r from-amber-500 via-rose-500 to-rose-600 hover:from-amber-600 hover:to-rose-700 shadow-rose-500/20 active:scale-95' 
                                    : 'bg-gray-300 cursor-not-allowed'">
                            <i class="fas fa-bolt"></i>
                            <span>Fiyat Değişikliğini Uygula (<span x-text="targetProductsCount"></span> Çiçek)</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- 3. CANLI SİMÜLASYON VE ÖNİZLEME TABLOSU -->
            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden mb-12">
                
                <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gray-50/50">
                    <div>
                        <h4 class="font-black text-base text-gray-900 flex items-center gap-2">
                            <i class="fas fa-eye text-rose-500"></i>
                            Canlı Fiyat Simülasyonu &amp; Önizleme Tablosu
                        </h4>
                        <p class="text-xs text-gray-500 mt-0.5">Fiyatlar kaydedilmeden önce yeni tutarları canlı olarak inceleyin. İstediğiniz ürünün onay kutusunu kaldırarak bu işlemden muaf tutabilirsiniz.</p>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <!-- Ürün Arama -->
                        <div class="relative flex-1 sm:w-64">
                            <input type="text" 
                                   x-model="searchTerm" 
                                   placeholder="Çiçek adı ile ara..." 
                                   class="w-full text-xs rounded-xl border-gray-200 focus:border-amber-500 focus:ring-amber-500 pl-8 pr-3 py-2">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>

                        <!-- Hepsini Seç / Kaldır -->
                        <button type="button" 
                                @click="toggleSelectAll()" 
                                class="px-3 py-2 rounded-xl text-xs font-bold border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 whitespace-nowrap shadow-2xs">
                            <span x-text="allSelected ? 'Seçimi Kaldır' : 'Tümünü Seç'"></span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 text-[11px] font-black text-gray-500 uppercase tracking-wider border-b border-gray-100">
                                <th class="py-3 px-4 w-12 text-center">
                                    <input type="checkbox" 
                                           :checked="allSelected" 
                                           @change="toggleSelectAll()"
                                           class="rounded text-amber-600 focus:ring-amber-500">
                                </th>
                                <th class="py-3 px-4">Çiçek Ürünü</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-right">Mevcut Normal Fiyat</th>
                                <th class="py-3 px-4 text-right">Mevcut Kampanya</th>
                                <th class="py-3 px-4 text-right bg-amber-50/40 text-amber-900">YENİ Normal Fiyat</th>
                                <th class="py-3 px-4 text-right bg-amber-50/40 text-amber-900">YENİ Kampanya</th>
                                <th class="py-3 px-4 text-center">Fark / Değişim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            <template x-for="prod in filteredProducts" :key="prod.id">
                                <tr class="hover:bg-gray-50/60 transition-colors"
                                    :class="{'opacity-40 bg-gray-50/40': scope === 'selected' && !selectedIds.includes(prod.id)}">
                                    
                                    <!-- Checkbox (Forma Seçili ID'leri iletmek için) -->
                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox" 
                                               name="product_ids[]" 
                                               :value="prod.id"
                                               :checked="selectedIds.includes(prod.id)"
                                               @change="toggleProduct(prod.id)"
                                               class="rounded text-amber-600 focus:ring-amber-500">
                                    </td>

                                    <!-- Ürün Bilgisi & Görsel -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center p-0.5">
                                                <img :src="prod.image_url" :alt="prod.name" class="w-full h-full object-cover rounded-lg">
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-gray-900 truncate max-w-xs" x-text="prod.name"></div>
                                                <div class="text-[10px] text-gray-400">ID: #<span x-text="prod.id"></span></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="py-3 px-4 text-gray-600 font-medium">
                                        <span class="inline-block px-2 py-0.5 rounded-lg bg-gray-100 text-gray-700 text-[11px]" x-text="prod.category_name"></span>
                                    </td>

                                    <!-- Mevcut Normal Fiyat -->
                                    <td class="py-3 px-4 text-right font-bold text-gray-800" x-text="formatCurrency(prod.price)"></td>

                                    <!-- Mevcut Kampanya Fiyatı -->
                                    <td class="py-3 px-4 text-right">
                                        <template x-if="prod.campaign_price">
                                            <span class="font-bold text-rose-600" x-text="formatCurrency(prod.campaign_price)"></span>
                                        </template>
                                        <template x-if="!prod.campaign_price">
                                            <span class="text-gray-300">—</span>
                                        </template>
                                    </td>

                                    <!-- YENİ Normal Fiyat (Simülasyon) -->
                                    <td class="py-3 px-4 text-right font-black bg-amber-50/40"
                                        :class="calcNew(prod).price !== prod.price ? 'text-amber-800 font-extrabold' : 'text-gray-500'"
                                        x-text="formatCurrency(calcNew(prod).price)"></td>

                                    <!-- YENİ Kampanya Fiyatı (Simülasyon) -->
                                    <td class="py-3 px-4 text-right font-black bg-amber-50/40">
                                        <template x-if="calcNew(prod).campaign_price">
                                            <span class="text-rose-600 font-extrabold" x-text="formatCurrency(calcNew(prod).campaign_price)"></span>
                                        </template>
                                        <template x-if="!calcNew(prod).campaign_price">
                                            <span class="text-gray-300">—</span>
                                        </template>
                                    </td>

                                    <!-- Fark / Değişim Rozeti -->
                                    <td class="py-3 px-4 text-center">
                                        <template x-if="calcNew(prod).delta !== 0">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black"
                                                  :class="calcNew(prod).delta > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                                <i :class="calcNew(prod).delta > 0 ? 'fas fa-arrow-up text-[9px]' : 'fas fa-arrow-down text-[9px]'"></i>
                                                <span x-text="(calcNew(prod).delta > 0 ? '+' : '') + formatCurrency(calcNew(prod).delta)"></span>
                                            </span>
                                        </template>
                                        <template x-if="calcNew(prod).delta === 0">
                                            <span class="text-gray-300 text-xs">Değişim Yok</span>
                                        </template>
                                    </td>

                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="filteredProducts.length === 0" class="p-8 text-center text-gray-400 text-xs">
                    Aranan kriterlere uygun çiçek ürünü bulunamadı.
                </div>

            </div>

        </form>

    </div>

    <!-- Alpine.js Hesaplama ve Simülasyon Mantığı -->
    <script>
        function bulkPricing(initialProducts) {
            return {
                products: initialProducts,
                actionType: 'markup',        // markup, discount, set_campaign, clear_campaign, set_fixed
                valueType: 'percent',        // percent, fixed
                amount: 10,
                targetField: 'price',        // price, campaign_price, both
                scope: 'all',                // all, category, selected
                selectedCategory: '',
                rounding: 'integer',         // none, integer, psychological_9, psychological_90, ten
                searchTerm: '',
                selectedIds: initialProducts.map(p => p.id),

                get filteredProducts() {
                    return this.products.filter(p => {
                        // Kategori filtresi
                        if (this.scope === 'category' && this.selectedCategory) {
                            if (p.category_id != this.selectedCategory) return false;
                        }
                        // Arama filtresi
                        if (this.searchTerm) {
                            const term = this.searchTerm.toLowerCase();
                            return p.name.toLowerCase().includes(term) || p.category_name.toLowerCase().includes(term);
                        }
                        return true;
                    });
                },

                get targetProductsCount() {
                    if (this.scope === 'all') {
                        return this.products.length;
                    } else if (this.scope === 'category') {
                        return this.selectedCategory ? this.products.filter(p => p.category_id == this.selectedCategory).length : 0;
                    } else {
                        return this.selectedIds.length;
                    }
                },

                get allSelected() {
                    const visible = this.filteredProducts.map(p => p.id);
                    return visible.length > 0 && visible.every(id => this.selectedIds.includes(id));
                },

                toggleSelectAll() {
                    const visible = this.filteredProducts.map(p => p.id);
                    if (this.allSelected) {
                        this.selectedIds = this.selectedIds.filter(id => !visible.includes(id));
                    } else {
                        this.selectedIds = Array.from(new Set([...this.selectedIds, ...visible]));
                    }
                },

                toggleProduct(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(i => i !== id);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                calcNew(prod) {
                    // Check if this product is affected
                    let isAffected = false;
                    if (this.scope === 'all') isAffected = true;
                    else if (this.scope === 'category' && this.selectedCategory == prod.category_id) isAffected = true;
                    else if (this.scope === 'selected' && this.selectedIds.includes(prod.id)) isAffected = true;

                    if (!isAffected) {
                        return {
                            price: prod.price,
                            campaign_price: prod.campaign_price,
                            delta: 0
                        };
                    }

                    let newPrice = prod.price;
                    let newCampaign = prod.campaign_price;
                    const amt = parseFloat(this.amount) || 0;
                    const isMarkup = (this.actionType === 'markup');

                    if (this.actionType === 'clear_campaign') {
                        newCampaign = null;
                    } else if (this.actionType === 'set_campaign') {
                        let camp = (this.valueType === 'percent') 
                            ? (prod.price * (1 - (amt / 100))) 
                            : (prod.price - amt);
                        camp = this.applyRounding(camp);
                        if (camp > 0 && camp < prod.price) {
                            newCampaign = camp;
                        }
                    } else if (this.actionType === 'set_fixed') {
                        const fixedPrice = this.applyRounding(amt);
                        if (this.targetField === 'price' || this.targetField === 'both') {
                            newPrice = fixedPrice;
                        }
                        if (this.targetField === 'campaign_price' || this.targetField === 'both') {
                            if (prod.campaign_price !== null) newCampaign = fixedPrice;
                        }
                    } else {
                        // markup or discount
                        if (this.targetField === 'price' || this.targetField === 'both') {
                            const diff = (this.valueType === 'percent') ? (prod.price * (amt / 100)) : amt;
                            newPrice = isMarkup ? (prod.price + diff) : Math.max(1, prod.price - diff);
                            newPrice = this.applyRounding(newPrice);
                        }
                        if (this.targetField === 'campaign_price' || this.targetField === 'both') {
                            if (prod.campaign_price !== null && prod.campaign_price > 0) {
                                const diff = (this.valueType === 'percent') ? (prod.campaign_price * (amt / 100)) : amt;
                                newCampaign = isMarkup ? (prod.campaign_price + diff) : Math.max(1, prod.campaign_price - diff);
                                newCampaign = this.applyRounding(newCampaign);
                            }
                        }
                    }

                    // Calculate effective delta
                    const oldEffective = prod.campaign_price || prod.price;
                    const newEffective = newCampaign || newPrice;
                    const delta = newEffective - oldEffective;

                    return {
                        price: newPrice,
                        campaign_price: newCampaign,
                        delta: delta
                    };
                },

                applyRounding(val) {
                    if (this.rounding === 'integer') {
                        return Math.round(val);
                    } else if (this.rounding === 'psychological_9') {
                        const rounded = Math.round(val);
                        if (rounded < 10) return 9;
                        return Math.floor(rounded / 10) * 10 + 9;
                    } else if (this.rounding === 'psychological_90') {
                        return Math.floor(val) + 0.90;
                    } else if (this.rounding === 'ten') {
                        return Math.round(val / 10) * 10;
                    } else {
                        return Math.round(val * 100) / 100;
                    }
                },

                formatCurrency(val) {
                    if (val === null || val === undefined || isNaN(val)) return '—';
                    return new Intl.NumberFormat('tr-TR', {
                        minimumFractionDigits: (this.rounding === 'none' || this.rounding === 'psychological_90') ? 2 : 0,
                        maximumFractionDigits: 2
                    }).format(val) + ' ₺';
                },

                getSummaryText() {
                    if (this.actionType === 'clear_campaign') {
                        return 'Tüm Kampanyalı İndirimleri Sıfırla';
                    }
                    const unit = this.valueType === 'percent' ? '%' + this.amount : this.amount + ' ₺';
                    if (this.actionType === 'markup') return unit + ' Fiyat Artışı (Zam)';
                    if (this.actionType === 'discount') return unit + ' İndirim';
                    if (this.actionType === 'set_campaign') return unit + ' İndirimli Kampanya Oluştur';
                    if (this.actionType === 'set_fixed') return this.amount + ' ₺ Sabit Fiyat';
                    return 'Fiyat Güncellemesi';
                },

                submitForm(e) {
                    const count = this.targetProductsCount;
                    if (count === 0) {
                        alert('Lütfen işlem yapılacak en az bir ürün veya kategori seçiniz.');
                        return;
                    }

                    const summary = this.getSummaryText();
                    const confirmed = confirm(
                        `${count} adet çiçek ürününe "${summary}" işlemi uygulanacaktır.\n\nFiyatların güncellenmesini onaylıyor musunuz?`
                    );

                    if (confirmed) {
                        e.target.submit();
                    }
                }
            };
        }
    </script>
</x-app-layout>
