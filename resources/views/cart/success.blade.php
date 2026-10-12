<x-frontend-layout>
    <x-slot name="title">Sipariş Başarılı - İstanbul Çiçek Evi</x-slot>

    <div class="bg-gray-100 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-emerald-500 py-8 text-center text-white">
                    <i class="fas fa-check-circle text-6xl mb-4"></i>
                    <h1 class="text-3xl font-bold">Siparişiniz Başarıyla Alındı!</h1>
                    <p class="mt-2 text-emerald-100">Bizi tercih ettiğiniz için teşekkür ederiz. 🌸</p>
                </div>
                
                <div class="p-8 text-center">
                    <div class="mb-8">
                        <p class="text-gray-600 mb-2">Sipariş Numarası</p>
                        <p class="text-4xl font-extrabold text-rose-600 tracking-widest">{{ $order->order_number }}</p>
                    </div>

                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6 mb-8 text-left">
                        <h3 class="font-bold text-rose-900 mb-3"><i class="fas fa-info-circle mr-2 text-rose-600"></i>Sırada Ne Var?</h3>
                        <ul class="space-y-2 text-gray-700 text-sm">
                            <li>Siparişiniz ve kart notunuz ekibimiz tarafından incelenerek onaylanacaktır.</li>
                            <li>Çiçekleriniz atölyemizde taze olarak özenle hazırlanıp VIP çiçek kuryemiz ile seçtiğiniz teslimat zamanında ulaştırılacaktır.</li>
                            <li>Siparişinizin durumunu "Sipariş Takibi" sayfasından Sipariş Numaranız ve Telefon Numaranız ile anlık sorgulayabilirsiniz.</li>
                        </ul>
                    </div>

                    @php
                        $wpNumber = preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905013798236');
                        if (str_starts_with($wpNumber, '0')) {
                            $wpNumber = '90' . substr($wpNumber, 1);
                        } elseif (!str_starts_with($wpNumber, '90')) {
                            $wpNumber = '90' . $wpNumber;
                        }
                        $wpCustomerMsg = "Merhaba İstanbul Çiçek Evi 🌸\n\n#" . $order->order_number . " numaralı çiçek siparişimi oluşturdum. Teslimat saatimi ve kart notumu teyit etmek istiyorum.";
                    @endphp

                    <div class="mb-6">
                        <a href="https://wa.me/{{ $wpNumber }}?text={{ urlencode($wpCustomerMsg) }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3.5 px-8 rounded-2xl shadow-lg shadow-emerald-600/25 transition-all text-sm sm:text-base">
                            <i class="fab fa-whatsapp text-xl"></i>
                            <span>WhatsApp ile Siparişimi Onayla &amp; Hızlandır</span>
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="{{ route('order.track') }}" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-8 rounded-xl shadow transition-colors flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Siparişimi Takip Et
                        </a>
                        <a href="{{ route('home') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-8 rounded-xl shadow-sm transition-colors">
                            Alışverişe Devam Et
                        </a>
                    </div>
                </div>
                
                @if($order->wants_service)
                <div class="bg-gray-50 p-6 border-t border-gray-100 text-sm text-center text-gray-500">
                    Siparişiniz VIP çiçek kuryemiz ile aynı gün özenle adrese ulaştırılacaktır.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 📊 Google Analytics 4 (GA4) & Google Ads E-Ticaret Satın Alma Takip Kodu -->
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'purchase',
            'ecommerce': {
                'transaction_id': '{{ $order->order_number }}',
                'value': {{ $order->total_amount }},
                'currency': 'TRY',
                'items': [
                    @foreach($order->items as $item)
                    {
                        'item_id': '{{ $item->product_id }}',
                        'item_name': '{{ addslashes($item->product->name ?? "Çiçek & Buket Tasarımı") }}',
                        'price': {{ $item->unit_price }},
                        'quantity': {{ $item->quantity }}
                    }@if(!$loop->last),@endif
                    @endforeach
                ]
            }
        });

        // Google Ads Satın Alma Dönüşümü (AW-307666873)
        if (typeof gtag === 'function') {
            gtag('event', 'conversion', {
                'send_to': 'AW-307666873',
                'value': {{ $order->total_amount }},
                'currency': 'TRY',
                'transaction_id': '{{ $order->order_number }}'
            });
        }
    </script>
</x-frontend-layout>
