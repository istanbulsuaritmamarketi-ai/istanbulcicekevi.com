<x-frontend-layout>
    <div class="bg-gray-50 py-12 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Sipariş Takibi</h1>

            <!-- Sorgulama Formu -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-8">
                <form action="{{ route('order.track') }}" method="GET" class="space-y-4 sm:flex sm:space-y-0 sm:gap-4">
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full">
                        <div class="flex-grow w-full relative">
                            <input type="text" name="order_number" id="order_number" value="{{ request('order_number') }}" required
                                class="shadow-sm focus:ring-rose-500 focus:border-rose-500 block w-full sm:text-sm border-gray-300 rounded-md p-3"
                                placeholder="Sipariş Numaranızı Girin (Örn: 2026...)">
                        </div>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-md shadow-sm text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transition-colors">
                            Sorgula
                        </button>
                    </div>
                </form>
                <p class="text-xs text-gray-500 mt-3 text-center">Sipariş numaranızı girerek siparişinizin durumunu öğrenebilirsiniz.</p>
            </div>

            <!-- Hata Mesajı -->
            @if($error)
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-8 text-center">
                    {{ $error }}
                </div>
            @endif

            <!-- Sipariş Sonucu -->
            @if($order)
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-8 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Sipariş: {{ $order->order_number }}</h2>
                            <p class="text-gray-500 mt-1">Sipariş Tarihi: {{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-500 block mb-1">Durum:</span>
                            @php
                                $statusColor = match($order->status) {
                                    'Sipariş alındı' => 'bg-rose-100 text-rose-800',
                                    'Onay bekliyor' => 'bg-yellow-100 text-yellow-800',
                                    'Onaylandı' => 'bg-indigo-100 text-indigo-800',
                                    'Randevu planlandı' => 'bg-purple-100 text-purple-800',
                                    'Hazırlanıyor' => 'bg-orange-100 text-orange-800',
                                    'Yolda' => 'bg-cyan-100 text-cyan-800',
                                    'Tamamlandı' => 'bg-green-100 text-green-800',
                                    'İptal edildi' => 'bg-red-100 text-red-800',
                                    'Ek bilgi gerekli' => 'bg-gray-200 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                
                                $statusMeaning = match($order->status) {
                                    'Sipariş alındı' => 'Talep sisteme kaydedildi.',
                                    'Onay bekliyor' => 'Sipariş/teslimat bilgileri kontrol ediliyor.',
                                    'Onaylandı' => 'Sipariş işleme alındı.',
                                    'Randevu planlandı' => 'Servis tarihi ve saat aralığı belirlendi.',
                                    'Hazırlanıyor' => 'Ürün/servis süreci hazırlanıyor.',
                                    'Yolda' => 'Teslimat veya ekip yönlendirildi.',
                                    'Tamamlandı' => 'Sipariş veya servis başarıyla tamamlandı.',
                                    'İptal edildi' => 'Sipariş iptal edildi.',
                                    'Ek bilgi gerekli' => 'Müşterinin bizimle iletişime geçmesi gerekiyor.',
                                    default => ''
                                };
                            @endphp
                            <span class="px-4 py-2 inline-flex text-sm leading-5 font-bold rounded-full {{ $statusColor }}">
                                {{ $order->status }}
                            </span>
                            <p class="text-xs text-gray-600 mt-2 font-medium">{{ $statusMeaning }}</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-3 border-b pb-2">Sipariş İçeriği</h4>
                        <ul class="divide-y divide-gray-200 mb-4">
                            @foreach($order->items as $item)
                                <li class="py-3 flex justify-between text-sm">
                                    <span class="text-gray-900 font-medium">{{ $item->quantity }}x {{ $item->product->name ?? 'Ürün' }}</span>
                                    <span class="text-gray-600">{{ number_format($item->unit_price * $item->quantity, 2) }} TL</span>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                            <span class="font-bold text-gray-900">Toplam Tutar:</span>
                            <span class="text-xl font-extrabold text-rose-600">{{ number_format($order->total_amount, 2) }} TL</span>
                        </div>
                        
                        <div class="mt-6 bg-gray-50 border border-gray-100 rounded-lg p-6 flex flex-col sm:flex-row gap-4 justify-between items-center">
                            <p class="text-sm text-gray-500">
                                Siparişinizle ilgili yardıma mı ihtiyacınız var?
                            </p>
                            <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text={{ urlencode('Merhaba, ' . $order->order_number . ' numaralı siparişim hakkında destek almak istiyorum. (Kaynak: Sipariş Takip)') }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-green-500 rounded-md font-semibold text-white hover:bg-green-600 transition-colors">
                                <i class="fab fa-whatsapp text-xl mr-2"></i> WhatsApp Destek
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-frontend-layout>
