# 🌸 İSTANBUL ÇİÇEK EVİ — KAPSAMLI SİSTEM & TEKNİK MİMARİ REHBERİ
**Sürüm:** 3.0.0 (Flower Studio & Bulk Pricing Edition)  
**Yerel Çalışma:** http://istanbul-cicek-evi.test  
**Güncelleme Tarihi:** 4 Ekim 2026  

---

## 📑 İÇİNDEKİLER
1. [Genel Bakış ve Teknoloji Yığını](#1-genel-bakış-ve-teknoloji-yığını)
2. [Sistem Mimarisi ve Dizin Ağacı](#2-sistem-mimarisi-ve-dizin-ağacı)
3. [Veritabanı Modelleri ve İlişki Haritası](#3-veritabanı-modelleri-ve-ilişki-haritası)
4. [Tüm Controller'lar ve Sorumluluk Alanları](#4-tüm-controllerlar-ve-sorumluluk-alanları)
5. [Kendi Çiçeğini Tasarla (2.5D Stüdyo Mimarisi)](#5-kendi-çiçeğini-tasarla-25d-stüdyo-mimarisi)
6. [Toplu Fiyat & İndirim / Zam Yönetim Paneli](#6-toplu-fiyat--indirim--zam-yönetim-paneli)
7. [Görsel Yönetimi ve Git Push Güvencesi](#7-görsel-yönetimi-ve-git-push-güvencesi)
8. [Admin Yönetim Paneli Modülleri](#8-admin-yönetim-paneli-modülleri)

---

## 🏢 1. GENEL BAKIŞ VE TEKNOLOJİ YIĞINI

İstanbul Çiçek Evi; butik çiçekçilik, özel gün hediyeleri, interaktif canlı 2.5D çiçek tasarım stüdyosu, İstanbul 39 ilçe teslimat organizasyonu ve gelişmiş e-ticaret yönetim araçlarını tek çatı altında buluşturan modern bir Laravel platformudur.

| Katman | Teknoloji / Kütüphane | Kullanım Amacı |
| :--- | :--- | :--- |
| **Backend Çekirdeği** | PHP 8.3 / 8.5 & Laravel 12 | MVC mimarisi, Eloquent ORM, Servis Katmanı, Blade Motoru |
| **Frontend Altyapısı** | TailwindCSS v4, Alpine.js v3, Vite v8 | Modern, reaktif, sıfır gereksiz bağımlılıklı lüks arayüz |
| **Veritabanı** | SQLite (Geliştirme) / MySQL (Canlı) | Dinamik ürün, kategori, malzeme ve sipariş tabloları |
| **Görsel & Medya** | 98 Gerçek Çiçek Fotoğrafı | Git-tracked `public/images/products` ve `storage/app/public/products` |
| **Tipografi** | Self-Hosted Figtree Fontları | Harici CDN zincirini ortadan kaldıran sıfır gecikmeli tipografi |

---

## 📁 2. SİSTEM MİMARİSİ VE DİZİN AĞACI

```text
istanbul-cicek-evi/
├── .agents/                                     # Antigravity ve AI Otonom Ajan Kuralları & Yetenekleri
│   ├── rules/                                   # Modüler kurallar (architecture, studio, pricing, assets)
│   └── skills/project-memory/                   # Sistem hafızası ve canlılık kontrol yeteneği
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                           # Yönetim Paneli Controller Grubu
│   │   │   │   ├── BulkPriceController.php      # 🏷️ Toplu Zam/İndirim ve Kampanya Paneli
│   │   │   │   ├── CategoryController.php       # Kategori yönetimi
│   │   │   │   ├── DashboardController.php      # İstatistikler, sipariş ve gelir grafikleri
│   │   │   │   ├── DistrictController.php       # 39 İlçe teslimat ve ücret yönetimi
│   │   │   │   ├── OrderController.php          # Sipariş yönetimi ve durum takibi
│   │   │   │   └── ProductController.php        # Çiçek ürünleri ve galeri yönetimi
│   │   │   ├── CartController.php               # Sepet, kupon ve teslimat seçimi
│   │   │   ├── CustomFlowerController.php       # 🌸 2.5D Kendi Çiçeğini Tasarla Motoru
│   │   │   ├── HomeController.php               # Ana sayfa, slider, vitrin
│   │   │   ├── OrderController.php              # Müşteri sipariş takibi ve tamamlama
│   │   │   └── ProductController.php            # Genel çiçek listeleme ve filtreleme
│   ├── Models/
│   │   ├── Category.php                         # Kategori modeli + image_url erişimcisi
│   │   ├── CustomDesign.php                     # Müşteri çiçek tasarım taslakları
│   │   ├── CustomOrder.php                      # Tasarlanan çiçeğin sipariş kaydı
│   │   ├── District.php                         # İstanbul 39 ilçe modeli
│   │   ├── FlowerMaterial.php                   # Tasarım stüdyosu malzemeleri
│   │   ├── Order.php                            # Ana sipariş modeli
│   │   ├── Product.php                          # Çiçek modeli + image_url erişimcisi
│   │   └── ProductImage.php                     # Ürün galeri fotoğrafları
├── database/
│   ├── migrations/                              # Veritabanı tablo şemaları
│   └── seeders/
│       ├── FloristSeeder.php                    # Gerçek fotoğraflı 23 ürün, 6 kategori, 39 ilçe tohumları
│       └── DatabaseSeeder.php                   # Ana veritabanı tohumlayıcı
├── public/
│   ├── images/products/                         # Git tarafından doğrudan takip edilen 98 gerçek fotoğraf
│   └── storage/products/                        # Doğrudan web erişim kopyası
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   └── pricing/bulk.blade.php           # Toplu fiyatlandırma paneli (Alpine.js canlı simülasyon)
│   │   ├── custom_flower/
│   │   │   └── studio.blade.php                 # 2.5D Kendi Çiçeğini Tasarla stüdyosu
│   │   └── components/
│   │       └── frontend-layout.blade.php        # Header, navbar, mobil çekmece ve footer
├── AGENTS.md                                    # Çoklu ajan hafıza ve bağlam belgesi
├── GEMINI.md                                    # Antigravity birincil workspace kural dosyası
└── MEMORY.md                                    # Karar defteri ve gelişim günlüğü
```

---

## 🌸 5. KENDİ ÇİÇEĞİNİ TASARLA (2.5D STÜDYO MİMARİSİ)
* Kullanıcı vazo/saksı türünü (cam silindir, kutu, saksı, tül/kraft ambalaj) seçer.
* Üzerine sap bazlı çiçekler (kırmızı gül, beyaz gül, pembe gül, orkide, papatya vb.) ve garnitürler (okaliptüs) yerleştirir.
* Seçilen her çiçeğin koordinatları ve katman sırası (z-index) tuval üzerinde canlı konumlanır.
* Her malzemenin `base_price` tutarı toplanarak sepete benzersiz bir özel sipariş olarak eklenir.

---

## 🏷️ 6. TOPLU FİYAT & İNDİRİM / ZAM YÖNETİM PANELİ
* **URL:** `/admin/pricing/bulk`
* Yönetici tüm kataloğa ya da kategoriye tek işlemle zam veya indirim uygulayabilir.
* Seçenekler: Zam (+), İndirim (-), Kampanya İndirimi (`campaign_price`), Kampanya Sıfırlama, Sabit Fiyat.
* Alpine.js ile form alanlarına yazıldığı anda alt tablodaki tüm çiçeklerin yeni fiyatı 0ms gecikmeyle simüle edilir.

---

## 📸 7. GÖRSEL YÖNETİMİ VE GİT PUSH GÜVENCESİ
* 98 adet gerçek çiçek fotoğrafı depolanmıştır.
* `storage/app/public/.gitignore` içine `!products/**` kuralı konularak fotoğrafların git commit'lerine ve `git push` komutuna dahil edilmesi garanti altına alınmıştır.