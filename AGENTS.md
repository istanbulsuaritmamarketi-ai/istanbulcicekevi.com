# 🌸 İSTANBUL ÇİÇEK EVİ — AGENTS MEMORY & CONTEXT (AGENTS.md)

Bu dosya, projede çalışan tüm otonom ajanlar (Antigravity, Claude, Cursor, Copilot vb.) için merkezi kural ve hafıza belgesidir.

---

## 🚀 Hızlı Başlangıç & Sistem Durumu
* **Uygulama:** İstanbul Çiçek Evi (Laravel 12 / PHP 8.3+)
* **Çalışma URL'i (Vhost):** `http://istanbul-cicek-evi.test`
* **Alternatif Port:** `http://127.0.0.1:8000` veya `http://localhost:8000`
* **Veritabanı:** SQLite (`database/database.sqlite`)
* **Yönetici Bilgileri:** `admin@istanbulcicekevi.com` / `cicek123456`
* **Ana Rotalar:**
  * Ana Sayfa: `/`
  * Çiçek Tasarım Stüdyosu: `/kendi-cicegini-tasarla` (`route('flower.studio')`)
  * Toplu Fiyatlandırma: `/admin/pricing/bulk` (`route('admin.pricing.bulk')`)
  * Ürün Yönetimi: `/admin/products`
  * Siparişler: `/admin/orders`
  * İlçe Teslimat: `/admin/districts`

---

## 📌 Mimari Kurallar ve Standartlar
1. **Model Görsel Erişimi:**
   * Ürün veya kategori görselleri çekilirken `$product->image_url` erişimcisi kullanılmalıdır.
   * Projede 98 gerçek çiçek fotoğrafı hem `public/images/products/` hem `storage/app/public/products/` altında Git takipli olarak mevcuttur.
2. **Kendi Çiçeğini Tasarla (Flower Studio):**
   * Controller: `App\Http\Controllers\CustomFlowerController`
   * View: `resources/views/custom_flower/studio.blade.php`
   * Günlük Stok: `resources/views/admin/custom_flower/daily_stock.blade.php`
   * Asla kodları veya migration tablolarını silmeyiniz.
3. **Toplu Fiyatlandırma:**
   * Controller: `App\Http\Controllers\Admin\BulkPriceController`
   * View: `resources/views/admin/pricing/bulk.blade.php`
   * Tüm matematiksel işlemler (zam, indirim, kampanya, yuvarlama) bu denetleyici üzerinden yürütülür.
4. **Önbellek & Varlıklar:**
   * Blade şablon veya CSS/JS değişikliklerinden sonra: `php artisan optimize:clear` ve `npm run build` çalıştırılmalıdır.
