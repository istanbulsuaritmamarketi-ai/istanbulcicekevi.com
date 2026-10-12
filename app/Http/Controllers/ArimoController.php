<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArimoController extends Controller
{
    /**
     * Arimo Yapay Zeka Asistanı Özel Sayfası
     */
    public function index()
    {
        try {
            $featuredProducts = Product::where('is_active', true)
                ->latest()
                ->take(4)
                ->get();
        } catch (\Throwable $e) {
            $featuredProducts = collect();
        }

        try {
            $brands = \Illuminate\Support\Facades\Schema::hasTable('brands')
                ? Brand::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->take(6)->get()
                : collect();
        } catch (\Throwable $e) {
            $brands = collect();
        }

        return view('arimo', compact('featuredProducts', 'brands'));
    }

    /**
     * Arimo'ya Soru Sorma (AJAX API Endpoint)
     */
    public function ask(Request $request)
    {
        $message = trim($request->input('message', ''));
        if (empty($message)) {
            return response()->json([
                'reply' => 'Lütfen su arıtma ile ilgili sormak istediğiniz konuyu yazınız.',
                'products' => [],
                'action_url' => null,
                'action_text' => null,
            ]);
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $geminiKey = $settings['gemini_api_key'] ?? env('GEMINI_API_KEY');
        $openaiKey = $settings['openai_api_key'] ?? env('OPENAI_API_KEY');
        $aiProvider = $settings['ai_provider'] ?? 'gemini';

        // 1. Eğer LLM API anahtarı tanımlıysa yapay zekaya sor
        if (!empty($geminiKey) && $aiProvider === 'gemini') {
            $llmResponse = $this->askGemini($message, $geminiKey, $settings);
            if ($llmResponse) {
                return response()->json($llmResponse);
            }
        } elseif (!empty($openaiKey) && $aiProvider === 'openai') {
            $llmResponse = $this->askOpenAi($message, $openaiKey, $settings);
            if ($llmResponse) {
                return response()->json($llmResponse);
            }
        }

        // 2. Akıllı Yerel Kural & Ürün Öneri Motoru (Her zaman %100 çalışır)
        return response()->json($this->smartLocalAssistant($message));
    }

    /**
     * Akıllı Yerel Kural Motoru (API Anahtarı olmadan da anında cevap verir)
     */
    protected function smartLocalAssistant(string $msg): array
    {
        $lower = Str::lower($msg);
        $recommendedProducts = collect();
        $actionUrl = null;
        $actionText = null;
        $reply = '';

        // 1. Aşk, Sevgili, Romantik, Gül
        if (Str::contains($lower, ['aşk', 'sevgili', 'sevgilim', 'gül', 'kırmızı gül', 'romantik', 'yıldönümü', 'evlilik'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where('name', 'like', '%gül%')
                ->latest()
                ->take(3)
                ->get();

            $reply = "Aşk ve romantizmin en büyüleyici ifadesi **Taze İthal Ekvador Kırmızı Gülleri**dir! ❤️\n\nBuketlerimiz her sabah taze gonca güllerden özenle hazırlanır. Çiçeğin yanına mühürlü zarfta ücretsiz kart mesajınızı ekliyoruz.\n\n*Örnek Kart Notu:* \"Hayatıma kattığın tüm güzellikler ve sevgin için teşekkür ederim. İyi ki varsın bir tanem!\"";
            $actionUrl = route('products.index');
            $actionText = "Tüm Gül Buketlerini Gör";
        }
        // 2. Doğum Günü & Kutlama
        elseif (Str::contains($lower, ['doğum günü', 'dogum gunu', 'yaş günü', 'kutlama', 'parti'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%meltemi%')
                      ->orWhere('name', 'like', '%kutu%')
                      ->orWhere('name', 'like', '%gül%');
                })
                ->latest()
                ->take(3)
                ->get();

            $reply = "Doğum gününü unutulmaz kılmak için rengarenk mis kokulu **Bahar Meltemi Lilyum Buketi** veya **Lüks Kutuda Güller** harika bir seçim! 🎂\n\nİsterseniz çiçeğin yanına leziz Belçika çikolatası veya sevimli peluş ayıcık da ekleyebilirsiniz.\n\n*Örnek Kart Notu:* \"Yeni yaşın sana sağlık, mutluluk ve kalbin kadar güzel anlar getirsin. Doğum günün kutlu olsun!\"";
            $actionUrl = route('products.index');
            $actionText = "Doğum Günü Çiçeklerini İncele";
        }
        // 3. Orkide & Ofis & Yeni İş & Terfi
        elseif (Str::contains($lower, ['orkide', 'ofis', 'yeni iş', 'terfi', 'saksı', 'masa', 'işyeri'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%orkide%')
                      ->orWhere('name', 'like', '%saksı%');
                })
                ->latest()
                ->take(2)
                ->get();

            $reply = "Yeni iş, terfi ve ofis tebrikleri için en asil ve uzun ömürlü çiçek seçeneği **Çift Dallı Kraliyet Beyaz Orkidesi**dir! 🪴\n\nSeramik saksısıyla masalara zarafet katar ve aylarca açan tomurcuklarıyla kalıcı bir hatıra bırakır.\n\n*Örnek Kart Notu:* \"Yeni görevinde üstün başarılar dilerim, başarıların daim olsun!\"";
            $actionUrl = route('products.index');
            $actionText = "Orkideleri İncele";
        }
        // 4. Özür Dilerim & Barışma
        elseif (Str::contains($lower, ['özür', 'ozur', 'affet', 'barış', 'kırdım', 'üzgünüm'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('name', 'like', '%gül%')
                      ->orWhere('name', 'like', '%lilyum%');
                })
                ->latest()
                ->take(2)
                ->get();

            $reply = "Kırılan bir kalbi onarmanın ve samimi bir af dilemenin en zarif yolu taze çiçeklerdir. 🕊️\n\nPudra kutuda güller veya saf beyaz lilyumlar kalbinizin sesini ulaştırır.\n\n*Örnek Kart Notu:* \"Seni istemeden kırdığım için çok üzgünüm. Lütfen bu taze çiçekler eşliğinde beni affet, seni çok seviyorum.\"";
            $actionUrl = route('products.index');
            $actionText = "Barışma Çiçeklerini Gör";
        }
        // 5. Açılış, Düğün, Çelenk, Nikah
        elseif (Str::contains($lower, ['açılış', 'acilis', 'düğün', 'dugun', 'nikah', 'çelenk', 'celenk', 'tören'])) {
            $recommendedProducts = Product::where('is_active', true)
                ->where('name', 'like', '%açılış%')
                ->latest()
                ->take(2)
                ->get();

            $reply = "Düğün, nikah ve iş yeri açılışlarında firmanızı en prestijli şekilde temsil edecek **180 cm Ayaklı Ferforje Çelenk & Canlı Çiçek Sepetlerimiz** mevcuttur! 💐\n\nİpek kuşak üzerine dilediğiniz firma unvanı veya tebrik mesajınız büyük harflerle özenle basılır.";
            $actionUrl = route('products.index');
            $actionText = "Çelenk & Açılış Çiçekleri";
        }
        // 6. Teslimat, Aynı Gün, Saat, Kurye
        elseif (Str::contains($lower, ['teslimat', 'kurye', 'aynı gün', 'saat', 'ne zaman', 'kaç saatte', 'hızlı'])) {
            $reply = "İstanbul'un 39 ilçesine **aynı gün 2 saatte özel çiçek kuryelerimizle** teslimat sağlıyoruz! 🚗🌸\n\n• Sipariş verirken sabah (09-12), öğle (12-15), öğleden sonra (15-18) veya akşam (18-21) teslimat saat dilimini seçebilirsiniz.\n• İsterseniz \"Gizli Gönderim\" seçeneğiyle adınızı alıcıdan tamamen gizleyerek sürpriz yapabilirsiniz.";
            $actionUrl = route('checkout');
            $actionText = "Siparişi Başlat";
        }
        // 7. Genel Karşılama
        else {
            $recommendedProducts = Product::where('is_active', true)->latest()->take(3)->get();
            $reply = "Merhaba! Ben Flora 🌸 İstanbul Çiçek Evi'nin çiçek ve kart notu danışmanıyım. \n\nKime sürpriz yapmak istiyorsunuz? Sevgilinize, doğum gününe, yeni iş tebriğine veya özür dilemek için en anlamlı çiçekleri ve duygusal kart notlarını birlikte seçebiliriz!";
        }

        return [
            'reply' => $reply,
            'products' => $this->formatProducts($recommendedProducts),
            'action_url' => $actionUrl,
            'action_text' => $actionText,
        ];
    }

    /**
     * Gemini AI API Çağrısı (Zengin Çiçek Bağlamı ile)
     */
    protected function askGemini(string $message, string $apiKey, array $settings): ?array
    {
        try {
            $apiKey = trim($apiKey);
            if (empty($apiKey)) return null;

            $catalogList = '';
            try {
                $catalogItems = Product::where('is_active', true)->select('id', 'name', 'price', 'campaign_price')->take(8)->get();
                $catalogList = $catalogItems->map(function ($p) {
                    $pPrice = number_format($p->campaign_price ?: $p->price, 0, ',', '.') . ' TL';
                    return "- {$p->name} ({$pPrice})";
                })->implode("\n");
            } catch (\Throwable $e) {}

            $systemPrompt = "Sen Flora'sın; İstanbul Çiçek Evi'nin uzman, zarif, romantik ve son derece güler yüzlü çiçek danışmanı ve kart notu yazarısın.
Müşterinin özel günü (doğum günü, sevgililer günü, yıldönümü, özür, tebrik, orkide bakımı vb.) için en uygun taze çiçekleri tavsiye et.
Ayrıca müşteriye çiçeğin yanına iliştirebileceği kalbe dokunan, romantik veya anlamlı özel kart notları öner.
Konuşma kuralları:
1. Türkçe konuş, son derece samimi, zarif, pozitif ve çiçek temalı ol.
2. Önemli çiçek türlerini veya kart notlarını **kalın** veya tırnak içinde belirt.
3. Firmamızın avantajları: İstanbul 39 ilçeye aynı gün seçilen saat diliminde teslimat, ücretsiz mühürlü zarfta kart notu, gizli gönderim seçeneği, taze çiçek garantisi.

Mağazamızdaki bazı taze çiçekler ve fiyatlar:
{$catalogList}";

            // Modeller: Google'ın en güncel Gemini 3 modelleri
            $models = [
                'gemini-3.6-flash',
                'gemini-3.8-flash',
                'gemini-3.7-flash',
                'gemini-3.5-flash-lite',
                'gemini-3.1-pro',
            ];

            if (!empty($settings['ai_model']) && !in_array($settings['ai_model'], ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-2.0-flash', 'gemini-1.5-pro', 'gemini-2.5-flash', 'gemini-2.5-pro'])) {
                array_unshift($models, trim($settings['ai_model']));
                $models = array_unique($models);
            }

            $reply = null;

            foreach ($models as $model) {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

                $response = Http::withoutVerifying()
                    ->timeout(20)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'x-goog-api-key' => trim($apiKey),
                    ])
                    ->post($url, [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "{$systemPrompt}\n\nMüşteri Sorusu: {$message}"]
                                ]
                            ]
                        ]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    if (!empty($reply)) {
                        break;
                    }
                } else {
                    Log::warning("Arimo Gemini model {$model} warning: " . $response->body());
                }
            }

            // Eğer tanımlı modellerden yanıt alınamazsa, Google API'den açık modelleri dinamik keşfet
            if (empty($reply)) {
                try {
                    $listUrl = "https://generativelanguage.googleapis.com/v1beta/models?key=" . urlencode($apiKey);
                    $listResp = Http::withoutVerifying()->timeout(10)->get($listUrl);
                    if ($listResp->successful()) {
                        $available = $listResp->json('models', []);
                        foreach ($available as $av) {
                            $methods = $av['supportedGenerationMethods'] ?? [];
                            if (in_array('generateContent', $methods)) {
                                $autoModel = str_replace('models/', '', $av['name']);
                                $autoUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$autoModel}:generateContent?key=" . urlencode($apiKey);
                                $autoPost = Http::withoutVerifying()->timeout(15)->post($autoUrl, [
                                    'contents' => [
                                        ['parts' => [['text' => "{$systemPrompt}\n\nMüşteri Sorusu: {$message}"]]]
                                    ],
                                    'generationConfig' => [
                                        'temperature' => 0.7,
                                        'maxOutputTokens' => 800,
                                    ]
                                ]);
                                if ($autoPost->successful()) {
                                    $reply = $autoPost->json('candidates.0.content.parts.0.text');
                                    if (!empty($reply)) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning("Arimo dynamic model discovery error: " . $e->getMessage());
                }
            }

            if ($reply) {
                // Müşterinin sorusu ve Gemini'nin cevabına en uygun ürünleri veritabanından çek
                $matchedProducts = $this->findRelevantProducts($message . ' ' . $reply);

                return [
                    'reply' => trim($reply),
                    'products' => $this->formatProducts($matchedProducts),
                    'action_url' => route('products.index'),
                    'action_text' => 'Tüm Ürünleri İncele',
                ];
            }
        } catch (\Exception $e) {
            Log::warning("Arimo Gemini Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * OpenAI API Çağrısı
     */
    protected function askOpenAi(string $message, string $apiKey, array $settings): ?array
    {
        try {
            $response = Http::withoutVerifying()->withToken(trim($apiKey))->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "Sen Arimo'sun; İstanbul Su Arıtma Marketi'nin uzman yapay zeka asistanısın. Müşteriye en uygun su arıtma cihazı ve filtre tavsiyesini ver."],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                if ($reply) {
                    $matchedProducts = $this->findRelevantProducts($message . ' ' . $reply);
                    return [
                        'reply' => trim($reply),
                        'products' => $this->formatProducts($matchedProducts),
                        'action_url' => route('products.index'),
                        'action_text' => 'Ürünleri İncele',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("Arimo OpenAI Error: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Soru ve cevaba en uygun ürünleri akıllıca eşleştirir
     */
    protected function findRelevantProducts(string $text, int $limit = 3)
    {
        $lower = Str::lower($text);

        // 1. Marka eşleşmesi var mı?
        $brand = Brand::where(function ($q) use ($lower) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . $lower . '%'])
              ->orWhereRaw('LOWER(search_query) LIKE ?', ['%' . $lower . '%']);
        })->first();

        if ($brand) {
            $brandIds = array_merge([$brand->id], $brand->children()->pluck('id')->toArray());
            $products = Product::where('is_active', true)
                ->where(function ($q) use ($brandIds, $brand) {
                    $q->whereIn('brand_id', $brandIds)
                      ->orWhereHas('brands', fn($bq) => $bq->whereIn('brands.id', $brandIds))
                      ->orWhere('name', 'like', '%' . $brand->name . '%');
                })->latest()->take($limit)->get();

            if ($products->isNotEmpty()) {
                return $products;
            }
        }

        // 2. Özel anahtar kelimelere göre filtrele
        $keywords = ['pompalı', 'pompasız', 'membran', 'filtre', 'alkali', 'mineral', '12 aşama', 'tezgah altı'];
        foreach ($keywords as $kw) {
            if (Str::contains($lower, $kw)) {
                $products = Product::where('is_active', true)
                    ->where('name', 'like', '%' . $kw . '%')
                    ->latest()
                    ->take($limit)
                    ->get();
                if ($products->isNotEmpty()) {
                    return $products;
                }
            }
        }

        // 3. Fiyat veya ekonomik cihaz arayışı
        if (Str::contains($lower, ['ucuz', 'ekonomik', 'fiyat', 'bütçe', 'en uygun', 'kampanya'])) {
            return Product::where('is_active', true)->orderBy('price', 'asc')->take($limit)->get();
        }

        // 4. Öne çıkan ürünler (Kampanyalı veya en son eklenenler)
        $campaignProducts = Product::where('is_active', true)->whereNotNull('campaign_price')->latest()->take($limit)->get();
        if ($campaignProducts->isNotEmpty()) {
            return $campaignProducts;
        }

        return Product::where('is_active', true)->latest()->take($limit)->get();
    }

    /**
     * Ürünleri Arimo vitrin formatına çevirir
     */
    protected function formatProducts($products): array
    {
        return $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'url' => route('product.show', $p->slug),
                'price' => number_format($p->price, 0, ',', '.') . ' TL',
                'campaign_price' => $p->campaign_price ? number_format($p->campaign_price, 0, ',', '.') . ' TL' : null,
                'image' => $p->image_path ? asset('storage/' . $p->image_path) : null,
                'discount_percent' => $p->discount_percent,
            ];
        })->toArray();
    }
}
