<x-guest-layout>
    <div class="space-y-6">
        
        <!-- Başlık & Karşılama -->
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                Giriş Yap
            </h2>
            <p class="text-xs text-gray-500 max-w-sm mx-auto">
                Siparişlerinizi takip etmek ve indirim kuponlarınıza erişmek için giriş yapın.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Telefon veya E-posta -->
            <div>
                <label for="login" class="block text-xs font-bold text-gray-700 mb-1">
                    Telefon Numarası veya E-Posta
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user-circle text-xs"></i>
                    </div>
                    <input id="login" type="text" name="login" value="{{ old('login', old('email')) }}" required autofocus
                           placeholder="05xx xxx xx xx veya e-posta"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <!-- Şifre -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-gray-700">
                        Şifreniz
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-[11px] font-medium text-rose-600 hover:underline" href="{{ route('password.request') }}">
                            Şifremi unuttum?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="Şifrenizi girin"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                </div>
            </div>

            <!-- Beni Hatırla -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded-md border-gray-300 text-rose-600 shadow-xs focus:ring-rose-500" name="remember">
                    <span class="ms-2 text-xs text-gray-600 font-medium">Beni hatırla</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black text-xs py-3 px-4 rounded-xl shadow-md transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </button>
        </form>

        <!-- Kayıt Ol Linki -->
        <div class="pt-4 border-t border-gray-100 text-center space-y-2">
            <p class="text-xs text-gray-500">
                Henüz bir hesabınız yok mu?
            </p>
            <a href="{{ route('register') }}" 
               class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-purple-50 hover:bg-purple-100 text-purple-700 font-black text-xs rounded-xl border border-purple-200 transition-colors">
                <i class="fas fa-tags text-purple-600"></i> 10 Saniyede Kayıt Ol &amp; İndirim Kazan
            </a>
        </div>

    </div>
</x-guest-layout>
