<x-frontend-layout>
    <div class="bg-gray-50 py-10 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-shopping-cart text-rose-600"></i> Alışveriş Sepetiniz
                </h1>
                @if(!empty($cart))
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Sepetinizdeki tüm ürünleri silmek istediğinize emin misiniz?');">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-bold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5">
                            <i class="fas fa-trash-alt"></i> Sepeti Temizle
                        </button>
                    </form>
                @endif
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-sm font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(empty($cart))
                <div class="bg-white p-12 text-center rounded-3xl shadow-sm border border-gray-100 max-w-lg mx-auto my-8">
                    <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                        <i class="fas fa-shopping-basket"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Sepetiniz şu an boş</h2>
                    <p class="text-gray-500 text-sm mb-6">İstanbul'un en taze çiçek ve özel tasarım buketlerini inceleyerek hemen sepetinize ekleyebilirsiniz.</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-rose-600 text-white font-bold text-sm py-3 px-6 rounded-xl hover:bg-rose-700 transition-colors shadow">
                        <i class="fas fa-spa"></i> Çiçekleri İncele
                    </a>
                </div>
            @else
                <!-- ⚡ SIFIR RİSK BİLGİLENDİRME BANDI -->
                <div class="mb-6 p-4 sm:p-5 bg-gradient-to-r from-emerald-50 via-teal-50 to-teal-50 border-2 border-emerald-300 rounded-2xl sm:rounded-3xl shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-emerald-500/20">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-emerald-950 flex items-center gap-2">
                            <span>⚡ Kayıt Olmadan &amp; Kart Bilgisi Girmeden Sipariş</span>
                            <span class="bg-emerald-600 text-white text-[9px] px-2 py-0.5 rounded-full font-black uppercase">SIFIR RİSK</span>
                        </h3>
                        <p class="text-[11px] sm:text-xs text-emerald-800/90 mt-0.5 font-medium leading-relaxed">
                            Kayıt olmadan 10 saniyede siparişinizi tamamlayabilirsiniz! Ödemenizi kapıda ürün tesliminde nakit veya kredi kartı POS cihazı ile güvenle yapın.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Sol: Ürün Listesi Tablosu -->
                    <div class="lg:col-span-8 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 hidden sm:grid grid-cols-12 text-xs font-extrabold text-gray-400 uppercase tracking-wider">
                            <div class="col-span-6">Ürün</div>
                            <div class="col-span-3 text-center">Adet</div>
                            <div class="col-span-3 text-right">Toplam</div>
                        </div>

                        <ul class="divide-y divide-gray-100">
                            @foreach($cart as $id => $item)
                                @php 
                                    $itemTotal = $item['price'] * $item['quantity'];
                                @endphp
                                <li class="p-6 flex flex-col sm:grid sm:grid-cols-12 items-center gap-4 hover:bg-gray-50/50 transition-colors">
                                    
                                    <!-- Ürün Bilgisi -->
                                    <div class="col-span-6 flex items-center gap-4 w-full">
                                        <div class="w-20 h-20 bg-rose-50/50 rounded-2xl border border-rose-100 flex-shrink-0 overflow-hidden flex items-center justify-center p-1">
                                            @if(!empty($item['is_custom']))
                                                <div class="w-full h-full flex flex-col items-center justify-center text-rose-500 bg-gradient-to-br from-rose-100/60 to-pink-100/60 rounded-xl">
                                                    <span class="text-2xl">🌸</span>
                                                    <span class="text-[9px] font-black uppercase text-rose-700 mt-0.5">Özel</span>
                                                </div>
                                            @elseif(isset($item['image']) && $item['image'])
                                                <img src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-contain">
                                            @else
                                                <i class="fas fa-spa text-rose-300 text-2xl"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            @if(!empty($item['is_custom']))
                                                <div class="inline-block bg-rose-100 text-rose-700 text-[10px] font-black px-2 py-0.5 rounded-full mb-1">
                                                    ✨ 2.5D KİŞİYE ÖZEL TASARIM
                                                </div>
                                            @endif
                                            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-snug">
                                                @if(isset($item['slug']))
                                                    <a href="{{ route('product.show', $item['slug']) }}" class="hover:text-rose-600">{{ $item['name'] }}</a>
                                                @else
                                                    {{ $item['name'] }}
                                                @endif
                                            </h3>
                                            @if(!empty($item['is_custom']) && !empty($item['custom_design_data']))
                                                <div class="text-[11px] text-gray-500 mt-1 space-y-0.5">
                                                    <div><strong>Kap:</strong> {{ $item['custom_design_data']['container']['name'] ?? 'Seçildi' }}</div>
                                                    <div><strong>Toplam Dal:</strong> {{ $item['custom_design_data']['total_stems'] ?? 0 }} dal çiçek &amp; yeşillik</div>
                                                    @if(!empty($item['custom_design_data']['card_note']))
                                                        <div class="italic text-amber-800"><strong>💌 Not:</strong> "{{ \Illuminate\Support\Str::limit($item['custom_design_data']['card_note'], 35) }}"</div>
                                                    @endif
                                                </div>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">Birim Fiyat: <strong class="text-gray-700">{{ number_format($item['price'], 0, ',', '.') }} TL</strong></p>
                                        </div>
                                    </div>

                                    <!-- Adet Arttırma / Azaltma Butonları -->
                                    <div class="col-span-3 flex items-center justify-center gap-2 w-full sm:w-auto">
                                        <div class="flex items-center border border-gray-200 rounded-xl bg-gray-50 overflow-hidden shadow-inner">
                                            <!-- Azalt (-) -->
                                            <form action="{{ route('cart.update', $id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 hover:text-red-600 transition-colors">
                                                    <i class="fas fa-minus text-[10px]"></i>
                                                </button>
                                            </form>

                                            <!-- Sayı -->
                                            <span class="w-10 text-center font-extrabold text-sm text-gray-900 bg-white py-1">
                                                {{ $item['quantity'] }}
                                            </span>

                                            <!-- Arttır (+) -->
                                            <form action="{{ route('cart.update', $id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 hover:text-green-600 transition-colors">
                                                    <i class="fas fa-plus text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Tek Tıkla Sil Butonu (Çöp Kutusu) -->
                                        <form action="{{ route('cart.remove', $id) }}" method="POST">
                                            @csrf
                                            <button type="submit" title="Ürünü Sepetten Kaldır" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 flex items-center justify-center transition-colors">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Satır Toplamı -->
                                    <div class="col-span-3 text-right w-full sm:w-auto">
                                        <div class="text-base font-black text-rose-600">
                                            {{ number_format($itemTotal, 0, ',', '.') }} TL
                                        </div>
                                    </div>

                                </li>
                            @endforeach
                        </ul>

                        <!-- Alt Bilgi / Güven İpuçları -->
                        <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1.5"><i class="fas fa-truck text-rose-600"></i> İstanbul Geneli VIP Kurye Teslimat</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-spa text-emerald-600"></i> %100 Günlük Taze Çiçek Garantisi</span>
                            <span class="flex items-center gap-1.5"><i class="fas fa-hand-holding-usd text-rose-600"></i> Kapıda Güvenli Ödeme (Nakit/Kart)</span>
                        </div>
                    </div>

                    <!-- Sağ: Sipariş Özeti & Kupon & Ödeme Butonu -->
                    <div class="lg:col-span-4 bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 space-y-6">
                        <h3 class="font-extrabold text-gray-900 text-lg border-b border-gray-100 pb-4">
                            Sipariş Özeti
                        </h3>

                        <!-- İndirim Kuponu Kutusu -->
                        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-3">
                            <label class="block text-xs font-bold text-gray-700">İndirim Kuponu / Promosyon Kodu</label>
                            
                            @if(isset($couponData) && $couponData)
                                <div class="flex items-center justify-between bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
                                    <div>
                                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-800 font-mono">
                                            <i class="fas fa-check-circle text-emerald-600"></i> {{ $couponData['code'] }}
                                        </div>
                                        <div class="text-[11px] text-emerald-600 font-bold">-{{ number_format($discount, 0, ',', '.') }} TL İndirim Uygulandı</div>
                                    </div>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold p-1">
                                            <i class="fas fa-times"></i> Kaldır
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="code" placeholder="KUPON KODU" required
                                        class="w-full text-xs font-mono uppercase font-bold rounded-xl border-gray-200 bg-white focus:border-rose-500 focus:ring-rose-500">
                                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white font-bold text-xs rounded-xl shadow-sm transition-colors whitespace-nowrap">
                                        Uygula
                                    </button>
                                </form>
                            @endif

                            @if(session('error'))
                                <p class="text-xs text-red-600 font-bold">{{ session('error') }}</p>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Ara Toplam</span>
                                <span class="font-bold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }} TL</span>
                            </div>

                            @if($discount > 0)
                                <div class="flex justify-between text-emerald-600 font-bold">
                                    <span>Kupon İndirimi</span>
                                    <span>-{{ number_format($discount, 0, ',', '.') }} TL</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-gray-600">
                                <span>VIP Kurye Teslimat</span>
                                <span class="font-bold text-emerald-600">ÜCRETSİZ</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>KDV</span>
                                <span class="text-xs text-gray-400">Dahil</span>
                            </div>

                            <div class="border-t border-gray-100 pt-4 flex justify-between items-end">
                                <div>
                                    <span class="text-xs text-gray-500 font-bold block">Ödenecek Tutar</span>
                                    <span class="text-2xl font-extrabold text-rose-600">{{ number_format($total, 0, ',', '.') }} TL</span>
                                </div>
                                <span class="text-[11px] bg-emerald-50 text-emerald-700 font-bold px-2.5 py-1 rounded-full">Kapıda Ödeme</span>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <a href="{{ route('checkout') }}" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-base py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-200 text-center block">
                                <i class="fas fa-arrow-right mr-2"></i> Adres & Sipariş Adımına Geç
                            </a>

                            @php
                                $cleanWa = preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905555555555');
                            @endphp
                            <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Merhaba, sepetimdeki ' . number_format($total, 0, ',', '.') . ' TL tutarındaki ürünler hakkında sipariş öncesi bilgi almak istiyorum.') }}"
                                target="_blank"
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold text-xs sm:text-sm py-3 px-4 rounded-xl shadow transition-colors flex items-center justify-center gap-2">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span>WhatsApp'tan Sipariş Öncesi Danış</span>
                            </a>
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-frontend-layout>
