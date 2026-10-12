<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kategori/{slug}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/kategoriler', [FrontendController::class, 'categories'])->name('categories.index');
Route::get('/tasarruf-hesapla', [FrontendController::class, 'calculator'])->name('calculator');
Route::get('/cicek-sihirbazi', [FrontendController::class, 'wizard'])->name('wizard');
Route::get('/cihaz-sihirbazi', fn() => redirect()->route('wizard', [], 301));
Route::get('/filtre-bulucu', [FrontendController::class, 'filterFinder'])->name('filter.finder');
Route::get('/marka/{brand:slug}', [FrontendController::class, 'brandShow'])->name('brand.show');
Route::get('/arimo', [\App\Http\Controllers\ArimoController::class, 'index'])->name('arimo');
Route::post('/arimo/ask', [\App\Http\Controllers\ArimoController::class, 'ask'])->name('arimo.ask');
Route::get('/urunler', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/urun/{slug}', [FrontendController::class, 'product'])->name('product.show');
Route::get('/hizmet-bolgeleri', [\App\Http\Controllers\DistrictController::class, 'index'])->name('districts.index');
Route::get('/istanbul-su-aritma/{slug}', [\App\Http\Controllers\DistrictController::class, 'show'])->name('district.show');
Route::get('/istanbul-cicek-siparisi/{slug}', [\App\Http\Controllers\DistrictController::class, 'show'])->name('district.cicek');
Route::get('/siparis-takip', [FrontendController::class, 'trackOrder'])->name('order.track');
Route::get('/servis-talebi', [App\Http\Controllers\ServiceController::class, 'create'])->name('service.create');
Route::post('/servis-talebi', [App\Http\Controllers\ServiceController::class, 'store'])->name('service.store');
Route::get('/sayfa/{slug}', [FrontendController::class, 'page'])->name('page.show');
Route::get('/sosyal-medya', [FrontendController::class, 'social'])->name('social');

// 🌸 Kendi Çiçeğini Tasarla (2.5D Canlı Tasarım Stüdyosu)
Route::get('/kendi-cicegini-tasarla', [\App\Http\Controllers\CustomFlowerController::class, 'studio'])->name('flower.studio');
Route::post('/kendi-cicegini-tasarla/sepete-ekle', [\App\Http\Controllers\CustomFlowerController::class, 'addToCart'])->name('flower.studio.addToCart');

// KVKK Çerez Rıza Kaydı (Public Endpoint)
Route::post('/cookie-consent/log', [\App\Http\Controllers\CookieConsentController::class, 'store'])->name('cookieConsent.store');

// SEO XML Sitemap, Robots.txt & Google Merchant Center (Google Alışveriş)
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\SitemapController::class, 'robots'])->name('robots');
Route::get('/google-shopping.xml', [\App\Http\Controllers\FeedController::class, 'googleMerchant'])->name('feed.googleShopping');
Route::get('/feed/google-merchant.xml', [\App\Http\Controllers\FeedController::class, 'googleMerchant'])->name('feed.googleMerchant');

