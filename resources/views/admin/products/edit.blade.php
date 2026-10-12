<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight">
                    {{ __('Ürünü Düzenle: ') }} <span class="text-blue-600">{{ $product->name }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Ürün bilgilerini, fiyatını, görsellerini ve detaylı metinlerini güncelleyin</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fas fa-external-link-alt"></i> Sitede Gör
                </a>
                <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900 text-xs font-bold bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Listeye Dön
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Quill.js Zengin Görsel Metin Editörü -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            padding: 0.75rem;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #cbd5e1;
            font-family: inherit;
            font-size: 0.875rem;
            background-color: #ffffff;
            min-height: 200px;
        }
        .ql-editor {
            min-height: 200px;
            line-height: 1.8 !important;
            font-size: 0.925rem;
            color: #1e293b;
            white-space: pre-wrap !important;
        }
        .ql-editor p {
            margin-bottom: 0.85rem !important;
        }
        .ql-editor ul, .ql-editor ol {
            margin-bottom: 0.85rem !important;
            padding-left: 1.5rem !important;
        }
        .ql-editor li {
            margin-bottom: 0.35rem !important;
        }
        .ql-editor h1, .ql-editor h2, .ql-editor h3, .ql-editor h4 {
            font-weight: 800 !important;
            color: #0f172a !important;
            margin-top: 1.25rem !important;
            margin-bottom: 0.5rem !important;
        }
        .ql-editor strong {
            font-weight: 700 !important;
            color: #0f172a !important;
        }
    </style>

    <div class="py-8"
         x-init="initStudio()"
         x-data="{
            aiLoading: false,
            fieldLoading: {
                all: false,
                short_description: false,
                description: false,
                technical_features: false,
                seo: false
            },
            aiInstruction: '',
            successMsg: '',
            errorMsg: '',

            // 📸 Gelişmiş Görsel Yönetimi & Çiçek Stüdyosu
            imagesList: [
                @if($product->image_path)
                {
                    id: 'cover_{{ $product->id }}',
                    url: '{{ asset('storage/' . $product->image_path) }}',
                    file: null,
                    path: '{{ $product->image_path }}',
                    name: 'Mevcut Ana Kapak',
                    size: 'Kayıtlı Kapak',
                    dimensions: '',
                    dbId: null,
                    isExistingCover: true
                },
                @endif
                @foreach($product->images as $gImg)
                {
                    id: 'gallery_{{ $gImg->id }}',
                    url: '{{ asset('storage/' . $gImg->image_path) }}',
                    file: null,
                    path: '{{ $gImg->image_path }}',
                    name: 'Galeri Fotoğrafı #{{ $loop->iteration }}',
                    size: 'Mevcut Galeri',
                    dimensions: '',
                    dbId: {{ $gImg->id }},
                    isExistingCover: false
                },
                @endforeach
            ],
            isDragging: false,
            urlInput: '',
            urlLoading: false,

            initStudio() {
                // Ctrl+V ile Pano Yapıştırma Dinleyicisi
                window.addEventListener('paste', (e) => {
                    if (['INPUT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
                    const items = (e.clipboardData || window.clipboardData)?.items;
                    if (!items) return;
                    const files = [];
                    for (let i = 0; i < items.length; i++) {
                        if (items[i].type && items[i].type.indexOf('image') !== -1) {
                            const f = items[i].getAsFile();
                            if (f) files.push(f);
                        }
                    }
                    if (files.length > 0) {
                        this.handleFiles(files);
                    }
                });
            },

            async handleFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type || !file.type.startsWith('image/')) continue;
                    const item = await this.optimizeImage(file);
                    this.imagesList.push(item);
                }
                this.syncInputs();
            },

            optimizeImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const maxDim = 1600;
                            let w = img.width;
                            let h = img.height;
                            if (w > maxDim || h > maxDim) {
                                if (w > h) {
                                    h = Math.round((h * maxDim) / w);
                                    w = maxDim;
                                } else {
                                    w = Math.round((w * maxDim) / h);
                                    h = maxDim;
                                }
                            }
                            const canvas = document.createElement('canvas');
                            canvas.width = w;
                            canvas.height = h;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, w, h);
                            canvas.toBlob((blob) => {
                                const optimizedFile = new File([blob || file], file.name.replace(/\.[^/.]+$/, '') + '.webp', {
                                    type: 'image/webp'
                                });
                                resolve({
                                    id: 'img_' + Math.random().toString(36).substr(2, 9),
                                    url: URL.createObjectURL(blob || file),
                                    file: optimizedFile,
                                    path: null,
                                    name: optimizedFile.name,
                                    size: ((blob ? blob.size : file.size) / 1024).toFixed(0) + ' KB',
                                    dimensions: w + 'x' + h,
                                    dbId: null,
                                    isExistingCover: false
                                });
                            }, 'image/webp', 0.88);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            },

            addImageFromPath(path, assetUrl, name = 'Görsel', isFirst = false) {
                const item = {
                    id: 'path_' + Math.random().toString(36).substr(2, 9),
                    url: assetUrl,
                    file: null,
                    path: path,
                    name: name,
                    size: 'Sunucu',
                    dimensions: '',
                    dbId: null,
                    isExistingCover: false
                };
                if (isFirst) {
                    this.imagesList.unshift(item);
                } else {
                    this.imagesList.push(item);
                }
                this.syncInputs();
            },

            fetchFromUrl() {
                if (!this.urlInput) return;
                this.urlLoading = true;
                this.errorMsg = '';
                fetch('{{ route('admin.products.fetchImageUrl') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ url: this.urlInput })
                })
                .then(r => r.json())
                .then(data => {
                    this.urlLoading = false;
                    if (data.success) {
                        this.addImageFromPath(data.image_path, data.image_asset, 'URL Görseli');
                        this.urlInput = '';
                        this.successMsg = '✅ ' + data.message;
                    } else {
                        this.errorMsg = data.message || 'Görsel URL\'den indirilemedi.';
                    }
                })
                .catch(err => {
                    this.urlLoading = false;
                    this.errorMsg = 'Hata: ' + err.message;
                });
            },

            makeCover(idx) {
                if (idx <= 0 || idx >= this.imagesList.length) return;
                const item = this.imagesList[idx];

                // Eğer veritabanındaki bir galeri görseli ise API ile hemen kapak yap
                if (item.dbId) {
                    fetch('/admin/products/{{ $product->id }}/images/' + item.dbId + '/set-cover', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const oldCover = this.imagesList[0];
                            if (oldCover && oldCover.isExistingCover) {
                                oldCover.isExistingCover = false;
                                oldCover.dbId = data.swapped_gallery_id || null;
                                if (data.swapped_gallery_url) oldCover.url = data.swapped_gallery_url;
                                oldCover.name = 'Galeri Fotoğrafı';
                                oldCover.size = 'Mevcut Galeri';
                            }
                            item.isExistingCover = true;
                            item.dbId = null;
                            item.name = 'Ana Kapak Görseli';
                            item.size = 'Mevcut Kapak';
                            if (data.new_cover_url) item.url = data.new_cover_url;

                            this.imagesList.splice(idx, 1);
                            this.imagesList.unshift(item);
                            this.syncInputs();
                            this.successMsg = '👑 ' + (data.message || 'Kapak fotoğrafı güncellendi.');
                        } else {
                            this.errorMsg = data.message || 'İşlem başarısız oldu.';
                        }
                    })
                    .catch(err => {
                        this.errorMsg = 'Hata: ' + err.message;
                    });
                    return;
                }

                // Yeni yüklenen görsel ise doğrudan listenin başına al
                const moved = this.imagesList.splice(idx, 1)[0];
                this.imagesList.unshift(moved);
                this.syncInputs();
                this.successMsg = '👑 Seçilen görsel ana kapak yapıldı! Form güncellenince kaydedilecektir.';
            },

            moveImage(idx, dir) {
                const target = idx + dir;
                if (target < 0 || target >= this.imagesList.length) return;
                const item = this.imagesList.splice(idx, 1)[0];
                this.imagesList.splice(target, 0, item);
                this.syncInputs();
            },

            removeImage(idx) {
                const item = this.imagesList[idx];
                if (!item) return;

                if (item.isExistingCover) {
                    if (!confirm('Ürünün ana kapak fotoğrafını silmek istediğinize emin misiniz?')) return;
                    fetch('{{ route('admin.products.cover.destroy', $product) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.imagesList.splice(idx, 1);
                            this.syncInputs();
                            this.successMsg = '🗑️ ' + (data.message || 'Ana kapak görseli kaldırıldı.');
                        } else {
                            this.errorMsg = data.message || 'Kapak resmi kaldırılamadı.';
                        }
                    })
                    .catch(err => {
                        this.errorMsg = 'Hata: ' + err.message;
                    });
                    return;
                }

                if (item.dbId) {
                    if (!confirm('Bu galeri fotoğrafını silmek istediğinize emin misiniz?')) return;
                    fetch('/admin/products/{{ $product->id }}/images/' + item.dbId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.imagesList.splice(idx, 1);
                            this.syncInputs();
                            this.successMsg = '🗑️ ' + (data.message || 'Galeri fotoğrafı silindi.');
                        } else {
                            this.errorMsg = data.message || 'Galeri fotoğrafı silinemedi.';
                        }
                    })
                    .catch(err => {
                        this.errorMsg = 'Hata: ' + err.message;
                    });
                    return;
                }

                // Yeni dosya veya URL
                if (item.url && item.url.startsWith('blob:')) {
                    URL.revokeObjectURL(item.url);
                }
                this.imagesList.splice(idx, 1);
                this.syncInputs();
            },

            syncInputs() {
                const dtCover = new DataTransfer();
                const dtGallery = new DataTransfer();
                const scrapedCoverInput = document.getElementById('scraped_image_path');
                const scrapedGalleryContainer = document.getElementById('scraped_gallery_inputs_container');
                if (scrapedGalleryContainer) scrapedGalleryContainer.innerHTML = '';

                if (this.imagesList.length > 0) {
                    const cover = this.imagesList[0];
                    if (cover.file) {
                        dtCover.items.add(cover.file);
                        if (scrapedCoverInput) scrapedCoverInput.value = '';
                    } else if (cover.path && !cover.isExistingCover) {
                        if (scrapedCoverInput) scrapedCoverInput.value = cover.path;
                    }

                    for (let i = 1; i < this.imagesList.length; i++) {
                        const g = this.imagesList[i];
                        if (g.file) {
                            dtGallery.items.add(g.file);
                        } else if (g.path && !g.dbId && !g.isExistingCover) {
                            if (scrapedGalleryContainer) {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = 'scraped_gallery_paths[]';
                                hidden.value = g.path;
                                scrapedGalleryContainer.appendChild(hidden);
                            }
                        }
                    }
                }

                const coverInput = document.getElementById('image');
                if (coverInput) coverInput.files = dtCover.files;

                const galleryInput = document.getElementById('gallery_images');
                if (galleryInput) galleryInput.files = dtGallery.files;
            },

            // 🎨 Canvas Çiçek Stüdyosu Modal Durumu
            studioModal: false,
            studioIdx: null,
            studioImgElement: null,
            studioCropPreset: '1:1', // '1:1', '4:5', 'full'
            studioRotation: 0, // 0, 90, 180, 270
            studioFlipH: false,
            studioFlipV: false,
            studioFilter: 'none', // 'none', 'bloom', 'romantic', 'studio'
            studioBrightness: 0, // -100..100
            studioContrast: 0, // -100..100
            studioSaturation: 0, // -100..100
            studioOriginalName: '',
            studioLoading: false,

            openStudio(idx) {
                if (idx < 0 || idx >= this.imagesList.length) return;
                this.studioIdx = idx;
                const item = this.imagesList[idx];
                this.studioOriginalName = item.name;
                this.studioCropPreset = '1:1';
                this.studioRotation = 0;
                this.studioFlipH = false;
                this.studioFlipV = false;
                this.studioFilter = 'none';
                this.studioBrightness = 0;
                this.studioContrast = 0;
                this.studioSaturation = 0;
                this.studioLoading = true;
                this.studioModal = true;

                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => {
                    this.studioImgElement = img;
                    this.studioLoading = false;
                    this.$nextTick(() => {
                        this.renderStudioCanvas();
                    });
                };
                img.src = item.url;
            },

            setFilterPreset(preset) {
                this.studioFilter = preset;
                if (preset === 'none') {
                    this.studioBrightness = 0;
                    this.studioContrast = 0;
                    this.studioSaturation = 0;
                } else if (preset === 'bloom') {
                    this.studioBrightness = 4;
                    this.studioContrast = 16;
                    this.studioSaturation = 40;
                } else if (preset === 'romantic') {
                    this.studioBrightness = 6;
                    this.studioContrast = 10;
                    this.studioSaturation = 20;
                } else if (preset === 'studio') {
                    this.studioBrightness = 18;
                    this.studioContrast = 22;
                    this.studioSaturation = 8;
                }
                this.renderStudioCanvas();
            },

            rotate(deg) {
                this.studioRotation = (this.studioRotation + deg + 360) % 360;
                this.renderStudioCanvas();
            },

            toggleFlip(dir) {
                if (dir === 'h') this.studioFlipH = !this.studioFlipH;
                if (dir === 'v') this.studioFlipV = !this.studioFlipV;
                this.renderStudioCanvas();
            },

            renderStudioCanvas() {
                const canvas = document.getElementById('studioCanvas');
                if (!canvas || !this.studioImgElement) return;
                const img = this.studioImgElement;
                const ctx = canvas.getContext('2d');

                let srcX = 0, srcY = 0, srcW = img.width, srcH = img.height;
                if (this.studioCropPreset === '1:1') {
                    const side = Math.min(img.width, img.height);
                    srcX = Math.round((img.width - side) / 2);
                    srcY = Math.round((img.height - side) / 2);
                    srcW = side;
                    srcH = side;
                } else if (this.studioCropPreset === '4:5') {
                    const targetRatio = 4 / 5;
                    const curRatio = img.width / img.height;
                    if (curRatio > targetRatio) {
                        srcH = img.height;
                        srcW = Math.round(srcH * targetRatio);
                        srcX = Math.round((img.width - srcW) / 2);
                        srcY = 0;
                    } else {
                        srcW = img.width;
                        srcH = Math.round(srcW / targetRatio);
                        srcX = 0;
                        srcY = Math.round((img.height - srcH) / 2);
                    }
                }

                const maxDisplay = 540;
                let outW = srcW;
                let outH = srcH;
                if (this.studioRotation === 90 || this.studioRotation === 270) {
                    outW = srcH;
                    outH = srcW;
                }
                const scale = Math.min(maxDisplay / outW, maxDisplay / outH, 1);
                canvas.width = Math.round(outW * scale);
                canvas.height = Math.round(outH * scale);

                ctx.save();
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                const b = 1 + this.studioBrightness / 100;
                const c = 1 + this.studioContrast / 100;
                const s = 1 + this.studioSaturation / 100;
                ctx.filter = `brightness(${b}) contrast(${c}) saturate(${s})`;

                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate((this.studioRotation * Math.PI) / 180);
                ctx.scale(this.studioFlipH ? -1 : 1, this.studioFlipV ? -1 : 1);

                const drawW = Math.round(srcW * scale);
                const drawH = Math.round(srcH * scale);
                ctx.drawImage(img, srcX, srcY, srcW, srcH, -drawW / 2, -drawH / 2, drawW, drawH);

                ctx.restore();
            },

            applyStudioCrop() {
                if (!this.studioImgElement || this.studioIdx === null) return;
                const img = this.studioImgElement;
                const item = this.imagesList[this.studioIdx];

                let srcX = 0, srcY = 0, srcW = img.width, srcH = img.height;
                if (this.studioCropPreset === '1:1') {
                    const side = Math.min(img.width, img.height);
                    srcX = Math.round((img.width - side) / 2);
                    srcY = Math.round((img.height - side) / 2);
                    srcW = side;
                    srcH = side;
                } else if (this.studioCropPreset === '4:5') {
                    const targetRatio = 4 / 5;
                    const curRatio = img.width / img.height;
                    if (curRatio > targetRatio) {
                        srcH = img.height;
                        srcW = Math.round(srcH * targetRatio);
                        srcX = Math.round((img.width - srcW) / 2);
                        srcY = 0;
                    } else {
                        srcW = img.width;
                        srcH = Math.round(srcW / targetRatio);
                        srcX = 0;
                        srcY = Math.round((img.height - srcH) / 2);
                    }
                }

                let outW = srcW;
                let outH = srcH;
                if (this.studioRotation === 90 || this.studioRotation === 270) {
                    outW = srcH;
                    outH = srcW;
                }

                const maxDim = 1600;
                if (outW > maxDim || outH > maxDim) {
                    const sc = maxDim / Math.max(outW, outH);
                    outW = Math.round(outW * sc);
                    outH = Math.round(outH * sc);
                }

                const outCanvas = document.createElement('canvas');
                outCanvas.width = outW;
                outCanvas.height = outH;
                const outCtx = outCanvas.getContext('2d');

                const b = 1 + this.studioBrightness / 100;
                const c = 1 + this.studioContrast / 100;
                const s = 1 + this.studioSaturation / 100;
                outCtx.filter = `brightness(${b}) contrast(${c}) saturate(${s})`;

                outCtx.translate(outCanvas.width / 2, outCanvas.height / 2);
                outCtx.rotate((this.studioRotation * Math.PI) / 180);
                outCtx.scale(this.studioFlipH ? -1 : 1, this.studioFlipV ? -1 : 1);

                const finalDrawW = (this.studioRotation === 90 || this.studioRotation === 270) ? outH : outW;
                const finalDrawH = (this.studioRotation === 90 || this.studioRotation === 270) ? outW : outH;
                outCtx.drawImage(img, srcX, srcY, srcW, srcH, -finalDrawW / 2, -finalDrawH / 2, finalDrawW, finalDrawH);

                const dataUrl = outCanvas.toDataURL('image/webp', 0.90);

                outCanvas.toBlob((blob) => {
                    if (!blob) return;

                    // Eğer veritabanında kayıtlı bir görsel ise
                    if (item.isExistingCover || item.dbId) {
                        this.studioLoading = true;
                        fetch('{{ route('admin.products.saveEditedImage') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                image_data: dataUrl,
                                product_id: {{ $product->id }},
                                type: item.isExistingCover ? 'cover' : 'gallery',
                                gallery_image_id: item.dbId || null
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.studioLoading = false;
                            if (data.success) {
                                item.url = data.image_asset + '?v=' + Date.now();
                                item.path = data.image_path;
                                item.file = null;
                                item.size = (blob.size / 1024).toFixed(0) + ' KB (Kaydedildi)';
                                item.dimensions = outW + 'x' + outH;
                                this.studioModal = false;
                                this.successMsg = '✨ ' + data.message;
                            } else {
                                this.errorMsg = data.message || 'Görsel kaydedilemedi.';
                            }
                        })
                        .catch(err => {
                            this.studioLoading = false;
                            this.errorMsg = 'Hata: ' + err.message;
                        });
                    } else {
                        // Yeni yüklenen görsel
                        const newFile = new File([blob], 'cicek_' + Date.now() + '.webp', { type: 'image/webp' });
                        const newUrl = URL.createObjectURL(blob);
                        if (item.url && item.url.startsWith('blob:')) {
                            URL.revokeObjectURL(item.url);
                        }
                        item.file = newFile;
                        item.url = newUrl;
                        item.size = (blob.size / 1024).toFixed(0) + ' KB';
                        item.dimensions = outW + 'x' + outH;
                        this.studioModal = false;
                        this.syncInputs();
                        this.successMsg = '✨ Görsel stüdyoda başarıyla düzenlendi ve WebP olarak hazırlandı!';
                    }
                }, 'image/webp', 0.90);
            },
            rewriteWithAi(targetField = 'all') {
                const nameVal = document.getElementById('name').value;
                if (!nameVal) {
                    this.errorMsg = 'Lütfen en azından bir ürün adı girin.';
                    return;
                }
                this.fieldLoading[targetField] = true;
                this.aiLoading = true;
                this.errorMsg = '';
                this.successMsg = '';

                const currentDesc = window.descQuillInstance ? window.descQuillInstance.root.innerHTML : '';
                const currentTech = window.techQuillInstance ? window.techQuillInstance.root.innerHTML : '';
                const currentShort = document.getElementById('short_description') ? document.getElementById('short_description').value : '';

                fetch('{{ route('admin.products.aiRewrite') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        name: nameVal,
                        description: currentDesc,
                        technical_features: currentTech,
                        short_description: currentShort,
                        custom_instruction: this.aiInstruction,
                        target_field: targetField
                    })
                })
                .then(res => res.json())
                .then(res => {
                    this.fieldLoading[targetField] = false;
                    this.aiLoading = Object.values(this.fieldLoading).some(Boolean);
                    if (res.success && res.data) {
                        const d = res.data;
                        if (targetField === 'all' || targetField === 'name') {
                            if (d.name) document.getElementById('name').value = d.name;
                        }
                        if (d.short_description && document.getElementById('short_description')) {
                            const curShort = document.getElementById('short_description').value.trim();
                            if (!curShort || targetField === 'all' || targetField === 'short_description') {
                                document.getElementById('short_description').value = d.short_description;
                            }
                        }
                        if (targetField === 'all' || targetField === 'description') {
                            if (d.description && window.descQuillInstance) {
                                let cleanHtml = d.description;
                                if (!cleanHtml.includes('<p>') && !cleanHtml.includes('<br>')) {
                                    cleanHtml = cleanHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                                }
                                window.descQuillInstance.clipboard.dangerouslyPasteHTML(cleanHtml);
                                const descInput = document.getElementById('description_input');
                                if (descInput) descInput.value = cleanHtml;
                            }
                        }
                        if (d.technical_features && window.techQuillInstance) {
                            const curTech = window.techQuillInstance.getText().trim();
                            if (!curTech || targetField === 'all' || targetField === 'technical_features') {
                                let techHtml = d.technical_features;
                                if (!techHtml.includes('<p>') && !techHtml.includes('<br>')) {
                                    techHtml = techHtml.split(/\r?\n/).filter(x => x.trim()).map(x => `<p>${x}</p>`).join('');
                                }
                                window.techQuillInstance.clipboard.dangerouslyPasteHTML(techHtml);
                                const techInput = document.getElementById('technical_features_input');
                                if (techInput) techInput.value = techHtml;
                            }
                        }
                        if (targetField === 'all' || targetField === 'seo') {
                            if (d.seo_title && document.getElementById('seo_title')) {
                                document.getElementById('seo_title').value = d.seo_title;
                            }
                            if (d.seo_description && document.getElementById('seo_description')) {
                                document.getElementById('seo_description').value = d.seo_description;
                            }
                        }
                        this.successMsg = '✨ ' + (res.message || 'Yapay zeka içeriği başarıyla güncelledi!');
                        if (d.notice) {
                            this.successMsg += ' — ' + d.notice;
                        }
                    } else {
                        this.errorMsg = res.message || 'Yapay zeka yanıt veremedi.';
                    }
                })
                .catch(err => {
                    this.fieldLoading[targetField] = false;
                    this.aiLoading = Object.values(this.fieldLoading).some(Boolean);
                    this.errorMsg = 'Yapay zeka işleminde hata: ' + err.message;
                });
            }
         }">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200/80 p-6 sm:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 rounded-r-xl text-xs font-bold">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="product-form" action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="scraped_image_path" id="scraped_image_path">
                    <div id="scraped_gallery_inputs_container"></div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="category_id">Kategori <span class="text-red-500">*</span></label>
                            <select class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-rose-500 focus:ring-rose-500 bg-white" id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="name">Ürün Adı <span class="text-red-500">*</span></label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs font-bold focus:border-rose-500 focus:ring-rose-500" id="name" name="name" type="text" value="{{ old('name', $product->name) }}" required>
                        </div>
                    </div>

                    <!-- Fiyat ve İndirim Alanları (3'lü Grid) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="price">
                                Normal Satış Fiyatı (TL) <span class="text-red-500">*</span>
                            </label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-rose-600 font-black text-sm focus:border-rose-500 focus:ring-rose-500" id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" required>
                            <p class="text-[10px] text-gray-400 mt-1">İndirim girilirse üzeri çizili eski fiyat olur.</p>
                        </div>
                        <div>
                            <label class="block text-rose-700 text-xs font-extrabold mb-1" for="campaign_price">
                                🏷️ İndirimli / Kampanya Fiyatı (TL)
                            </label>
                            <input class="border border-rose-200 bg-rose-50/40 rounded-2xl w-full py-2.5 px-3.5 text-rose-600 font-black text-sm focus:border-rose-500 focus:ring-rose-500" id="campaign_price" name="campaign_price" type="number" step="0.01" value="{{ old('campaign_price', $product->campaign_price) }}" placeholder="Örn: 4990 (Boşsa indirim yok)">
                            <p class="text-[10px] text-rose-500 mt-1">Doldurulursa sitede % İndirimli olarak satılır.</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="cost_price">
                                Maliyet Fiyatı (TL) <span class="text-gray-400 font-normal">(Gizli)</span>
                            </label>
                            <input class="border border-amber-200 bg-amber-50/50 rounded-2xl w-full py-2.5 px-3.5 text-gray-800 text-xs font-bold focus:border-amber-500 focus:ring-amber-500" id="cost_price" name="cost_price" type="number" step="0.01" value="{{ old('cost_price', $product->cost_price) }}">
                            <p class="text-[10px] text-gray-400 mt-1">Sadece admin panelde kâr hesabı için görünür.</p>
                        </div>
                    </div>

                    <!-- 🌸 GELİŞMİŞ GÖRSEL YÖNETİMİ & ÇİÇEK FOTOĞRAF STÜDYOSU -->
                    <div class="bg-gradient-to-br from-rose-50/40 via-white to-pink-50/30 p-6 rounded-3xl border-2 border-rose-100 shadow-sm space-y-5">
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-rose-100">
                            <div>
                                <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center text-xs shadow-xs">
                                        <i class="fas fa-camera-retro"></i>
                                    </span>
                                    <span>Çiçek Fotoğraf Stüdyosu &amp; Görsel Yönetimi</span>
                                </h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">Kapak ve galeri fotoğraflarını yönetin, kırpın, renklerini canlandırın ve sıralayın</p>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400">
                                <span class="bg-white border border-rose-200 text-rose-700 px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-2xs">
                                    <i class="fas fa-bolt text-amber-500"></i> Otomatik WebP Sıkıştırma
                                </span>
                                <span class="bg-white border border-purple-200 text-purple-700 px-2.5 py-1 rounded-lg flex items-center gap-1 shadow-2xs">
                                    <i class="fas fa-paste text-purple-600"></i> Ctrl+V Pano Desteği
                                </span>
                            </div>
                        </div>

                        <!-- 1. BİRLEŞİK DROPZONE & URL İÇE AKTARMA -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            
                            <!-- Sol/Büyük: Sürükle-Bırak & Dosya Seç & Ctrl+V -->
                            <div class="lg:col-span-2 relative">
                                <div @dragover.prevent="isDragging = true" 
                                     @dragleave.prevent="isDragging = false" 
                                     @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
                                     :class="isDragging ? 'border-rose-500 bg-rose-100/50 scale-[0.99]' : 'border-rose-200 bg-white hover:border-rose-300 hover:bg-rose-50/20'"
                                     class="border-2 border-dashed rounded-3xl p-6 text-center transition-all cursor-pointer flex flex-col items-center justify-center min-h-[170px] group"
                                     @click="$refs.fileInput.click()">
                                    
                                    <input type="file" x-ref="fileInput" @change="handleFiles($event.target.files); $event.target.value = ''" 
                                           multiple accept="image/*" class="hidden">
                                    
                                    <div class="w-14 h-14 rounded-2xl bg-rose-50 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center text-2xl transition-transform group-hover:scale-110 mb-2 shadow-2xs">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h4 class="text-xs font-black text-gray-800">
                                        Yeni Fotoğraf Yüklemek İçin Sürükleyin veya <span class="text-rose-600 underline underline-offset-2">Dosya Seçin</span>
                                    </h4>
                                    <p class="text-[11px] text-gray-400 mt-1">
                                        JPG, PNG, WEBP, HEIC desteklenir • Boyut sınırı yok (tarayıcıda otomatik sıkıştırılır)
                                    </p>
                                    <div class="mt-2.5 flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                                            📋 Panodan yapıştırmak için sayfadayken <kbd class="font-mono font-black text-rose-600">Ctrl + V</kbd> yapabilirsiniz
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ/Küçük: Web Linkinden / URL'den Görsel İndir -->
                            <div class="bg-white p-4 rounded-3xl border border-gray-200/80 flex flex-col justify-between shadow-2xs">
                                <div>
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <h4 class="text-xs font-black text-gray-800">Web URL'sinden Görsel İndir</h4>
                                    </div>
                                    <p class="text-[11px] text-gray-400 leading-tight mb-3">
                                        İnternetteki herhangi bir çiçek fotoğrafının bağlantısını yapıştırarak anında galeriye ekleyin.
                                    </p>
                                </div>
                                <div class="space-y-2">
                                    <input type="url" x-model="urlInput" placeholder="https://ornek.com/cicek.jpg" 
                                           @keydown.enter.prevent="fetchFromUrl()"
                                           class="w-full text-xs py-2 px-3 border border-gray-200 rounded-xl focus:ring-rose-500 focus:border-rose-500">
                                    <button type="button" @click="fetchFromUrl()" :disabled="urlLoading || !urlInput"
                                            class="w-full py-2 px-3 bg-gray-900 hover:bg-black disabled:bg-gray-300 text-white text-xs font-black rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                        <i class="fas fa-spinner fa-spin" x-show="urlLoading" x-cloak></i>
                                        <i class="fas fa-arrow-down" x-show="!urlLoading"></i>
                                        <span x-text="urlLoading ? 'İndiriliyor...' : 'Görseli İndir &amp; Ekle'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- 2. GÖRSEL LİSTESİ & KART ŞERİDİ (Kapak + Galeri) -->
                        <div x-show="imagesList.length > 0" x-cloak class="space-y-3 pt-3 border-t border-rose-100/70">
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black text-gray-900">
                                        Ürün Görselleri (<span x-text="imagesList.length"></span> adet)
                                    </span>
                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        👑 1. Sıradaki görsel ürünün Ana Kapağıdır
                                    </span>
                                </div>
                            </div>

                            <!-- Responsive Kart Izgarası -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
                                <template x-for="(img, idx) in imagesList" :key="img.id || idx">
                                    <div class="relative group bg-white rounded-2xl border-2 transition-all p-2 flex flex-col justify-between shadow-2xs hover:shadow-md"
                                         :class="idx === 0 ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-gray-200 hover:border-gray-300'">
                                        
                                        <!-- Rozet: Kapak veya Galeri Sırası -->
                                        <div class="absolute top-2 left-2 z-10">
                                            <template x-if="idx === 0">
                                                <span class="bg-gradient-to-r from-rose-600 to-pink-600 text-white text-[9px] font-black px-2 py-0.5 rounded-md shadow-xs flex items-center gap-1">
                                                    👑 ANA KAPAK
                                                </span>
                                            </template>
                                            <template x-if="idx > 0">
                                                <span class="bg-gray-800/80 backdrop-blur-xs text-white text-[9px] font-black px-1.5 py-0.5 rounded-md shadow-2xs" x-text="'#' + (idx + 1)"></span>
                                            </template>
                                        </div>

                                        <!-- Aksiyon: Sil Butonu -->
                                        <button type="button" @click="removeImage(idx)" title="Bu fotoğrafı kaldır"
                                                class="absolute top-2 right-2 z-10 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] shadow-sm transition-transform hover:scale-110 cursor-pointer">
                                            <i class="fas fa-times"></i>
                                        </button>

                                        <!-- Görsel Önizleme -->
                                        <div class="w-full h-36 rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center p-1 relative my-1">
                                            <img :src="img.url" class="max-h-full max-w-full object-contain rounded-lg">
                                        </div>

                                        <!-- Dosya Bilgisi -->
                                        <div class="text-[10px] text-gray-500 truncate w-full px-1 flex items-center justify-between">
                                            <span class="font-bold truncate" x-text="img.name"></span>
                                            <span class="text-gray-400 font-mono ml-1 flex-shrink-0" x-text="img.size"></span>
                                        </div>

                                        <!-- Butonlar Toolbar -->
                                        <div class="mt-2 pt-2 border-t border-gray-100 flex flex-col gap-1">
                                            
                                            <!-- 🎨 Çiçek Stüdyosu & Kırp Butonu -->
                                            <button type="button" @click="openStudio(idx)"
                                                    class="w-full py-1 px-2 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-[10px] font-black transition-all flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                                <i class="fas fa-crop-alt text-[9px]"></i>
                                                <span>Kırp &amp; Filtrele</span>
                                            </button>

                                            <div class="flex items-center gap-1">
                                                <!-- Kapak Yap (Eğer kapak değilse) -->
                                                <template x-if="idx > 0">
                                                    <button type="button" @click="makeCover(idx)" title="Bu fotoğrafı Ana Kapak yap"
                                                            class="flex-1 py-1 px-1.5 rounded-lg bg-rose-50 hover:bg-rose-600 hover:text-white text-rose-700 text-[9px] font-black transition-colors flex items-center justify-center gap-1 cursor-pointer">
                                                        <i class="fas fa-star text-[8px]"></i> Kapak
                                                    </button>
                                                </template>

                                                <!-- Sıralama Okları -->
                                                <button type="button" @click="moveImage(idx, -1)" :disabled="idx === 0" title="Öne Al"
                                                        class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-30 text-gray-700 flex items-center justify-center text-[10px] cursor-pointer">
                                                        <i class="fas fa-chevron-left"></i>
                                                </button>
                                                <button type="button" @click="moveImage(idx, 1)" :disabled="idx === imagesList.length - 1" title="Sona Al"
                                                        class="w-6 h-6 rounded-lg bg-gray-100 hover:bg-gray-200 disabled:opacity-30 text-gray-700 flex items-center justify-center text-[10px] cursor-pointer">
                                                        <i class="fas fa-chevron-right"></i>
                                                </button>
                                            </div>

                                        </div>

                                    </div>
                                </template>
                            </div>

                        </div>

                        <!-- Gizli standart dosya inputları (Form submiti için senkronize edilir) -->
                        <input type="file" id="image" name="image" class="hidden" accept="image/*">
                        <input type="file" id="gallery_images" name="gallery_images[]" multiple class="hidden" accept="image/*">

                    </div>

                    <!-- Kısa Açıklama -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-700 text-xs font-bold" for="short_description">Kısa Özet Açıklama</label>
                            <button type="button" @click="rewriteWithAi('short_description')" :disabled="fieldLoading.short_description"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.short_description" x-cloak></i>
                                <i class="fas fa-sparkles text-[10px]" x-show="!fieldLoading.short_description"></i>
                                <span x-text="fieldLoading.short_description ? 'Yazılıyor...' : 'AI ile Kısa Özet Yaz'"></span>
                            </button>
                        </div>
                        <textarea class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:border-blue-500 focus:ring-blue-500" id="short_description" name="short_description" rows="2" placeholder="Ürünün 1-2 cümlelik vurucu spot açıklaması...">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <!-- Başarı / Hata Mesajları -->
                    <div x-show="successMsg" x-cloak class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                        <span x-text="successMsg"></span>
                    </div>
                    <div x-show="errorMsg" x-cloak class="p-3 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl text-xs font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-600 text-sm"></i>
                        <span x-text="errorMsg"></span>
                    </div>

                    <!-- 🤖 Yapay Zeka Metin Yazarı Paneli (Tüm Metinler) -->
                    <div class="p-4 bg-gradient-to-r from-purple-50 via-indigo-50/60 to-blue-50 rounded-2xl border border-purple-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center text-sm shadow-xs flex-shrink-0">
                                <i class="fas fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-black text-purple-950">Yapay Zeka Tüm Metinleri & Açıklamaları Yeniden Yazıcı</h4>
                                <p class="text-[10px] text-purple-800">Ürün başlığını, kısa özetini, detaylı HTML açıklamasını, teknik özelliklerini ve SEO alanlarını tek tıkla firmanıza özel satış diline dönüştürün.</p>
                            </div>
                        </div>
                        <button type="button" @click="rewriteWithAi('all')" :disabled="aiLoading"
                                class="bg-purple-600 hover:bg-purple-700 disabled:bg-purple-400 text-white text-xs font-black px-4 py-2.5 rounded-xl shadow-xs transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer flex-shrink-0">
                            <i class="fas fa-spinner fa-spin" x-show="fieldLoading.all" x-cloak></i>
                            <i class="fas fa-wand-magic-sparkles" x-show="!fieldLoading.all"></i>
                            <span x-text="fieldLoading.all ? 'Tüm Metinler Yazılıyor...' : '✨ Tüm Metinleri AI ile Yeniden Yaz'"></span>
                        </button>
                    </div>

                    <!-- Detaylı Açıklama (Zengin Görsel Editör) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-900 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-edit text-blue-600"></i> Detaylı Ürün Açıklaması (Görsel Metin Düzenleyici)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] text-gray-400 font-bold hidden sm:inline">Paragraf ve satır boşlukları tam korunur</span>
                                <button type="button" @click="rewriteWithAi('description')" :disabled="fieldLoading.description"
                                        class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                    <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.description" x-cloak></i>
                                    <i class="fas fa-wand-magic-sparkles text-[10px]" x-show="!fieldLoading.description"></i>
                                    <span x-text="fieldLoading.description ? 'Yazılıyor...' : 'AI ile Detaylı Açıklama Yaz'"></span>
                                </button>
                            </div>
                        </div>
                        <div id="description_editor">{!! old('description', $product->description) !!}</div>
                        <input type="hidden" name="description" id="description_input">
                    </div>
                    
                    <!-- Çiçek & Buket Özellikleri (Zengin Görsel Editör) -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-gray-900 text-xs font-black flex items-center gap-1.5">
                                <i class="fas fa-spa text-rose-600"></i> Çiçek &amp; Buket Özellikleri (Görsel Metin Düzenleyici)
                            </label>
                            <button type="button" @click="rewriteWithAi('technical_features')" :disabled="fieldLoading.technical_features"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.technical_features" x-cloak></i>
                                <i class="fas fa-spa text-[10px]" x-show="!fieldLoading.technical_features"></i>
                                <span x-text="fieldLoading.technical_features ? 'Çıkarılıyor...' : 'AI ile Özellikleri Çıkar'"></span>
                            </button>
                        </div>
                        <div id="technical_features_editor">{!! old('technical_features', $product->technical_features) !!}</div>
                        <input type="hidden" name="technical_features" id="technical_features_input">
                    </div>

                    <!-- Rozetler ve Teslimat Ayarları -->
                    <div class="bg-gray-50/70 p-6 rounded-3xl border border-gray-200/80 space-y-4">
                        <h3 class="font-black text-sm text-gray-900 flex items-center gap-2">
                            <i class="fas fa-certificate text-rose-500"></i> Rozet ve Teslimat Seçenekleri
                        </h3>
                        
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1" for="custom_badge">Özel Çiçek Rozeti (İsteğe Bağlı)</label>
                            <input class="border border-gray-300 rounded-2xl w-full py-2.5 px-3.5 text-gray-900 text-xs focus:border-rose-500 focus:ring-rose-500" id="custom_badge" name="custom_badge" type="text" value="{{ old('custom_badge', $product->custom_badge) }}" placeholder="Örn: EN ÇOK TERCİH EDİLEN, GÜNÜN FIRSATI, TAPTAPTAZE">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2">
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-emerald-400 transition-colors">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-emerald-600 shadow-sm w-4 h-4" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-emerald-700">🟢 Satışta (Aktif)</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-rose-400 transition-colors">
                                <input type="checkbox" name="in_stock" value="1" class="rounded border-gray-300 text-rose-600 shadow-sm w-4 h-4" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800">📦 Stokta Var</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-rose-400 transition-colors">
                                <input type="checkbox" name="has_free_installation" value="1" class="rounded border-gray-300 text-rose-600 shadow-sm w-4 h-4" {{ old('has_free_installation', $product->has_free_installation) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800">🚚 VIP Kurye / Aynı Gün</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 rounded-2xl border bg-white cursor-pointer hover:border-purple-400 transition-colors">
                                <input type="checkbox" name="has_cash_on_delivery" value="1" class="rounded border-gray-300 text-purple-600 shadow-sm w-4 h-4" {{ old('has_cash_on_delivery', $product->has_cash_on_delivery) ? 'checked' : '' }}>
                                <span class="text-xs font-black text-gray-800">💵 Kapıda Ödeme</span>
                            </label>
                        </div>
                    </div>

                    <!-- SEO Ayarları -->
                    <div class="border-t border-gray-100 pt-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="font-black text-sm text-gray-900 flex items-center gap-1.5">
                                <i class="fab fa-google text-rose-500"></i> Google SEO Ayarları (Opsiyonel)
                            </h3>
                            <button type="button" @click="rewriteWithAi('seo')" :disabled="fieldLoading.seo"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-purple-700 hover:text-white bg-purple-50 hover:bg-purple-600 border border-purple-200 px-2.5 py-1 rounded-xl transition-all cursor-pointer">
                                <i class="fas fa-spinner fa-spin text-[10px]" x-show="fieldLoading.seo" x-cloak></i>
                                <i class="fas fa-wand-magic-sparkles text-[10px]" x-show="!fieldLoading.seo"></i>
                                <span x-text="fieldLoading.seo ? 'Optimize Ediliyor...' : 'AI ile SEO Başlık & Açıklama Üret'"></span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="seo_title">Özel SEO Başlığı</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs focus:border-rose-500 focus:ring-rose-500" id="seo_title" name="seo_title" type="text" value="{{ old('seo_title', $product->seo_title) }}" placeholder="Boş bırakırsanız ürün adı kullanılır">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1" for="seo_description">Özel SEO Açıklaması</label>
                                <input class="border border-gray-300 rounded-2xl w-full py-2 px-3 text-gray-900 text-xs focus:border-rose-500 focus:ring-rose-500" id="seo_description" name="seo_description" type="text" value="{{ old('seo_description', $product->seo_description) }}" placeholder="Google arama sonuçlarında çıkacak açıklama">
                            </div>
                        </div>
                    </div>

                    <!-- Kaydet Butonları -->
                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                        <a class="px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-2xl transition-colors" href="{{ route('admin.products.index') }}">İptal</a>
                        <button class="bg-rose-600 hover:bg-rose-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg transition-all transform active:scale-95 text-xs flex items-center gap-2 cursor-pointer" type="submit">
                            <i class="fas fa-save"></i> Değişiklikleri Güncelle
                        </button>
                    </div>
                </form>

                @if($product->image_path)
                    <form id="delete-product-cover-form" action="{{ route('admin.products.cover.destroy', $product) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

                @foreach($product->images as $img)
                    <form id="delete-gallery-img-form-{{ $img->id }}" action="{{ route('admin.products.images.destroy', [$product, $img]) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form id="set-cover-form-{{ $img->id }}" action="{{ route('admin.products.images.setCover', [$product, $img]) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endforeach

            </div>
        </div>

        <!-- 🌸 ÇİÇEK FOTOĞRAF STÜDYOSU MODALI (HTML5 Canvas, No AI) -->
        <div x-show="studioModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity" @click="studioModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-4xl border border-gray-100 flex flex-col max-h-[92vh]">
                    
                    <!-- Modal Başlığı -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50 via-pink-50/50 to-amber-50">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-600 text-white flex items-center justify-center shadow-md text-base">
                                <i class="fas fa-magic"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-black text-gray-900">Çiçek Fotoğraf Stüdyosu</h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 uppercase">CANVAS &amp; WEBP</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="'Düzenlenen: ' + studioOriginalName"></p>
                            </div>
                        </div>
                        <button type="button" @click="studioModal = false" class="w-8 h-8 rounded-full bg-white hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center transition-colors border border-gray-200 cursor-pointer">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <!-- Modal İçeriği (İki Kolonlu: Sol Canvas, Sağ Kontroller) -->
                    <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        
                        <!-- Sol Kolon: Canvas Önizleme Alanı (lg:col-span-7) -->
                        <div class="lg:col-span-7 flex flex-col items-center justify-center bg-gray-950 rounded-2xl p-4 min-h-[380px] relative overflow-hidden border border-gray-800">
                            
                            <div x-show="studioLoading" class="flex flex-col items-center justify-center gap-2 text-white">
                                <i class="fas fa-spinner fa-spin text-2xl text-rose-500"></i>
                                <span class="text-xs font-bold">Görsel işleniyor...</span>
                            </div>

                            <canvas id="studioCanvas" class="max-h-[380px] max-w-full rounded-lg shadow-2xl transition-all" x-show="!studioLoading"></canvas>

                            <!-- Önizleme Çözünürlük ve Oran Etiketi -->
                            <div class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-xs text-white text-[10px] font-mono px-2 py-1 rounded-md">
                                <span x-text="studioCropPreset === '1:1' ? '1:1 Kare (E-Ticaret)' : (studioCropPreset === '4:5' ? '4:5 Dikey (Katalog)' : 'Orijinal Boyut')"></span>
                                <span x-show="studioRotation !== 0" x-text="' • ' + studioRotation + '°'"></span>
                            </div>
                        </div>

                        <!-- Sağ Kolon: Araçlar & Ayarlar (lg:col-span-5) -->
                        <div class="lg:col-span-5 space-y-4">
                            
                            <!-- 1. Kırpma Oranları (Presets) -->
                            <div>
                                <label class="block text-xs font-black text-gray-800 mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-vector-square text-rose-600"></i> Kırpma &amp; Kadraj Oranı:
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button type="button" @click="studioCropPreset = '1:1'; renderStudioCanvas()"
                                            :class="studioCropPreset === '1:1' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:border-rose-300'"
                                            class="py-2 px-2 text-xs font-bold rounded-xl border transition-all flex flex-col items-center justify-center gap-0.5 cursor-pointer">
                                        <span class="text-sm font-black">1:1</span>
                                        <span class="text-[9px] opacity-80">Kare Vitrin</span>
                                    </button>
                                    <button type="button" @click="studioCropPreset = '4:5'; renderStudioCanvas()"
                                            :class="studioCropPreset === '4:5' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:border-rose-300'"
                                            class="py-2 px-2 text-xs font-bold rounded-xl border transition-all flex flex-col items-center justify-center gap-0.5 cursor-pointer">
                                        <span class="text-sm font-black">4:5</span>
                                        <span class="text-[9px] opacity-80">Dikey Buket</span>
                                    </button>
                                    <button type="button" @click="studioCropPreset = 'full'; renderStudioCanvas()"
                                            :class="studioCropPreset === 'full' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:border-rose-300'"
                                            class="py-2 px-2 text-xs font-bold rounded-xl border transition-all flex flex-col items-center justify-center gap-0.5 cursor-pointer">
                                        <span class="text-sm font-black"><i class="fas fa-expand"></i></span>
                                        <span class="text-[9px] opacity-80">Tam Boyut</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. Döndürme & Yansıtma (Transforms) -->
                            <div>
                                <label class="block text-xs font-black text-gray-800 mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-sync-alt text-blue-600"></i> Döndür &amp; Yansıt:
                                </label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" @click="rotate(-90)" title="90° Sola Döndür"
                                            class="py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl border border-gray-200 text-xs font-bold flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="fas fa-undo text-[11px]"></i> -90°
                                    </button>
                                    <button type="button" @click="rotate(90)" title="90° Sağa Döndür"
                                            class="py-2 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl border border-gray-200 text-xs font-bold flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="fas fa-redo text-[11px]"></i> +90°
                                    </button>
                                    <button type="button" @click="toggleFlip('h')" title="Yatay Aynala"
                                            :class="studioFlipH ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-700'"
                                            class="py-2 rounded-xl border border-gray-200 text-xs font-bold flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="fas fa-arrows-alt-h"></i> Yatay
                                    </button>
                                    <button type="button" @click="toggleFlip('v')" title="Dikey Aynala"
                                            :class="studioFlipV ? 'bg-blue-600 text-white' : 'bg-gray-50 text-gray-700'"
                                            class="py-2 rounded-xl border border-gray-200 text-xs font-bold flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="fas fa-arrows-alt-v"></i> Dikey
                                    </button>
                                </div>
                            </div>

                            <!-- 3. Çiçekçiye Özel Renk & Canlılık Filtreleri -->
                            <div>
                                <label class="block text-xs font-black text-gray-800 mb-1.5 flex items-center gap-1.5">
                                    <i class="fas fa-magic text-purple-600"></i> Çiçek Renk Filtreleri:
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" @click="setFilterPreset('none')"
                                            :class="studioFilter === 'none' ? 'border-purple-600 bg-purple-50 text-purple-900 font-black' : 'border-gray-200 bg-white text-gray-700'"
                                            class="p-2 rounded-xl border text-left text-xs transition-all cursor-pointer">
                                        <div class="font-bold">🌿 Doğal</div>
                                        <div class="text-[9px] text-gray-400">Orijinal renkler</div>
                                    </button>
                                    <button type="button" @click="setFilterPreset('bloom')"
                                            :class="studioFilter === 'bloom' ? 'border-rose-600 bg-rose-50 text-rose-900 font-black' : 'border-gray-200 bg-white text-gray-700'"
                                            class="p-2 rounded-xl border text-left text-xs transition-all cursor-pointer">
                                        <div class="font-bold">🌸 Canlı Çiçek</div>
                                        <div class="text-[9px] text-gray-400">Canlı yaprak ve taçlar</div>
                                    </button>
                                    <button type="button" @click="setFilterPreset('romantic')"
                                            :class="studioFilter === 'romantic' ? 'border-amber-600 bg-amber-50 text-amber-900 font-black' : 'border-gray-200 bg-white text-gray-700'"
                                            class="p-2 rounded-xl border text-left text-xs transition-all cursor-pointer">
                                        <div class="font-bold">✨ Romantik Sıcak</div>
                                        <div class="text-[9px] text-gray-400">Ilık gün ışığı tonu</div>
                                    </button>
                                    <button type="button" @click="setFilterPreset('studio')"
                                            :class="studioFilter === 'studio' ? 'border-blue-600 bg-blue-50 text-blue-900 font-black' : 'border-gray-200 bg-white text-gray-700'"
                                            class="p-2 rounded-xl border text-left text-xs transition-all cursor-pointer">
                                        <div class="font-bold">📸 Aydınlık Stüdyo</div>
                                        <div class="text-[9px] text-gray-400">Net &amp; ferah beyaz ayarı</div>
                                    </button>
                                </div>
                            </div>

                            <!-- 4. Hassas Ayar Sürgüleri (Sliders) -->
                            <div class="space-y-2.5 pt-2 border-t border-gray-100">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                        <span>Parlaklık</span>
                                        <span class="font-mono text-gray-400" x-text="(studioBrightness > 0 ? '+' : '') + studioBrightness"></span>
                                    </div>
                                    <input type="range" min="-50" max="50" x-model.number="studioBrightness" @input="renderStudioCanvas()"
                                           class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-rose-600">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                        <span>Kontrast</span>
                                        <span class="font-mono text-gray-400" x-text="(studioContrast > 0 ? '+' : '') + studioContrast"></span>
                                    </div>
                                    <input type="range" min="-50" max="50" x-model.number="studioContrast" @input="renderStudioCanvas()"
                                           class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-rose-600">
                                </div>

                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                        <span>Canlılık / Doygunluk</span>
                                        <span class="font-mono text-gray-400" x-text="(studioSaturation > 0 ? '+' : '') + studioSaturation"></span>
                                    </div>
                                    <input type="range" min="-50" max="80" x-model.number="studioSaturation" @input="renderStudioCanvas()"
                                           class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-rose-600">
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Modal Alt Barı (Aksiyonlar) -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                        <button type="button" @click="setFilterPreset('none'); studioCropPreset = '1:1'; studioRotation = 0; studioFlipH = false; studioFlipV = false; renderStudioCanvas()"
                                class="text-xs font-bold text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">
                            <i class="fas fa-undo-alt text-[10px]"></i> Sıfırla
                        </button>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="studioModal = false"
                                    class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                                Vazgeç
                            </button>
                            <button type="button" @click="applyStudioCrop()" :disabled="studioLoading"
                                    class="px-5 py-2 bg-rose-600 hover:bg-rose-700 disabled:opacity-50 text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                                <i class="fas fa-spinner fa-spin" x-show="studioLoading" x-cloak></i>
                                <i class="fas fa-check" x-show="!studioLoading"></i>
                                <span x-text="studioLoading ? 'Kaydediliyor...' : 'Uygula &amp; WebP Kaydet'"></span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Quill.js Başlatma Scripti -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'clean']
            ];

            const descQuill = new Quill('#description_editor', {
                theme: 'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Ürünün tüm özelliklerini, avantajlarını ve detaylarını buraya yazın veya Word/web sayfasından kopyalayıp yapıştırın...'
            });
            window.descQuillInstance = descQuill;

            const techQuill = new Quill('#technical_features_editor', {
                theme: 'snow',
                modules: { toolbar: toolbarOptions },
                placeholder: 'Çiçek özelliklerini girin (Örn: • Dal / Adet Sayısı: 25 Kırmızı Gül, • Ambalaj: İtalyan Buket Kağıdı, • Vazo Ölçüsü: 30 cm...)'
            });
            window.techQuillInstance = techQuill;

            const form = document.getElementById('product-form');
            form.addEventListener('submit', function () {
                const descHtml = descQuill.root.innerHTML;
                const techHtml = techQuill.root.innerHTML;
                document.getElementById('description_input').value = descHtml === '<p><br></p>' ? '' : descHtml;
                document.getElementById('technical_features_input').value = techHtml === '<p><br></p>' ? '' : techHtml;
            });
        });
    </script>
</x-app-layout>
