# Mimari ve Altyapı Kuralları (Architecture Rules)

## Teknoloji Altyapısı
* **Framework:** Laravel 12 (PHP 8.3 / 8.5)
* **Veritabanı:** Yerel ortamda SQLite (`database/database.sqlite`), canlıda MySQL/MariaDB uyumlu.
* **Frontend:** TailwindCSS v4 + Alpine.js v3 + Vite v8.
* **Sunucu:** Laragon Apache (Vhost: `istanbul-cicek-evi.test`).

## Dizin Yapısı & Controller Standartları
* **Müşteri Uç Noktaları:** `app/Http/Controllers/`
  * `HomeController.php`: Ana sayfa, slider, öne çıkanlar.
  * `ProductController.php`: Ürün detay, filtreleme, arama.
  * `CustomFlowerController.php`: 2.5D Kendi Çiçeğini Tasarla motoru.
  * `CartController.php`: Sepet ve sipariş tamamlama.
* **Yönetici Uç Noktaları:** `app/Http/Controllers/Admin/`
  * `BulkPriceController.php`: Toplu Fiyat & İndirim/Zam paneli.
  * `ProductController.php`: Ürün CRUD & fotoğraf galerisi.
  * `OrderController.php`: Sipariş takibi ve durum yönetimi.
  * `DistrictController.php`: 39 ilçe teslimat ve ücret yönetimi.

## Kodlama Standartları
* Kontrol yapılarında daima süslü parantez `{}` kullanılmalıdır.
* PHP 8 constructor property promotion tercih edilmelidir.
* Tip ipuçları ve dönüş tipleri açıkça belirtilmelidir.