// Blog & Rehber Rotaları
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Ürün Yorum & Değerlendirme Rotası
Route::post('/urun/{product}/yorum', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Contact Route
Route::get('iletisim', [\App\Http\Controllers\FrontendController::class, 'contact'])->name('contact');
Route::post('iletisim', [\App\Http\Controllers\FrontendController::class, 'storeContact'])->name('contact.store');

// Cart routes
Route::get('/sepet', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/sepet/ekle/{id}', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/sepet/guncelle/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::post('/sepet/sil/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/sepet/temizle', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/sepet/kupon-uygula', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/sepet/kupon-kaldir', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::get('/odeme', [App\Http\Controllers\CartController::class, 'checkout'])->name('checkout');
Route::post('/odeme', [App\Http\Controllers\CartController::class, 'processCheckout'])->name('checkout.process');
Route::get('/odeme/basarili/{order:order_number}', [App\Http\Controllers\CartController::class, 'success'])->name('cart.success');
// PayTR Online Ödeme Rotaları (Webhook & Hata Sayfası)
Route::post('/paytr/callback', [\App\Http\Controllers\PayTRController::class, 'callback'])->name('paytr.callback');
Route::get('/odeme/hata/{order:order_number}', [\App\Http\Controllers\PayTRController::class, 'fail'])->name('paytr.fail');

Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/hesabim', [\App\Http\Controllers\CustomerController::class, 'account'])->name('customer.account');
    Route::get('/hesabim/kupon-kullan/{code}', [\App\Http\Controllers\CustomerController::class, 'applyMyCoupon'])->name('customer.coupons.apply');
    Route::post('/hesabim/profil-guncelle', [\App\Http\Controllers\CustomerController::class, 'updateProfile'])->name('customer.profile.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin Routes
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::delete('categories/{category}/image', [\App\Http\Controllers\Admin\CategoryController::class, 'deleteImage'])->name('categories.image.destroy');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        
        // Products & Bulk Excel Import & URL Scraper & Image Management
        Route::patch('products/{product}/toggle', [\App\Http\Controllers\Admin\ProductController::class, 'toggle'])->name('products.toggle');
        Route::post('products/scrape-url', [\App\Http\Controllers\Admin\ProductController::class, 'scrapeUrl'])->name('products.scrapeUrl');
        Route::post('products/ai-rewrite', [\App\Http\Controllers\Admin\ProductController::class, 'aiRewrite'])->name('products.aiRewrite');
        Route::post('products/ai-generate-image', [\App\Http\Controllers\Admin\ProductController::class, 'aiGenerateImage'])->name('products.aiGenerateImage');
        Route::post('products/format-ecommerce-image', [\App\Http\Controllers\Admin\ProductController::class, 'formatEcommerceImage'])->name('products.formatEcommerceImage');
        Route::post('products/save-edited-image', [\App\Http\Controllers\Admin\ProductController::class, 'saveEditedCanvasImage'])->name('products.saveEditedImage');
        Route::post('products/fetch-image-url', [\App\Http\Controllers\Admin\ProductController::class, 'fetchImageUrl'])->name('products.fetchImageUrl');
        Route::get('products/export/sample-template', [\App\Http\Controllers\Admin\ProductController::class, 'downloadSampleTemplate'])->name('products.sampleTemplate');
        Route::post('products/import/excel', [\App\Http\Controllers\Admin\ProductController::class, 'importExcel'])->name('products.import');
        Route::delete('products/{product}/cover-image', [\App\Http\Controllers\Admin\ProductController::class, 'deleteCoverImage'])->name('products.cover.destroy');
        Route::post('products/{product}/format-cover-ecommerce', [\App\Http\Controllers\Admin\ProductController::class, 'formatExistingCover'])->name('products.formatCover');
        Route::post('products/{product}/ai-generate-attach-image', [\App\Http\Controllers\Admin\ProductController::class, 'aiGenerateAndAttach'])->name('products.aiGenerateAttach');
        Route::delete('products/{product}/images/{image}', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('products.images.destroy');
        Route::post('products/images/{image}/format-ecommerce', [\App\Http\Controllers\Admin\ProductController::class, 'formatExistingGalleryImage'])->name('products.images.formatEcommerce');
        Route::post('products/{product}/images/{image}/set-cover', [\App\Http\Controllers\Admin\ProductController::class, 'setCoverImage'])->name('products.images.setCover');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        
        // 🏷️ Toplu Fiyatlandırma, Zam & İndirim Paneli
        Route::get('pricing/bulk', [\App\Http\Controllers\Admin\BulkPriceController::class, 'index'])->name('pricing.bulk');
        Route::post('pricing/bulk/apply', [\App\Http\Controllers\Admin\BulkPriceController::class, 'apply'])->name('pricing.bulk.apply');
        
        Route::resource('regions', \App\Http\Controllers\Admin\RegionController::class);
        Route::get('brands/{brand}/products', [\App\Http\Controllers\Admin\BrandController::class, 'manageProducts'])->name('brands.products');
        Route::post('brands/{brand}/products', [\App\Http\Controllers\Admin\BrandController::class, 'syncProducts'])->name('brands.products.sync');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        
        // Orders & Print & Excel Export
        Route::get('orders/export/excel', [\App\Http\Controllers\Admin\OrderController::class, 'exportExcel'])->name('orders.export');
        Route::get('orders/{order}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);

        // 🌸 Çiçek Tasarım Stüdyosu & Günlük Mezat Stok Masası
        Route::prefix('flower-studio')->name('flower_studio.')->group(function () {
            Route::get('/daily-stock', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'dailyStock'])->name('daily_stock');
            Route::post('/materials/{material}/update-stock', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'updateStock'])->name('update_stock');
            Route::post('/materials/{material}/update-price', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'updatePrice'])->name('update_price');
            Route::post('/materials/{material}/toggle', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'toggle'])->name('toggle');
            Route::post('/materials', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'store'])->name('materials.store');
            Route::put('/materials/{material}', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'update'])->name('materials.update');
            Route::delete('/materials/{material}', [\App\Http\Controllers\Admin\FlowerStudioController::class, 'destroy'])->name('materials.destroy');
        });
        
        // Leads Excel Export
        Route::get('leads/export/excel', [\App\Http\Controllers\Admin\LeadController::class, 'exportExcel'])->name('leads.export');
        Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class)->only(['index', 'update', 'destroy']);

        // Services & Print & Maintenance Reminders & Trash & Completed Jobs & Excel Export
        Route::get('services/export/excel', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'exportExcel'])->name('services.export');
        Route::get('services/completed', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'completed'])->name('services.completed');
        Route::get('services/trash', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'trash'])->name('services.trash');
        Route::delete('services/trash/empty', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'emptyTrash'])->name('services.emptyTrash');
        Route::post('services/{id}/restore', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'restore'])->name('services.restore');
        Route::delete('services/{id}/force-delete', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'forceDelete'])->name('services.forceDelete');
        Route::get('services/calendar', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'calendar'])->name('services.calendar');
        Route::get('services/{service}/print', [\App\Http\Controllers\Admin\ServiceRequestController::class, 'print'])->name('services.print');
        Route::resource('services', \App\Http\Controllers\Admin\ServiceRequestController::class);
        Route::get('maintenance', [\App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance/{service}/renew', [\App\Http\Controllers\Admin\MaintenanceController::class, 'renew'])->name('maintenance.renew');

        // Coupons
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

        // Customers & Excel Export
        Route::get('customers/export/excel', [\App\Http\Controllers\Admin\CustomerController::class, 'exportExcel'])->name('customers.export');
        Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        
        // CMS & İçerik Rotaları
        Route::post('posts/scrape-url', [\App\Http\Controllers\Admin\PostController::class, 'scrapeUrl'])->name('posts.scrapeUrl');
        Route::post('posts/ai-generate', [\App\Http\Controllers\Admin\PostController::class, 'aiGenerate'])->name('posts.aiGenerate');
        Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
        Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/toggle', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleApproval'])->name('reviews.toggle');
        Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

        Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class)->except('show');
        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class)->except('show');
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->except('show');
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->except('show');
        
        // Pazarlama & Satış Araçları (Özel Ayrı Sayfalar)
        Route::get('marketing/flash-deal', [\App\Http\Controllers\Admin\MarketingController::class, 'flashDeal'])->name('marketing.flashDeal');
        Route::get('marketing/quiz', [\App\Http\Controllers\Admin\MarketingController::class, 'quiz'])->name('marketing.quiz');
        Route::get('marketing/wheel', [\App\Http\Controllers\Admin\MarketingController::class, 'wheel'])->name('marketing.wheel');
        Route::get('marketing/payment', [\App\Http\Controllers\Admin\MarketingController::class, 'payment'])->name('marketing.payment');
        Route::get('marketing/cookie-consents/export/excel', [\App\Http\Controllers\Admin\MarketingController::class, 'exportCookieConsents'])->name('marketing.cookieConsents.export');
        Route::get('marketing/cookie-consents', [\App\Http\Controllers\Admin\MarketingController::class, 'cookieConsents'])->name('marketing.cookieConsents');
        Route::get('marketing/seo', [\App\Http\Controllers\Admin\MarketingController::class, 'seo'])->name('marketing.seo');
        Route::get('marketing/performance', [\App\Http\Controllers\Admin\MarketingController::class, 'performance'])->name('marketing.performance');
        Route::post('marketing/performance/clear-cache', [\App\Http\Controllers\Admin\MarketingController::class, 'clearCache'])->name('marketing.performance.clearCache');
        Route::post('marketing/store', [\App\Http\Controllers\Admin\MarketingController::class, 'store'])->name('marketing.store');

        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
        
        // Sadece Superadmin erişimi olması gerekenler
        Route::middleware('can:manage-users')->group(function () {
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            Route::get('activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        });
        
        Route::resource('leads', \App\Http\Controllers\Admin\LeadController::class)->only(['index', 'update', 'destroy']);
        Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('contact-messages/{message}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    });
});

// Public Lead Capture Route (Şans Çarkı / Kampanya Talepleri)
Route::post('/leads', [\App\Http\Controllers\LeadController::class, 'store'])->name('leads.store');

require __DIR__.'/auth.php';

// Güvenli Görsel Fallback Rotası (Symlink olmasa bile ürün görsellerinin her ortamda sorunsuz yüklenmesini sağlar)
Route::get('/storage/products/{file}', function ($file) {
    $path = public_path('images/products/' . $file);
    if (file_exists($path)) {
        return response()->file($path);
    }
    $storagePath = storage_path('app/public/products/' . $file);
    if (file_exists($storagePath)) {
        return response()->file($storagePath);
    }
    abort(404);
})->where('file', '.*');
