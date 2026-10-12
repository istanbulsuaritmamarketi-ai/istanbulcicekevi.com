# Toplu Fiyatlandırma ve İndirim/Zam Kuralları (Pricing Rules)

## Rotalar ve Dosyalar
* **GET:** `/admin/pricing/bulk` (`admin.pricing.bulk`)
* **POST:** `/admin/pricing/bulk/apply` (`admin.pricing.bulk.apply`)
* **Controller:** `App\Http\Controllers\Admin\BulkPriceController`
* **Görünüm:** `resources/views/admin/pricing/bulk.blade.php`

## Desteklenen İşlem Türleri (`action_type`)
1. `markup`: Satış fiyatına zam uygulama (Yüzde veya Sabit TL).
2. `discount`: Satış fiyatından indirim düşme (Yüzde veya Sabit TL).
3. `set_campaign`: Satış fiyatını koruyup üzeri çizili indirimli kampanya fiyatı oluşturma (`campaign_price`).
4. `clear_campaign`: Tüm veya seçili ürünlerin kampanya fiyatını `null` yaparak kampanyayı sıfırlama.
5. `set_fixed`: Seçilen ürünlere belirlenen tek bir sabit fiyat atama.

## Yuvarlama Seçenekleri (`rounding`)
* `integer`: En yakın tam sayı (örn: 499.60 ➔ 500 ₺)
* `psychological_9`: 9 ile biten psikolojik fiyat (örn: 503 ➔ 499 ₺)
* `psychological_90`: .90 kuruş psikolojik fiyat (örn: 500 ➔ 499.90 ₺)
* `ten`: En yakın 10'luk (örn: 443 ➔ 440 ₺)
* `none`: Orijinal kuruş küsuratını koru.

## Arayüz Özelliği
* Alpine.js ile 0ms gecikmeli canlı simülasyon tablosu. Değer yazıldığı an tablo üzerinde anlık olarak Yeni Fiyat ve Fark (+/- ₺) gösterilir.
