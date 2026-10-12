<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'brands', 'images']);
        
        if ($request->filled('brand_id')) {
            $brandId = $request->brand_id;
            $query->where(function($q) use ($brandId) {
                $q->where('brand_id', $brandId)
                  ->orWhereHas('brands', fn($bq) => $bq->where('brands.id', $brandId));
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function toggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success', 'Ürün durumu güncellendi: ' . ($product->is_active ? 'Aktif (Satışta)' : 'Pasif (Gizlendi)'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $brands = Brand::with('children')->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'exists:brands,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'campaign_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'warranty_period' => 'nullable|string|max:100',
            'custom_badge' => 'nullable|string|max:50',
            'technical_features' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [
            'category_id.required' => 'Lütfen ürün kategorisini seçin.',
            'name.required' => 'Ürün adı zorunludur.',
            'price.required' => 'Ürün satış fiyatı zorunludur.',
            'image.mimes' => 'Ana görsel yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Ana görsel boyutu en fazla 25 MB olabilir.',
            'gallery_images.*.mimes' => 'Galeri görselleri yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'gallery_images.*.max' => 'Galeri görsellerinin her biri en fazla 25 MB olabilir.',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . ++$count;
        }

        $data = [
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'campaign_price' => $request->campaign_price,
            'cost_price' => $request->cost_price,
            'is_active' => $request->has('is_active'),
            'in_stock' => $request->has('in_stock'),
            'has_free_installation' => $request->has('has_free_installation'),
            'warranty_period' => $request->warranty_period ?: '2 Yıl Garanti',
            'has_cash_on_delivery' => $request->has('has_cash_on_delivery'),
            'custom_badge' => $request->custom_badge,
            'technical_features' => $request->technical_features,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
        ];

        // 1. Manuel Ana Kapak Görseli Yüklemesi
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'products', 1200, 1200, 85);
        } elseif ($request->filled('scraped_image_path')) {
            $data['image_path'] = $request->scraped_image_path;
        }

        $product = Product::create($data);

        // Uyumlu Marka & Alt Model Eşleştirmesi (Çoklu Seçim)
        if ($request->has('brand_ids')) {
            $product->brands()->sync($request->brand_ids);
        } elseif ($request->filled('brand_id')) {
            $product->brands()->sync([$request->brand_id]);
        }

        $galleryOrder = -1;

        // 2. Manuel Galeri Görselleri Kaydı
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $galleryFile) {
                if ($galleryFile && $galleryFile->isValid()) {
                    $path = ImageOptimizerService::optimizeAndStore($galleryFile, 'products/gallery', 1200, 1200, 85);
                    if ($path) {
                        $galleryOrder++;
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'sort_order' => $galleryOrder,
                        ]);
                        // Eğer ürünün ana kapak görseli boşsa ilk galeri görselini otomatik kapak yap
                        if (empty($product->image_path)) {
                            $product->update(['image_path' => $path]);
                        }
                    }
                }
            }
        }

        // 3. Scraped URL Galeri Görselleri Kaydı (Varsa)
        if ($request->has('scraped_gallery_paths') || $request->has('scraped_gallery_images')) {
            $galleryItems = $request->input('scraped_gallery_paths', $request->input('scraped_gallery_images', []));
            foreach ($galleryItems as $galleryPath) {
                if ($galleryPath) {
                    $galleryOrder++;
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $galleryPath,
                        'sort_order' => $galleryOrder,
                    ]);
                    if (empty($product->image_path)) {
                        $product->update(['image_path' => $galleryPath]);
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla oluşturuldu.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::with('children')->whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get();
        $selectedBrandIds = $product->brands()->pluck('brands.id')->toArray();
        if ($product->brand_id && !in_array($product->brand_id, $selectedBrandIds)) {
            $selectedBrandIds[] = $product->brand_id;
        }
        $product->load(['images', 'brands']);
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'selectedBrandIds'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'brand_ids' => 'nullable|array',
            'brand_ids.*' => 'exists:brands,id',
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'campaign_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|mimes:jpeg,png,jpg,webp,gif,svg,jfif,avif,bmp|max:25600',
            'warranty_period' => 'nullable|string|max:100',
            'custom_badge' => 'nullable|string|max:50',
            'technical_features' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ], [
            'category_id.required' => 'Lütfen ürün kategorisini seçin.',
            'name.required' => 'Ürün adı zorunludur.',
            'price.required' => 'Ürün satış fiyatı zorunludur.',
            'image.mimes' => 'Ana görsel yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'image.max' => 'Ana görsel boyutu en fazla 25 MB olabilir.',
            'gallery_images.*.mimes' => 'Galeri görselleri yalnızca JPG, PNG, WEBP, GIF, SVG veya AVIF formatında olmalıdır.',
            'gallery_images.*.max' => 'Galeri görsellerinin her biri en fazla 25 MB olabilir.',
        ]);

        $slug = Str::slug($request->name);
        if ($slug !== $product->slug) {
            $originalSlug = $slug;
            $count = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = "{$originalSlug}-" . ++$count;
            }
        }

        $data = [
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'price' => $request->price,
            'campaign_price' => $request->campaign_price,
            'cost_price' => $request->cost_price,
            'is_active' => $request->has('is_active'),
            'in_stock' => $request->has('in_stock'),
            'has_free_installation' => $request->has('has_free_installation'),
            'warranty_period' => $request->warranty_period ?: '2 Yıl Garanti',
            'has_cash_on_delivery' => $request->has('has_cash_on_delivery'),
            'custom_badge' => $request->custom_badge,
            'technical_features' => $request->technical_features,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
        ];

        // 1. Manuel Ana Kapak Görseli Değiştirme
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'products', 1200, 1200, 85);
        } elseif ($request->filled('scraped_image_path')) {
            $data['image_path'] = $request->scraped_image_path;
        }

        $product->update($data);

        // Uyumlu Marka & Alt Model Eşleştirmesi (Çoklu Seçim)
        if ($request->has('brand_ids')) {
            $product->brands()->sync($request->brand_ids);
        } else {
            $product->brands()->sync($request->filled('brand_id') ? [$request->brand_id] : []);
        }

        // 2. Yeni Galeri Görselleri Ekleme (Manuel Dosyalar)
        if ($request->hasFile('gallery_images')) {
            $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('gallery_images') as $galleryFile) {
                if ($galleryFile && $galleryFile->isValid()) {
                    $path = ImageOptimizerService::optimizeAndStore($galleryFile, 'products/gallery', 1200, 1200, 85);
                    if ($path) {
                        $currentMaxOrder++;
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'sort_order' => $currentMaxOrder,
                        ]);
                        // Eğer ürünün hiç kapak görseli yoksa bunu kapak yap
                        if (empty($product->image_path)) {
                            $product->update(['image_path' => $path]);
                        }
                    }
                }
            }
        }

        // 3. Scraped URL Galeri Görselleri Desteği
        if ($request->has('scraped_gallery_paths') || $request->has('scraped_gallery_images')) {
            $galleryItems = $request->input('scraped_gallery_paths', $request->input('scraped_gallery_images', []));
            $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($galleryItems as $galleryPath) {
                if ($galleryPath) {
                    $currentMaxOrder++;
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $galleryPath,
                        'sort_order' => $currentMaxOrder,
                    ]);
                    if (empty($product->image_path)) {
                        $product->update(['image_path' => $galleryPath]);
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Ürün güncellendi.');
    }

    public function deleteImage(Product $product, ProductImage $image)
    {
        if ((int) $image->product_id === (int) $product->id) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Galeri fotoğrafı başarıyla silindi.',
                    'remaining_count' => $product->images()->count(),
                ]);
            }

            return back()->with('success', 'Galeri görseli başarıyla silindi.');
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Görsel silinemedi veya bu ürüne ait değil.'
            ], 403);
        }

        return back()->with('error', 'Görsel silinemedi.');
    }

    public function deleteCoverImage(Product $product)
    {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->update(['image_path' => null]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ana kapak görseli başarıyla kaldırıldı.'
            ]);
        }

        return back()->with('success', 'Ana kapak görseli başarıyla kaldırıldı.');
    }

    public function setCoverImage(Product $product, ProductImage $image)
    {
        if ((int) $image->product_id === (int) $product->id) {
            $oldCover = $product->image_path;
            
            // Seçilen galeri görselini kapak yap
            $product->update(['image_path' => $image->image_path]);
            
            // Eğer eski kapak varsa onu galerideki bu slota devret, yoksa bu galeri kaydını sil
            if ($oldCover) {
                $image->update(['image_path' => $oldCover]);
            } else {
                $image->delete();
            }

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Seçilen görsel ana kapak fotoğrafı yapıldı.',
                    'new_cover_url' => asset('storage/' . $product->image_path),
                    'swapped_gallery_url' => $oldCover ? asset('storage/' . $oldCover) : null,
                    'swapped_gallery_id' => $oldCover ? $image->id : null,
                    'deleted_gallery_id' => $oldCover ? null : $image->id,
                ]);
            }

            return back()->with('success', 'Seçilen görsel ürünün ana kapak fotoğrafı yapıldı.');
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'İşlem başarısız oldu.'
            ], 403);
        }

        return back()->with('error', 'İşlem başarısız oldu.');
    }

    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Ürün silindi.');
    }

    /**
     * 📥 Toplu Ürün İçe Aktarma (Excel / CSV Import)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:20480',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        try {
            if (in_array($extension, ['csv', 'txt'])) {
                $rows = $this->parseCsvFile($file->getRealPath());
            } elseif ($extension === 'xlsx') {
                $rows = $this->parseXlsxFile($file->getRealPath());
            } else {
                // Varsayılan olarak CSV şeklinde dene
                $rows = $this->parseCsvFile($file->getRealPath());
            }

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'Yüklenen dosyada okunabilir ürün satırı bulunamadı. Lütfen örnek şablonu indirip doldurunuz.');
            }

            $headers = array_map(function ($h) {
                return Str::slug(trim(strval($h)), '_');
            }, $rows[0]);

            $importedCount = 0;
            $updatedCount = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }

                $data = [];
                foreach ($headers as $index => $key) {
                    if (isset($row[$index])) {
                        $data[$key] = trim(strval($row[$index]));
                    }
                }

                // Ürün Adı Tespiti
                $name = $data['urun_adi'] ?? $data['name'] ?? $data['urun'] ?? $data['baslik'] ?? null;
                if (!$name) {
                    continue;
                }

                // Kategori Tespiti / Otomatik Oluşturma
                $categoryName = $data['kategori'] ?? $data['category'] ?? $data['kategori_adi'] ?? 'Genel';
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName), 'is_active' => true, 'sort_order' => 0]
                );

                // Fiyat Ayrıştırma
                $rawPrice = $data['satis_fiyati'] ?? $data['fiyat'] ?? $data['price'] ?? '0';
                $price = $this->cleanPrice($rawPrice);

                $rawCostPrice = $data['maliyet_alis_fiyati'] ?? $data['alis_fiyati'] ?? $data['cost_price'] ?? null;
                $costPrice = $rawCostPrice !== null ? $this->cleanPrice($rawCostPrice) : null;

                $shortDesc = $data['kisa_aciklama'] ?? $data['short_description'] ?? null;
                $description = $data['detayli_aciklama'] ?? $data['aciklama'] ?? $data['description'] ?? null;
                $badge = $data['ozel_rozet'] ?? $data['rozet'] ?? $data['badge'] ?? null;
                $warranty = $data['garanti_suresi'] ?? $data['garanti'] ?? '2 Yıl Garanti';
                
                $freeInstall = isset($data['ucretsiz_montaj_1_0']) ? (bool) $data['ucretsiz_montaj_1_0'] : (isset($data['ucretsiz_montaj']) ? (bool) $data['ucretsiz_montaj'] : true);
                $inStock = isset($data['stok_durumu']) ? (bool) $data['stok_durumu'] : true;
                $isActive = isset($data['aktiflik_durumu']) ? (bool) $data['aktiflik_durumu'] : true;

                $slug = Str::slug($name);

                // Ürünü Bul veya Oluştur
                $product = Product::where('name', $name)->orWhere('slug', $slug)->first();

                if ($product) {
                    $product->update([
                        'category_id' => $category->id,
                        'price' => $price,
                        'cost_price' => $costPrice,
                        'short_description' => $shortDesc ?: $product->short_description,
                        'description' => $description ?: $product->description,
                        'custom_badge' => $badge ?: $product->custom_badge,
                        'warranty_period' => $warranty ?: $product->warranty_period,
                        'has_free_installation' => $freeInstall,
                        'in_stock' => $inStock,
                        'is_active' => $isActive,
                    ]);
                    $updatedCount++;
                } else {
                    $originalSlug = $slug;
                    $countSlug = 1;
                    while (Product::where('slug', $slug)->exists()) {
                        $slug = "{$originalSlug}-" . ++$countSlug;
                    }

                    Product::create([
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => $slug,
                        'price' => $price,
                        'cost_price' => $costPrice,
                        'short_description' => $shortDesc,
                        'description' => $description,
                        'custom_badge' => $badge,
                        'warranty_period' => $warranty,
                        'has_free_installation' => $freeInstall,
                        'has_cash_on_delivery' => true,
                        'in_stock' => $inStock,
                        'is_active' => $isActive,
                    ]);
                    $importedCount++;
                }
            }

            return back()->with('success', "🎉 Toplu İçe Aktarma Başarılı! {$importedCount} yeni ürün eklendi, {$updatedCount} ürün güncellendi.");
        } catch (\Exception $e) {
            return back()->with('error', 'Dosya işlenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * 📄 Örnek Excel / CSV Yükleme Şablonunu İndir
     */
    public function downloadSampleTemplate()
    {
        $fileName = 'ornek_urun_yukleme_sablonu.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Excel'in Türkçe karakterleri (ğ, ş, ı, İ, ç, ö, ü) otomatik tanıması için UTF-8 BOM ekle
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Başlık Sütunları
            fputcsv($handle, [
                'Ürün Adı',
                'Kategori',
                'Satış Fiyatı',
                'Maliyet / Alış Fiyatı',
                'Stok Durumu',
                'Ücretsiz Montaj (1/0)',
                'Özel Rozet',
                'Garanti Süresi',
                'Kısa Açıklama',
                'Detaylı Açıklama'
            ], ';');

            // Örnek Satırlar
            $samples = [
                [
                    '12 Aşamalı Kapalı Kasa Su Arıtma Cihazı (Alkali + Mineral)',
                    'Ev Tipi Su Arıtma',
                    '4750',
                    '2500',
                    '1',
                    '1',
                    'En Çok Tercih Edilen',
                    '2 Yıl Garanti',
                    'Alkali pH 8.5+ filtresi ve antibakteriyel tankı ile 4-6 kişilik aileler için ideal seçim.',
                    '12 aşamalı ters ozmoz filtrasyon sistemi, sessiz hidrofor pompası, NSF onaylı antibakteriyel çelik tank ve şık krom lüks musluk.'
                ],
                [
                    '5 Aşamalı Tezgah Altı Klasik Su Arıtma Cihazı (Pompasız)',
                    'Ev Tipi Su Arıtma',
                    '3450',
                    '1800',
                    '1',
                    '1',
                    'Ekonomik Model',
                    '2 Yıl Garanti',
                    'Elektrik gerektirmeyen, minimum yer kaplayan çekirdek aileler için arıtma çözümü.',
                    '5 aşamalı ters ozmoz filtrasyon, elektrik gerektirmeyen tasarım, LG membran filtre ve ücretsiz yerinde montaj.'
                ],
                [
                    'Orijinal 5li Filtre Değişim Seti (Sediment + Karbon + Membran)',
                    'Filtre Setleri',
                    '1250',
                    '600',
                    '1',
                    '1',
                    'Fırsat Paketi',
                    '1 Yıl Garanti',
                    'Tüm marka ve model su arıtma cihazlarıyla %100 uyumlu orijinal filtre seti.',
                    'Sediment tortu filtresi, granül aktif karbon, blok karbon, post tatlandırıcı karbon ve orijinal membran filtre.'
                ]
            ];

            foreach ($samples as $sample) {
                fputcsv($handle, $sample, ';');
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * CSV Dosyasını Satır Dizisine Çevirir
     */
    private function parseCsvFile(string $filePath): array
    {
        $rows = [];
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return $rows;
        }

        $content = file_get_contents($filePath);
        // BOM temizleme
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        // Ayraç tespiti (; veya ,)
        $firstLine = strtok($content, "\r\n");
        $delimiter = (substr_count($firstLine, ';') >= substr_count($firstLine, ',')) ? ';' : ',';

        $handle = fopen($filePath, 'r');
        while (($row = fgetcsv($handle, 4096, $delimiter)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /**
     * XLSX Dosyasını Native Zip & XML ile Okur (Zero Dependency)
     */
    private function parseXlsxFile(string $filePath): array
    {
        $rows = [];
        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            return $rows;
        }

        // Shared Strings Okuma
        $sharedStrings = [];
        if (($index = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->si as $val) {
                $sharedStrings[] = (string) ($val->t ?: (isset($val->r) ? implode('', array_map('strval', $val->xpath('.//t'))) : ''));
            }
        }

        // 1. Çalışma Sayfasını Okuma (sheet1.xml)
        if (($index = $zip->locateName('xl/worksheets/sheet1.xml')) !== false) {
            $xml = simplexml_load_string($zip->getFromIndex($index));
            foreach ($xml->sheetData->row as $rowNode) {
                $row = [];
                foreach ($rowNode->c as $cell) {
                    $attr = $cell->attributes();
                    $type = isset($attr['t']) ? (string) $attr['t'] : '';
                    $val = (string) $cell->v;

                    if ($type === 's' && isset($sharedStrings[(int) $val])) {
                        $val = $sharedStrings[(int) $val];
                    }

                    $row[] = $val;
                }
                $rows[] = $row;
            }
        }

        $zip->close();
        return $rows;
    }

    /**
     * Fiyat Dizgisini Sayısala Çevirir
     */
    private function cleanPrice($raw): float
    {
        $cleaned = trim(str_replace(['TL', '₺', ' ', '$', '€'], '', strval($raw)));
        // Virgül ile ayrılmış ondalık (örn: 4.750,50 veya 4750,00)
        if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } elseif (strpos($cleaned, ',') !== false) {
            $cleaned = str_replace(',', '.', $cleaned);
        }
        return floatval($cleaned);
    }

    /**
     * ⚡ URL'den Ürün Bilgilerini ve Tüm Galeri Görsellerini Çeker (AJAX)
     */
    public function scrapeUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $scraped = \App\Services\ProductScraperService::scrape($request->url);

        if (empty($scraped['name'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bu bağlantıdan ürün bilgisi otomatik okunamadı. Lütfen geçerli bir ürün sayfası linki giriniz.',
            ], 422);
        }

        // Ana görseli indirip optimize et (Referer olarak sayfa URL'si geçilerek CDN 403 hotlink koruması aşılır)
        $localImagePath = null;
        if (!empty($scraped['image_url'])) {
            $localImagePath = \App\Services\ProductScraperService::downloadAndSaveImage($scraped['image_url'], $request->url);
        }

        // Galeri görsellerini indirip optimize et
        $localGalleryPaths = [];
        $localGalleryAssets = [];
        if (!empty($scraped['gallery_images'])) {
            foreach ($scraped['gallery_images'] as $gUrl) {
                $savedGPath = \App\Services\ProductScraperService::downloadAndSaveImage($gUrl, $request->url);
                if ($savedGPath) {
                    $localGalleryPaths[] = $savedGPath;
                    $localGalleryAssets[] = asset('storage/' . $savedGPath);
                }
            }
        }

        // Fallback: Ana görsel indirilemediyse ama galeride görsel varsa ilkini ana kapak yap
        if (!$localImagePath && !empty($localGalleryPaths)) {
            $localImagePath = array_shift($localGalleryPaths);
            array_shift($localGalleryAssets);
        }

        // Yapay zeka ile otomatik özelleştirme talep edilmişse
        $aiNotice = null;
        if ($request->boolean('use_ai')) {
            $aiData = \App\Services\AiProductService::rewriteProduct([
                'name' => $scraped['name'],
                'description' => $scraped['description'],
                'technical_features' => '',
            ], $request->input('custom_instruction'));

            $scraped['name'] = $aiData['name'] ?? $scraped['name'];
            $scraped['description'] = $aiData['description'] ?? $scraped['description'];
            $scraped['short_description'] = $aiData['short_description'] ?? $scraped['short_description'];
            $scraped['technical_features'] = $aiData['technical_features'] ?? '';
            $scraped['seo_title'] = $aiData['seo_title'] ?? '';
            $scraped['seo_description'] = $aiData['seo_description'] ?? '';
            $aiNotice = $aiData['notice'] ?? null;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $scraped['name'],
                'price' => $scraped['price'],
                'campaign_price' => $scraped['campaign_price'] ?? null,
                'short_description' => $scraped['short_description'],
                'description' => $scraped['description'],
                'technical_features' => $scraped['technical_features'] ?? null,
                'seo_title' => $scraped['seo_title'] ?? null,
                'seo_description' => $scraped['seo_description'] ?? null,
                'image_url' => $scraped['image_url'],
                'local_image_path' => $localImagePath,
                'local_image_asset' => $localImagePath ? asset('storage/' . $localImagePath) : null,
                'local_gallery_paths' => $localGalleryPaths,
                'local_gallery_assets' => $localGalleryAssets,
                'ai_notice' => $aiNotice,
            ],
            'message' => $request->boolean('use_ai') 
                ? 'Ürün bilgileri, fotoğrafları çekildi ve yapay zeka ile firmanıza özel uyarlandı!' 
                : 'Ürün bilgileri, tüm fotoğrafları ve fiyatı başarıyla çekildi!',
        ]);
    }

    /**
     * ✨ Mevcut Ürün Verilerini Yapay Zeka ile Firma Kimliğine Göre Yeniden Yazar (AJAX)
     */
    public function aiRewrite(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'technical_features' => 'nullable|string',
            'short_description' => 'nullable|string',
            'custom_instruction' => 'nullable|string',
            'target_field' => 'nullable|string|in:all,short_description,description,technical_features,seo',
        ]);

        $targetField = $request->input('target_field', 'all');

        $aiData = \App\Services\AiProductService::rewriteProduct([
            'name' => $request->name,
            'description' => $request->description,
            'technical_features' => $request->technical_features,
            'short_description' => $request->short_description,
        ], $request->custom_instruction, $targetField);

        $messages = [
            'all' => 'Tüm ürün metinleri firmanıza özel başarıyla uyarlandı!',
            'short_description' => 'Kısa özet açıklama yapay zeka ile başarıyla oluşturuldu!',
            'description' => 'Detaylı ürün açıklaması yapay zeka ile zengin HTML formatında hazırlandı!',
            'technical_features' => 'Teknik özellikler listesi yapay zeka ile çıkarıldı!',
            'seo' => 'SEO başlığı ve açıklaması yapay zeka ile optimize edildi!',
        ];

        return response()->json([
            'success' => true,
            'target_field' => $targetField,
            'data' => $aiData,
            'message' => $messages[$targetField] ?? 'İçerik başarıyla üretildi!',
        ]);
    }

    /**
     * 🎨 Yapay Zeka ile Profesyonel Ürün Görseli Üretir (AJAX)
     */
    public function aiGenerateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        $savedPath = \App\Services\AiProductService::generateImage($request->prompt);

        if (!$savedPath) {
            return response()->json([
                'success' => false,
                'message' => 'Yapay zeka görseli üretirken bir sorun oluştu. Lütfen bağlantıyı kontrol edin veya promptu değiştirin.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'image_path' => $savedPath,
            'image_asset' => asset('storage/' . $savedPath),
            'message' => '✨ Yapay zeka stüdyo görseliniz başarıyla üretildi ve galeriye hazırlandı!',
        ]);
    }

    /**
     * ✂️ Görseli E-Ticaret Beyaz Fonuna Dönüştürür (AJAX)
     */
    public function formatEcommerceImage(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $formattedPath = \App\Services\AiProductService::formatToEcommerceWhiteBackground($request->image_path);

        if (!$formattedPath) {
            return response()->json([
                'success' => false,
                'message' => 'Görsel dönüştürülürken bir sorun oluştu veya dosya bulunamadı.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'image_path' => $formattedPath,
            'image_asset' => asset('storage/' . $formattedPath),
            'message' => '✨ Görsel başarıyla bembeyaz e-ticaret stüdyo formatına uyarlandı!',
        ]);
    }

    /**
     * ✂️ Mevcut bir ürünün ana kapak görselini e-ticaret beyaz fonuna dönüştürür ve kaydeder.
     */
    public function formatExistingCover(Product $product)
    {
        if (!$product->image_path) {
            return response()->json(['success' => false, 'message' => 'Ürünün kapak görseli bulunmuyor.'], 404);
        }

        $formattedPath = \App\Services\AiProductService::formatToEcommerceWhiteBackground($product->image_path);
        if (!$formattedPath) {
            return response()->json(['success' => false, 'message' => 'Görsel dönüştürülemedi.'], 500);
        }

        $product->update(['image_path' => $formattedPath]);

        return response()->json([
            'success' => true,
            'image_asset' => asset('storage/' . $formattedPath),
            'message' => 'Kapak görseli başarıyla beyaz e-ticaret stüdyo fonuna dönüştürüldü ve kaydedildi!',
        ]);
    }

    /**
     * ✂️ Mevcut bir galeri görselini e-ticaret beyaz fonuna dönüştürür ve kaydeder.
     */
    public function formatExistingGalleryImage(ProductImage $image)
    {
        if (!$image->image_path) {
            return response()->json(['success' => false, 'message' => 'Görsel bulunamadı.'], 404);
        }

        $formattedPath = \App\Services\AiProductService::formatToEcommerceWhiteBackground($image->image_path);
        if (!$formattedPath) {
            return response()->json(['success' => false, 'message' => 'Görsel dönüştürülemedi.'], 500);
        }

        $image->update(['image_path' => $formattedPath]);

        return response()->json([
            'success' => true,
            'image_asset' => asset('storage/' . $formattedPath),
            'message' => 'Galeri fotoğrafı beyaz e-ticaret fonuna dönüştürüldü ve kaydedildi!',
        ]);
    }

    /**
     * 🎨 Mevcut ürüne yapay zeka ile görsel üretip doğrudan galeriye ekler.
     */
    public function aiGenerateAndAttach(Request $request, Product $product)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        $savedPath = \App\Services\AiProductService::generateImage($request->prompt);
        if (!$savedPath) {
            return response()->json(['success' => false, 'message' => 'Yapay zeka görseli üretilemedi.'], 500);
        }

        $newImage = $product->images()->create([
            'image_path' => $savedPath,
        ]);

        return response()->json([
            'success' => true,
            'image_id' => $newImage->id,
            'image_asset' => asset('storage/' . $savedPath),
            'message' => 'Yapay zeka stüdyo fotoğrafı üretildi ve ürünün galerisine eklendi!',
        ]);
    }

    /**
     * 🎨 Canvas Stüdyoda Düzenlenen Görseli Kaydet (Base64 WebP / JPEG)
     */
    public function saveEditedCanvasImage(Request $request)
    {
        $request->validate([
            'image_data' => 'required|string',
            'product_id' => 'nullable|integer|exists:products,id',
            'target_type' => 'nullable|string|in:cover,gallery_replace,gallery_add,standalone,gallery',
            'type' => 'nullable|string|in:cover,gallery',
            'gallery_image_id' => 'nullable|integer',
        ]);

        $base64 = $request->image_data;
        if (preg_match('/^data:image\/(\w+);base64,/', $base64)) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }
        $decoded = base64_decode($base64);
        if (!$decoded) {
            return response()->json(['success' => false, 'message' => 'Geçersiz görsel verisi.'], 422);
        }

        $filename = 'products/studio_' . uniqid() . '_' . time() . '.webp';
        Storage::disk('public')->put($filename, $decoded);

        $targetType = $request->target_type ?? ($request->type === 'cover' ? 'cover' : 'gallery_replace');

        if ($request->product_id) {
            $product = Product::findOrFail($request->product_id);
            if ($targetType === 'cover' || $request->type === 'cover') {
                if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $product->update(['image_path' => $filename]);
                return response()->json([
                    'success' => true,
                    'image_path' => $filename,
                    'image_asset' => asset('storage/' . $filename),
                    'message' => 'Ana kapak görseli başarıyla güncellendi!'
                ]);
            } elseif (($targetType === 'gallery_replace' || $request->type === 'gallery') && $request->gallery_image_id) {
                $img = ProductImage::where('product_id', $product->id)->find($request->gallery_image_id);
                if ($img) {
                    if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    $img->update(['image_path' => $filename]);
                    return response()->json([
                        'success' => true,
                        'image_id' => $img->id,
                        'image_path' => $filename,
                        'image_asset' => asset('storage/' . $filename),
                        'message' => 'Galeri fotoğrafı başarıyla güncellendi!'
                    ]);
                }
            } elseif ($targetType === 'gallery_add') {
                $maxOrder = $product->images()->max('sort_order') ?? -1;
                $newImg = $product->images()->create([
                    'image_path' => $filename,
                    'sort_order' => $maxOrder + 1,
                ]);
                return response()->json([
                    'success' => true,
                    'image_id' => $newImg->id,
                    'image_path' => $filename,
                    'image_asset' => asset('storage/' . $filename),
                    'message' => 'Fotoğraf galeriye eklendi!'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'image_path' => $filename,
            'image_asset' => asset('storage/' . $filename),
            'message' => 'Görsel başarıyla kaydedildi!'
        ]);
    }

    /**
     * 🌐 URL'den Görsel İndir ve Yerel Depoya Kaydet
     */
    public function fetchImageUrl(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->url;
            $response = \Illuminate\Support\Facades\Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'])
                ->get($url);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'Görsel indirilemedi. Durum kodu: ' . $response->status()], 400);
            }

            $content = $response->body();
            $contentType = $response->header('Content-Type');
            $ext = 'webp';
            if (str_contains($contentType, 'png')) $ext = 'png';
            elseif (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg')) $ext = 'jpg';
            elseif (str_contains($contentType, 'webp')) $ext = 'webp';
            elseif (str_contains($contentType, 'gif')) $ext = 'gif';

            $filename = 'products/url_' . uniqid() . '_' . time() . '.' . $ext;
            Storage::disk('public')->put($filename, $content);

            return response()->json([
                'success' => true,
                'image_path' => $filename,
                'image_asset' => asset('storage/' . $filename),
                'message' => 'Görsel linkten başarıyla indirildi!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Hata: ' . $e->getMessage()], 500);
        }
    }
}

