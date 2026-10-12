<x-frontend-layout 
    title="Flora AI - Yapay Zeka Çiçek & Kart Notu Danışmanı | İstanbul Çiçek Evi"
    description="Flora yapay zeka asistanı ile sevdikleriniz için en anlamlı taze çiçekleri keşfedin, duygusal ve romantik kart notları yazdırın.">

    <div class="bg-gradient-to-b from-rose-50/60 via-pink-50/40 to-slate-50 min-h-[calc(100vh-140px)] py-4 sm:py-8 px-3 sm:px-6 lg:px-8 flex flex-col justify-center"
         x-data="arimoChat()">

        <div class="max-w-4xl mx-auto w-full flex flex-col h-[82vh] sm:h-[84vh] bg-white rounded-3xl shadow-xl border border-rose-200/80 overflow-hidden">
            
            <!-- Flora Başlık Bandı -->
            <div class="p-4 sm:p-5 bg-gradient-to-r from-rose-600 via-pink-600 to-slate-900 text-white flex items-center justify-between shadow-md shrink-0">
                <div class="flex items-center gap-3 sm:gap-3.5">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-white/20 backdrop-blur-md border border-white/25 flex items-center justify-center text-2xl sm:text-3xl shadow-inner">
                            🌸
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse" title="Çevrimiçi"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-base sm:text-xl font-black text-white tracking-tight">Flora AI</h1>
                            <span class="text-[10px] font-black uppercase tracking-wider bg-rose-400/40 text-rose-100 border border-white/20 px-2 py-0.5 rounded-full">
                                Çiçek &amp; Kart Asistanı
                            </span>
                        </div>
                        <p class="text-xs text-rose-100/90 font-medium">İstanbul Çiçek Evi Akıllı Danışmanı</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20Flora%20asistan%C4%B1n%C4%B1zdan%20destek%20ald%C4%B1m.%20Canl%C4%B1%20uzmanla%20g%C3%B6r%C3%BC%C5%9Fmek%20istiyorum." 
                       target="_blank" 
                       class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow transition-all">
                        <i class="fab fa-whatsapp text-sm sm:text-base"></i> 
                        <span class="hidden sm:inline">Canlı Çiçekçiye Bağlan</span>
                        <span class="sm:hidden">WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Sohbet Alanı (Kaydırılabilir Mesaj Penceresi) -->
            <div id="chatMessages" class="flex-1 overflow-y-auto space-y-4 p-4 sm:p-6 bg-slate-50/70">
                
                <!-- Başlangıç Karşılama Balonu -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-lg sm:text-xl shadow-sm shrink-0 border border-rose-500/20">
                        🌸
                    </div>
                    <div class="space-y-3 max-w-2xl">
                        <div class="bg-white border border-rose-100 p-4 sm:p-5 rounded-3xl rounded-tl-sm text-xs sm:text-sm text-gray-800 leading-relaxed shadow-sm">
                            <p class="font-black text-gray-900 text-sm sm:text-base mb-1.5 flex items-center gap-2">
                                <span>Merhaba! Ben Flora</span> 
                                <span class="text-rose-600">💐</span>
                            </p>
                            <p class="text-gray-700">
                                İstanbul Çiçek Evi'nin yapay zeka çiçek danışmanıyım. Özel bir gün için çiçek mi arıyorsunuz, yoksa çiçeğin yanına eklenecek kalbe dokunan bir kart notu mu yazdırmak istiyorsunuz? Size en taze önerileri sunmak için buradayım!
                            </p>
                            <p class="mt-3 text-gray-500 text-xs font-semibold flex items-center gap-1.5">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                <span>Aşağıdaki hazır konulardan birine dokunabilir veya doğrudan sorunuzu yazabilirsiniz:</span>
                            </p>
                        </div>

                        <!-- Hızlı Öneri Butonları -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            <button type="button" @click="askQuick('Sevgililer günü / Yıldönümü için en etkileyici ve romantik çiçek hangisi?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-rose-50 border border-gray-200 hover:border-rose-400 text-gray-800 hover:text-rose-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-base group-hover:scale-110 transition-transform">❤️</span>
                                <span>Aşk &amp; Romantik en iyi çiçek hangisi?</span>
                            </button>
                            <button type="button" @click="askQuick('Doğum günü için renkli ve neşeli bir buket ve kart notu önerir misin?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-pink-50 border border-gray-200 hover:border-pink-400 text-gray-800 hover:text-pink-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-base group-hover:scale-110 transition-transform">🎂</span>
                                <span>Doğum günü çiçeği ve kart notu tavsiyesi</span>
                            </button>
                            <button type="button" @click="askQuick('Yeni iş veya ofis tebriki için hangi dayanıklı saksı çiçeği uygundur?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-400 text-gray-800 hover:text-emerald-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-base group-hover:scale-110 transition-transform">🪴</span>
                                <span>Ofis &amp; Terfi için orkide veya saksı çiçeği</span>
                            </button>
                            <button type="button" @click="askQuick('Özür dilemek için samimi bir kart notu ve barışma çiçeği önerir misin?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-purple-50 border border-gray-200 hover:border-purple-400 text-gray-800 hover:text-purple-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-base group-hover:scale-110 transition-transform">🕊️</span>
                                <span>Özür ve barışma kart notu yaz</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dinamik Mesajlar -->
                <template x-for="(item, index) in messages" :key="index">
                    <div>
                        <!-- Kullanıcı Mesajı -->
                        <div x-show="item.sender === 'user'" class="flex justify-end mb-3">
                            <div class="bg-gradient-to-r from-rose-600 to-pink-600 text-white rounded-2xl rounded-tr-xs px-4 py-2.5 max-w-lg text-xs sm:text-sm font-medium shadow-sm">
                                <p x-text="item.text"></p>
                            </div>
                        </div>

                        <!-- Flora Cevabı -->
                        <div x-show="item.sender === 'bot'" class="flex items-start gap-3 mb-4">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-lg sm:text-xl shadow-sm shrink-0 border border-rose-500/20">
                                🌸
                            </div>
                            <div class="space-y-3 max-w-2xl">
                                <div class="bg-white border border-gray-200 p-4 sm:p-5 rounded-3xl rounded-tl-sm text-xs sm:text-sm text-gray-800 leading-relaxed shadow-sm">
                                    <div class="prose prose-xs sm:prose-sm max-w-none text-gray-800" x-html="item.text"></div>

                                    <!-- Aksiyon Butonu -->
                                    <div x-show="item.action_url" class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-2">
                                        <a :href="item.action_url" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow transition-all">
                                            <span x-text="item.action_text || 'İncele'"></span>
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Önerilen Çiçek Kartları -->
                                <div x-show="item.products && item.products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                    <template x-for="prod in item.products" :key="prod.id">
                                        <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-2xs hover:shadow-md transition-all flex items-center gap-3">
                                            <template x-if="prod.image">
                                                <img :src="prod.image" :alt="prod.name" class="w-14 h-14 rounded-xl object-contain bg-gray-50 p-1 shrink-0 border border-gray-100">
                                            </template>
                                            <template x-if="!prod.image">
                                                <div class="w-14 h-14 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0 font-bold">🌸</div>
                                            </template>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-xs font-bold text-gray-900 truncate" x-text="prod.name"></h4>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-xs font-black text-rose-600" x-text="prod.campaign_price || prod.price"></span>
                                                    <template x-if="prod.campaign_price">
                                                        <span class="text-[10px] text-gray-400 line-through" x-text="prod.price"></span>
                                                    </template>
                                                </div>
                                                <a :href="prod.url" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 mt-1 inline-flex items-center gap-1">
                                                    Çiçeği İncele <i class="fas fa-arrow-right text-[8px]"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Yazıyor Animasyonu -->
                <div x-show="isLoading" class="flex items-start gap-3 mb-4" x-cloak>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-lg shrink-0">
                        🌸
                    </div>
                    <div class="bg-white border border-gray-200 p-3.5 px-4 rounded-2xl rounded-tl-sm shadow-2xs flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-rose-500 animate-bounce"></div>
                        <div class="w-2 h-2 rounded-full bg-pink-500 animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 rounded-full bg-rose-400 animate-bounce" style="animation-delay: 0.4s"></div>
                        <span class="text-xs text-gray-500 font-medium ml-1">Flora çiçekleri ve kart notunu hazırlıyor...</span>
                    </div>
                </div>

            </div>

            <!-- Mesaj Gönderme Çubuğu -->
            <div class="p-3 sm:p-4 bg-white border-t border-gray-200 shrink-0">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                    <input type="text" x-model="userInput" :disabled="isLoading"
                           placeholder="Flora'ya çiçek türü veya duygusal kart notu sorun... (Örn: Sevgilime romantik kart notu yaz)"
                           class="flex-1 text-xs sm:text-sm rounded-2xl border border-gray-300 focus:border-rose-500 focus:ring-2 focus:ring-rose-500 px-4 py-3 bg-gray-50/60 transition-all">
                    <button type="submit" :disabled="isLoading || !userInput.trim()"
                            class="bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-bold p-3 sm:px-6 sm:py-3 rounded-2xl shadow-md transition-all disabled:opacity-50 flex items-center gap-2 cursor-pointer">
                        <span class="hidden sm:inline text-xs sm:text-sm font-black">Gönder</span>
                        <i class="fas fa-paper-plane text-xs sm:text-sm"></i>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <script>
        function arimoChat() {
            return {
                messages: [],
                userInput: '',
                isLoading: false,
                askQuick(text) {
                    this.userInput = text;
                    this.sendMessage();
                },
                async sendMessage() {
                    const text = this.userInput.trim();
                    if (!text || this.isLoading) return;

                    this.messages.push({ sender: 'user', text: text });
                    this.userInput = '';
                    this.isLoading = true;
                    this.scrollToBottom();

                    try {
                        const res = await fetch("{{ route('arimo.ask') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ message: text })
                        });

                        const data = await res.json();
                        let formattedReply = (data.reply || '')
                            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                            .replace(/\n\n/g, '<br><br>')
                            .replace(/\n/g, '<br>');

                        this.messages.push({
                            sender: 'bot',
                            text: formattedReply,
                            products: data.products || [],
                            action_url: data.action_url || null,
                            action_text: data.action_text || null
                        });
                    } catch (e) {
                        this.messages.push({
                            sender: 'bot',
                            text: 'Üzgünüm, şu an bağlantıda kısa bir kesinti oldu. Dilerseniz üstteki WhatsApp butonundan canlı çiçek uzmanımıza hemen ulaşabilirsiniz.',
                            products: []
                        });
                    } finally {
                        this.isLoading = false;
                        this.scrollToBottom();
                    }
                },
                scrollToBottom() {
                    this.$nextTick(() => {
                        const box = document.getElementById('chatMessages');
                        if (box) box.scrollTop = box.scrollHeight;
                    });
                }
            }
        }
    </script>
</x-frontend-layout>
