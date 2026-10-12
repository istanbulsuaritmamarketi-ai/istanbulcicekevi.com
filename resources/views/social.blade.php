<x-frontend-layout>
    <div class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Başlık -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Sosyal Medyamız</h1>
                <p class="text-gray-500 text-lg">Bizi takip edin, kampanyalardan ve yeniliklerden haberdar olun!</p>
            </div>

            <!-- Kanallar -->
            <div class="space-y-4">

                <!-- Instagram -->
                <a href="{{ $siteSettings['instagram'] ?? '#' }}" target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-instagram text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-pink-600 transition-colors">Instagram</div>
                        <div class="text-gray-500 text-sm mt-1">En yeni buket tasarımları, teslimat videoları ve kampanyalarımız için Instagram sayfamızı takip edin.</div>
                        <div class="text-pink-500 text-sm font-semibold mt-2">{{ $siteSettings['instagram'] ? '@instagramhesabiniz' : 'Yakında aktif olacak' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-pink-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Facebook -->
                <a href="{{ $siteSettings['facebook'] ?? '#' }}" target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-rose-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-facebook-f text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-rose-600 transition-colors">Facebook</div>
                        <div class="text-gray-500 text-sm mt-1">Taze çiçek duyurularımızı, özel gün buketlerimizi ve mutlu müşteri anlarını Facebook sayfamızdan takip edin.</div>
                        <div class="text-rose-500 text-sm font-semibold mt-2">{{ $siteSettings['facebook'] ? 'Facebook Sayfamız' : 'Yakında aktif olacak' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-rose-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=Merhaba, sosyal medya sayfanızdan ulaşıyorum." target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-500 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-whatsapp text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">WhatsApp</div>
                        <div class="text-gray-500 text-sm mt-1">Çiçek siparişi, teslimat durumu, özel not kartı ve aranjman talepleriniz için WhatsApp destek hattımızdan bize ulaşın.</div>
                        <div class="text-emerald-500 text-sm font-semibold mt-2">{{ $siteSettings['whatsapp'] ?? 'Destek Hattımız' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all"></i>
                </a>

            </div>

            <!-- CTA -->
            <div class="text-center mt-12 p-8 bg-gradient-to-br from-rose-600 via-pink-600 to-rose-800 rounded-3xl text-white shadow-lg">
                <h2 class="text-2xl font-black mb-2">Hemen İletişime Geçin! 🌸</h2>
                <p class="text-rose-100 mb-6 text-sm">Çiçek tasarımlarımız, VIP kurye teslimatı ve kurumsal çiçek talepleriniz hakkında her sorunuzu yanıtlamaktan mutluluk duyarız.</p>
                <a href="{{ route('contact') }}" class="bg-white text-rose-700 font-black px-8 py-3 rounded-2xl hover:bg-rose-50 transition-all shadow-md inline-block text-sm">
                    İletişim &amp; Atölyemiz →
                </a>
            </div>

        </div>
    </div>
</x-frontend-layout>
