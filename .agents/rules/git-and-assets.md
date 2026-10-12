# Görsel Yönetimi ve Git Güvencesi (Git & Assets Rules)

## Kritik Kural: 98 Gerçek Çiçek Fotoğrafı Git Takibindedir
* Tüm fotoğraflar:
  1. `public/images/products/`
  2. `storage/app/public/products/`
  3. `public/storage/products/`
* `storage/app/public/.gitignore` içine `!products/**` kuralı yazılmıştır.
* `git status` veya `git push` yapıldığında görseller asla göz ardı edilmez.

## Model Görsel Erişimi
* `Product::getImageUrlAttribute()`
* `Category::getImageUrlAttribute()`
* `ProductImage::getImageUrlAttribute()`
* Şablonlarda doğrudan `asset('storage/' . $product->image)` yerine daima `$product->image_url` erişimcisi kullanılmalıdır. Bu sayede dosya yolu ister `products/1.png` ister tam url olsun otomatik çözümlenir.

## Güvenli Fallback Rotası
* `routes/web.php` içinde yer alan `storage/products/{file}` rotası, sunucu ortamında `php artisan storage:link` symlink'i bulunmasa dahi fotoğrafların doğrudan `storage/app/public/products` üzerinden 404 vermeden sunulmasını sağlar.
