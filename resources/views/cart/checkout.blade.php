<x-frontend-layout>
    <div class="bg-gradient-to-b from-rose-50/50 via-gray-50 to-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Başlık ve Adım -->
            <div class="mb-8">
                <nav class="flex items-center gap-2 text-xs font-semibold text-rose-800/70 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-rose-600">Ana Sayfa</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <a href="{{ route('cart.index') }}" class="hover:text-rose-600">Sepetim</a>
                    <i class="fas fa-chevron-right text-[9px]"></i>
                    <span class="text-rose-950 font-bold">Çiçek Siparişi &amp; Teslimat Bilgileri</span>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                    <span>🌸 Siparişi Tamamla</span>
                    <span class="text-xs font-bold uppercase tracking-wider bg-rose-100 text-rose-700 px-3 py-1 rounded-full border border-rose-200">Aynı Gün Hızlı Teslimat</span>
                </h1>
            </div>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12">
                <!-- Sol Taraf: Sipariş Formu -->
                <div class="lg:col-span-7">
                    <!-- Bilgilendirme Rozeti -->
                    <div class="mb-6 p-4 sm:p-5 bg-gradient-to-r from-rose-50 via-pink-50 to-amber-50 border-2 border-rose-200 rounded-2xl shadow-xs flex items-center gap-3.5">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-500/20">
                            <i class="fas fa-heart text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xs sm:text-sm font-black text-rose-950 flex items-center gap-2">
                                <span>💐 Taze Çiçek &amp; Güvenli Teslimat Garantisi</span>
                                <span class="bg-rose-600 text-white text-[9px] px-2 py-0.5 rounded-full font-black uppercase">ÖZEL KURYE</span>
                            </h3>
                            <p class="text-[11px] sm:text-xs text-rose-900/90 mt-0.5 font-medium leading-relaxed">
                                Çiçekleriniz siparişinize özel taptaze hazırlanır, dilediğiniz saat diliminde alıcısına güleryüzle teslim edilir.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
                        @csrf

                        <!-- 1. ADIM: TESLİMAT ZAMANI & SAAT DİLİMİ -->
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-rose-100 mb-8">
                            <div class="bg-gradient-to-r from-rose-600 to-pink-600 px-6 py-4 flex items-center justify-between">
                                <h2 class="text-base font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-calendar-alt"></i> 1. Teslimat Zamanı &amp; Saat Dilimi
                                </h2>
                                <span class="text-xs text-rose-100 font-semibold">İstanbul İçi Aynı Gün</span>
                            </div>
                            <div class="p-6 space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Teslimat Günü Seçin</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <label class="relative flex flex-col p-3.5 border-2 rounded-xl cursor-pointer transition-all border-rose-500 bg-rose-50/50 has-checked:border-rose-600 has-checked:bg-rose-50">
                                            <input type="radio" name="delivery_date" value="{{ date('d.m.Y') }} (Bugün)" checked class="sr-only">
                                            <span class="text-xs font-black text-rose-950">Bugün (Aynı Gün)</span>
                                            <span class="text-[11px] text-rose-700 font-semibold mt-0.5">{{ date('d.m.Y') }}</span>
                                        </label>
                                        <label class="relative flex flex-col p-3.5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-rose-300 has-checked:border-rose-600 has-checked:bg-rose-50">
                                            <input type="radio" name="delivery_date" value="{{ date('d.m.Y', strtotime('+1 day')) }} (Yarın)" class="sr-only">
                                            <span class="text-xs font-black text-gray-900">Yarın</span>
                                            <span class="text-[11px] text-gray-500 font-medium mt-0.5">{{ date('d.m.Y', strtotime('+1 day')) }}</span>
                                        </label>
                                        <label class="relative flex flex-col p-3.5 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-rose-300 has-checked:border-rose-600 has-checked:bg-rose-50 col-span-2 sm:col-span-1">
                                            <input type="radio" name="delivery_date" value="{{ date('d.m.Y', strtotime('+2 days')) }}" class="sr-only">
                                            <span class="text-xs font-black text-gray-900">2 Gün Sonra</span>
                                            <span class="text-[11px] text-gray-500 font-medium mt-0.5">{{ date('d.m.Y', strtotime('+2 days')) }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Teslimat Saat Aralığı</label>
                                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        @foreach(['09:00 - 12:00', '12:00 - 15:00', '15:00 - 18:00', '18:00 - 21:00'] as $index => $slot)
                                            <label class="flex flex-col items-center justify-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer transition-all hover:border-rose-300 has-checked:border-rose-600 has-checked:bg-rose-50/70 text-center">
                                                <input type="radio" name="delivery_time_slot" value="{{ $slot }}" {{ $index === 1 ? 'checked' : '' }} class="sr-only">
                                                <i class="far fa-clock text-rose-500 mb-1 text-sm"></i>
                                                <span class="text-xs font-bold text-gray-900">{{ $slot }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ADIM: ALICI BİLGİLERİ -->
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-200 mb-8">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-rose-600"></i> 2. Çiçeğin Ulaşacağı Alıcı Bilgileri
                                </h2>
                                <span class="text-xs text-gray-500">Teslimat Adresi</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-2">
                                    <div>
                                        <label for="recipient_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alıcı Ad Soyad *</label>
                                        <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required placeholder="Çiçeği teslim alacak kişi" class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">
                                        @error('recipient_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="recipient_phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alıcı Telefonu *</label>
                                        <input type="text" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone') }}" required placeholder="05XX XXX XX XX" class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">
                                        @error('recipient_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="region_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Teslimat İlçesi (İstanbul) *</label>
                                        <select id="region_id" name="region_id" required class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3 bg-white">
                                            <option value="">-- Çiçeğin Gideceği İlçeyi Seçin --</option>
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>İstanbul — {{ $region->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('region_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="address" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Alıcı Açık Adresi (Mahalle, Cadde, No, Daire) *</label>
                                        <textarea id="address" name="address" rows="3" required placeholder="Ev, iş yeri, hastane veya okul tam adresini yazınız..." class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">{{ old('address') }}</textarea>
                                        @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. ADIM: 💌 ÖZEL ÇİÇEK KART MESAJI -->
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-rose-200 mb-8" x-data="{
                            note: '',
                            setTemplate(text) {
                                this.note = text;
                            }
                        }">
                            <div class="bg-gradient-to-r from-rose-50 via-pink-50 to-rose-100/60 px-6 py-4 border-b border-rose-200 flex items-center justify-between">
                                <h2 class="text-base font-black text-rose-950 flex items-center gap-2">
                                    <i class="fas fa-envelope-open-text text-rose-600 text-lg"></i> 3. Çiçek Kart Mesajı (Ücretsiz Mühürlü Zarf)
                                </h2>
                                <span class="text-xs text-rose-700 font-bold bg-white px-2.5 py-1 rounded-full border border-rose-200">🎁 Ücretsiz Kart</span>
                            </div>
                            <div class="p-6">
                                <!-- Hızlı Şablon Butonları -->
                                <div class="mb-4">
                                    <span class="block text-xs font-bold text-gray-600 mb-2">Hızlı Mesaj Şablonları (Tek Tıkla Ekle):</span>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" @click="setTemplate('Doğum günün kutlu olsun, hayat sana hep senin gibi güzel çiçekler getirsin. Seni çok seviyorum!')" class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-800 font-semibold px-3 py-1.5 rounded-lg border border-rose-200 transition-colors">🎂 Doğum Günü</button>
                                        <button type="button" @click="setTemplate('Hayatıma kattığın tüm güzellikler ve sevgin için teşekkür ederim. İyi ki varsın bir tanem!')" class="text-xs bg-pink-50 hover:bg-pink-100 text-pink-800 font-semibold px-3 py-1.5 rounded-lg border border-pink-200 transition-colors">❤️ Aşk &amp; Romantik</button>
                                        <button type="button" @click="setTemplate('Yeni işinde/görevinde üstün başarılar dilerim, her şey gönlünce olsun!')" class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold px-3 py-1.5 rounded-lg border border-emerald-200 transition-colors">🎉 Tebrik &amp; Başarı</button>
                                        <button type="button" @click="setTemplate('Geçmiş olsun dileklerimle, en kısa sürede sağlığına kavuşup neşeli günlere dönmen dileğiyle!')" class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-800 font-semibold px-3 py-1.5 rounded-lg border border-amber-200 transition-colors">🌻 Geçmiş Olsun</button>
                                        <button type="button" @click="setTemplate('Seni kırdığım için çok üzgünüm. Lütfen bu çiçekler eşliğinde beni affet...') class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-800 font-semibold px-3 py-1.5 rounded-lg border border-purple-200 transition-colors">🕊️ Özür Dilerim</button>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label for="card_note" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kart Notunuz</label>
                                        <textarea id="card_note" name="card_note" x-model="note" rows="3" placeholder="Çiçeğin zarfına yazılmasını istediğiniz sevgi dolu notunuz..." class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">{{ old('card_note') }}</textarea>
                                    </div>

                                    <div>
                                        <label for="card_sender_title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Karta Yazılacak İsim / İmza (İsteğe Bağlı)</label>
                                        <input type="text" id="card_sender_title" name="card_sender_title" value="{{ old('card_sender_title') }}" placeholder="Örn: Seni Seven Biri, Sevgilin, Ailen veya Adınız..." class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. ADIM: GÖNDERİCİ BİLGİLERİ -->
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-200 mb-8">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-user-circle text-gray-600"></i> 4. Siparişi Veren (Gönderici) Bilgileri
                                </h2>
                                <span class="text-xs text-gray-500">Fatura &amp; SMS Bilgilendirmesi</span>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 gap-y-5 gap-x-4 sm:grid-cols-2">
                                    <div>
                                        <label for="customer_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Adınız Soyadınız *</label>
                                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required placeholder="Kendi adınız ve soyadınız" class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">
                                        @error('customer_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="customer_phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Telefon Numaranız *</label>
                                        <input type="text" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="Sipariş durumu SMS ile bildirilir" class="border border-gray-300 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 block w-full text-sm rounded-xl p-3">
                                        @error('customer_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="flex items-start gap-3 p-3.5 bg-rose-50/60 border border-rose-200 rounded-xl cursor-pointer hover:bg-rose-50 transition-colors">
                                            <input type="checkbox" name="is_anonymous" value="1" class="mt-1 h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300 rounded">
                                            <div>
                                                <span class="text-xs font-black text-rose-950 block">🤫 Gizli Gönderim (İsmim Alıcıya İletilmesin)</span>
                                                <span class="text-[11px] text-rose-800/90 font-medium block mt-0.5">İşaretlerseniz teslimat anında ve kart zarfında adınız gizli tutulur, sadece sürpriziniz teslim edilir.</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ÖDEME YÖNTEMİ -->
                        <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-200 mb-8">
                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-wallet text-rose-600"></i> Ödeme Yöntemi Seçin
                                </h2>
                                <span class="text-xs text-gray-400 font-bold">256-bit SSL Güvenli</span>
                            </div>
                            <div class="p-6 space-y-4">
                                @if(($siteSettings['paytr_active'] ?? '0') == '1')
                                    <label class="flex items-start p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50/40 cursor-pointer transition-all hover:bg-emerald-50/70">
                                        <input id="payment_paytr" name="payment_method" type="radio" value="paytr" checked class="mt-1 focus:ring-emerald-500 h-4 w-4 text-emerald-600 border-gray-300">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="block text-sm font-black text-gray-900 flex items-center gap-2">
                                                    <span>💳 Kredi / Banka Kartı (PayTR 3D Secure)</span>
                                                    <span class="bg-emerald-600 text-white text-[9px] px-2 py-0.5 rounded-full font-black uppercase">TAVSİYE EDİLEN</span>
                                                </span>
                                            </div>
                                            <span class="block text-xs text-gray-500 mt-1">Tüm banka ve kredi kartlarına tek çekim veya taksit imkanı.</span>
                                        </div>
                                    </label>
                                @endif

                                <label class="flex items-start p-4 rounded-2xl border border-gray-200 cursor-pointer transition-all hover:border-rose-300 hover:bg-rose-50/30">
                                    <input id="payment_cod" name="payment_method" type="radio" value="cod" {{ ($siteSettings['paytr_active'] ?? '0') != '1' ? 'checked' : '' }} class="mt-1 focus:ring-rose-500 h-4 w-4 text-rose-600 border-gray-300">
                                    <div class="ml-3 flex-1">
                                        <span class="block text-sm font-black text-gray-900">💵 Havale / EFT ile Güvenli Ödeme</span>
                                        <span class="block text-xs text-gray-500 mt-1">Siparişi verdikten sonra banka IBAN hesabımıza 30 dakika içinde kolayca ödeme yapabilirsiniz.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Sipariş Tamamlama Butonu -->
                        <div class="mt-6">
                            <button type="submit" class="w-full bg-gradient-to-r from-rose-600 to-pink-600 text-white py-4 px-6 rounded-2xl font-black text-lg hover:from-rose-700 hover:to-pink-700 shadow-lg shadow-rose-500/25 transition-all transform active:scale-98 flex items-center justify-center gap-3">
                                <span>🌸 Çiçek Siparişini Onayla</span>
                                <i class="fas fa-arrow-right text-sm"></i>
                            </button>
                            <p class="text-center text-xs text-gray-500 mt-3 flex items-center justify-center gap-2">
                                <i class="fas fa-shield-alt text-emerald-600"></i>
                                <span>256-Bit SSL Şifreleme &amp; Taze Çiçek Teslimat Güvencesi</span>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Sağ Taraf: Sipariş Özeti -->
                <div class="lg:col-span-5 mt-10 lg:mt-0">
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden sticky top-8">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-base font-black text-gray-900 flex items-center gap-2">
                                <i class="fas fa-shopping-bag text-rose-600"></i> Sipariş Özeti
                            </h2>
                        </div>
                        <div class="p-6">
                            <ul class="divide-y divide-gray-100 mb-6">
                                @foreach($cart as $item)
                                    <li class="py-3 flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            @if(!empty($item['image']))
                                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-black">🌸</div>
                                            @endif
                                            <div>
                                                <h4 class="text-xs font-bold text-gray-900 leading-tight line-clamp-2">{{ $item['name'] }}</h4>
                                                @if(!empty($item['is_custom']))
                                                    <span class="text-[10px] text-rose-600 font-bold block">✨ Kişiye Özel Tasarım</span>
                                                @endif
                                                <span class="text-xs text-gray-500">{{ $item['quantity'] }} adet &times; {{ number_format($item['price'], 0, ',', '.') }} TL</span>
                                            </div>
                                        </div>
                                        <span class="text-xs font-black text-gray-900 whitespace-nowrap">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} TL</span>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Fiyat Hesaplaması -->
                            <div class="space-y-2.5 border-t border-gray-100 pt-4 text-xs font-medium text-gray-600">
                                <div class="flex justify-between">
                                    <span>Ara Toplam</span>
                                    <span class="font-bold text-gray-900">{{ number_format($subtotal, 0, ',', '.') }} TL</span>
                                </div>
                                <div class="flex justify-between text-emerald-600">
                                    <span>İstanbul İçi Çiçek Kuryesi</span>
                                    <span class="font-bold">ÜCRETSİZ</span>
                                </div>
                                @if($discount > 0)
                                    <div class="flex justify-between text-rose-600 font-bold">
                                        <span>Kupon İndirimi</span>
                                        <span>-{{ number_format($discount, 0, ',', '.') }} TL</span>
                                    </div>
                                @endif
                                <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                    <span class="text-sm font-black text-gray-900">Toplam Tutar</span>
                                    <span class="text-xl font-black text-rose-600">{{ number_format($total, 0, ',', '.') }} TL</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
