@php
    $roseProduct = \App\Models\Product::where('category_id', 1)->first() ?? \App\Models\Product::first();
    $orchidProduct = \App\Models\Product::where('category_id', 3)->first() ?? \App\Models\Product::first();
    $boxProduct = \App\Models\Product::where('category_id', 2)->first() ?? \App\Models\Product::first();
@endphp

<div class="bg-gradient-to-br from-rose-950 via-rose-900 to-pink-950 text-white rounded-3xl p-6 sm:p-12 shadow-2xl border border-rose-800/80 my-12 relative overflow-hidden"
     x-data="{
        step: 1,
        occasion: 'love',
        recipient: 'partner',
        style: 'rose',
        matchedProduct: null,
        products: [
            {
                id: {{ $roseProduct ? $roseProduct->id : 1 }},
                name: '{{ $roseProduct ? addslashes($roseProduct->name) : '101 Kırmızı Gül Premium Buket' }}',
                tag: 'En Romantik Tercih',
                price: '{{ $roseProduct ? number_format($roseProduct->price, 2, ',', '.') . ' TL' : '1.450 TL' }}',
                desc: 'Aşkınızı ve tutkunuzu anlatmanın en etkileyici yolu. Taze kırmızı ekvator gülleri ve şık ambalajıyla.',
                features: ['101 İthal Kırmızı Gül', 'Özel Vellum Ambalaj', 'Ücretsiz Flora AI Kart Notu', 'Aynı Gün Teslimat'],
                link: '{{ $roseProduct ? route('product.show', $roseProduct->slug) : route('products.index') }}',
                image: '{{ $roseProduct && $roseProduct->image_path ? asset('storage/' . $roseProduct->image_path) : '' }}'
            },
            {
                id: {{ $orchidProduct ? $orchidProduct->id : 2 }},
                name: '{{ $orchidProduct ? addslashes($orchidProduct->name) : 'Çift Dallı Asil Beyaz Orkide' }}',
                tag: 'Zarafet & Tebrik',
                price: '{{ $orchidProduct ? number_format($orchidProduct->price, 2, ',', '.') . ' TL' : '890 TL' }}',
                desc: 'Yeni iş, açılış veya doğum günleri için uzun ömürlü ve zarif seramik saksılı beyaz orkide.',
                features: ['2 Dallı Beyaz Phalaenopsis', 'Seramik Tasarım Saksı', 'Bakım Rehberi Broşürü', 'Hızlı Kurye'],
                link: '{{ $orchidProduct ? route('product.show', $orchidProduct->slug) : route('products.index') }}',
                image: '{{ $orchidProduct && $orchidProduct->image_path ? asset('storage/' . $orchidProduct->image_path) : '' }}'
            },
            {
                id: {{ $boxProduct ? $boxProduct->id : 3 }},
                name: '{{ $boxProduct ? addslashes($boxProduct->name) : 'Kutuda Kırmızı Gül & Çikolata' }}',
                tag: 'Lüks Aranjman Kutusu',
                price: '{{ $boxProduct ? number_format($boxProduct->price, 2, ',', '.') . ' TL' : '1.150 TL' }}',
                desc: 'Silindir kadife kutuda taze güller ve el yapımı leziz çikolatalar ile eksiksiz bir kutlama sürprizi.',
                features: ['Silindir Lüks Kutu', 'Taze Güller & Çikolata', 'Sürpriz Gizli Gönderim Seçeneği', 'Aynı Gün Teslimat'],
                link: '{{ $boxProduct ? route('product.show', $boxProduct->slug) : route('products.index') }}',
                image: '{{ $boxProduct && $boxProduct->image_path ? asset('storage/' . $boxProduct->image_path) : '' }}'
            }
        ],
        calculateResult() {
            if (this.occasion === 'work' || this.recipient === 'colleague') {
                this.matchedProduct = this.products[1];
            } else if (this.occasion === 'birthday' || this.style === 'box') {
                this.matchedProduct = this.products[2];
            } else {
                this.matchedProduct = this.products[0];
            }
            this.step = 4;
        },
        resetQuiz() {
            this.step = 1;
            this.matchedProduct = null;
        }
     }">

    <!-- Dekoratif Arka Plan Işıkları -->
    <div class="absolute -right-20 -top-20 w-80 h-80 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 max-w-4xl mx-auto">
        
        <!-- Başlık & İlerleme Çubuğu -->
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 bg-rose-500/30 text-rose-200 text-xs font-black px-4 py-1.5 rounded-full border border-rose-400/30 mb-3 shadow-inner">
                <i class="fas fa-magic"></i> Akıllı Çiçek Sihirbazı
            </span>
            <h3 class="text-2xl sm:text-4xl font-black tracking-tight text-white">
                Sevdiklerinize En Uygun Çiçeği <span class="text-pink-300">1 Dakikada Bulun</span>
            </h3>
            <p class="text-rose-200/80 text-xs sm:text-sm mt-2 max-w-xl mx-auto">
                3 basit soruya yanıt verin; gönderim amacınıza, alıcınıza ve tarzınıza en uygun buketi ücretsiz kart notuyla keşfedin.
            </p>

            <!-- Adım Göstergesi (Step Indicator) -->
            <div class="flex items-center justify-center gap-3 mt-6">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 1 ? 'bg-pink-400 text-gray-950 shadow-lg shadow-pink-400/50' : 'bg-rose-900 text-rose-300'">1</div>
                <div class="w-10 sm:w-16 h-1 rounded-full transition-all" :class="step >= 2 ? 'bg-pink-400' : 'bg-rose-900'"></div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 2 ? 'bg-pink-400 text-gray-950 shadow-lg shadow-pink-400/50' : 'bg-rose-900 text-rose-300'">2</div>
                <div class="w-10 sm:w-16 h-1 rounded-full transition-all" :class="step >= 3 ? 'bg-pink-400' : 'bg-rose-900'"></div>
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-all"
                     :class="step >= 3 ? 'bg-pink-400 text-gray-950 shadow-lg shadow-pink-400/50' : 'bg-rose-900 text-rose-300'">3</div>
            </div>
        </div>

        <!-- 1. ADIM: Gönderim Amacı -->
        <div x-show="step === 1" x-transition>
            <h4 class="text-center font-bold text-base sm:text-lg text-rose-100 mb-6">
                1. Çiçeği hangi özel gün veya his için gönderiyorsunuz?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button type="button" @click="occasion = 'love'; step = 2"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Aşk &amp; Romantizm / Yıldönümü</div>
                    <div class="text-xs text-rose-200/70">Kırmızı güller, tutku ve derin sevgiyi ifade eden buketler.</div>
                </button>

                <button type="button" @click="occasion = 'birthday'; step = 2"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-birthday-cake"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Doğum Günü &amp; Kutlama</div>
                    <div class="text-xs text-rose-200/70">Renkli, enerjik tasarım aranjmanlar ve kutuda çiçekler.</div>
                </button>

                <button type="button" @click="occasion = 'work'; step = 2"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Yeni İş, Tebrik &amp; Teşekkür</div>
                    <div class="text-xs text-rose-200/70">Zarif beyaz orkideler, saksı bitkileri ve şık vazolar.</div>
                </button>
            </div>
        </div>

        <!-- 2. ADIM: Alıcı Kim -->
        <div x-show="step === 2" x-transition x-cloak>
            <h4 class="text-center font-bold text-base sm:text-lg text-rose-100 mb-6">
                2. Bu güzel sürpriz kime gidiyor?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button type="button" @click="recipient = 'partner'; step = 3"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-kiss-wink-heart"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Sevgiliye / Eşe</div>
                    <div class="text-xs text-rose-200/70">Duygusal, etkileyici ve unutulmaz anlar için.</div>
                </button>

                <button type="button" @click="recipient = 'family'; step = 3"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Anneye / Aileye</div>
                    <div class="text-xs text-rose-200/70">Sıcak, içten ve uzun ömürlü taze çiçek tasarımları.</div>
                </button>

                <button type="button" @click="recipient = 'colleague'; step = 3"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Arkadaşa / İş Arkadaşına</div>
                    <div class="text-xs text-rose-200/70">Asil, şık ve ofis masasına uyumlu bitki &amp; çiçekler.</div>
                </button>
            </div>
            <div class="text-center mt-6">
                <button type="button" @click="step = 1" class="text-xs font-bold text-rose-300 hover:text-white underline">
                    <i class="fas fa-arrow-left mr-1"></i> Önceki Adıma Dön
                </button>
            </div>
        </div>

        <!-- 3. ADIM: Tercih Edilen Tarz -->
        <div x-show="step === 3" x-transition x-cloak>
            <h4 class="text-center font-bold text-base sm:text-lg text-rose-100 mb-6">
                3. Hangi çiçek formunu tercih edersiniz?
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <button type="button" @click="style = 'rose'; calculateResult()"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-spa"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Klasik Taze Buket &amp; Güller</div>
                    <div class="text-xs text-rose-200/70">Kucak dolusu taze çiçek ambalajı ve kurdeleli tasarım.</div>
                </button>

                <button type="button" @click="style = 'box'; calculateResult()"
                        class="p-6 bg-white/10 hover:bg-rose-600/60 border border-rose-400/30 hover:border-pink-400 rounded-2xl text-center transition-all group flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/20 flex items-center justify-center text-pink-300 text-2xl mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="font-extrabold text-sm text-white mb-1">Kutuda Çiçek veya Orkide / Saksı</div>
                    <div class="text-xs text-rose-200/70">Kalıcı lüks kutuda veya saksıda uzun ömürlü asil çiçekler.</div>
                </button>
            </div>
            <div class="text-center mt-6">
                <button type="button" @click="step = 2" class="text-xs font-bold text-rose-300 hover:text-white underline">
                    <i class="fas fa-arrow-left mr-1"></i> Önceki Adıma Dön
                </button>
            </div>
        </div>

        <!-- 4. ADIM: SONUÇ EKRANI (MATCHED PRODUCT) -->
        <div x-show="step === 4" x-transition x-cloak>
            <div class="bg-white text-gray-900 rounded-3xl p-6 sm:p-8 shadow-2xl border border-gray-100 max-w-2xl mx-auto">
                <div class="flex items-center justify-between gap-4 mb-4 pb-3 border-b border-gray-100">
                    <span class="inline-flex items-center gap-1.5 bg-rose-100 text-rose-800 text-xs font-black px-3.5 py-1.5 rounded-full">
                        <i class="fas fa-check-circle"></i> %100 Size Özel Çiçek Önerisi
                    </span>
                    <span class="text-xs font-extrabold text-rose-600" x-text="matchedProduct?.tag"></span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6 my-4">
                    <div class="w-32 h-32 bg-rose-50 rounded-2xl p-2 flex items-center justify-center flex-shrink-0 overflow-hidden">
                        <template x-if="matchedProduct?.image">
                            <img :src="matchedProduct?.image" :alt="matchedProduct?.name" class="object-cover h-full w-full rounded-xl">
                        </template>
                        <template x-if="!matchedProduct?.image">
                            <i class="fas fa-spa text-5xl text-rose-600"></i>
                        </template>
                    </div>
                    <div class="text-center sm:text-left flex-1">
                        <h4 class="text-lg font-black text-gray-900 leading-snug" x-text="matchedProduct?.name"></h4>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed" x-text="matchedProduct?.desc"></p>
                        
                        <div class="flex flex-wrap gap-1.5 mt-3 justify-center sm:justify-start">
                            <template x-for="feat in matchedProduct?.features" :key="feat">
                                <span class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2 py-0.5 rounded-md" x-text="feat"></span>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Fiyat ve Sipariş Butonları -->
                <div class="pt-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <div class="text-xs text-gray-400 font-bold">Aynı Gün Teslimat Fiyatı:</div>
                        <div class="text-2xl font-black text-rose-600" x-text="matchedProduct?.price"></div>
                        <div class="text-[10px] text-emerald-600 font-extrabold">✓ Ücretsiz Özel Kart Notu Dahil</div>
                    </div>

                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                        <template x-if="matchedProduct?.link">
                            <a :href="matchedProduct?.link" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black px-4 py-3 rounded-xl shadow transition-colors">
                                <i class="fas fa-shopping-bag text-sm"></i> Ürünü Gör &amp; Sipariş Ver
                            </a>
                        </template>
                        <a :href="'https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=' + encodeURIComponent('Merhaba, web sitenizdeki çiçek sihirbazı bana ' + matchedProduct?.name + ' önerdi. Sipariş vermek istiyorum.')"
                           target="_blank"
                           class="p-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold transition-colors" title="WhatsApp Destek">
                            <i class="fab fa-whatsapp text-base"></i>
                        </a>
                        <button type="button" @click="resetQuiz()" class="p-3 text-gray-400 hover:text-gray-600 text-xs font-bold" title="Yeniden Başla">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
