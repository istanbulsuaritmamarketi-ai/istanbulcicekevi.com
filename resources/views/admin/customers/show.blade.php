<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Müşteri Detayı: <span class="text-blue-600">{{ $customer->name }}</span>
            </h2>
            <a href="{{ route('admin.customers.index') }}" class="text-gray-600 hover:text-gray-900 border px-4 py-2 rounded">
                Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Müşteri Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Ad Soyad</p>
                            <p class="font-medium">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telefon</p>
                            <p class="font-medium">
                                <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:underline">{{ $customer->phone }}</a>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kayıt Tarihi</p>
                            <p class="font-medium">{{ $customer->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Adres / Bölge</p>
                            <p class="font-medium">{{ $customer->region->name ?? 'Bilinmiyor' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sipariş Geçmişi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Sipariş Geçmişi</h3>
                        @forelse($orders as $order)
                            <div class="mb-4 p-4 border rounded hover:bg-gray-50">
                                <div class="flex justify-between items-center mb-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-blue-600 hover:underline">{{ $order->order_number }}</a>
                                    <span class="text-sm font-medium">{{ number_format($order->total_amount, 2) }} TL</span>
                                </div>
                                <div class="text-sm text-gray-500 mb-2">Tarih: {{ $order->created_at->format('d.m.Y') }} | Durum: {{ $order->status }}</div>
                                <p class="text-sm text-gray-700">Adres: {{ $order->address }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500">Bu müşteriye ait sipariş bulunmuyor.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Müşteri Sipariş Notları & Özel Tercihler -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">🌸 Çiçek &amp; Özel Gün Tercihleri</h3>
                        <div class="space-y-4 text-sm">
                            <div class="p-4 bg-rose-50/60 border border-rose-100 rounded-2xl">
                                <h4 class="font-bold text-rose-900 mb-1">Müşteri Sadakat Durumu</h4>
                                <p class="text-rose-700 text-xs">
                                    Bu müşteri toplam <strong class="font-black">{{ $orders->count() }}</strong> çiçek siparişi vermiştir.
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 border rounded-2xl">
                                <h4 class="font-bold text-gray-800 mb-1">Hızlı WhatsApp İletişimi</h4>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                                    if(str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '90' . substr($cleanPhone, 1);
                                    } elseif(!str_starts_with($cleanPhone, '90')) {
                                        $cleanPhone = '90' . $cleanPhone;
                                    }
                                    $waLink = "https://wa.me/" . $cleanPhone . "?text=" . urlencode("Merhaba Sayın " . $customer->name . ", İstanbul Çiçek Evi'nden ulaşıyoruz.");
                                @endphp
                                <a href="{{ $waLink }}" target="_blank" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition-colors">
                                    <i class="fab fa-whatsapp text-sm"></i> Müşteriye WhatsApp'tan Yaz
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
