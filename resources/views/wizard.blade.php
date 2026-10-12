<x-frontend-layout title="Sevdiklerinize En Uygun Çiçeği Bulun | Çiçek Sihirbazı" description="30 saniyede gönderim amacınıza, alıcınıza ve özel gününüze en uygun taze çiçek aranjmanını keşfedin.">
    <!-- Sihirbaz Bileşeni -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        <x-quiz-wizard />

        <!-- Doğrudan Destek & Danışmanlık -->
        <div class="mt-8 bg-white border border-gray-200/80 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-gray-900">İstanbul İçi Aynı Gün Randevulu Çiçek Teslimatı</h4>
                    <p class="text-xs text-gray-500 mt-0.5">39 ilçede sevdiklerinize taze buketler ve zarflı kart notlarıyla kapıda teslim ediyoruz.</p>
                </div>
            </div>
            <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20çiçek%20sihirbazından%20geliyorum.%20Özel%20bir%20aranjman%20için%20birlikte%20seçebilir%20miyiz?" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-5 py-3 rounded-2xl shadow transition-all flex items-center gap-2 flex-shrink-0">
                <i class="fab fa-whatsapp text-sm"></i> Çiçek Ustasına Danış
            </a>
        </div>
    </div>
</x-frontend-layout>
