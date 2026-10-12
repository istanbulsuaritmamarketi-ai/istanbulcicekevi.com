<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-gray-900 leading-tight flex items-center gap-2">
                    <span class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-base shadow-xs">
                        <i class="fas fa-spa"></i>
                    </span>
                    <span>Günlük Çiçek Masası &amp; Mezat Stokları</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Sabah mezatından aldığınız taze çiçek adetlerini ve fiyatlarını buradan tek tıkla güncelleyin. Müşteriler atölyede bu stoklara göre sipariş verir.
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('flower.studio') }}" target="_blank"
                   class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-black rounded-2xl transition-all border border-rose-200 flex items-center gap-1.5 shadow-2xs">
                    <i class="fas fa-external-link-alt text-[11px]"></i>
                    <span>Canlı Atölyeyi Gör</span>
                </a>

                <button type="button" @click="$dispatch('open-add-material-modal')"
                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-2xl transition-all shadow-md flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-plus"></i>
                    <span>Yeni Malzeme Ekle</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        activeCategory: '{{ $category }}',
        filterCategory(cat) {
            window.location.href = '{{ route('admin.flower_studio.daily_stock') }}?category=' + cat;
        },
        updateStock(materialId, newStock) {
            if (newStock < 0) newStock = 0;
            const input = document.getElementById('stock-input-' + materialId);
            if (input) input.value = newStock;

            fetch('/admin/flower-studio/materials/' + materialId + '/update-stock', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stock: newStock })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                    const badge = document.getElementById('stock-badge-' + materialId);
                    if (badge) {
                        if (newStock == 0) {
                            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700';
                            badge.innerText = 'TÜKENDİ';
                        } else {
                            badge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700';
                            badge.innerText = newStock + ' Adet';
                        }
                    }
                }
            })
            .catch(err => {
                this.showToast('Hata: ' + err.message, 'error');
            });
        },
        updatePrice(materialId) {
            const input = document.getElementById('price-input-' + materialId);
            if (!input) return;
            const newPrice = parseFloat(input.value);
            if (isNaN(newPrice) || newPrice < 0) return;

            fetch('/admin/flower-studio/materials/' + materialId + '/update-price', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ unit_price: newPrice })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                }
            })
            .catch(err => {
                this.showToast('Hata: ' + err.message, 'error');
            });
        },
        toggleActive(materialId) {
            fetch('/admin/flower-studio/materials/' + materialId + '/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.showToast(data.message, 'success');
                }
            })
            .catch(err => {
                this.showToast('Hata: ' + err.message, 'error');
            });
        },
        toastMsg: '',
        toastType: 'success',
        toastVisible: false,
        showToast(msg, type = 'success') {
            this.toastMsg = msg;
            this.toastType = type;
            this.toastVisible = true;
            setTimeout(() => { this.toastVisible = false; }, 3000);
        }
    }">

        <!-- Toast Bildirimi -->
        <div x-show="toastVisible" x-cloak 
             class="fixed bottom-6 right-6 z-50 px-5 py-3 rounded-2xl shadow-xl font-bold text-xs flex items-center gap-2 transition-all transform"
             :class="toastType === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'">
            <i :class="toastType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
            <span x-text="toastMsg"></span>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Kategori Filtre Sekmeleri -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button type="button" @click="filterCategory('all')"
                        :class="activeCategory === 'all' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <i class="fas fa-th-large"></i>
                    <span>Tüm Malzemeler ({{ $counts['all'] }})</span>
                </button>

                <button type="button" @click="filterCategory('flower')"
                        :class="activeCategory === 'flower' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <span>🌸 Taze Çiçekler ({{ $counts['flower'] }})</span>
                </button>

                <button type="button" @click="filterCategory('vase')"
                        :class="activeCategory === 'vase' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <span>🏺 Vazo &amp; Kaplar ({{ $counts['vase'] }})</span>
                </button>

                <button type="button" @click="filterCategory('filler')"
                        :class="activeCategory === 'filler' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <span>🌿 Dolgu &amp; Yeşillik ({{ $counts['filler'] }})</span>
                </button>

                <button type="button" @click="filterCategory('wrapping')"
                        :class="activeCategory === 'wrapping' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <span>📜 Buket Kağıtları ({{ $counts['wrapping'] }})</span>
                </button>

                <button type="button" @click="filterCategory('accessory')"
                        :class="activeCategory === 'accessory' ? 'bg-rose-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-rose-50 border border-gray-200'"
                        class="px-4 py-2.5 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <span>🎀 Kurdele &amp; Aksesuar ({{ $counts['accessory'] }})</span>
                </button>
            </div>

            <!-- Malzeme Kartları Izgarası (Mobil ve Masaüstü Optimize) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($materials as $item)
                    <div class="bg-white rounded-3xl border border-gray-200/80 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between space-y-4">
                        
                        <!-- Üst Başlık & Kategori & Durum -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl border flex items-center justify-center text-lg flex-shrink-0 relative overflow-hidden"
                                     style="background-color: {{ $item->color_hex ? $item->color_hex . '15' : '#fff1f2' }}; border-color: {{ $item->color_hex ? $item->color_hex . '40' : '#fecdd3' }}">
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="max-w-full max-h-full object-contain p-1">
                                    @else
                                        @if($item->category === 'flower')
                                            <i class="fas fa-spa" style="color: {{ $item->color_hex ?: '#e11d48' }}"></i>
                                        @elseif($item->category === 'vase')
                                            <i class="fas fa-wine-glass-alt text-slate-600"></i>
                                        @elseif($item->category === 'filler')
                                            <i class="fas fa-leaf text-emerald-600"></i>
                                        @elseif($item->category === 'wrapping')
                                            <i class="fas fa-scroll text-amber-700"></i>
                                        @else
                                            <i class="fas fa-ribbon text-rose-600"></i>
                                        @endif
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xs font-black text-gray-900 leading-tight">{{ $item->name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400">
                                            @if($item->category === 'flower') ÇİÇEK
                                            @elseif($item->category === 'vase') VAZO / KAP ({{ $item->stem_capacity }} Dal)
                                            @elseif($item->category === 'filler') DOLGU &amp; YEŞİLLİK
                                            @elseif($item->category === 'wrapping') AMBALAJ KAĞIDI
                                            @else AKSESUAR
                                            @endif
                                        </span>
                                        <span class="text-gray-300">•</span>
                                        <span id="stock-badge-{{ $item->id }}" 
                                              class="px-2 py-0.5 rounded-full text-[9px] font-black {{ $item->daily_stock > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $item->daily_stock > 0 ? $item->daily_stock . ' Adet' : 'TÜKENDİ' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Aktif / Pasif Toggle -->
                            <label class="relative inline-flex items-center cursor-pointer" title="Satışta Açık/Kapalı">
                                <input type="checkbox" {{ $item->is_active ? 'checked' : '' }} 
                                       @change="toggleActive({{ $item->id }})" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>

                        <!-- Orta Alan: Hızlı Stok Değiştirme Butonları (Amcanız İçin Büyük Dokunmatik Butonlar) -->
                        <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 space-y-2">
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-500">
                                <span>Günlük Eldeki Stok:</span>
                                <button type="button" @click="updateStock({{ $item->id }}, 0)" 
                                        class="text-[10px] text-rose-600 hover:text-rose-800 font-black cursor-pointer hover:underline">
                                    Tükendi Yap (0)
                                </button>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="updateStock({{ $item->id }}, parseInt(document.getElementById('stock-input-{{ $item->id }}').value || 0) - 5)"
                                        class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs text-gray-700 flex items-center justify-center shadow-2xs cursor-pointer">
                                    -5
                                </button>
                                <button type="button" @click="updateStock({{ $item->id }}, parseInt(document.getElementById('stock-input-{{ $item->id }}').value || 0) - 1)"
                                        class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs text-gray-700 flex items-center justify-center shadow-2xs cursor-pointer">
                                    -1
                                </button>

                                <input type="number" id="stock-input-{{ $item->id }}" value="{{ $item->daily_stock }}" min="0"
                                       @change="updateStock({{ $item->id }}, parseInt($event.target.value || 0))"
                                       class="w-full text-center font-black text-base py-1.5 px-2 rounded-xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500 bg-white">

                                <button type="button" @click="updateStock({{ $item->id }}, parseInt(document.getElementById('stock-input-{{ $item->id }}').value || 0) + 1)"
                                        class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs text-gray-700 flex items-center justify-center shadow-2xs cursor-pointer">
                                    +1
                                </button>
                                <button type="button" @click="updateStock({{ $item->id }}, parseInt(document.getElementById('stock-input-{{ $item->id }}').value || 0) + 5)"
                                        class="w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 font-black text-xs text-gray-700 flex items-center justify-center shadow-2xs cursor-pointer">
                                    +5
                                </button>
                            </div>
                        </div>

                        <!-- Alt Alan: Birim Satış Fiyatı Düzenleme & Silme & Düzenle Modalı -->
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-100">
                            <div class="flex items-center gap-1.5 flex-1">
                                <span class="text-[10px] font-black text-gray-500 whitespace-nowrap">Birim:</span>
                                <div class="relative flex-1">
                                    <input type="number" id="price-input-{{ $item->id }}" value="{{ $item->unit_price }}" step="0.50" min="0"
                                           class="w-full text-xs font-black text-rose-600 py-1 px-2 pr-6 rounded-xl border border-gray-200 bg-white focus:border-rose-500 focus:ring-rose-500">
                                    <span class="absolute right-2 top-1 text-[10px] font-bold text-gray-400">TL</span>
                                </div>
                                <button type="button" @click="updatePrice({{ $item->id }})" title="Fiyatı Kaydet"
                                        class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-600 flex items-center justify-center text-xs transition-colors cursor-pointer">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>

                            <div class="flex items-center gap-1">
                                <button type="button" @click="$dispatch('open-edit-material-modal', {{ $item->toJson() }})"
                                        class="w-8 h-8 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 flex items-center justify-center text-xs transition-colors cursor-pointer" title="Malzemeyi Düzenle">
                                    <i class="fas fa-pen"></i>
                                </button>

                                <form action="{{ route('admin.flower_studio.materials.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Bu malzemeyi silmek istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-xl bg-gray-50 hover:bg-rose-50 hover:text-rose-600 text-gray-400 flex items-center justify-center text-xs transition-colors cursor-pointer" title="Sil">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full p-12 bg-white rounded-3xl border border-dashed border-gray-200 text-center">
                        <i class="fas fa-spa text-4xl text-gray-300 mb-3 block"></i>
                        <h4 class="text-sm font-black text-gray-700">Bu kategoride henüz malzeme bulunmuyor</h4>
                        <p class="text-xs text-gray-400 mt-1">Yukarıdaki "Yeni Malzeme Ekle" butonuna basarak ilk çiçeğinizi veya vazonuzu ekleyebilirsiniz.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <!-- ➕ YENİ MALZEME EKLEME MODALI -->
        <div x-data="{ 
                 open: false,
                 cat: 'flower',
                 name: '',
                 unitPrice: '75.00',
                 stock: 50,
                 stemCapacity: 25,
                 color: '#e11d48',
                 applyPreset(p) {
                     this.cat = p.cat;
                     this.name = p.name;
                     this.unitPrice = p.price;
                     this.stock = p.stock || 50;
                     this.stemCapacity = p.capacity || 25;
                     this.color = p.color || '#e11d48';
                 }
             }" 
             @open-add-material-modal.window="open = true" 
             x-show="open" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-xs transition-opacity" @click="open = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100 p-6 space-y-4">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-plus text-rose-600"></i> Yeni Çiçek / Malzeme Ekle
                        </h3>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xs p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- ⚡ HIZLI ŞABLON SEÇİCİ (1 TIKLA OTOMATİK DOLDURUR) -->
                    <div class="bg-rose-50/70 p-3 rounded-2xl border border-rose-200/80">
                        <span class="text-[11px] font-black text-rose-950 uppercase tracking-wider block mb-1.5">
                            ⚡ Hazır Şablonlar (Tek Tıkla Seç):
                        </span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Kır Papatyası', price: '45.00', stock: 60, color: '#fef08a'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🌼 Papatya</button>
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Kırmızı Ekvador Gülü', price: '75.00', stock: 80, color: '#e11d48'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🌹 Kırmızı Gül</button>
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Beyaz Kar Gülü', price: '65.00', stock: 50, color: '#ffffff'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🤍 Beyaz Gül</button>
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Sarı Hollanda Lalesi', price: '55.00', stock: 40, color: '#eab308'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🌷 Sarı Lale</button>
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Pembe Şakayık (Peony)', price: '135.00', stock: 20, color: '#fb7185'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🌺 Şakayık</button>
                            <button type="button" @click="applyPreset({cat: 'flower', name: 'Casablanca Lilyum', price: '110.00', stock: 25, color: '#f8fafc'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🪷 Lilyum</button>
                            <button type="button" @click="applyPreset({cat: 'filler', name: 'Cipsofilya (Bebek Nefesi)', price: '50.00', stock: 30, color: '#ffffff'})" class="text-[11px] font-bold bg-white hover:bg-emerald-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🌿 Cipso</button>
                            <button type="button" @click="applyPreset({cat: 'filler', name: 'Okaliptüs Dalı', price: '35.00', stock: 40, color: '#4ade80'})" class="text-[11px] font-bold bg-white hover:bg-emerald-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🍃 Okaliptüs</button>
                            <button type="button" @click="applyPreset({cat: 'vase', name: 'Şeffaf Silindir Cam Vazo', price: '220.00', stock: 25, capacity: 25, color: '#e2e8f0'})" class="text-[11px] font-bold bg-white hover:bg-sky-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🏺 Cam Vazo</button>
                            <button type="button" @click="applyPreset({cat: 'vase', name: 'Kadife Silindir Kutu (Siyah / Gold)', price: '350.00', stock: 18, capacity: 20, color: '#18181b'})" class="text-[11px] font-bold bg-white hover:bg-amber-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🎁 Kadife Kutu</button>
                            <button type="button" @click="applyPreset({cat: 'accessory', name: 'Bordo Saten Kurdele', price: '25.00', stock: 100, color: '#991b1b'})" class="text-[11px] font-bold bg-white hover:bg-rose-100 text-stone-800 border border-stone-200 px-2.5 py-1 rounded-xl shadow-2xs cursor-pointer">🎀 Bordo Kurdele</button>
                        </div>
                    </div>

                    <form action="{{ route('admin.flower_studio.materials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Malzeme Türü <span class="text-rose-600">*</span></label>
                            <select name="category" x-model="cat" required class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 bg-white focus:border-rose-500 focus:ring-rose-500">
                                <option value="flower">🌸 Taze Çiçek (Gül, Lilyum, Lale, Papatya vb.)</option>
                                <option value="vase">🏺 Vazo / Kap (Cam Vazo, Kutu vb.)</option>
                                <option value="filler">🌿 Dolgu &amp; Yeşillik (Cipso, Okaliptus vb.)</option>
                                <option value="wrapping">📜 Buket Ambalaj Kağıdı</option>
                                <option value="accessory">🎀 Kurdele &amp; Aksesuar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Malzeme Adı <span class="text-rose-600">*</span></label>
                            <input type="text" name="name" x-model="name" required placeholder="Örn: Kır Papatyası veya Kırmızı Gül"
                                   class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Birim Satış Fiyatı (TL) <span class="text-rose-600">*</span></label>
                                <input type="number" step="0.50" name="unit_price" x-model="unitPrice" required placeholder="75.00"
                                       class="w-full text-xs font-black text-rose-600 py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Başlangıç Stok Adedi <span class="text-rose-600">*</span></label>
                                <input type="number" name="daily_stock" x-model="stock" required value="50" min="0"
                                       class="w-full text-xs font-black py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Vazo Dal Kapasitesi</label>
                                <input type="number" name="stem_capacity" x-model="stemCapacity" min="1" max="200" placeholder="Örn: 25 dal"
                                       class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                                <span class="text-[10px] text-gray-400">Yalnızca vazolar için geçerlidir.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Renk Vurgusu</label>
                                <input type="color" name="color_hex" x-model="color"
                                       class="w-full h-10 p-1 rounded-xl border border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Şeffaf Görsel (PNG/WebP)</label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full text-[11px] file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-rose-50 file:text-rose-700 file:font-bold border rounded-2xl p-1">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama (Opsiyonel)</label>
                            <textarea name="description" rows="2" placeholder="Malzeme hakkında kısa bilgi..."
                                      class="w-full text-xs py-2 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="open = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                İptal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                Kaydet &amp; Masaya Ekle
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- ✏️ MALZEME DÜZENLEME MODALI -->
        <div x-data="{ 
                 open: false, 
                 mat: {}, 
                 actionUrl: '' 
             }" 
             @open-edit-material-modal.window="
                 mat = $event.detail;
                 actionUrl = '/admin/flower-studio/materials/' + mat.id;
                 open = true;
             " 
             x-show="open" x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-xs transition-opacity" @click="open = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100 p-6 space-y-4">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <h3 class="text-sm font-black text-gray-900 flex items-center gap-2">
                            <i class="fas fa-pen text-blue-600"></i> Malzemeyi Düzenle
                        </h3>
                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 text-xs p-1">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Malzeme Türü <span class="text-rose-600">*</span></label>
                            <select name="category" x-model="mat.category" required class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 bg-white focus:border-rose-500 focus:ring-rose-500">
                                <option value="flower">🌸 Taze Çiçek (Gül, Lilyum, Lale vb.)</option>
                                <option value="vase">🏺 Vazo / Kap (Cam Vazo, Kutu vb.)</option>
                                <option value="filler">🌿 Dolgu &amp; Yeşillik (Cipso, Okaliptus vb.)</option>
                                <option value="wrapping">📜 Buket Ambalaj Kağıdı</option>
                                <option value="accessory">🎀 Kurdele &amp; Aksesuar</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Malzeme Adı <span class="text-rose-600">*</span></label>
                            <input type="text" name="name" x-model="mat.name" required
                                   class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Birim Satış Fiyatı (TL) <span class="text-rose-600">*</span></label>
                                <input type="number" step="0.50" name="unit_price" x-model="mat.unit_price" required
                                       class="w-full text-xs font-black text-rose-600 py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Günlük Mezat Stoğu <span class="text-rose-600">*</span></label>
                                <input type="number" name="daily_stock" x-model="mat.daily_stock" required min="0"
                                       class="w-full text-xs font-black py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Vazo Dal Kapasitesi</label>
                                <input type="number" name="stem_capacity" x-model="mat.stem_capacity" min="1" max="200"
                                       class="w-full text-xs font-bold py-2.5 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500">
                                <span class="text-[10px] text-gray-400">Yalnızca vazolar için geçerlidir.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Renk Vurgusu</label>
                                <input type="color" name="color_hex" x-model="mat.color_hex"
                                       class="w-full h-10 p-1 rounded-xl border border-gray-300 cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Yeni Görsel Yükle (PNG/WebP)</label>
                            <input type="file" name="image" accept="image/*"
                                   class="w-full text-[11px] file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:bg-rose-50 file:text-rose-700 file:font-bold border rounded-2xl p-1">
                            <template x-if="mat.image_path">
                                <p class="text-[10px] text-emerald-600 font-semibold mt-1">✓ Mevcut bir görsel yüklü. Değiştirmek istemiyorsanız boş bırakın.</p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Açıklama (Opsiyonel)</label>
                            <textarea name="description" x-model="mat.description" rows="2"
                                      class="w-full text-xs py-2 px-3 rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <button type="button" @click="open = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                İptal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-md transition-all">
                                Değişiklikleri Kaydet
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
