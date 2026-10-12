<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sipariş Detayı: ') }} <span class="text-rose-600 font-black">{{ $order->order_number }}</span>
            </h2>
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '90' . substr($cleanPhone, 1);
                } elseif (!str_starts_with($cleanPhone, '90')) {
                    $cleanPhone = '90' . $cleanPhone;
                }
                
                $orderItemsText = "";
                foreach($order->items as $item) {
                    $itemTitle = $item->custom_title ?: ($item->product->name ?? 'Çiçek Tasarımı');
                    $orderItemsText .= "- " . $itemTitle . " (" . $item->quantity . " adet) - " . number_format($item->unit_price * $item->quantity, 2) . " TL\n";
                }

                $whatsappMsg = "🌸 *İSTANBUL ÇİÇEK EVİ - SİPARİŞ ONAYI*\n\n"
                             . "Sayın *" . ($order->sender_name ?: $order->customer_name) . "*,\n\n"
                             . "Sipariş Numaranız: *#" . $order->order_number . "*\n"
                             . "Durum: *" . $order->status . "*\n\n"
                             . "*Sipariş Edilen Çiçekler:*\n" . $orderItemsText . "\n"
                             . ($order->card_note ? "💌 Kart Notu: *\"" . $order->card_note . "\"*\n\n" : "")
                             . "🕒 Teslimat Zamanı: *" . ($order->delivery_date ?? 'Bugün') . " (" . ($order->delivery_time_slot ?? 'Standart') . ")*\n"
                             . "👤 Alıcı: *" . ($order->recipient_name ?: $order->customer_name) . "*\n"
                             . "📍 Teslimat Adresi: " . ($order->region->name ?? '') . " / " . $order->address . "\n\n"
                             . "💰 Toplam Tutar: *" . number_format($order->total_amount, 2) . " TL*\n\n"
                             . "💐 *Çiçeğiniz usta ellerde taze olarak özenle hazırlanıp VIP kuryemiz ile adrese teslim edilecektir.*\n\n"
                             . "Bizi tercih ettiğiniz için teşekkür ederiz! 🌸\n"
                             . "📞 İletişim: " . ($siteSettings['phone'] ?? '0212 444 88 99') . "\n"
                             . "🌐 https://istanbulcicekevi.com";
                
                $whatsappUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($whatsappMsg);
            @endphp
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ $whatsappUrl }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fab fa-whatsapp text-sm"></i> WhatsApp Sipariş Onayı Gönder
                </a>
                <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-4 py-2 rounded-xl text-xs flex items-center gap-1.5 shadow-sm transition-colors">
                    <i class="fas fa-print"></i> Makbuz (PDF)
                </a>
                <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900 border px-4 py-2 rounded-xl text-xs font-bold bg-white">
                    Geri Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Sipariş Durumu & Müşteri Bilgisi -->
            <div class="md:col-span-1 space-y-6">
                <!-- Durum Güncelleme -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Sipariş Durumu</h3>
                        @if(session('success'))
                            <div class="text-green-600 text-sm mb-4">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-4">
                                <select name="status" class="w-full rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 text-sm font-semibold">
                                    <option value="Sipariş alındı" {{ $order->status == 'Sipariş alındı' ? 'selected' : '' }}>Sipariş alındı</option>
                                    <option value="Onay bekliyor" {{ $order->status == 'Onay bekliyor' ? 'selected' : '' }}>Onay bekliyor</option>
                                    <option value="Onaylandı" {{ $order->status == 'Onaylandı' ? 'selected' : '' }}>Onaylandı</option>
                                    <option value="Randevu planlandı" {{ $order->status == 'Randevu planlandı' ? 'selected' : '' }}>Randevu planlandı</option>
                                    <option value="Hazırlanıyor" {{ $order->status == 'Hazırlanıyor' ? 'selected' : '' }}>Hazırlanıyor</option>
                                    <option value="Yolda" {{ $order->status == 'Yolda' ? 'selected' : '' }}>Yolda</option>
                                    <option value="Tamamlandı" {{ $order->status == 'Tamamlandı' ? 'selected' : '' }}>Tamamlandı</option>
                                    <option value="İptal edildi" {{ $order->status == 'İptal edildi' ? 'selected' : '' }}>İptal edildi</option>
                                    <option value="Ek bilgi gerekli" {{ $order->status == 'Ek bilgi gerekli' ? 'selected' : '' }}>Ek bilgi gerekli</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-rose-600 text-white py-2 rounded-xl font-bold hover:bg-rose-700 transition-colors shadow-sm">Durumu Güncelle</button>
                        </form>
                    </div>
                </div>

                <!-- Müşteri & Çiçek Teslimat Bilgileri -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2 flex items-center justify-between">
                            <span>🌸 Teslimat &amp; Çiçek Detayları</span>
                            @if($order->is_anonymous)
                                <span class="bg-purple-100 text-purple-800 text-[10px] font-black px-2 py-0.5 rounded-full">GİZLİ GÖNDERİM</span>
                            @endif
                        </h3>
                        <div class="space-y-3 text-sm">
                            @if($order->delivery_date || $order->delivery_time_slot)
                                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl">
                                    <span class="text-rose-900 font-bold block text-xs uppercase">🕒 İstenen Teslimat Zamanı:</span>
                                    <span class="text-rose-950 font-black text-sm">{{ $order->delivery_date ?? 'Aynı Gün' }} — {{ $order->delivery_time_slot ?? 'Standart' }}</span>
                                </div>
                            @endif

                            <div class="p-3 bg-gray-50 border rounded-xl space-y-1">
                                <span class="text-gray-500 font-bold block text-xs uppercase">💐 Alıcı Bilgileri (Çiçeği Alacak Kişi):</span>
                                <p><span class="font-bold text-gray-900">{{ $order->recipient_name ?: $order->customer_name }}</span></p>
                                <p><a href="tel:{{ $order->recipient_phone ?: $order->customer_phone }}" class="text-rose-600 font-semibold hover:underline">{{ $order->recipient_phone ?: $order->customer_phone }}</a></p>
                                <p class="text-xs text-gray-600 mt-1"><strong>Adres:</strong> {{ $order->region->name ?? '' }} / {{ $order->address }}</p>
                            </div>

                            @if($order->sender_name || $order->sender_phone)
                                <div class="p-3 bg-slate-50 border rounded-xl space-y-1">
                                    <span class="text-gray-500 font-bold block text-xs uppercase">👤 Gönderici Bilgileri (Siparişi Veren):</span>
                                    <p><span class="font-bold text-gray-900">{{ $order->sender_name ?: $order->customer_name }}</span></p>
                                    <p><a href="tel:{{ $order->sender_phone ?: $order->customer_phone }}" class="text-rose-600 font-semibold hover:underline">{{ $order->sender_phone ?: $order->customer_phone }}</a></p>
                                </div>
                            @endif

                            @if($order->card_note)
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                                    <span class="text-amber-900 font-bold block text-xs uppercase mb-1">💌 Çiçek Kart Mesajı:</span>
                                    <p class="text-amber-950 italic font-serif text-sm">"{{ $order->card_note }}"</p>
                                    @if($order->card_sender_title)
                                        <p class="text-amber-800 text-xs font-bold text-right mt-1">— {{ $order->card_sender_title }}</p>
                                    @endif
                                </div>
                            @endif

                            @if($order->notes)
                            <div class="mt-3 pt-3 border-t border-dashed">
                                <span class="text-gray-500 block mb-1 text-xs font-bold">Müşteri Sipariş Notu:</span>
                                <p class="bg-gray-100 p-2.5 rounded text-gray-800 text-xs">{{ $order->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sipariş İçeriği -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Sipariş İçeriği</h3>
                        
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Adı</th>
                                    <th class="py-3 text-center text-xs font-medium text-gray-500 uppercase">Adet</th>
                                    <th class="py-3 text-right text-xs font-medium text-gray-500 uppercase">Birim Fiyat</th>
                                    <th class="py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="py-4">
                                            <div class="font-bold text-gray-900 flex items-center gap-2">
                                                <span>{{ $item->custom_title ?: ($item->product->name ?? 'Silinmiş Ürün') }}</span>
                                                @if($item->custom_design_data)
                                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-black px-2 py-0.5 rounded-full uppercase">
                                                        ✨ Özel Tasarım
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- 🌸 ÇİÇEKÇİ HAZIRLAMA REÇETESİ -->
                                            @if($item->custom_design_data)
                                                <div class="mt-3 p-3.5 bg-gradient-to-br from-rose-50 to-pink-50/50 rounded-xl border border-rose-200/80 text-xs text-rose-950 space-y-2">
                                                    <div class="font-black text-[11px] uppercase tracking-wider text-rose-800 flex items-center gap-1.5 border-b border-rose-200/60 pb-1.5">
                                                        <span>🌸 ÇİÇEKÇİ HAZIRLAMA REÇETESİ</span>
                                                        <span class="text-rose-500 font-normal">({{ $item->custom_design_data['total_stems'] ?? 0 }} Dal Toplam)</span>
                                                    </div>

                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                        <div>
                                                            <strong class="text-gray-700 block text-[11px]">🏺 Seçilen Kap / Sunum:</strong>
                                                            <span class="font-bold text-rose-900">{{ $item->custom_design_data['container']['name'] ?? 'Standart' }}</span>
                                                        </div>
                                                        @if(!empty($item->custom_design_data['wrapping']) || !empty($item->custom_design_data['ribbon']))
                                                            <div>
                                                                <strong class="text-gray-700 block text-[11px]">🎀 Ambalaj &amp; Kurdele:</strong>
                                                                <span>{{ $item->custom_design_data['wrapping']['name'] ?? 'Standart' }}</span>
                                                                @if(!empty($item->custom_design_data['ribbon']))
                                                                    <span> + {{ $item->custom_design_data['ribbon']['name'] }} Kurdele ({{ $item->custom_design_data['ribbon']['position_label'] ?? 'Vazo Boynu' }})</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if(!empty($item->custom_design_data['greenery_level']) || !empty($item->custom_design_data['arrangement_form']))
                                                        <div class="flex items-center gap-4 text-xs bg-white/70 p-2 rounded-lg border border-rose-100">
                                                            <div><strong class="text-gray-700">🌿 Yeşillik:</strong> {{ ucfirst($item->custom_design_data['greenery_level'] ?? 'Doğal') }}</div>
                                                            <div><strong class="text-gray-700">📐 Form:</strong> {{ ($item->custom_design_data['arrangement_form'] ?? '') === 'spread' ? 'Geniş / Bohem' : 'Sıkı Kubbe (Biedermeier)' }}</div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($item->custom_design_data['slots']))
                                                        <div>
                                                            <strong class="text-gray-700 block text-[11px] mb-1">🗺️ Oasis Yuva Yerleşimi (Kuşbakışı):</strong>
                                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1.5 bg-white/70 p-2 rounded-lg border border-rose-100">
                                                                @foreach($item->custom_design_data['slots'] as $sNum => $sFl)
                                                                    <div class="flex items-center gap-1.5 text-[11px] bg-stone-50 px-2 py-1 rounded border border-stone-200">
                                                                        <span class="w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[9px] font-bold">{{ $sNum }}</span>
                                                                        <span class="truncate font-semibold text-stone-800">{{ $sFl['name'] ?? 'Boş' }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if(!empty($item->custom_design_data['flowers']))
                                                        <div>
                                                            <strong class="text-gray-700 block text-[11px] mb-1">💐 Çiçek Dal Dağılımı:</strong>
                                                            <ul class="space-y-1 bg-white/70 p-2 rounded-lg border border-rose-100">
                                                                @foreach($item->custom_design_data['flowers'] as $fl)
                                                                    <li class="flex items-center gap-2">
                                                                        <span class="w-3 h-3 rounded-full border shadow-2xs" style="background-color: {{ $fl['color_code'] ?? '#e11d48' }}"></span>
                                                                        <span class="font-black text-rose-950">{{ $fl['quantity'] }}x</span>
                                                                        <span>{{ $fl['name'] }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if(!empty($item->custom_design_data['fillers']))
                                                        <div>
                                                            <strong class="text-gray-700 block text-[11px] mb-1">🌿 Dolgu &amp; Yeşillik Dağılımı:</strong>
                                                            <ul class="space-y-1 bg-white/70 p-2 rounded-lg border border-rose-100">
                                                                @foreach($item->custom_design_data['fillers'] as $fil)
                                                                    <li class="flex items-center gap-2">
                                                                        <span class="text-emerald-600">🌿</span>
                                                                        <span class="font-black text-emerald-950">{{ $fil['quantity'] }}x</span>
                                                                        <span>{{ $fil['name'] }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if(!empty($item->custom_design_data['card_note']))
                                                        <div class="bg-amber-50 p-2.5 rounded-lg border border-amber-200 text-amber-950 font-serif">
                                                            <div class="text-[10px] font-sans font-black uppercase text-amber-800">💌 Kart Notu:</div>
                                                            <p class="italic text-xs mt-0.5">"{{ $item->custom_design_data['card_note'] }}"</p>
                                                            @if(!empty($item->custom_design_data['card_sender_title']))
                                                                <p class="text-right text-[10px] font-bold mt-1">— {{ $item->custom_design_data['card_sender_title'] }}</p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="py-4 text-center font-bold">{{ $item->quantity }}</td>
                                        <td class="py-4 text-right">{{ number_format($item->unit_price, 2) }} TL</td>
                                        <td class="py-4 text-right font-semibold text-rose-600">{{ number_format($item->unit_price * $item->quantity, 2) }} TL</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-6 border-t pt-4 flex justify-end">
                            <div class="w-1/2 text-right space-y-2">
                                <p class="text-gray-500 text-sm flex justify-between"><span>Teslimat:</span> <span class="text-green-600 font-medium">Ücretsiz</span></p>
                                <p class="text-gray-500 text-sm flex justify-between"><span>Ödeme Tipi:</span> <span class="font-medium text-gray-900">Kapıda Ödeme</span></p>
                                <p class="text-xl font-bold text-gray-900 flex justify-between border-t pt-2 mt-2"><span>Genel Toplam:</span> <span class="text-rose-600">{{ number_format($order->total_amount, 2) }} TL</span></p>
                            </div>
                        </div>

                        <!-- Finans ve Kaynak Bilgileri (Faz 5) -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Gelişmiş Finans ve Kaynak Verileri</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded border">
                                    <h4 class="font-bold text-sm text-gray-500 uppercase mb-2">Kârlılık Analizi</h4>
                                    <p class="mb-1"><span class="font-medium text-gray-700">Toplam Satış:</span> {{ number_format($order->total_amount, 2) }} TL</p>
                                    <p class="mb-1"><span class="font-medium text-gray-700">Ürün Maliyeti:</span> {{ number_format($order->total_cost, 2) }} TL</p>
                                    <p class="font-bold {{ ($order->total_amount - $order->total_cost) > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Net Kâr: {{ number_format($order->total_amount - $order->total_cost, 2) }} TL
                                    </p>
                                </div>
                                <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-100">
                                    <h4 class="font-bold text-sm text-rose-800 uppercase mb-2">UTM Reklam Kaynağı</h4>
                                    @if($order->utm_source || $order->utm_medium)
                                        <p class="mb-1 text-sm"><span class="font-medium text-rose-900">Kaynak (Source):</span> {{ $order->utm_source ?? '-' }}</p>
                                        <p class="mb-1 text-sm"><span class="font-medium text-rose-900">Araç (Medium):</span> {{ $order->utm_medium ?? '-' }}</p>
                                        <p class="mb-1 text-sm"><span class="font-medium text-rose-900">Kampanya:</span> {{ $order->utm_campaign ?? '-' }}</p>
                                        <p class="mb-1 text-sm"><span class="font-medium text-rose-900">İçerik/Terim:</span> {{ $order->utm_content ?? '-' }}</p>
                                    @else
                                        <p class="text-sm text-gray-600 italic">Bu sipariş için kaynak bilgisi (UTM) bulunamadı. Muhtemelen direkt giriş yapılmış.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
