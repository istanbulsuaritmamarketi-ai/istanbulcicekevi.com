<x-frontend-layout>
    <x-slot name="title">İletişim &amp; Atölyemiz - İstanbul Çiçek Evi</x-slot>

    <div class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl font-black text-center text-gray-900 mb-8">Bize Ulaşın 🌸</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-3xl shadow-lg overflow-hidden border border-gray-100">
                <!-- İletişim Bilgileri ve Harita -->
                <div class="p-8 bg-rose-50/50">
                    <h2 class="text-2xl font-bold text-rose-950 mb-6">İletişim &amp; Sipariş Bilgileri</h2>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt text-rose-600 mt-1 text-xl"></i>
                            <div>
                                <strong class="block text-gray-800">Atölye Adresi:</strong>
                                <span class="text-gray-600">{{ $siteSettings['address'] ?? 'İstanbul, Türkiye' }}</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-phone text-rose-600 mt-1 text-xl"></i>
                            <div>
                                <strong class="block text-gray-800">Müşteri &amp; Sipariş Hattı:</strong>
                                <a href="tel:{{ $siteSettings['phone'] ?? '0212 444 88 99' }}" class="text-rose-600 font-bold hover:underline">{{ $siteSettings['phone'] ?? '0212 444 88 99' }}</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fab fa-whatsapp text-emerald-500 mt-1 text-xl"></i>
                            <div>
                                <strong class="block text-gray-800">WhatsApp Çiçek Danışmanı:</strong>
                                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}" class="text-emerald-600 font-bold hover:underline">{{ $siteSettings['whatsapp'] ?? '0555 555 55 55' }}</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-envelope text-rose-600 mt-1 text-xl"></i>
                            <div>
                                <strong class="block text-gray-800">E-Posta:</strong>
                                <!--email_off-->
                                <a href="mailto:{{ $siteSettings['email'] ?? 'info@istanbulcicekevi.com' }}" class="text-rose-600 hover:underline">{{ $siteSettings['email'] ?? 'info@istanbulcicekevi.com' }}</a>
                                <!--/email_off-->
                            </div>
                        </li>
                    </ul>

                    @if(!empty($siteSettings['map_url']))
                    <div class="w-full h-64 rounded-2xl overflow-hidden shadow">
                        <iframe src="{{ $siteSettings['map_url'] }}" width="100%" height="100%" class="border-0 w-full h-full" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    @else
                    <div class="w-full h-64 rounded-2xl overflow-hidden shadow bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-sm">Harita yüklenemedi</span>
                    </div>
                    @endif
                </div>

                <!-- Form -->
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Özel Sipariş / Mesaj Gönderin</h2>
                    
                    @if(session('success'))
                        <div class="bg-emerald-100 border border-emerald-400 text-emerald-800 px-4 py-3 rounded-2xl relative mb-4 text-sm font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-xs font-bold mb-2">Adınız Soyadınız *</label>
                            <input type="text" name="name" required class="shadow-xs appearance-none border rounded-xl w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-rose-500 focus:ring-rose-500 text-sm" value="{{ old('name') }}">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-2">Telefon *</label>
                                <input type="text" name="phone" required class="shadow-xs appearance-none border rounded-xl w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-rose-500 focus:ring-rose-500 text-sm" value="{{ old('phone') }}">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-2">E-Posta (İsteğe Bağlı)</label>
                                <input type="email" name="email" class="shadow-xs appearance-none border rounded-xl w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-rose-500 focus:ring-rose-500 text-sm" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-xs font-bold mb-2">Mesajınız / Çiçek Talebiniz *</label>
                            <textarea name="message" rows="5" required class="shadow-xs appearance-none border rounded-xl w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-rose-500 focus:ring-rose-500 text-sm">{{ old('message') }}</textarea>
                        </div>
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-black py-3 px-8 rounded-xl shadow-lg focus:outline-none w-full md:w-auto transition-all transform active:scale-95 text-sm">
                                <i class="fas fa-paper-plane mr-1.5"></i> Mesajı Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
