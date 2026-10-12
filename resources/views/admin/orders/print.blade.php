<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparis-{{ $order->order_number }} - {{ config('app.name', 'İstanbul Çiçek Evi') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 text-gray-800">

    <!-- Yazdır & Geri Butonları -->
    <div class="max-w-3xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-bold text-gray-600 hover:text-gray-900 bg-white px-4 py-2 rounded-xl shadow-sm border">
            &larr; Siparişe Dön
        </a>
        <button onclick="window.print()" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-6 py-2 rounded-xl shadow text-sm flex items-center gap-2">
            🖨️ Sayfayı Yazdır (A4 / PDF)
        </button>
    </div>

    <!-- A4 Fatura / Sipariş Fişi Kağıdı -->
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-12 rounded-2xl shadow-md border border-gray-200">
        
        <!-- Üst Başlık & Firma Bilgileri -->
        <div class="flex justify-between items-start border-b pb-8">
            <div>
                <img src="{{ asset('images/logo.svg') }}?v={{ @filemtime(public_path('images/logo.svg')) ?: 20261009 }}" alt="İstanbul Çiçek Evi" class="h-10 mb-2 object-contain">
                <p class="text-xs text-gray-500">Özel Tasarım Çiçek & VIP Aynı Gün Teslimat</p>
                <p class="text-xs text-gray-500">İstanbul / Türkiye | www.istanbulcicekevi.com</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-rose-50 text-rose-800 text-xs font-extrabold px-3 py-1 rounded-lg border border-rose-200 mb-1">
                    SİPARİŞ FİŞİ &amp; KART NOTU
                </span>
                <p class="text-sm font-bold text-gray-900">{{ $order->order_number }}</p>
                <p class="text-xs text-gray-500">{{ $order->created_at->translatedFormat('d F Y - H:i') }}</p>
            </div>
        </div>

        <!-- Müşteri ve Teslimat Bilgisi -->
        <div class="grid grid-cols-2 gap-8 my-8 text-xs leading-relaxed">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="font-black text-rose-950 uppercase text-[11px] mb-2">Müşteri Bilgileri</h3>
                <p><strong class="text-gray-700">Adı Soyadı:</strong> {{ $order->customer_name }}</p>
                <p><strong class="text-gray-700">Telefon:</strong> {{ $order->customer_phone }}</p>
                <p><strong class="text-gray-700">Bölge/İlçe:</strong> {{ $order->region->name ?? 'Belirtilmemiş' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h3 class="font-black text-rose-950 uppercase text-[11px] mb-2">Teslimat & Ödeme</h3>
                <p><strong class="text-gray-700">Açık Adres:</strong> {{ $order->address }}</p>
                <p><strong class="text-gray-700">Ödeme Şekli:</strong> Kapıda Ödeme (Nakit / POS)</p>
                <p><strong class="text-gray-700">Sipariş Durumu:</strong> {{ $order->status }}</p>
            </div>
        </div>

        <!-- Sipariş Kalemleri Tablosu -->
        <div class="mb-8 overflow-hidden rounded-xl border border-gray-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 text-gray-600 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">Ürün Açıklaması</th>
                        <th class="p-3 text-center">Adet</th>
                        <th class="p-3 text-right">Birim Fiyat</th>
                        <th class="p-3 text-right">Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $idx => $item)
                        <tr>
                            <td class="p-3 text-gray-400">{{ $idx + 1 }}</td>
                            <td class="p-3 font-bold text-gray-900">
                                <div>{{ $item->custom_title ?: ($item->product->name ?? 'Ürün') }}</div>
                                @if($item->custom_design_data)
                                    <div class="mt-2 p-2.5 bg-rose-50 rounded-lg border border-rose-200 text-[11px] text-rose-950 font-normal space-y-1">
                                        <div class="font-bold text-rose-900 uppercase">🌸 ÇİÇEKÇİ HAZIRLAMA REÇETESİ ({{ $item->custom_design_data['total_stems'] ?? 0 }} Dal):</div>
                                        <div><strong>Sunum/Kap:</strong> {{ $item->custom_design_data['container']['name'] ?? 'Standart' }}</div>
                                        @if(!empty($item->custom_design_data['flowers']))
                                            <div>
                                                <strong>Çiçekler:</strong>
                                                @foreach($item->custom_design_data['flowers'] as $fl)
                                                    <span class="inline-block mr-2 font-semibold">[ ] {{ $fl['quantity'] }}x {{ $fl['name'] }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($item->custom_design_data['fillers']))
                                            <div>
                                                <strong>Yeşillik/Dolgu:</strong>
                                                @foreach($item->custom_design_data['fillers'] as $fil)
                                                    <span class="inline-block mr-2 font-semibold">[ ] {{ $fil['quantity'] }}x {{ $fil['name'] }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($item->custom_design_data['greenery_level']) || !empty($item->custom_design_data['arrangement_form']))
                                            <div>
                                                <strong>Yeşillik &amp; Form:</strong> 
                                                {{ ucfirst($item->custom_design_data['greenery_level'] ?? 'Doğal') }} Yeşillik / {{ ($item->custom_design_data['arrangement_form'] ?? '') === 'spread' ? 'Geniş Bohem' : 'Sıkı Kubbe' }}
                                            </div>
                                        @endif
                                        @if(!empty($item->custom_design_data['slots']))
                                            <div>
                                                <strong>Yuva Haritası:</strong>
                                                <div class="mt-0.5 text-[10px]">
                                                    @foreach($item->custom_design_data['slots'] as $sNum => $sFl)
                                                        <span class="inline-block mr-1.5 bg-gray-100 px-1 rounded border border-gray-300">Y{{ $sNum }}: {{ $sFl['name'] ?? 'Boş' }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty($item->custom_design_data['wrapping']) || !empty($item->custom_design_data['ribbon']))
                                            <div>
                                                <strong>Ambalaj/Kurdele:</strong> 
                                                {{ $item->custom_design_data['wrapping']['name'] ?? '-' }} / {{ $item->custom_design_data['ribbon']['name'] ?? '-' }}
                                                @if(!empty($item->custom_design_data['ribbon']['position_label']))
                                                    ({{ $item->custom_design_data['ribbon']['position_label'] }})
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="p-3 text-right">{{ number_format($item->unit_price, 2, ',', '.') }} TL</td>
                            <td class="p-3 text-right font-bold text-rose-900">{{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }} TL</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 💌 KESİLEBİLİR ÇİÇEK KART NOTU ALANI (AMCA İÇİN MAKASLA KESİP ÇİÇEĞE TAKMA ALANI) -->
        @if($order->card_note)
            <div class="my-8 p-6 bg-amber-50/70 border-2 border-dashed border-amber-300 rounded-2xl relative">
                <div class="absolute -top-3 left-6 bg-white px-2 text-[10px] font-black uppercase text-amber-800 tracking-wider flex items-center gap-1 border border-amber-300 rounded">
                    <span>✂️ BURADAN KESİNİZ — ÇİÇEK KART NOTU</span>
                </div>
                <div class="text-center space-y-2 font-serif py-2">
                    <div class="text-xs uppercase font-sans font-bold text-amber-800 tracking-widest">İSTANBUL ÇİÇEK EVİ</div>
                    <div class="w-8 h-0.5 bg-rose-400 mx-auto"></div>
                    <p class="text-base sm:text-lg italic text-amber-950 px-4">"{{ $order->card_note }}"</p>
                    @if($order->card_sender_title)
                        <p class="text-xs font-sans font-bold text-amber-900 mt-2">— {{ $order->card_sender_title }}</p>
                    @endif
                </div>
            </div>
        @endif

        <!-- Toplam & Dipnot -->
        <div class="flex justify-between items-end border-t pt-6">
            <div class="text-[11px] text-gray-500 space-y-1 max-w-sm">
                <p>• Bu belge teslimat ve sipariş takibi amacıyla düzenlenmiştir.</p>
                <p>• Çiçeklerimiz taze kesme olup oda sıcaklığında, doğrudan güneş görmeyen yerde muhafaza edilmelidir.</p>
                <p>• Destek ve özel siparişleriniz için WhatsApp hattımızdan bize ulaşabilirsiniz.</p>
            </div>
            <div class="text-right space-y-1">
                <div class="text-xs text-gray-500">Teslimat: <span class="text-emerald-600 font-bold">ÜCRETSİZ / VIP</span></div>
                <div class="text-sm text-gray-600">Toplam Tutar:</div>
                <div class="text-2xl font-black text-rose-900">{{ number_format($order->total_amount, 2, ',', '.') }} TL</div>
            </div>
        </div>

        <!-- İmza Alanı -->
        <div class="grid grid-cols-2 gap-8 mt-12 pt-8 border-t border-dashed text-center text-xs text-gray-500">
            <div>
                <p class="font-bold text-gray-700">Teslim Eden (Firma)</p>
                <div class="h-14"></div>
                <p class="border-t pt-1">İmza / Kaşe</p>
            </div>
            <div>
                <p class="font-bold text-gray-700">Teslim Alan (Müşteri)</p>
                <div class="h-14"></div>
                <p class="border-t pt-1">İmza</p>
            </div>
        </div>

    </div>

</body>
</html>
