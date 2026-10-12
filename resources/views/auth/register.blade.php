<x-guest-layout>
    <div class="space-y-6">
        
        <!-- Başlık & Karşılama -->
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                Müşteri Hesabı Oluştur
            </h2>
            <p class="text-xs text-gray-500 max-w-sm mx-auto">
                İstanbul Çiçek Evi'ne katılın, siparişlerinizi ve sevdiklerinize özel çiçek indirimlerinizi kolayca yönetin.
            </p>
        </div>

        <!-- 🎁 Hoş Geldin Kuponu Avantaj Kutusu -->
        @php
            $isWelcomeActive = ($settings['welcome_coupon_enabled'] ?? '1') == '1';
            $discountVal = $settings['welcome_coupon_discount'] ?? 150;
            $type = $settings['welcome_coupon_type'] ?? 'fixed';
            $minOrder = $settings['welcome_coupon_min_order'] ?? 500;
        @endphp
        @if($isWelcomeActive)
            <div class="p-3.5 bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border border-purple-200 rounded-2xl flex items-center gap-3 text-left">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-base shadow-xs shrink-0">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <div class="text-xs font-black text-purple-950">
                        {{ $type === 'percent' ? '%' . $discountVal : $discountVal . ' TL' }} Hoş Geldin Kuponu!
                    </div>
                    <p class="text-[11px] text-purple-800">
                        Kayıt olduğunuz an hesabınıza otomatik tanımlanır ve ilk siparişinizde kullanılabilir.
                    </p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- 1. Ad Soyad -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 mb-1">
                    Adınız Soyadınız <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Örn: Ahmet Yılmaz"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <!-- 2. Cep Telefonu (SADECE TELEFON) -->
            <div>
                <label for="phone" class="block text-xs font-bold text-gray-700 mb-1">
                    Cep Telefonu Numaranız <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-phone text-xs"></i>
                    </div>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="05xx xxx xx xx"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Giriş yaparken bu telefon numaranızı kullanacaksınız.</p>
            </div>

            <!-- 3. Şifre -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 mb-1">
                    Şifreniz <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           placeholder="En az 6 karakter"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-md transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer mt-2">
                <i class="fas fa-user-plus"></i> Hemen Üye Ol &amp; Kuponumu Tanımla
            </button>
        </form>

        <div class="pt-4 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-500">
                Zaten bir hesabınız var mı?
                <a href="{{ route('login') }}" class="font-bold text-rose-600 hover:text-rose-700 hover:underline">
                    Giriş Yapın
                </a>
            </p>
        </div>

    </div>
</x-guest-layout>
