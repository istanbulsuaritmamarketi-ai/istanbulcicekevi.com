# Yönetici ve Kimlik Doğrulama Kuralları (Admin & Auth Rules)

## Yönetici Hesapları & Veritabanı Seed
* **Yönetici E-Posta:** `admin@istanbulcicekevi.com`
* **Şifre:** `cicek123456`
* **Rol:** `superadmin`, `is_admin = true`
* **Test Müşteri:** `yigituzar43@gmail.com`

## Yönetici Menüsü (Sidebar) Bileşenleri
* `resources/views/layouts/navigation.blade.php`:
  * 📊 Gösterge Paneli (`/admin/dashboard`)
  * 🌸 Çiçek Kataloğu (`/admin/products`)
  * 🏷️ Toplu Fiyat & İndirim/Zam (`/admin/pricing/bulk`)
  * 📦 Siparişler (`/admin/orders`)
  * 📍 İlçe & Teslimat Ayarları (`/admin/districts`)
  * 🎨 Çiçek Stüdyosu Günlük Stok (`/admin/flower-studio/daily-stock`)
