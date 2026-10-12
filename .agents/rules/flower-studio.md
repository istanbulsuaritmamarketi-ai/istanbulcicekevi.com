# Kendi Çiçeğini Tasarla (Flower Studio Rules)

## Amacı ve Önemi
Kullanıcıların vazo/ambalaj seçerek üzerine gül, orkide, papatya ve garnitür çiçekleri ekleyip dinamik fiyatlandırma ile kendi buketlerini oluşturabildiği 2.5D interaktif tasarım stüdyosudur.

## Kritik Dosyalar ve Yollar
* **Rota:** `/kendi-cicegini-tasarla` (`route('flower.studio')`)
* **Controller:** `App\Http\Controllers\CustomFlowerController`
* **Blade Şablonu:** `resources/views/custom_flower/studio.blade.php`
* **Admin Stok:** `resources/views/admin/custom_flower/daily_stock.blade.php`
* **Veritabanı Modelleri:** `FlowerMaterial`, `CustomDesign`, `CustomOrder`

## Malzeme Türleri (`flower_materials.type`)
1. `stem`: Ana çiçek sapları (Kırmızı Gül, Beyaz Gül, Pembe Gül, Papatya, Orkide vb.)
2. `vase`: Cam vazo, lüks kutu, seramik saksı, kraft ambalaj
3. `foliage`: Okaliptüs, yeşillik ve garnitürler
4. `wrap`: Buket kağıdı ve tül kaplamalar
5. `ribbon`: Kurdele ve fiyonklar
6. `accessory`: Çikolata, tebrik kartı, peluş vb.

## Koruma Kuralı
* Stüdyoya ait kontrolcü mantığı, sepet entegrasyonu veya veritabanı tabloları kesinlikle silinmemelidir.
* Menü bağlantıları `resources/views/components/frontend-layout.blade.php` içinde masaüstü (satır ~160) ve mobil çekmece (satır ~270) alanında yönetilir.
