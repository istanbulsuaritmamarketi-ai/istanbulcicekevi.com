<x-frontend-layout>
    <!-- Alpine.js Çiçek Stüdyosu Motoru (Önden Yükleme) -->
    <script>
        function flowerStudioPath2(config) {
            return {
                viewMode: 'perspective', // 'perspective' (Stüdyo Cephe) or 'topdown' (90° Kuşbakışı)
                activeTab: 'flowers', // 'flowers', 'fillers', 'vases', 'greenery', 'note'
                containers: config.containers || [],
                flowers: config.flowers || [],
                fillers: config.fillers || [],
                wrappings: config.wrappings || [],
                ribbons: config.ribbons || [],
                addToCartUrl: config.addToCartUrl,
                csrfToken: config.csrfToken,

                selectedContainer: null,
                selectedRibbon: null,
                ribbonPosition: 'neck', // 'neck', 'waist', 'base'
                greeneryLevel: 'natural', // 'none', 'light', 'natural', 'lush'
                arrangementForm: 'compact', // 'compact' (Sıkı Kubbe) or 'spread' (Geniş Bohem)

                cardNote: '',
                cardSenderTitle: '',
                isSubmitting: false,
                showRecipeModal: false,

                // Yuva Durumları: slots[slotId] = flowerObject | null
                slots: {},
                // Yuva Yükseklikleri: slotHeights[slotId] = 'short' | 'medium' | 'tall'
                slotHeights: {},
                selectedSlotId: null,
                hoveredSlotId: null,
                draggedFlower: null,
                isDragging: false,

                initStudio() {
                    if (this.containers.length > 0) {
                        this.selectedContainer = this.containers.find(c => c.id === 16) || this.containers[0];
                    }
                    this.selectedRibbon = null;

                    this.initializeSlots();

                    // Referans fotoğrafa uygun başlangıç aranjmanı (Güller + Santini + Portakal Süsü)
                    if (this.flowers.length > 0) {
                        const mainRose = this.flowers[0];
                        const secondFlower = this.flowers.length > 1 ? this.flowers[1] : mainRose;
                        const santiniFiller = this.fillers.find(f => f.name.toLowerCase().includes('santini')) || (this.fillers.length > 0 ? this.fillers[0] : mainRose);
                        const orangeFiller = this.fillers.find(f => f.name.toLowerCase().includes('portakal')) || (this.fillers.length > 1 ? this.fillers[1] : secondFlower);

                        // Arka Sıra: Uzun güller (Taç Noktası)
                        this.slots[1] = mainRose;
                        this.slotHeights[1] = 'tall';
                        this.slots[2] = secondFlower;
                        this.slotHeights[2] = 'tall';
                        this.slots[3] = mainRose;
                        this.slotHeights[3] = 'tall';

                        // Orta Sıra: Güller ve aralara serpiştirilmiş yeşil Santini ponponlar
                        this.slots[4] = santiniFiller;
                        this.slotHeights[4] = 'medium';
                        this.slots[5] = mainRose;
                        this.slotHeights[5] = 'medium';
                        this.slots[6] = secondFlower;
                        this.slotHeights[6] = 'medium';
                        this.slots[7] = santiniFiller;
                        this.slotHeights[7] = 'medium';
                        this.slots[8] = mainRose;
                        this.slotHeights[8] = 'medium';

                        // Ön Sıra: Vazo ağzında dolgunluk ve portakal süsü
                        this.slots[10] = mainRose;
                        this.slotHeights[10] = 'short';
                        this.slots[11] = orangeFiller;
                        this.slotHeights[11] = 'short';
                    }
                },

                get totalSlotCount() {
                    if (!this.selectedContainer) return 12;
                    return this.selectedContainer.type === 'box' ? 19 : 12;
                },

                initializeSlots() {
                    const count = this.totalSlotCount;
                    const newSlots = {};
                    const newHeights = {};
                    for (let i = 1; i <= count; i++) {
                        newSlots[i] = this.slots[i] || null;
                        newHeights[i] = this.slotHeights[i] || this.getDefaultHeightForSlot(i);
                    }
                    this.slots = newSlots;
                    this.slotHeights = newHeights;
                },

                selectContainer(cnt) {
                    this.selectedContainer = cnt;
                    this.initializeSlots();
                },

                getDefaultHeightForSlot(slotId) {
                    // Arka sıra: tall (uzun), Orta sıra: medium (orta), Ön sıra: short (kısa)
                    if (slotId <= 3) return 'tall';
                    if (slotId <= 8) return 'medium';
                    return 'short';
                },

                // 12 Yuva (Doğal Vazo Kubbesi & Gerçekçi 2.5D Biedermeier Çiçek Düzeni)
                get currentSlots() {
                    return [
                        // 🌟 ARKA SIRA (3 Adet - Taç / Zirve Noktası - zIndex: 10)
                        { id: 1, row: 'back', rowLabel: 'Arka Sıra (Sol)', x: -55, y: 345, tilt: -8, scale: 0.98, zIndex: 10, stemHeight: 180, defaultHeight: 'tall', topDownX: 34, topDownY: 22 },
                        { id: 2, row: 'back', rowLabel: 'Arka Sıra (Merkez)', x: 0, y: 365, tilt: 0, scale: 1.02, zIndex: 10, stemHeight: 200, defaultHeight: 'tall', topDownX: 50, topDownY: 16 },
                        { id: 3, row: 'back', rowLabel: 'Arka Sıra (Sağ)', x: 55, y: 345, tilt: 8, scale: 0.98, zIndex: 10, stemHeight: 180, defaultHeight: 'tall', topDownX: 66, topDownY: 22 },

                        // 🌟 ORTA SIRA (5 Adet - Kubbe Ana Gövdesi - zIndex: 20)
                        { id: 4, row: 'mid', rowLabel: 'Orta Sıra (Sol Dış)', x: -105, y: 285, tilt: -16, scale: 0.92, zIndex: 20, stemHeight: 135, defaultHeight: 'medium', topDownX: 18, topDownY: 48 },
                        { id: 5, row: 'mid', rowLabel: 'Orta Sıra (Sol İç)', x: -52, y: 298, tilt: -7, scale: 0.96, zIndex: 20, stemHeight: 145, defaultHeight: 'medium', topDownX: 34, topDownY: 45 },
                        { id: 6, row: 'mid', rowLabel: 'Orta Sıra (Merkez)', x: 0, y: 308, tilt: 0, scale: 0.98, zIndex: 21, stemHeight: 155, defaultHeight: 'medium', topDownX: 50, topDownY: 42 },
                        { id: 7, row: 'mid', rowLabel: 'Orta Sıra (Sağ İç)', x: 52, y: 298, tilt: 7, scale: 0.96, zIndex: 20, stemHeight: 145, defaultHeight: 'medium', topDownX: 66, topDownY: 45 },
                        { id: 8, row: 'mid', rowLabel: 'Orta Sıra (Sağ Dış)', x: 105, y: 285, tilt: 16, scale: 0.92, zIndex: 20, stemHeight: 135, defaultHeight: 'medium', topDownX: 82, topDownY: 48 },

                        // 🌟 ÖN SIRA (4 Adet - Saksı Ağzına Yaslanan, Öne Açılan - zIndex: 30)
                        { id: 9,  row: 'front', rowLabel: 'Ön Sıra (Sol Dış)', x: -78, y: 215, tilt: -14, scale: 0.90, zIndex: 30, stemHeight: 80, defaultHeight: 'short', topDownX: 28, topDownY: 72 },
                        { id: 10, row: 'front', rowLabel: 'Ön Sıra (Sol)', x: -28, y: 228, tilt: -5, scale: 0.94, zIndex: 30, stemHeight: 90, defaultHeight: 'short', topDownX: 42, topDownY: 76 },
                        { id: 11, row: 'front', rowLabel: 'Ön Sıra (Sağ)', x: 28, y: 228, tilt: 5, scale: 0.94, zIndex: 30, stemHeight: 90, defaultHeight: 'short', topDownX: 58, topDownY: 76 },
                        { id: 12, row: 'front', rowLabel: 'Ön Sıra (Sağ Dış)', x: 78, y: 215, tilt: 14, scale: 0.90, zIndex: 30, stemHeight: 80, defaultHeight: 'short', topDownX: 72, topDownY: 72 }
                    ];
                },

                getFlowerHeadUrl(material) {
                    if (!material) return '';
                    const name = (material.name || '').toLowerCase();
                    if (name.includes('santini') || name.includes('ponpon') || name.includes('krizantem')) return '/images/studio/green_santini.png';
                    if (name.includes('portakal') || name.includes('tarçın')) return '/images/studio/dried_orange.png';
                    if (name.includes('papatya')) return '/images/studio/daisy.png';
                    if (name.includes('lale')) return '/images/studio/yellow_tulip.png';
                    if (name.includes('beyaz') && name.includes('gül')) return '/images/studio/white_rose.png';
                    if (name.includes('pembe') || name.includes('pudra')) return '/images/studio/pink_rose.png';
                    if (name.includes('okalipt')) return '/images/studio/eucalyptus.png';
                    if (name.includes('cipso') || name.includes('bebek nefesi')) return '/images/studio/gypsophila.png';
                    return '/images/studio/red_rose.png';
                },

                getFlowerStemUrl(material) {
                    if (!material) return '';
                    const name = (material.name || '').toLowerCase();
                    if (name.includes('santini') || name.includes('ponpon') || name.includes('krizantem')) return '/images/studio/green_santini_stem.png';
                    if (name.includes('portakal') || name.includes('tarçın')) return '/images/studio/dried_orange_stem.png';
                    if (name.includes('papatya')) return '/images/studio/daisy_stem.png';
                    if (name.includes('lale')) return '/images/studio/yellow_tulip_stem.png';
                    if (name.includes('beyaz') && name.includes('gül')) return '/images/studio/white_rose_stem.png';
                    if (name.includes('pembe') || name.includes('pudra')) return '/images/studio/pink_rose_stem.png';
                    if (name.includes('okalipt')) return '/images/studio/eucalyptus_stem.png';
                    if (name.includes('cipso') || name.includes('bebek nefesi')) return '/images/studio/gypsophila_stem.png';
                    return '/images/studio/red_rose_stem.png';
                },

                getContainerImageUrl() {
                    if (!this.selectedContainer) return '/images/studio/ceramic_pot.png';
                    const cName = (this.selectedContainer.name || '').toLowerCase();
                    if (this.selectedContainer.type === 'box' || cName.includes('kutu')) return '/images/studio/black_box.png';
                    if (cName.includes('cam')) return '/images/studio/glass_vase.png';
                    return '/images/studio/ceramic_pot.png';
                },

                // Çiçeğin Dal Yüksekliği / Boy Ayarı
                getSlotHeight(slotId) {
                    return this.slotHeights[slotId] || this.getDefaultHeightForSlot(slotId);
                },

                setSlotHeight(slotId, height) {
                    this.slotHeights[slotId] = height;
                },

                // Çiçeğe Doğrudan Tıklayınca Boyu Döndür (Orta -> Uzun -> Kısa -> Orta)
                cycleSlotHeight(slotId) {
                    const order = ['medium', 'tall', 'short'];
                    const current = this.getSlotHeight(slotId);
                    const nextIdx = (order.indexOf(current) + 1) % order.length;
                    const nextHeight = order[nextIdx];
                    this.setSlotHeight(slotId, nextHeight);
                },

                getSlotHeightOffset(slotId) {
                    const h = this.getSlotHeight(slotId);
                    if (h === 'short') return -15; // Kısa (aşağı oturur)
                    if (h === 'tall') return 18;   // Uzun (yukarı uzar)
                    return 0;                      // Orta (varsayılan)
                },

                getSlotHeightLabel(slotId) {
                    const h = this.getSlotHeight(slotId);
                    if (h === 'short') return 'Kısa (40cm)';
                    if (h === 'tall') return 'Uzun (60cm)';
                    return 'Orta (50cm)';
                },

                getSlotHeightBadge(slotId) {
                    const h = this.getSlotHeight(slotId);
                    if (h === 'short') return 'K';
                    if (h === 'tall') return 'U';
                    return 'O';
                },

                // Sürükle-Bırak Olayları
                startDragFlower(e, flower) {
                    this.isDragging = true;
                    this.draggedFlower = flower;
                    e.dataTransfer.setData('text/plain', JSON.stringify(flower));
                    e.dataTransfer.effectAllowed = 'copy';
                },

                endDragFlower() {
                    this.isDragging = false;
                    this.hoveredSlotId = null;
                },

                onDragOverSlot(e, slotId) {
                    this.hoveredSlotId = slotId;
                },

                onDragLeaveSlot(e, slotId) {
                    if (this.hoveredSlotId === slotId) {
                        this.hoveredSlotId = null;
                    }
                },

                onDropSlot(e, slotId) {
                    let flower = this.draggedFlower;
                    if (!flower && e.dataTransfer) {
                        try {
                            const data = e.dataTransfer.getData('text/plain');
                            if (data) flower = JSON.parse(data);
                        } catch (err) {
                            console.error(err);
                        }
                    }

                    if (flower && flower.id) {
                        this.placeFlowerInSlot(slotId, flower);
                    }

                    this.hoveredSlotId = null;
                    this.isDragging = false;
                },

                selectSlot(slotId) {
                    this.selectedSlotId = (this.selectedSlotId === slotId) ? null : slotId;
                },

                handleFlowerClick(flower) {
                    if (this.selectedSlotId) {
                        this.placeFlowerInSlot(this.selectedSlotId, flower);
                        this.selectNextEmptySlot();
                        return;
                    }

                    const nextEmpty = this.findNextEmptySlot();
                    if (nextEmpty) {
                        this.placeFlowerInSlot(nextEmpty, flower);
                    } else {
                        alert('Tüm yuvalar dolu! Bir çiçeği çıkarmak veya boyunu değiştirmek için vazodaki çiçeğin üzerine tıklayabilirsiniz.');
                    }
                },

                placeFlowerInSlot(slotId, flower) {
                    this.slots[slotId] = flower;
                    if (!this.slotHeights[slotId]) {
                        this.slotHeights[slotId] = this.getDefaultHeightForSlot(slotId);
                    }
                },

                clearSlot(slotId) {
                    this.slots[slotId] = null;
                    if (this.selectedSlotId === slotId) this.selectedSlotId = null;
                },

                clearAllSlots() {
                    for (const id of Object.keys(this.slots)) {
                        this.slots[id] = null;
                    }
                    this.selectedSlotId = null;
                },

                findNextEmptySlot() {
                    const count = this.totalSlotCount;
                    for (let i = 1; i <= count; i++) {
                        if (!this.slots[i]) return i;
                    }
                    return null;
                },

                selectNextEmptySlot() {
                    this.selectedSlotId = this.findNextEmptySlot();
                },

                autoFillEmptySlots() {
                    if (this.flowers.length === 0) return;
                    const defaultFlower = this.flowers[0];
                    const count = this.totalSlotCount;
                    for (let i = 1; i <= count; i++) {
                        if (!this.slots[i]) {
                            this.placeFlowerInSlot(i, defaultFlower);
                        }
                    }
                },

                get occupiedSlotsCount() {
                    return Object.values(this.slots).filter(fl => fl !== null).length;
                },

                get FlowerCountInSlots() {
                    return (flowerId) => Object.values(this.slots).filter(fl => fl && fl.id == flowerId).length;
                },

                getFlowerCountInSlots(flowerId) {
                    return Object.values(this.slots).filter(fl => fl && fl.id == flowerId).length;
                },

                get flowersSubtotal() {
                    let total = 0;
                    for (const fl of Object.values(this.slots)) {
                        if (fl && fl.unit_price) {
                            total += parseFloat(fl.unit_price);
                        }
                    }
                    return total;
                },

                get greenerySubtotal() {
                    const map = { none: 0, light: 35, natural: 65, lush: 110 };
                    return map[this.greeneryLevel] || 0;
                },

                get greeneryLevelLabel() {
                    const map = { none: 'Yok (0 TL)', light: 'Hafif (+35 TL)', natural: 'Doğal (+65 TL)', lush: 'Dolgun (+110 TL)' };
                    return map[this.greeneryLevel] || 'Doğal';
                },

                getGreeneryScale() {
                    if (this.occupiedSlotsCount === 0) return 0;
                    const spread = (this.arrangementForm === 'spread') ? 1.15 : 1.0;
                    const map = { none: 0, light: 0.85, natural: 1.0, lush: 1.22 };
                    return (map[this.greeneryLevel] || 1.0) * spread;
                },

                getGreeneryOpacity() {
                    const map = { none: 0, light: 0.7, natural: 0.95, lush: 1.0 };
                    return map[this.greeneryLevel] || 0.95;
                },

                get totalPrice() {
                    let price = 0;
                    if (this.selectedContainer) price += parseFloat(this.selectedContainer.unit_price);
                    if (this.selectedRibbon) price += parseFloat(this.selectedRibbon.unit_price || 0);
                    price += this.flowersSubtotal;
                    price += this.greenerySubtotal;
                    return price;
                },

                get ribbonPositionLabel() {
                    const map = { neck: 'Boyun', waist: 'Gövde', base: 'Taban' };
                    return map[this.ribbonPosition] || 'Boyun';
                },

                get recipeMaterialList() {
                    const summary = {};
                    for (const fl of Object.values(this.slots)) {
                        if (!fl) continue;
                        if (!summary[fl.id]) {
                            summary[fl.id] = { id: fl.id, name: fl.name, color_hex: fl.color_hex, quantity: 0 };
                        }
                        summary[fl.id].quantity++;
                    }
                    return Object.values(summary);
                },

                formatMoney(amount) {
                    return new Intl.NumberFormat('tr-TR', { maximumFractionDigits: 0 }).format(amount || 0);
                },

                printRecipe() {
                    window.print();
                },

                async submitToCart() {
                    if (this.occupiedSlotsCount === 0) {
                        alert('Lütfen vazonuza en az 1 dal çiçek ekleyin.');
                        return;
                    }

                    this.isSubmitting = true;

                    // Çiçek sayıları sözlüğü
                    const flowerCounts = {};
                    for (const fl of Object.values(this.slots)) {
                        if (fl) {
                            flowerCounts[fl.id] = (flowerCounts[fl.id] || 0) + 1;
                        }
                    }

                    // Yuva haritası nesnesi (boy/yükseklik bilgisi ile birlikte)
                    const slotsData = {};
                    for (const [sId, fl] of Object.entries(this.slots)) {
                        if (fl) {
                            slotsData[sId] = {
                                slot_number: parseInt(sId),
                                flower_id: fl.id,
                                name: fl.name,
                                color_hex: fl.color_hex,
                                height: this.getSlotHeight(sId),
                                height_label: this.getSlotHeightLabel(sId),
                            };
                        }
                    }

                    try {
                        const payload = {
                            container_id: this.selectedContainer ? this.selectedContainer.id : null,
                            flowers: flowerCounts,
                            fillers: {},
                            slots: slotsData,
                            greenery_level: this.greeneryLevel,
                            arrangement_form: this.arrangementForm,
                            ribbon_id: this.selectedRibbon ? this.selectedRibbon.id : null,
                            ribbon_position: this.ribbonPosition,
                            card_note: this.cardNote,
                            card_sender_title: this.cardSenderTitle,
                            custom_title: (this.selectedContainer ? this.selectedContainer.name : 'Vazo') + ' — ' + this.occupiedSlotsCount + ' Dallı Özel Tasarım',
                        };

                        const response = await fetch(this.addToCartUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await response.json();

                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            alert(data.message || 'Bir hata oluştu.');
                        }
                    } catch (error) {
                        console.error('Hata:', error);
                        alert('Tasarım sepete eklenirken bir bağlantı hatası oluştu.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
        window.flowerStudioPath2 = flowerStudioPath2;
    </script>

    <div class="bg-gradient-to-b from-stone-100 via-rose-50/30 to-stone-200/60 text-gray-900 min-h-screen py-4 sm:py-6 select-none"
         x-data="flowerStudioPath2({
             containers: {{ Js::from($containers) }},
             flowers: {{ Js::from($flowers) }},
             fillers: {{ Js::from($fillers) }},
             wrappings: {{ Js::from($wrappings) }},
             ribbons: {{ Js::from($ribbons) }},
             addToCartUrl: '{{ route('flower.studio.addToCart') }}',
             csrfToken: '{{ csrf_token() }}'
         })"
         x-init="initStudio()">

        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            
            <!-- ======================================================== -->
            <!-- 🌟 1. ÜST KONTROL ÇUBUĞU (AÇI GEÇİŞİ, REÇETE, ÖZET)      -->
            <!-- ======================================================== -->
            <div class="bg-white/95 backdrop-blur-md border border-stone-200/90 rounded-3xl p-3 sm:p-4 shadow-xl shadow-stone-200/50 mb-4 flex flex-col md:flex-row items-center justify-between gap-3">
                
                <!-- Sol: Görünüm Değiştirici (📸 Stüdyo Cephe vs. 🗺️ Kuşbakışı) -->
                <div class="flex items-center gap-2 w-full md:w-auto justify-between md:justify-start">
                    <div class="inline-flex p-1 rounded-2xl bg-stone-100 border border-stone-200">
                        <button type="button" @click="viewMode = 'perspective'"
                                :class="viewMode === 'perspective' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'text-stone-700 hover:text-rose-600'"
                                class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-camera"></i>
                            <span>📸 Stüdyo Cephe</span>
                        </button>
                        <button type="button" @click="viewMode = 'topdown'"
                                :class="viewMode === 'topdown' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'text-stone-700 hover:text-rose-600'"
                                class="px-3.5 sm:px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-bullseye"></i>
                            <span>🗺️ 90° Kuşbakışı</span>
                        </button>
                    </div>

                    <!-- Hızlı İşlemler: Usta Reçetesi & Temizle -->
                    <div class="flex items-center gap-1.5">
                        <button type="button" @click="showRecipeModal = true"
                                class="px-3 py-2 rounded-xl text-xs font-black bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-xs transition-all flex items-center gap-1.5 cursor-pointer"
                                title="Ustaya verilecek hazırlama şemasını görüntüle">
                            <i class="fas fa-clipboard-list text-rose-600"></i>
                            <span class="hidden sm:inline">Usta Reçetesi</span>
                        </button>

                        <button type="button" @click="clearAllSlots()"
                                class="p-2 rounded-xl text-xs font-bold bg-stone-100 hover:bg-stone-200 text-stone-600 border border-stone-200 transition-all cursor-pointer"
                                title="Tüm Çiçekleri Temizle">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Sağ: Canlı Durum, Yuva Doluluğu & Tutar -->
                <div class="flex items-center justify-between md:justify-end gap-4 w-full md:w-auto border-t md:border-t-0 pt-2 md:pt-0 border-stone-100">
                    <!-- Yuva Doluluk Rozeti -->
                    <div class="flex items-center gap-2">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block tracking-wider">Yuva Durumu</span>
                            <span class="text-xs font-black text-stone-800">
                                <span class="text-rose-600" x-text="occupiedSlotsCount"></span>
                                <span class="text-stone-400" x-text="' / ' + totalSlotCount + ' Yuva'"></span>
                            </span>
                        </div>
                        <div class="w-16 sm:w-20 bg-stone-200 h-2 rounded-full overflow-hidden border border-stone-300">
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-rose-500 transition-all duration-300"
                                 :style="'width:' + Math.min(100, (occupiedSlotsCount / totalSlotCount) * 100) + '%;'"></div>
                        </div>
                    </div>

                    <!-- Canlı Tutar -->
                    <div class="pl-3 border-l border-stone-200 text-right">
                        <span class="text-[10px] uppercase font-bold text-stone-400 block tracking-wider">Tahmini Tutar</span>
                        <span class="text-lg sm:text-xl font-black text-rose-600" x-text="formatMoney(totalPrice) + ' TL'"></span>
                    </div>
                </div>

            </div>


            <!-- ======================================================== -->
            <!-- 🌟 2. MERKEZ: 2.5D HİBRİT VAZO VE ÇİÇEK TASARIM STÜDYOSU -->
            <!-- ======================================================== -->
            <div class="relative bg-gradient-to-b from-stone-200 via-stone-100 to-stone-200/90 rounded-3xl border-2 border-stone-300/80 shadow-2xl overflow-hidden flex flex-col items-center justify-center select-none h-[520px] sm:h-[560px]">

                <!-- Florist Stüdyo Sıcak Işık Efekti -->
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_35%,rgba(255,255,255,0.95),rgba(231,229,228,0.5)_60%,rgba(214,211,209,0.8)_100%)] pointer-events-none"></div>

                <!-- 🌟 GÖRÜNÜM 1: 📸 STÜDYO CEPHE (DOĞAL BİEDERMEİER KUBBESİ) -->
                <div x-show="viewMode === 'perspective'" 
                     class="relative w-full h-full max-w-xl mx-auto flex items-center justify-center">

                    <!-- Masa & Zemin Yumuşak Temas Gölgesi -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-72 sm:w-80 h-10 bg-stone-900/25 rounded-full blur-md z-0 pointer-events-none"></div>

                    <!-- Vazo Ağzı Sarkan Yeşillik Çelengi (Foliage Collar) -->
                    <img src="/images/studio/leaves_collar.png" 
                         alt="Yaprak Çelengi" 
                         class="absolute bottom-[150px] sm:bottom-[160px] left-1/2 -translate-x-1/2 w-72 sm:w-80 h-32 sm:h-36 object-contain pointer-events-none opacity-90 z-26">

                    <!-- Vazo / Saksı / Kutu Gövdesi (z-index: 25 - Ön Çiçeklerin Arkasında, Orta/Arka Sapların Önünde) -->
                    <img :src="getContainerImageUrl()" 
                         :alt="selectedContainer ? selectedContainer.name : 'Vazo'"
                         class="absolute left-1/2 -translate-x-1/2 bottom-4 h-44 sm:h-48 object-contain pointer-events-none drop-shadow-xl z-25 transition-all duration-300">

                    <!-- 12 Yuva (Arka: z-10, Orta: z-20, Ön: z-30) -->
                    <template x-for="slot in currentSlots" :key="'slot-' + slot.id">
                        <div class="absolute flex flex-col items-center transition-all duration-300 pointer-events-auto"
                             :style="'left: calc(50% + ' + slot.x + 'px); bottom: ' + (slot.y + getSlotHeightOffset(slot.id)) + 'px; z-index: ' + slot.zIndex + '; transform: translate(-50%, 50%) rotate(' + slot.tilt + 'deg) scale(' + slot.scale + ');'">

                            <!-- DOLU YUVA (Çiçek Başı + Doğal Sap) -->
                            <div x-show="slots[slot.id]"
                                 class="relative flex flex-col items-center cursor-pointer group select-none"
                                 @click="cycleSlotHeight(slot.id)">

                                <!-- Çiçek Başı (Yüksek Çözünürlüklü Gerçek Fotoğraf) -->
                                <img :src="slots[slot.id] ? getFlowerHeadUrl(slots[slot.id]) : ''" 
                                     :alt="slots[slot.id] ? slots[slot.id].name : ''"
                                     class="w-20 h-20 sm:w-24 sm:h-24 object-contain filter drop-shadow-md group-hover:scale-105 group-active:scale-95 transition-transform relative z-10">
                                
                                <!-- Çiçek Dalı / Sapı (Saksı Ağzına Uzanan Botanik Sap) -->
                                <img :src="slots[slot.id] ? getFlowerStemUrl(slots[slot.id]) : ''" 
                                     alt="Dal"
                                     :style="'height: ' + slot.stemHeight + 'px;'"
                                     class="w-4 sm:w-5 object-contain absolute top-[60%] left-1/2 -translate-x-1/2 pointer-events-none opacity-90 z-0">

                                <!-- Hover'da Boy Bilgisi Rozeti -->
                                <div class="absolute -top-7 z-40 bg-stone-900/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity shadow-md pointer-events-none flex items-center gap-1">
                                    <span x-text="getSlotHeightLabel(slot.id)"></span>
                                    <span class="text-rose-400">↺</span>
                                </div>

                                <!-- Çiçeği Çıkar Butonu (Hover'da Sağ Üst) -->
                                <button type="button" 
                                        @click.stop="clearSlot(slot.id)" 
                                        class="absolute -top-2 -right-2 z-40 w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-rose-700 cursor-pointer"
                                        title="Çiçeği Çıkar">
                                    ×
                                </button>
                            </div>

                            <!-- BOŞ YUVA (Numaralı Tıklanabilir Rozet) -->
                            <div x-show="!slots[slot.id]"
                                 @click="selectSlot(slot.id)"
                                 @dragover.prevent="hoveredSlotId = slot.id"
                                 @dragleave="hoveredSlotId = null"
                                 @drop.prevent="onDropSlot($event, slot.id)"
                                 :class="selectedSlotId === slot.id ? 'border-rose-600 bg-rose-100/90 ring-4 ring-rose-400/40 scale-110' : (hoveredSlotId === slot.id ? 'border-rose-500 bg-rose-50 scale-105' : 'border-rose-400/70 bg-white/90 hover:border-rose-600 hover:bg-white')"
                                 class="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-dashed flex flex-col items-center justify-center text-rose-600 shadow-md transition-all cursor-pointer backdrop-blur-xs select-none"
                                 title="Çiçek eklemek için tıklayın veya sürükleyin">
                                <span class="text-xs font-black" x-text="slot.id"></span>
                                <span class="text-[8px] font-bold opacity-70">+</span>
                            </div>
                        </div>
                    </template>
                </div>


                <!-- 🌟 GÖRÜNÜM 2: 🗺️ 90° KUŞBAKIŞI (OASIS SÜNGERİ & DAĞILIM PLANI) -->
                <div x-show="viewMode === 'topdown'" 
                     class="relative w-full h-full flex items-center justify-center">

                    <!-- Oasis Süngeri Yuvarlak Tablası -->
                    <div class="relative w-72 h-72 sm:w-84 sm:h-84 rounded-full bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-950 border-4 border-emerald-700/80 shadow-2xl flex items-center justify-center select-none">
                        
                        <!-- Oasis Rehber Çizgileri -->
                        <div class="absolute inset-5 rounded-full border border-dashed border-emerald-600/30 pointer-events-none"></div>
                        <div class="absolute inset-16 rounded-full border border-dashed border-emerald-600/40 pointer-events-none"></div>
                        <div class="absolute text-[9px] uppercase tracking-widest font-black text-emerald-500/30 pointer-events-none">OASIS KUBBE PLANI</div>

                        <!-- 12 Yuva (Kuşbakışı) -->
                        <template x-for="slot in currentSlots" :key="'td-' + slot.id">
                            <div class="absolute -translate-x-1/2 -translate-y-1/2 transition-all duration-300"
                                 :style="'left: ' + slot.topDownX + '%; top: ' + slot.topDownY + '%; z-index: ' + slot.zIndex + ';'">
                                
                                <!-- Dolu Yuva: Üstten Çiçek Yüzü -->
                                <div x-show="slots[slot.id]"
                                     class="relative flex flex-col items-center cursor-pointer group"
                                     @click="cycleSlotHeight(slot.id)">
                                    <img :src="slots[slot.id] ? getFlowerHeadUrl(slots[slot.id]) : ''"
                                         :alt="slots[slot.id] ? slots[slot.id].name : ''"
                                         class="w-16 h-16 sm:w-20 sm:h-20 object-contain filter drop-shadow-lg group-hover:scale-110 group-active:scale-95 transition-transform">
                                    
                                    <!-- Boy Rozeti -->
                                    <div class="absolute -bottom-2 z-40 bg-stone-900/90 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity shadow-md pointer-events-none">
                                        <span x-text="getSlotHeightLabel(slot.id)"></span>
                                    </div>

                                    <!-- Sil Butonu -->
                                    <button type="button"
                                            @click.stop="clearSlot(slot.id)"
                                            class="absolute -top-1 -right-1 z-40 w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md hover:bg-rose-700 cursor-pointer"
                                            title="Çiçeği Çıkar">
                                        ×
                                    </button>
                                </div>

                                <!-- Boş Yuva -->
                                <div x-show="!slots[slot.id]"
                                     @click="selectSlot(slot.id)"
                                     @dragover.prevent="hoveredSlotId = slot.id"
                                     @dragleave="hoveredSlotId = null"
                                     @drop.prevent="onDropSlot($event, slot.id)"
                                     :class="selectedSlotId === slot.id ? 'border-rose-400 bg-rose-500/40 ring-4 ring-rose-400/50 scale-110' : (hoveredSlotId === slot.id ? 'border-emerald-300 bg-emerald-700/60 scale-105' : 'border-emerald-400/60 bg-emerald-900/60 hover:border-white hover:bg-emerald-800/80')"
                                     class="w-11 h-11 sm:w-12 sm:h-12 rounded-full border-2 border-dashed flex flex-col items-center justify-center text-white shadow-md transition-all cursor-pointer">
                                    <span class="text-xs font-black" x-text="slot.id"></span>
                                    <span class="text-[8px] font-bold opacity-80">+</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Aktif Seçili / Üzerine Gelinen Çiçek / Yuva Bilgi Çubuğu (Alt Orta) -->
                <div x-show="selectedSlotId || hoveredSlotId" 
                     x-transition
                     class="absolute bottom-3 z-40 bg-stone-900/90 text-white text-xs font-black px-4 py-2 rounded-2xl shadow-xl border border-stone-700/80 backdrop-blur-xs flex items-center gap-3 pointer-events-none">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] flex items-center justify-center font-black" 
                              x-text="hoveredSlotId || selectedSlotId"></span>
                        <span x-text="slots[hoveredSlotId || selectedSlotId] ? slots[hoveredSlotId || selectedSlotId].name : 'Boş Yuva (Çiçek Ekleyin)'"></span>
                    </div>
                    <template x-if="slots[hoveredSlotId || selectedSlotId]">
                        <div class="flex items-center gap-2 border-l border-stone-700 pl-3 text-stone-300 text-[11px] font-medium">
                            <span class="text-rose-400 font-bold" x-text="getSlotHeightLabel(hoveredSlotId || selectedSlotId)"></span>
                            <span>· Çiçeğe tıkla: Boy değişir</span>
                        </div>
                    </template>
                </div>

                <!-- Katman: Asılı Müşteri Kart Notu (Sağ Alt) -->
                <div class="absolute bottom-4 right-4 z-30 pointer-events-auto transform transition-all hover:scale-105"
                     x-show="cardNote.trim().length > 0">
                    <div class="bg-amber-50 border-2 border-amber-300 text-amber-950 p-3 rounded-2xl shadow-xl w-36 sm:w-44 transform rotate-3 font-serif">
                        <div class="text-[9px] font-black uppercase text-amber-800 tracking-wider mb-1 flex items-center justify-between font-sans">
                            <span>💌 Kart Notu</span>
                            <i class="fas fa-heart text-[8px] text-rose-600"></i>
                        </div>
                        <p class="text-[11px] leading-tight italic line-clamp-3" x-text="'“' + cardNote + '”'"></p>
                        <div class="text-[9px] font-bold text-amber-900 text-right mt-1 font-sans" x-text="cardSenderTitle ? '— ' + cardSenderTitle : ''"></div>
                    </div>
                </div>

            </div>


            <!-- ======================================================== -->
            <!-- 🗂️ 3. ALT SEÇİM ÇEKMECESİ (DAL ÇİÇEKLER, VAZO, VB.)     -->
            <!-- ======================================================== -->
            <div class="bg-white/95 backdrop-blur-md border border-stone-200/90 rounded-3xl p-4 sm:p-5 shadow-xl shadow-stone-200/50 mt-4 space-y-4">
                
                <!-- Alt Tab Butonları -->
                <div class="flex items-center justify-between border-b border-stone-100 pb-3 overflow-x-auto gap-2 scrollbar-none">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <button type="button" @click="activeTab = 'flowers'"
                                :class="activeTab === 'flowers' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
                                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                            <span>🌸 Ana Çiçekler</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" 
                                  :class="activeTab === 'flowers' ? 'bg-white/25 text-white' : 'bg-stone-200 text-stone-600'"
                                  x-text="flowers.length"></span>
                        </button>

                        <button type="button" @click="activeTab = 'fillers'"
                                :class="activeTab === 'fillers' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
                                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                            <span>🌾 Süs Çiçekleri & Dokular</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" 
                                  :class="activeTab === 'fillers' ? 'bg-white/25 text-white' : 'bg-stone-200 text-stone-600'"
                                  x-text="fillers.length"></span>
                        </button>

                        <button type="button" @click="activeTab = 'vases'"
                                :class="activeTab === 'vases' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
                                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                            <span>🏺 Vazo / Kap</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" 
                                  :class="activeTab === 'vases' ? 'bg-white/25 text-white' : 'bg-stone-200 text-stone-600'"
                                  x-text="containers.length"></span>
                        </button>

                        <button type="button" @click="activeTab = 'greenery'"
                                :class="activeTab === 'greenery' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
                                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                            <span>🌿 Yeşillik &amp; Form</span>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full" 
                                  :class="activeTab === 'greenery' ? 'bg-white/25 text-white' : 'bg-stone-200 text-stone-600'"
                                  x-text="greeneryLevelLabel"></span>
                        </button>

                        <button type="button" @click="activeTab = 'note'"
                                :class="activeTab === 'note' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700'"
                                class="px-3.5 sm:px-4 py-2 rounded-2xl text-xs font-black transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                            <span>💌 Kart Notu</span>
                            <span x-show="cardNote.length > 0" class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        </button>
                    </div>

                    <!-- Otomatik Kalanları Doldur Butonu -->
                    <button type="button" @click="autoFillEmptySlots()"
                            :disabled="occupiedSlotsCount >= totalSlotCount"
                            class="hidden md:flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 disabled:opacity-40 cursor-pointer">
                        <i class="fas fa-magic text-rose-500"></i>
                        <span>Kalan Yuvaları Doldur</span>
                    </button>
                </div>

                <!-- ================= TAB 1: TAZE DAL ÇİÇEKLER (SÜRÜKLE-BIRAK VE TIKLA) ================= -->
                <div x-show="activeTab === 'flowers'" x-transition class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-stone-500 font-semibold px-1">
                        <div class="flex items-center gap-2">
                            <span>💡 Çiçeği dalından tutup vazoya sürükleyin veya tıklayarak ekleyin.</span>
                            <button type="button" @click="activeTab = 'fillers'" 
                                    class="text-emerald-700 hover:text-emerald-800 font-black text-xs flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-lg border border-emerald-200 transition-colors cursor-pointer">
                                <span>🌾 Süs Çiçekleri (Santini, Cipso, Portakal...)</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                        <span x-show="selectedSlotId" class="text-rose-600 font-black" x-text="'Seçili: Yuva ' + selectedSlotId + ' (Tıklayınca buraya eklenecek)'"></span>
                    </div>

                    <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                        <template x-for="fl in flowers" :key="'fl-' + fl.id">
                            <div draggable="true"
                                 @dragstart="startDragFlower($event, fl)"
                                 @dragend="endDragFlower()"
                                 @click="handleFlowerClick(fl)"
                                 class="shrink-0 w-40 sm:w-48 p-3 rounded-2xl border-2 transition-all flex flex-col justify-between bg-white shadow-xs cursor-grab active:cursor-grabbing hover:border-rose-400 hover:shadow-md group"
                                 :class="getFlowerCountInSlots(fl.id) > 0 ? 'border-rose-400 bg-rose-50/30 ring-1 ring-rose-500/20' : 'border-stone-200'">
                                
                                <!-- DALLI ÇİÇEK ÖNİZLEMESİ (Dal + Çiçek Başı) -->
                                <div class="w-full h-36 rounded-xl flex items-center justify-center shrink-0 shadow-inner overflow-hidden border p-1 bg-stone-50 border-stone-200 group-hover:scale-102 transition-transform">
                                    <img :src="fl.stem_image_url || fl.image_url" :alt="fl.name" class="h-full object-contain filter drop-shadow-xs">
                                </div>

                                <div class="mt-2 min-w-0">
                                    <h4 class="text-xs font-black text-stone-900 leading-tight truncate" x-text="fl.name"></h4>
                                    <div class="flex items-center justify-between text-[10px] mt-1">
                                        <span class="font-black text-rose-600 text-xs" x-text="formatMoney(fl.unit_price) + ' TL'"></span>
                                        <span class="text-stone-500 font-semibold" x-text="'Stok: ' + fl.daily_stock"></span>
                                    </div>
                                </div>

                                <!-- Alt Çubuk: Sürükle Rozeti & Hızlı + Ekle Butonu -->
                                <div class="mt-2.5 pt-2 border-t border-stone-100 flex items-center justify-between text-xs">
                                    <span class="text-[10px] font-bold text-stone-400 flex items-center gap-1">
                                        <i class="fas fa-arrows-alt text-[9px] text-stone-400"></i>
                                        <span>Dalından Sürükle</span>
                                    </span>

                                    <div class="flex items-center gap-1.5">
                                        <span x-show="getFlowerCountInSlots(fl.id) > 0" 
                                              class="text-[10px] font-black text-rose-600 bg-rose-100 px-1.5 py-0.5 rounded-md" 
                                              x-text="getFlowerCountInSlots(fl.id) + ' Dal'"></span>

                                        <button type="button" @click.stop="handleFlowerClick(fl)"
                                                :disabled="occupiedSlotsCount >= totalSlotCount"
                                                class="w-7 h-7 rounded-lg bg-rose-600 hover:bg-rose-700 disabled:opacity-30 text-white flex items-center justify-center text-xs font-bold cursor-pointer transition-colors shadow-xs"
                                                title="Sıradaki yuvaya ekle">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ================= TAB 2: SÜS ÇİÇEKLERİ & DOKULAR (SANTİNİ, CİPSO, PORTAKAL, OKALİPTÜS) ================= -->
                <div x-show="activeTab === 'fillers'" x-transition class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-stone-500 font-semibold px-1">
                        <div class="flex items-center gap-2">
                            <span>💡 Çiçeklerin arasına dolgunluk katmak için süs çiçeklerini istediğiniz yuvaya sürükleyin veya tıklayın.</span>
                            <button type="button" @click="activeTab = 'flowers'" 
                                    class="text-rose-700 hover:text-rose-800 font-black text-xs flex items-center gap-1 bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded-lg border border-rose-200 transition-colors cursor-pointer">
                                <span>🌸 Ana Çiçeklere Dön</span>
                            </button>
                        </div>
                        <span x-show="selectedSlotId" class="text-emerald-700 font-black" x-text="'Seçili: Yuva ' + selectedSlotId + ' (Tıklayınca buraya eklenecek)'"></span>
                    </div>

                    <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                        <template x-for="fl in fillers" :key="'filler-' + fl.id">
                            <div draggable="true"
                                 @dragstart="startDragFlower($event, fl)"
                                 @dragend="endDragFlower()"
                                 @click="handleFlowerClick(fl)"
                                 class="shrink-0 w-40 sm:w-48 p-3 rounded-2xl border-2 transition-all flex flex-col justify-between bg-white shadow-xs cursor-grab active:cursor-grabbing hover:border-emerald-500 hover:shadow-md group"
                                 :class="getFlowerCountInSlots(fl.id) > 0 ? 'border-emerald-500 bg-emerald-50/30 ring-1 ring-emerald-500/20' : 'border-stone-200'">
                                
                                <!-- DALLI SÜS ÇİÇEĞİ ÖNİZLEMESİ -->
                                <div class="w-full h-36 rounded-xl flex items-center justify-center shrink-0 shadow-inner overflow-hidden border p-1 bg-stone-50 border-stone-200 group-hover:scale-102 transition-transform">
                                    <img :src="fl.stem_image_url || fl.image_url" :alt="fl.name" class="h-full object-contain filter drop-shadow-xs">
                                </div>

                                <div class="mt-2 min-w-0">
                                    <h4 class="text-xs font-black text-stone-900 leading-tight truncate" x-text="fl.name"></h4>
                                    <div class="flex items-center justify-between text-[10px] mt-1">
                                        <span class="font-black text-emerald-700 text-xs" x-text="formatMoney(fl.unit_price) + ' TL'"></span>
                                        <span class="text-stone-500 font-semibold" x-text="'Stok: ' + fl.daily_stock"></span>
                                    </div>
                                </div>

                                <!-- Alt Çubuk: Sürükle Rozeti & Hızlı + Ekle Butonu -->
                                <div class="mt-2.5 pt-2 border-t border-stone-100 flex items-center justify-between text-xs">
                                    <span class="text-[10px] font-bold text-stone-400 flex items-center gap-1">
                                        <i class="fas fa-arrows-alt text-[9px] text-stone-400"></i>
                                        <span>Dalından Sürükle</span>
                                    </span>

                                    <div class="flex items-center gap-1.5">
                                        <span x-show="getFlowerCountInSlots(fl.id) > 0" 
                                              class="text-[10px] font-black text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded-md" 
                                              x-text="getFlowerCountInSlots(fl.id) + ' Dal'"></span>

                                        <button type="button" @click.stop="handleFlowerClick(fl)"
                                                :disabled="occupiedSlotsCount >= totalSlotCount"
                                                class="w-7 h-7 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-30 text-white flex items-center justify-center text-xs font-bold cursor-pointer transition-colors shadow-xs"
                                                title="Sıradaki yuvaya ekle">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ================= TAB 2: VAZOLAR & KAPLAR ================= -->
                <div x-show="activeTab === 'vases'" x-transition class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                    <template x-for="cnt in containers" :key="'cnt-' + cnt.id">
                        <div @click="selectContainer(cnt)"
                             class="shrink-0 w-48 sm:w-56 p-3.5 rounded-2xl border-2 cursor-pointer transition-all relative flex flex-col justify-between bg-white shadow-xs"
                             :class="selectedContainer && selectedContainer.id === cnt.id ? 'border-rose-600 bg-rose-50/40 ring-2 ring-rose-500/20 shadow-md' : 'border-stone-200 hover:border-rose-300'">
                            
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 rounded-xl bg-stone-50 border border-stone-200 flex items-center justify-center shrink-0 overflow-hidden p-1 shadow-inner">
                                    <template x-if="cnt.image_url">
                                        <img :src="cnt.image_url" :alt="cnt.name" class="w-full h-full object-contain">
                                    </template>
                                    <template x-if="!cnt.image_url">
                                        <div class="text-2xl">
                                            <template x-if="cnt.type === 'box'"><span>🎁</span></template>
                                            <template x-if="cnt.type === 'bouquet'"><span>💐</span></template>
                                            <template x-if="cnt.type === 'vase'"><span>🏺</span></template>
                                        </div>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xs font-black text-stone-900 leading-tight line-clamp-2" x-text="cnt.name"></h4>
                                    <p class="text-[10px] text-stone-500 font-semibold mt-1" x-text="'Kapasite: ' + (cnt.type === 'box' ? 19 : 12) + ' Dal'"></p>
                                </div>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-stone-100 flex items-center justify-between text-xs">
                                <span class="text-stone-500 text-[11px] font-medium" x-text="cnt.daily_stock > 0 ? ('Stok: ' + cnt.daily_stock) : 'Tükendi'"></span>
                                <span class="font-black text-rose-600 text-sm" x-text="formatMoney(cnt.unit_price) + ' TL'"></span>
                            </div>

                            <div x-show="selectedContainer && selectedContainer.id === cnt.id" 
                                 class="absolute top-2 right-2 w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] shadow-sm">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ================= TAB 3: YEŞİLLİK VE FORM AYARLARI ================= -->
                <div x-show="activeTab === 'greenery'" x-transition class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Yeşillik Yoğunluğu -->
                        <div>
                            <span class="text-xs font-bold text-stone-800 block mb-2">🌿 Yeşillik &amp; Dolgu Yoğunluğu:</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" @click="greeneryLevel = 'none'"
                                        :class="greeneryLevel === 'none' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Yok</span>
                                    <span class="block text-[10px] font-normal opacity-80">+0 TL</span>
                                </button>
                                <button type="button" @click="greeneryLevel = 'light'"
                                        :class="greeneryLevel === 'light' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Hafif</span>
                                    <span class="block text-[10px] font-normal opacity-80">+35 TL</span>
                                </button>
                                <button type="button" @click="greeneryLevel = 'natural'"
                                        :class="greeneryLevel === 'natural' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Doğal</span>
                                    <span class="block text-[10px] font-normal opacity-80">+65 TL</span>
                                </button>
                                <button type="button" @click="greeneryLevel = 'lush'"
                                        :class="greeneryLevel === 'lush' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Dolgun</span>
                                    <span class="block text-[10px] font-normal opacity-80">+110 TL</span>
                                </button>
                            </div>
                        </div>

                        <!-- Aranjman Formu (Sıkı Kubbe vs Geniş Bohem) -->
                        <div>
                            <span class="text-xs font-bold text-stone-800 block mb-2">📐 Aranjman Dizilim Formu:</span>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="arrangementForm = 'compact'"
                                        :class="arrangementForm === 'compact' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-3 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Sıkı Kubbe (Biedermeier)</span>
                                    <span class="block text-[10px] font-normal opacity-80">Derli toplu klasik yuvarlak</span>
                                </button>
                                <button type="button" @click="arrangementForm = 'spread'"
                                        :class="arrangementForm === 'spread' ? 'bg-rose-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2.5 px-3 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    <span>Geniş / Bohem</span>
                                    <span class="block text-[10px] font-normal opacity-80">Doğal, ferah ve yayvan</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 4: KURDELE & BAĞLAMA ================= -->
                <div x-show="activeTab === 'ribbon'" x-transition class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-xs font-bold text-stone-700 block mb-2">🎀 Kurdele Rengi:</span>
                            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                                <template x-for="rbn in ribbons" :key="'rbn-' + rbn.id">
                                    <button type="button" @click="selectedRibbon = rbn"
                                            class="shrink-0 px-3.5 py-2 rounded-xl border-2 flex items-center gap-2 text-xs font-bold transition-all cursor-pointer bg-white"
                                            :class="selectedRibbon && selectedRibbon.id === rbn.id ? 'border-rose-600 bg-rose-50 text-rose-950 shadow-xs' : 'border-stone-200 text-stone-700 hover:border-stone-300'">
                                        <span class="w-4 h-4 rounded-full border shadow-2xs shrink-0" :style="'background-color:' + rbn.color_hex"></span>
                                        <span x-text="rbn.name"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-bold text-stone-700 block mb-2">📍 Kurdele Bağlama Noktası:</span>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="ribbonPosition = 'neck'"
                                        :class="ribbonPosition === 'neck' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    Vazo Boynu
                                </button>
                                <button type="button" @click="ribbonPosition = 'waist'"
                                        :class="ribbonPosition === 'waist' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    Orta Gövde
                                </button>
                                <button type="button" @click="ribbonPosition = 'base'"
                                        :class="ribbonPosition === 'base' ? 'bg-gradient-to-r from-rose-600 to-pink-600 text-white shadow-md' : 'bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-200'"
                                        class="py-2 px-2 rounded-xl text-xs font-black transition-all text-center cursor-pointer">
                                    Taban Kısmı
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 5: KART NOTU ================= -->
                <div x-show="activeTab === 'note'" x-transition class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-bold text-stone-700">Özel Kart Notunuz</label>
                                <span class="text-[10px] text-stone-400 font-semibold" x-text="cardNote.length + ' / 300'"></span>
                            </div>
                            <textarea x-model="cardNote" rows="2" maxlength="300"
                                      placeholder="Sevgilim, hayatıma kattığın tüm güzellikler için teşekkürler..."
                                      class="w-full text-xs p-3 rounded-2xl bg-stone-50 border border-stone-300 text-stone-900 placeholder-stone-400 focus:border-rose-500 focus:ring-1 focus:ring-rose-500"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-stone-700 block mb-1">Kart İmzası (Gönderici Adı)</label>
                            <input type="text" x-model="cardSenderTitle" placeholder="Örn: Seni Çok Seven Eşin"
                                   class="w-full text-xs p-2.5 rounded-xl bg-stone-50 border border-stone-300 text-stone-900 placeholder-stone-400 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 mb-2">
                            <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
                                <button type="button" @click="cardNote = 'İyi ki doğdun birtanem, yeni yaşın sana tüm mutlulukları getirsin!'" class="text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 px-2.5 py-1 rounded-lg shrink-0">🎂 Doğum Günü</button>
                                <button type="button" @click="cardNote = 'Gözlerin gibi güzel bu çiçekler kalbimin bir parçası, seni çok seviyorum...'" class="text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 px-2.5 py-1 rounded-lg shrink-0">❤️ Romantik Aşk</button>
                                <button type="button" @click="cardNote = 'Yeni görevinde ve hayatında sana sonsuz başarılar dilerim!'" class="text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 px-2.5 py-1 rounded-lg shrink-0">💐 Tebrik</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= ALT FİYAT VE SEPETE EKLE ÇUBUĞU ================= -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-3 border-t border-stone-100">
                    <div class="flex items-center gap-3 text-xs text-stone-600 flex-wrap">
                        <div>Vazo: <strong class="text-stone-900" x-text="formatMoney(selectedContainer ? selectedContainer.unit_price : 0) + ' TL'"></strong></div>
                        <div>Çiçekler: <strong class="text-stone-900" x-text="formatMoney(flowersSubtotal) + ' TL'"></strong></div>
                        <div>Yeşillik: <strong class="text-stone-900" x-text="formatMoney(greenerySubtotal) + ' TL'"></strong></div>
                        <div x-show="selectedRibbon">Kurdele: <strong class="text-stone-900" x-text="selectedRibbon.name + ' (' + ribbonPositionLabel + ')'"></strong></div>
                    </div>

                    <div class="flex items-center gap-3 self-end sm:self-auto">
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block tracking-wider">Toplam Tutar</span>
                            <span class="text-2xl font-black text-rose-600" x-text="formatMoney(totalPrice) + ' TL'"></span>
                        </div>

                        <button type="button" @click="submitToCart()" 
                                :disabled="occupiedSlotsCount === 0 || isSubmitting"
                                class="bg-gradient-to-r from-rose-600 via-pink-600 to-rose-700 hover:from-rose-700 hover:to-pink-700 disabled:opacity-40 text-white font-black text-sm px-6 py-3.5 rounded-2xl shadow-lg shadow-rose-500/25 transition-all transform active:scale-98 flex items-center gap-2.5 cursor-pointer">
                            <i class="fas fa-shopping-bag" :class="{'fa-spin': isSubmitting}"></i>
                            <span x-text="isSubmitting ? 'Sepete Ekleniyor...' : '🌸 Tasarımı Sepete Ekle'"></span>
                        </button>
                    </div>
                </div>

            </div>

        </div>


        <!-- ======================================================== -->
        <!-- 👨‍🌾 4. USTA HAZIRLAMA REÇETESİ MODALI (YAZDIRILABİLİR FİŞ) -->
        <!-- ======================================================== -->
        <div x-show="showRecipeModal" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-stone-900/70 backdrop-blur-xs">
            
            <div @click.away="showRecipeModal = false"
                 class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-stone-200 p-5 sm:p-8 space-y-6">
                
                <!-- Reçete Başlığı & Kapat Butonu -->
                <div class="flex items-center justify-between border-b border-stone-200 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-rose-100 text-rose-800">ATÖLYE ÜRETİM FİŞİ</span>
                            <span class="text-xs font-mono text-stone-400" x-text="'REC-' + Math.floor(Date.now() / 1000)"></span>
                        </div>
                        <h3 class="text-lg font-black text-stone-900 mt-1" x-text="(selectedContainer ? selectedContainer.name : 'Vazo') + ' — Çiçekçi Hazırlama Reçetesi'"></h3>
                    </div>
                    <button type="button" @click="showRecipeModal = false" class="text-stone-400 hover:text-stone-700 text-lg p-2 cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- 1. Özellikler & Form Özeti -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-stone-50 p-4 rounded-2xl border border-stone-200 text-xs">
                    <div>
                        <span class="text-stone-400 font-bold block text-[10px] uppercase">Sunum / Kap</span>
                        <strong class="text-stone-800" x-text="selectedContainer ? selectedContainer.name : 'Cam Vazo'"></strong>
                    </div>
                    <div>
                        <span class="text-stone-400 font-bold block text-[10px] uppercase">Toplam Dal</span>
                        <strong class="text-rose-600" x-text="occupiedSlotsCount + ' Dal'"></strong>
                    </div>
                    <div>
                        <span class="text-stone-400 font-bold block text-[10px] uppercase">Yeşillik Yoğunluğu</span>
                        <strong class="text-emerald-700" x-text="greeneryLevelLabel"></strong>
                    </div>
                    <div>
                        <span class="text-stone-400 font-bold block text-[10px] uppercase">Dizilim Formu</span>
                        <strong class="text-stone-800" x-text="arrangementForm === 'spread' ? 'Geniş / Bohem' : 'Sıkı Kubbe (Biedermeier)'"></strong>
                    </div>
                </div>

                <!-- 2. Malzeme Dal Sayım Kontrol Tablosu -->
                <div>
                    <h4 class="text-xs font-black text-stone-700 uppercase tracking-wider mb-2">📋 Çiçek Malzeme Sayımı:</h4>
                    <div class="space-y-1.5 bg-stone-50 p-3 rounded-2xl border border-stone-200">
                        <template x-for="item in recipeMaterialList" :key="'rec-mat-' + item.id">
                            <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-white border border-stone-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-3.5 h-3.5 rounded-full border" :style="'background-color:' + (item.color_hex || '#e11d48')"></span>
                                    <strong class="text-stone-900" x-text="item.name"></strong>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-rose-600" x-text="item.quantity + ' Dal'"></span>
                                    <span class="text-stone-300">|</span>
                                    <span class="text-[11px] text-stone-400 font-mono">[ ] Hazırlandı</span>
                                </div>
                            </div>
                        </template>

                        <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-white border border-stone-100">
                            <div class="flex items-center gap-2">
                                <span>🎀</span>
                                <strong class="text-stone-900">Kurdele:</strong>
                                <span x-text="selectedRibbon ? (selectedRibbon.name + ' (' + ribbonPositionLabel + ')') : 'Seçilmedi'"></span>
                            </div>
                            <span class="text-[11px] text-stone-400 font-mono">[ ] Bağlandı</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Oasis Yuva Konumlandırma Haritası (Boy / Yükseklik ile birlikte) -->
                <div>
                    <h4 class="text-xs font-black text-stone-700 uppercase tracking-wider mb-2">🗺️ Yuva Bazlı Konumlandırma &amp; Dal Boyu Haritası:</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <template x-for="slot in currentSlots" :key="'rec-slot-' + slot.id">
                            <div class="p-2.5 rounded-xl border text-xs flex items-center gap-2.5"
                                 :class="slots[slot.id] ? 'bg-rose-50/50 border-rose-200 text-rose-950' : 'bg-stone-50 border-stone-200 text-stone-400'">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black shrink-0"
                                      :class="slots[slot.id] ? 'bg-rose-600 text-white' : 'bg-stone-200 text-stone-600'"
                                      x-text="slot.id"></span>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold truncate text-[11px]" x-text="slots[slot.id] ? slots[slot.id].name : 'Boş Yuva'"></div>
                                    <div class="text-[9px] text-stone-500 font-medium flex items-center justify-between">
                                        <span x-text="slot.rowLabel"></span>
                                        <span x-show="slots[slot.id]" class="font-bold text-rose-700" x-text="getSlotHeightLabel(slot.id)"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 4. Kart Notu -->
                <div x-show="cardNote.trim().length > 0" class="bg-amber-50 p-4 rounded-2xl border border-amber-200 text-amber-950 font-serif">
                    <div class="text-[10px] font-sans font-black uppercase text-amber-800 mb-1">💌 Müşteri Kart Notu:</div>
                    <p class="italic text-xs leading-relaxed" x-text="'“' + cardNote + '”'"></p>
                    <div class="text-[10px] font-bold text-amber-900 text-right mt-1 font-sans" x-text="cardSenderTitle ? '— ' + cardSenderTitle : ''"></div>
                </div>

                <!-- Alt Butonlar -->
                <div class="flex items-center justify-between border-t border-stone-200 pt-4">
                    <button type="button" @click="showRecipeModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-stone-600 hover:bg-stone-100 cursor-pointer">
                        Kapat
                    </button>
                    <button type="button" @click="printRecipe()"
                            class="px-5 py-2.5 rounded-xl text-xs font-black bg-stone-900 hover:bg-black text-white shadow-md flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-print"></i>
                        <span>Yazdır (Fiş Çıktısı Al)</span>
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- Yazdırma Stili (Fiş & Usta Reçetesi Çıktısı İçin) -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .fixed.inset-0, .fixed.inset-0 * {
                visibility: visible;
            }
            .fixed.inset-0 {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white !important;
                padding: 0 !important;
            }
            button {
                display: none !important;
            }
        }
    </style>
</x-frontend-layout>
