# 🌸 İSTANBUL ÇİÇEK EVİ — PROJE HAFIZASI & GELİŞTİRİCİ REHBERİ (GEMINI.md)

Bu dosya **Antigravity** ve tüm AI ajanları için birincil, kalıcı proje hafızasıdır. Her oturumda ve her işlem adımında bu kurallar ve mimari kararlar otomatik olarak geçerlidir.

---

## 🏛️ 1. Proje Kimliği ve Teknoloji Yığını
* **Proje Adı:** İstanbul Çiçek Evi (Lüks Çiçek E-Ticaret & Özel Çiçek Tasarım Stüdyosu)
* **Yerel URL (Laragon Vhost):** `http://istanbul-cicek-evi.test`
* **Yerel URL (Doğrudan Port):** `http://127.0.0.1:8000` veya `http://localhost:8000`
* **Geliştirme Sunucusu:** Laragon Apache (Port 80/443) & PHP Built-in Server (`php artisan serve --port=8000`), SQLite (`database/database.sqlite`)
* **Backend:** PHP 8.3 / 8.5 & Laravel 12
* **Frontend:** TailwindCSS v4, Alpine.js v3, Vite
* **Yönetici Girişi:** `admin@istanbulcicekevi.com` / `cicek123456`
* **Müşteri Hesabı:** `yigituzar43@gmail.com`

---

## 💎 2. Temel Modüller ve Mimari Kararlar

### A. 🎨 Kendi Çiçeğini Tasarla (2.5D Canlı Tasarım Stüdyosu)
* **URL:** `/kendi-cicegini-tasarla` (`route('flower.studio')`)
* **Controller:** `app/Http/Controllers/CustomFlowerController.php`
* **Blade Görünümü:** `resources/views/custom_flower/studio.blade.php`
* **Yönetici Stok Paneli:** `/admin/flower-studio/daily-stock`
* **Tablolar:**
  * `flower_materials` (type: `stem`, `vase`, `foliage`, `wrap`, `ribbon`, `accessory`, `category_id`, `base_price`, `stock`, `image_url`, `svg_path`)
  * `custom_designs`, `custom_orders`
* **Önemli Kural:** Stüdyo dosyaları, rotaları, kontrolleri ve veritabanı şemaları **asla silinmemeli ve bozulmamalıdır**. Menüdeki bağlantısı `frontend-layout.blade.php` dosyasında masaüstü ve mobil menüde yer alır.

### B. 🏷️ Toplu Fiyat & İndirim / Zam Yönetim Paneli
* **URL:** `/admin/pricing/bulk` (`route('admin.pricing.bulk')`)
* **Controller:** `app/Http/Controllers/Admin/BulkPriceController.php`
* **Blade Görünümü:** `resources/views/admin/pricing/bulk.blade.php`
* **Yetenekler:**
  * **İşlem:** Zam (`markup`), İndirim (`discount`), Kampanya Oluşturma (`set_campaign`), Kampanya Temizleme (`clear_campaign`), Sabit Fiyat (`set_fixed`).
  * **Hesaplama:** Yüzdesel (%) veya Sabit Tutar (₺).
  * **Kapsam:** Tüm ürünler (`all`), Kategoriye göre (`category`), Seçilen ürünler (`selected`).
  * **Yuvarlama:** En yakın tam sayı, Psikolojik .90, Psikolojik 9 ile biten, En yakın 10'luk, Kuruşlu.
  * **0ms Canlı Önizleme:** Alpine.js simülasyon tablosunda anlık yeni fiyat ve (+/- ₺) fark rozeti.

### C. 📸 Çiçek Fotoğrafları & Git Push Güvencesi
* **98 adet gerçek çiçek fotoğrafı** projede aktif kullanılmaktadır.
* **Depolama Lokasyonları:**
  * `public/images/products/` (Git tarafından doğrudan takip edilir).
  * `storage/app/public/products/` (`storage/app/public/.gitignore` içine `!products/**` kuralı eklenerek Git takibine alınmıştır).
  * `public/storage/products/` (Sembolik/doğrudan web erişimi).
* **Model Erişimi:** `Product`, `Category` ve `ProductImage` modellerinde `image_url` erişimcisi (accessor) mevcuttur.
* **Fallback Rotası:** `routes/web.php` içinde `/storage/products/{file}` rotası symlink olmayan ortamlarda bile fotoğrafların 404 vermesini engeller.
* **Kural:** `git add .` ve `git push` yapıldığında tüm çiçek fotoğrafları depoya dahil edilir.

### D. 🚚 İstanbul 39 İlçe & Teslimat Yönetimi
* **Model/Tablo:** `District` (`districts`)
* **Saat Dilimleri:** 10:00-14:00, 14:00-18:00, 18:00-22:00, VIP Gece Teslimatı.
* **Kargo Ücreti:** Ücretsiz teslimat eşiği (`delivery_fee`, `free_delivery_threshold`).

---

## ⚙️ 3. Rutin Komutlar & Bakım
* **Önbellek Temizleme:** `php artisan optimize:clear`
* **Vite Varlık Derleme:** `npm run build`
* **Servisleri Canlandırma:** Laragon başlatılır (`C:\laragon\laragon.exe`) veya `httpd.exe` port 80'de dinler.
* **Veritabanı Sıfırlama/Tohumlama:** `php artisan db:seed --class=FloristSeeder`

---

## 🚫 4. Yasaklar ve Kırmızı Çizgiler
1. `custom_flower` şablonlarını, kontrolcülerini ve tablolarını silmeyiniz.
2. `public/images/products` altındaki fotoğrafları `.gitignore` içine almayınız.
3. Model ve Blade dosyalarında doğrudan `asset('storage/...')` yerine daima `$product->image_url` erişimcisini kullanınız.
4. Her geliştirme ve arayüz/altyapı güncellemesinde kullanıcının masaüstündeki **`C:\Users\yigit\Desktop\ISTANBUL_CICEK_EVI_GELISIM_RAPORU.md`** dosyasını ve proje içi `MEMORY.md` kütüğünü eksiksiz güncel tutunuz.
