<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Region;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FloristSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Yönetici Hesabı
        $admin = User::firstOrCreate(
            ['email' => 'admin@istanbulcicekevi.com'],
            [
                'name' => 'İstanbul Çiçek Evi Yönetici',
                'password' => Hash::make('cicek123456'),
                'is_admin' => true,
                'role' => 'superadmin',
                'phone' => '05550001122',
            ]
        );

        // 2. İstanbul 39 İlçe Tanımlaması
        $districts = [
            'Adalar', 'Arnavutköy', 'Ataşehir', 'Avcılar', 'Bağcılar', 'Bahçelievler', 'Bakırköy', 'Başakşehir',
            'Bayrampaşa', 'Beşiktaş', 'Beykoz', 'Beylikdüzü', 'Beyoğlu', 'Büyükçekmece', 'Çatalca', 'Çekmeköy',
            'Esenler', 'Esenyurt', 'Eyüpsultan', 'Fatih', 'Gaziosmanpaşa', 'Güngören', 'Kadıköy', 'Kağıthane',
            'Kartal', 'Küçükçekmece', 'Maltepe', 'Pendik', 'Sancaktepe', 'Sarıyer', 'Silivri', 'Sultanbeyli',
            'Sultangazi', 'Şile', 'Şişli', 'Tuzla', 'Ümraniye', 'Üsküdar', 'Zeytinburnu'
        ];

        foreach ($districts as $dName) {
            Region::firstOrCreate(
                ['name' => $dName],
                [
                    'is_active' => true,
                ]
            );
        }

        // 3. Genel Site & Çiçekçi Ayarları
        $settings = [
            'site_title' => 'İstanbul Çiçek Evi — Aynı Gün Taze Çiçek & Özel Aranjman Siparişi',
            'site_slogan' => 'Sevginizi En Taze Çiçeklerle Ulaştırıyoruz',
            'company_name' => 'İstanbul Çiçek Evi',
            'phone' => '0212 555 12 34',
            'whatsapp_number' => '0501 379 82 36',
            'contact_email' => 'info@istanbulcicekevi.com',
            'address' => 'Bağdat Caddesi No: 142 Kadıköy / İstanbul',
            'working_hours' => 'Hergün 08:30 - 22:00',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-3.6-flash',
            'ai_system_prompt' => "Sen İstanbul Çiçek Evi'nin uzman, zarif, romantik ve son derece güler yüzlü çiçek danışmanı ve kart notu yazarısın. Müşterilere özel günleri için en anlamlı çiçekleri öner ve kalbe dokunan duygusal kart notları yaz.",
            'paytr_active' => '0',
            'wheel_active' => '1',
            'quiz_active' => '1',
            'flash_deal_active' => '1',
            'flash_deal_title' => 'Günün Çiçek Fırsatı: 101 Kırmızı Gül Buketinde %25 İndirim!',
            'flash_deal_subtitle' => 'Saat 18:00\'e kadar verilen siparişlerde aynı gün ücretsiz teslimat ve özel kart mesajı dahil.',
        ];

        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // 4. Çiçek Kategorileri
        $categories = [
            [
                'name' => 'Gül Buketleri',
                'slug' => 'gul-buketleri',
                'description' => 'Aşkın ve tutkunun simgesi taze kırmızı, beyaz ve pembe ithal gül buketleri.',
                'image_path' => 'products/6.png',
            ],
            [
                'name' => 'Orkide & Saksı Çiçekleri',
                'slug' => 'orkide-ve-saksi-cicekleri',
                'description' => 'Zarafetin simgesi çift dallı kraliyet orkideleri, bonsai ve dayanıklı iç mekan yeşil bitkileri.',
                'image_path' => 'products/5.png',
            ],
            [
                'name' => 'Vazoda & Kutuda Tasarımlar',
                'slug' => 'vazoda-ve-kutuda-tasarimlar',
                'description' => 'Şık cam vazolarda ve kadife kutularda özenle hazırlanmış lüks çiçek aranjmanları.',
                'image_path' => 'products/4.png',
            ],
            [
                'name' => 'Mevsim Çiçekleri & Lilyum',
                'slug' => 'mevsim-cicekleri-ve-lilyum',
                'description' => 'Mis kokulu taze lilyumlar, krizantemler, frezyalar ve rengarenk kır çiçekleri.',
                'image_path' => 'products/14.png',
            ],
            [
                'name' => 'Açılış, Düğün & Çelenk',
                'slug' => 'acilis-dugun-ve-celenk',
                'description' => 'İş yeri açılışı, düğün, nikah ve kurumsal kutlamalar için görkemli ayaklı sepet ve çelenkler.',
                'image_path' => 'products/1.png',
            ],
            [
                'name' => 'Çikolata & Peluş Ayıcık',
                'slug' => 'cikolata-ve-pelus-ayicik',
                'description' => 'Çiçeğinizin yanına ekleyebileceğiniz peluş ayıcıklar, spesiyal çikolatalar ve balonlar.',
                'image_path' => 'products/8.png',
            ],
        ];

        $catModels = [];
        foreach ($categories as $catData) {
            $catModels[$catData['slug']] = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'name' => $catData['name'],
                    'description' => $catData['description'],
                    'image_path' => $catData['image_path'] ?? null,
                ]
            );
        }

        // 5. Çiçek Ürün Kataloğu
        $products = [
            [
                'category_slug' => 'gul-buketleri',
                'name' => '101 Kırmızı Gül Masalı — Lüks İthal Gül Buketi',
                'slug' => '101-kirmizi-gul-masali-luks-ithal-gul-buketi',
                'price' => 3499.00,
                'campaign_price' => 2899.00,
                'image_path' => 'products/6.png',
                'short_description' => '101 adet birinci sınıf taze Ekvador kırmızı gülü ile sevdiğinize unutamayacağı bir aşk sürprizi yapın. Aynı gün özel kurye ile adrese teslim.',
                'description' => '<p>Aşkın en görkemli ifadesi olan <strong>101 Kırmızı Gül Buketi</strong>, en taze ithal gonca güllerle usta çiçek tasarımcılarımız tarafından elde hazırlanmaktadır.</p><h3>🌸 Buket Özellikleri</h3><ul><li>101 Adet canlı taze kırmızı Ekvador gülü</li><li>Lüks siyah & pudra özel buket kağıdı ve saten kurdele</li><li>Özel besin tozu ile 10 güne kadar canlılık garantisi</li><li>Ücretsiz mühürlü zarfta duygusal kart mesajı</li></ul><p>İstanbul geneli 39 ilçeye aynı gün teslimat yapılmaktadır.</p>',
                'technical_features' => '• Gül Sayısı: 101 Adet Orijinal Taze Gül
• Buket Çapı: 65 - 75 cm
• Ambalaj: Çift Kat Lüks Mat Kağıt & Saten Kurdele
• Teslimat: Aynı Gün 2 Saatte Özel Çiçek Kuryesi
• Canlılık: 7-10 Gün Vazoda Tazelik Güvencesi',
                'custom_badge' => 'AYNI GÜN TESLİMAT',
                'gallery' => ['products/6.jpeg'],
            ],
            [
                'category_slug' => 'gul-buketleri',
                'name' => 'Aşkın Zarafeti — 25 Kırmızı Gül Buketi',
                'slug' => 'askin-zarafeti-25-kirmizi-gul-buketi',
                'price' => 1290.00,
                'campaign_price' => 990.00,
                'image_path' => 'products/17.png',
                'short_description' => '25 adet kadife dokulu kırmızı gül ve okaliptus yapraklarıyla süslenmiş romantik ve zarif tasarım buket.',
                'description' => '<p>Doğum günü, yıldönümü ve romantik kutlamalar için en popüler buketimiz olan <strong>Aşkın Zarafeti</strong>, taptaze güllerin okaliptus dallarıyla harmanlanmasıyla hazırlanır.</p>',
                'technical_features' => '• Gül Sayısı: 25 Adet
• Boyut: Yaklaşık 50 cm yükseklik
• Aksesuar: Doğal Okaliptus & Gypsophila (Cipso)
• Teslimat: Aynı Gün Seçilen Saat Diliminde',
                'custom_badge' => 'KAMPANYA',
                'gallery' => ['products/17.jpeg'],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Kraliyet Zarafeti — 2 Dallı Beyaz Phalaenopsis Orkide',
                'slug' => 'kraliyet-zarafeti-2-dalli-beyaz-phalaenopsis-orkide',
                'price' => 1450.00,
                'campaign_price' => 1190.00,
                'image_path' => 'products/5.png',
                'short_description' => 'Özel dekoratif seramik saksısında, bol tomurcuklu ve uzun ömürlü birinci sınıf çift dallı beyaz orkide.',
                'description' => '<p>Zarafetin, masumiyetin ve saygının simgesi olan beyaz orkide, hem ev hem de ofis ortamları için kusursuz bir çiçek seçimidir.</p><h3>🪴 Bakım Bilgisi</h3><p>Haftada 1 kez oda sıcaklığında az su verilmeli, doğrudan yakıcı güneş ışığı yerine aydınlık tül arkası ışık tercih edilmelidir.</p>',
                'technical_features' => '• Dal Sayısı: 2 Gövdeli Bol Çiçekli
• Saksı: Lüks Beyaz / Gri Seramik Saksı
• Yükseklik: 60 - 70 cm
• Çiçek Ömrü: 2-3 Ay Boyunca Açan Tomurcuklar',
                'custom_badge' => 'ÇOK SATAN',
                'gallery' => ['products/5.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Pudra Kadife Kutuda Sonsuz Aşk — Kırmızı & Pembe Güller',
                'slug' => 'pudra-kadife-kutuda-sonsuz-ask',
                'price' => 1750.00,
                'campaign_price' => 1390.00,
                'image_path' => 'products/4.png',
                'short_description' => 'Lüks silindir kadife kutuda özel vaha süngeriyle hazırlanmış, suya ihtiyaç duymadan tazeliğini koruyan eşsiz kutu çiçek tasarımı.',
                'description' => '<p>Şık kadife silindir kutu içinde sunulan bu tasarım, masalara ve komodinlere anında asil bir hava katar. Çiçeğin altındaki özel vaha süngeri güllerin günlerce taze kalmasını sağlar.</p>',
                'technical_features' => '• Kutu: 20x20 cm Lüks Kadife Silindir Kutu
• Çiçekler: Kırmızı Gül, Pembe Çardak Gül & Kuru Çiçekler
• Bakım: Vaha süngerine 2 günde bir yarım çay bardağı su ilavesi yeterlidir',
                'custom_badge' => 'ÖZEL TASARIM',
                'gallery' => ['products/4.jpeg'],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Bahar Meltemi — Beyaz Lilyum & Rengarenk Kır Çiçekleri',
                'slug' => 'bahar-meltemi-beyaz-lilyum-kir-cicekleri',
                'price' => 1100.00,
                'campaign_price' => 890.00,
                'image_path' => 'products/14.png',
                'short_description' => 'Evinizi ve ofisinizi mis gibi kokusuyla saracak taze beyaz lilyumlar, pembe güller ve mevsim yeşillikleri aranjmanı.',
                'description' => '<p>Doğallığı ve baharın tazeliğini sevenler için hazırlanan Bahar Meltemi buketi, enerjisiyle girdiği her ortamı neşelendirir.</p>',
                'technical_features' => '• Çiçekler: Kokulu Beyaz Lilyum, Sarı/Pembe Papatyalar, Lisianthus
• Sunum: Rustik Jüt Ambalaj & Doğal Rafya
• Koku: Yoğun Doğal Parfüm Etkisi',
                'custom_badge' => 'MİS KOKULU',
                'gallery' => ['products/14.jpeg'],
            ],
            [
                'category_slug' => 'acilis-dugun-ve-celenk',
                'name' => 'Tebrik & Başarı — Ayaklı Görkemli Açılış Sepeti',
                'slug' => 'tebrik-basari-ayakli-gorkemli-acilis-sepeti',
                'price' => 2800.00,
                'campaign_price' => 2400.00,
                'image_path' => 'products/1.png',
                'short_description' => 'Yeni iş yeri açılışı, nikah, düğün ve kongreler için kurumsal firma isminizin yazılı olduğu görkemli ayaklı canlı çiçek sepeti.',
                'description' => '<p>Kurumsal kutlamalarda firmanızı en prestijli şekilde temsil edecek dev ayaklı çiçek aranjmanı. İsteğe göre bant kısmına firma unvanınız veya tebrik mesajınız büyük harflerle yazılır.</p>',
                'technical_features' => '• Yükseklik: 180 cm Ayaklı Ferforje Sistem
• Çiçekler: Gerbera, Glayöl, Lilyum ve Canlı Mevsim Çiçekleri
• Şerit: İpek Kuşak Üzerine Firma İsmi Baskısı',
                'custom_badge' => 'AÇILIŞ & DÜĞÜN',
                'gallery' => ['products/1.jpeg'],
            ],
            [
                'category_slug' => 'gul-buketleri',
                'name' => 'Origami Lüks Ambalajda Aşkın Büyüsü Kırmızı Gül Buketi',
                'slug' => 'gul-buketi',
                'price' => 3500.00,
                'campaign_price' => 3000.00,
                'image_path' => 'products/WhatsApp Image 2026-08-02 at 17.40.00.jpeg',
                'short_description' => '',
                'description' => '',
                'technical_features' => '',
                'custom_badge' => '',
                'gallery' => [],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Safir Mavi Phalaenopsis Orkide — Özel Tasarım Saksıda',
                'slug' => 'safir-mavi-phalaenopsis-orkide-ozel-tasarim-saksida',
                'price' => 1250.00,
                'campaign_price' => 990.00,
                'image_path' => 'products/9.png',
                'short_description' => 'Göz alıcı safir mavi tonlarıyla nadir ve büyüleyici ithal phalaenopsis orkide. Özel seramik saksısında şık teslimat.',
                'description' => '<p>Özel pigmentleme teknolojisiyle elde edilen derin safir mavi tonlarındaki <strong>Safir Mavi Phalaenopsis Orkide</strong>, zarafeti ve farklılığı sevenler için eşsiz bir tercihtir.</p>',
                'technical_features' => '• Çiçek Türü: Mavi Phalaenopsis Orkide
• Dal Sayısı: 2 Dal
• Saksı: Özel Mat Beyaz Seramik
• Boyut: 60 - 70 cm Yükseklik',
                'custom_badge' => 'NADİR TÜR',
                'gallery' => ['products/9.jpeg'],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Asil Mor Phalaenopsis Orkide — Çift Dallı Lüks Seri',
                'slug' => 'asil-mor-phalaenopsis-orkide-cift-dalli-luks-seri',
                'price' => 1100.00,
                'campaign_price' => 890.00,
                'image_path' => 'products/16.png',
                'short_description' => 'Zarafetin simgesi asil mor orkide. Bol goncalı ve uzun ömürlü, özel tasarım saksısında sunulur.',
                'description' => '<p>Asaletin ve zarafetin rengi mor çiçekleriyle <strong>Asil Mor Orkide</strong>, hem ev hem ofis ortamlarına benzersiz bir hava katar.</p>',
                'technical_features' => '• Çiçek Türü: Mor Phalaenopsis Orkide
• Dal Sayısı: Çift Dal
• Saksı: Lüks Beyaz Seramik
• Boyut: 65 - 75 cm',
                'custom_badge' => 'ÇOK SATAN',
                'gallery' => ['products/16.jpeg'],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Barış Çiçeği (Spathiphyllum) — Hava Temizleyen Salon Bitkisi',
                'slug' => 'baris-cicegi-spathiphyllum-hava-temizleyen-salon-bitkisi',
                'price' => 650.00,
                'campaign_price' => 520.00,
                'image_path' => 'products/2.png',
                'short_description' => 'Beyaz yelken çiçekleriyle huzurun simgesi Barış Çiçeği. Ortam havasını temizleyen canlı saksı bitkisi.',
                'description' => '<p>NASA hava temizleme listesinde yer alan <strong>Barış Çiçeği (Spathiphyllum)</strong>, parlak yeşil yaprakları ve beyaz çiçekleriyle yaşam alanlarınıza huzur getirir.</p>',
                'technical_features' => '• Bitki Türü: Spathiphyllum (Barış Çiçeği)
• Boyut: 55 - 65 cm
• Sulama: Haftada 1-2 kez',
                'custom_badge' => 'OFİS FAVORİSİ',
                'gallery' => ['products/2.jpeg'],
            ],
            [
                'category_slug' => 'orkide-ve-saksi-cicekleri',
                'name' => 'Kırmızı Antoryum (Anthurium) — Saksılı Canlı Bitki',
                'slug' => 'kirmizi-antoryum-anthurium-saksili-canli-bitki',
                'price' => 850.00,
                'campaign_price' => 690.00,
                'image_path' => 'products/3.png',
                'short_description' => 'Parlak kalp şeklindeki kırmızı çiçekleriyle aşkın ve mutluluğun kalıcı sembolü Kırmızı Antoryum.',
                'description' => '<p>Kalıcı ve güçlü duruşuyla dikkat çeken <strong>Kırmızı Antoryum</strong>, parlak kırmızı kalpleri andıran çiçekleriyle uzun aylar boyunca güzelliğini korur.</p>',
                'technical_features' => '• Bitki Türü: Anthurium Andreanum
• Çiçek Rengi: Canlı Kırmızı
• Boyut: 50 - 60 cm',
                'custom_badge' => 'UZUN ÖMÜRLÜ',
                'gallery' => ['products/3.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Işıklı Kalp Kutuda Kırmızı Güller — Sonsuz Aşk Serisi',
                'slug' => 'isikli-kalp-kutuda-kirmizi-guller-sonsuz-ask-serisi',
                'price' => 1450.00,
                'campaign_price' => 1190.00,
                'image_path' => 'products/11.png',
                'short_description' => 'Lüks pleksi pencereli kalp kutuda taze kırmızı güller. Özel ışıklandırma detayıyla büyüleyici bir çiçek sürprizi.',
                'description' => '<p>Aşkınızı kalbinizin en derinlerinden gelen bir ışıkla taçlandırın. <strong>Işıklı Kalp Kutu Kırmızı Gül Tasarımı</strong>, romantik anların unutulmaz sürprizidir.</p>',
                'technical_features' => '• Kutu: Pencereli Kalp Lüks Kutu
• Işık: Dahili Peri LED
• Boyut: 30x30 cm',
                'custom_badge' => 'IŞIKLI TASARIM',
                'gallery' => ['products/11.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Lüks Kalp Kutuda Pembe Güller — Zarafet Koleksiyonu',
                'slug' => 'luks-kalp-kutuda-pembe-guller-zarafet-koleksiyonu',
                'price' => 1350.00,
                'campaign_price' => 1090.00,
                'image_path' => 'products/18.png',
                'short_description' => 'Pudra ve bebek pembesi taze ithal güllerle hazırlanmış şık kalp kutu aranjmanı.',
                'description' => '<p>Nezaket, zarafet ve içten sevginin rengi pembe güller, lüks kalpli tasarım kutusunda hayat buluyor.</p>',
                'technical_features' => '• Çiçek: İthal Pembe Güller
• Kutu: Kalp Formlu Lüks Kutu
• Boyut: 28x28 cm',
                'custom_badge' => 'PREMİUM',
                'gallery' => ['products/18.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Siyah Silindir Kutuda Papatya & Başak Aranjmanı',
                'slug' => 'siyah-silindir-kutuda-papatya-basak-aranjmani',
                'price' => 750.00,
                'campaign_price' => 590.00,
                'image_path' => 'products/12.png',
                'short_description' => 'Modern siyah silindir kutuda saf beyaz papatyalar ve doğal başak detayları.',
                'description' => '<p>Siyahın asaleti ile beyaz papatyaların masumiyetini buluşturan modern aranjman.</p>',
                'technical_features' => '• Kutu: Mat Siyah Silindir Kutu
• Çiçekler: Beyaz Kır Papatyası & Doğal Başak
• Çap: 22 cm',
                'custom_badge' => 'MODERN TASARIM',
                'gallery' => ['products/12.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Cam Fanusta Beyaz Papatya & Kırmızı Gül Aranjmanı',
                'slug' => 'cam-fanusta-beyaz-papatya-kirmizi-gul-aranjmani',
                'price' => 680.00,
                'campaign_price' => 550.00,
                'image_path' => 'products/7.png',
                'short_description' => 'Şeffaf cam fanus içinde taze beyaz papatyalar ve kırmızı güllerin eşsiz uyumu.',
                'description' => '<p>Kristal berraklığındaki cam fanusta hazırlanan bu aranjman masalara ferahlık katar.</p>',
                'technical_features' => '• Vazo: Cam Yuvarlak Fanus
• İçerik: Beyaz Papatya, Kırmızı Güller
• Boyut: Çap 20 cm',
                'custom_badge' => 'ÇOK SEVİLEN',
                'gallery' => ['products/7.jpeg'],
            ],
            [
                'category_slug' => 'vazoda-ve-kutuda-tasarimlar',
                'name' => 'Seramik Vazoda Kırmızı Güller & Lilyum Zarafeti',
                'slug' => 'seramik-vazoda-kirmizi-guller-lilyum-zarafeti',
                'price' => 950.00,
                'campaign_price' => 790.00,
                'image_path' => 'products/10.png',
                'short_description' => 'Beyaz seramik vazoda mis kokulu beyaz lilyumlar, kırmızı güller ve papatya garnitürü.',
                'description' => '<p>Kokusuyla odayı dolduran beyaz lilyumlar ve tutkunun simgesi kırmızı güller seramik vazoda hazır sunumda.</p>',
                'technical_features' => '• Vazo: Lüks Beyaz Seramik
• Çiçekler: Kırmızı Gül, Beyaz Lilyum, Papatya
• Boyut: 45 - 55 cm',
                'custom_badge' => 'VAZOLU TASARIM',
                'gallery' => ['products/10.jpeg'],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Günışığı Ayçiçeği Buketi — Doğal Kraft & Kuru Portakal Detaylı',
                'slug' => 'gunisigi-aycicegi-buketi-dogal-kraft-kuru-portakal-detayli',
                'price' => 750.00,
                'campaign_price' => 590.00,
                'image_path' => 'products/WhatsApp Image 2026-08-05 at 16.47.41.jpeg',
                'short_description' => 'Taptaze sarı ayçiçekleri, kuru portakal dilimleri ve doğal kraft ambalajıyla sıcacık bir buket.',
                'description' => '<p>Pozitif enerjinin en güzel simgesi olan <strong>Günışığı Ayçiçeği Buketi</strong>, kurutulmuş doğal portakal dilimleri ve kraft kağıdıyla usta ellerde bağlandı.</p>',
                'technical_features' => '• İçerik: Taze Ayçiçekleri, Kuru Portakal Dilimleri
• Ambalaj: Doğal Kraft Kağıt
• Boyut: 50 cm',
                'custom_badge' => 'DOĞAL TASARIM',
                'gallery' => [],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Rengarenk Krizantem & Kır Papatyaları Buketi',
                'slug' => 'rengarenk-krizantem-kir-papatyalari-buketi',
                'price' => 550.00,
                'campaign_price' => 450.00,
                'image_path' => 'products/WhatsApp Image 2026-08-03 at 16.16.35.jpeg',
                'short_description' => 'Sarı, lila ve beyaz krizantemlerin cipsofilyalarla tül ambalajda sunulan neşeli buketi.',
                'description' => '<p>Günün her anına neşe katacak taze krizantem ve papatya buketi.</p>',
                'technical_features' => '• İçerik: Sarı, Pembe, Beyaz Krizantem & Papatya
• Ambalaj: Beyaz Dantel File Tül
• Boyut: 45 cm',
                'custom_badge' => 'NEŞELİ RENKLER',
                'gallery' => [],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Özel Tasarım Fuşya Glayöl & Gül Buketi',
                'slug' => 'ozel-tasarim-fusya-glayol-gul-buketi',
                'price' => 890.00,
                'campaign_price' => 720.00,
                'image_path' => 'products/WhatsApp Image 2026-07-27 at 19.21.30.jpeg',
                'short_description' => 'Görkemli fuşya glayöller, taze kırmızı güller ve krizantemlerin heybetli buluşması.',
                'description' => '<p>Fuşya glayöllerin dik ve gösterişli yapısıyla kırmızı güllerin zarafetini buluşturan tasarım.</p>',
                'technical_features' => '• Çiçekler: Fuşya Glayöl, Kırmızı Gül, Krizantem
• Ambalaj: Su Yeşili Lüks Kağıt
• Boyut: 70 cm',
                'custom_badge' => 'FLORİST ÖZEL',
                'gallery' => [],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Özel Saksıda Pembe Lilyum & Kır Çiçekleri Bahçesi',
                'slug' => 'ozel-saksida-pembe-lilyum-kir-cicekleri-bahcesi',
                'price' => 850.00,
                'campaign_price' => 690.00,
                'image_path' => 'products/13.png',
                'short_description' => 'Mis kokulu pembe lilyumlar ve taze kır çiçekleriyle dolu canlı aranjman.',
                'description' => '<p>Kokusuyla mekana bahar ferahlığı taşıyan özel aranjman.</p>',
                'technical_features' => '• Çiçekler: İthal Pembe Lilyum & Kır Çiçekleri
• Boyut: 50 cm',
                'custom_badge' => 'MİS KOKULU',
                'gallery' => ['products/13.jpeg'],
            ],
            [
                'category_slug' => 'mevsim-cicekleri-ve-lilyum',
                'name' => 'Beyaz Seramik Vazoda Taze Bahar Nergisleri',
                'slug' => 'beyaz-seramik-vazoda-taze-bahar-nergisleri',
                'price' => 620.00,
                'campaign_price' => 490.00,
                'image_path' => 'products/15.png',
                'short_description' => 'Baharın müjdecisi nergis çiçekleri, zarif beyaz seramik vazosunda taptaze kokusuyla.',
                'description' => '<p>Doğallığın ve taze başlangıçların sembolü nergis çiçekleri vazoda sunulmaktadır.</p>',
                'technical_features' => '• Çiçek: Taze Nergisler
• Vazo: Beyaz Seramik
• Boyut: 40 cm',
                'custom_badge' => 'MEVSİM ÇİÇEĞİ',
                'gallery' => ['products/15.jpeg'],
            ],
            [
                'category_slug' => 'gul-buketleri',
                'name' => 'VIP 101 Ekvador İthal Kırmızı Gül Dev Buketi',
                'slug' => 'vip-101-ekvador-ithal-kirmizi-gul-dev-buketi',
                'price' => 3800.00,
                'campaign_price' => 3200.00,
                'image_path' => 'products/27.jpeg',
                'short_description' => '101 adet iri başlı ithal Ekvador kırmızı gülü, egzotik palmiye yaprakları ve siyah lüks ambalajda.',
                'description' => '<p>Hayatınızın en unutulmaz anı için tasarlandı. VIP çift katmanlı siyah lüks buket ambalajı ve doğal palmiye yapraklarıyla nefes kesen büyüklük.</p>',
                'technical_features' => '• Gül Sayısı: 101 İthal Ekvador Gülü
• Buket Çapı: 85 - 95 cm
• Yeşillik: Palmiye Yaprakları & Cipsofilya
• Ambalaj: Mat Siyah VIP Kağıt',
                'custom_badge' => 'DEV BOY VIP',
                'gallery' => [],
            ],
            [
                'category_slug' => 'cikolata-ve-pelus-ayicik',
                'name' => 'Sevimli Peluş Ayıcık & Papatya Gül Özel Aranjmanı',
                'slug' => 'sevimli-pelus-ayicik-papatya-gul-ozel-aranjmani',
                'price' => 850.00,
                'campaign_price' => 690.00,
                'image_path' => 'products/8.png',
                'short_description' => 'Yumuşacık pembe peluş ayıcık, taze beyaz papatyalar ve kırmızı güllerle tatlı bir sürpriz paketi.',
                'description' => '<p>Tebrik, doğum günü ve özür için en tatlı sürpriz çiçek tasarımı! Peluş ayıcık ve taze papatyalar sevdiklerinizin kalbini fethedecek.</p>',
                'technical_features' => '• İçerik: 25 cm Peluş Ayıcık, Beyaz Papatya, Kırmızı Gül
• Sunum: Özel Lüks Kutu & Kurdele',
                'custom_badge' => 'ÖZEL SET',
                'gallery' => ['products/8.jpeg'],
            ],
            [
                'category_slug' => 'gul-buketleri',
                'name' => 'Origami Lüks Ambalajda Aşkın Büyüsü Kırmızı Gül Buketi',
                'slug' => 'origami-luks-ambalajda-askin-buyusu-kirmizi-gul-buketi',
                'price' => 1450.00,
                'campaign_price' => 1190.00,
                'image_path' => 'products/WhatsApp Image 2026-08-02 at 17.40.00.jpeg',
                'short_description' => 'Özel origami katlamalı beyaz-altın ambalajda taze kırmızı gonca güller ve cipsofilya aranjmanı.',
                'description' => '<p>Japon origami sanatından esinlenilmiş özel açılı katlamalı lüks ambalaj kağıdı içinde sunulan taze kırmızı güllerimiz, sevginizi en seçkin şekilde ifade eder.</p>',
                'technical_features' => '• Gül Sayısı: 15 Adet Taze Ekvador Gülü
• Ambalaj: Özel Geometrik Origami Katlama Kağıdı
• Aksesuar: Cipsofilya & Kurdele',
                'custom_badge' => 'TASARIM ÖDÜLLÜ',
                'gallery' => [],
            ],
        ];

        foreach ($products as $pData) {
            $cat = $catModels[$pData['category_slug']] ?? null;
            $gallery = $pData['gallery'] ?? [];
            unset($pData['category_slug'], $pData['gallery']);

            $product = Product::updateOrCreate(
                ['slug' => $pData['slug']],
                array_merge($pData, [
                    'category_id' => $cat ? $cat->id : 1,
                    'in_stock' => true,
                    'is_active' => true,
                    'has_free_installation' => false,
                    'has_cash_on_delivery' => true,
                    'cost_price' => ($pData['campaign_price'] ?? $pData['price']) * 0.45,
                    'seo_title' => "{$pData['name']} — İstanbul Çiçek Evi",
                    'seo_description' => Str::limit($pData['short_description'], 150),
                ])
            );

            if (!empty($gallery)) {
                foreach ($gallery as $idx => $galImg) {
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'image_path' => $galImg],
                        ['sort_order' => $idx + 1]
                    );
                }
            }
        }
    }
}
