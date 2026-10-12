<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class BulkPriceController extends Controller
{
    /**
     * Toplu Fiyat & İndirim / Zam Yönetim Paneli
     */
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $campaignProducts = Product::whereNotNull('campaign_price')->where('campaign_price', '>', 0)->count();
        $avgPrice = Product::where('is_active', true)->avg('price') ?: 0;
        
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        return view('admin.pricing.bulk', compact(
            'categories',
            'totalProducts',
            'activeProducts',
            'campaignProducts',
            'avgPrice',
            'products'
        ));
    }

    /**
     * Toplu Fiyat Güncellemesini Uygula
     */
    public function apply(Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:markup,discount,set_campaign,clear_campaign,set_fixed',
            'value_type' => 'required_unless:action_type,clear_campaign|nullable|in:percent,fixed',
            'amount' => 'required_unless:action_type,clear_campaign|nullable|numeric|min:0.01',
            'target_field' => 'required_unless:action_type,set_campaign,clear_campaign|nullable|in:price,campaign_price,both',
            'scope' => 'required|in:all,category,selected',
            'category_id' => 'required_if:scope,category|nullable|exists:categories,id',
            'product_ids' => 'required_if:scope,selected|nullable|array',
            'product_ids.*' => 'exists:products,id',
            'rounding' => 'required|in:none,integer,psychological_9,psychological_90,ten',
        ], [
            'amount.required_unless' => 'Lütfen geçerli bir artış/indirim tutarı veya yüzdesi giriniz.',
            'amount.min' => 'Tutar 0\'dan büyük olmalıdır.',
            'category_id.required_if' => 'Lütfen bir kategori seçiniz.',
            'product_ids.required_if' => 'Lütfen en az bir ürün seçiniz.',
        ]);

        $query = Product::where('is_active', true);

        if ($request->scope === 'category' && $request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->scope === 'selected' && !empty($request->product_ids)) {
            $query->whereIn('id', $request->product_ids);
        }

        $products = $query->get();
        $updatedCount = 0;

        $action = $request->action_type;
        $valType = $request->value_type ?: 'percent';
        $amount = (float) ($request->amount ?: 0);
        $target = $request->target_field ?: 'price';
        $rounding = $request->rounding ?: 'integer';

        foreach ($products as $product) {
            $changed = false;

            if ($action === 'clear_campaign') {
                if ($product->campaign_price !== null) {
                    $product->campaign_price = null;
                    $changed = true;
                }
            } elseif ($action === 'set_campaign') {
                // Normal satış fiyatından indirim hesaplayarak kampanya fiyatı oluştur
                $basePrice = (float) $product->price;
                if ($valType === 'percent') {
                    $newCamp = $basePrice * (1 - ($amount / 100));
                } else {
                    $newCamp = $basePrice - $amount;
                }
                $newCamp = $this->applyRounding($newCamp, $rounding);

                if ($newCamp > 0 && $newCamp < $basePrice) {
                    $product->campaign_price = $newCamp;
                    $changed = true;
                }
            } elseif ($action === 'set_fixed') {
                $newPrice = $this->applyRounding($amount, $rounding);
                if ($target === 'price' || $target === 'both') {
                    $product->price = $newPrice;
                    $changed = true;
                }
                if ($target === 'campaign_price' || $target === 'both') {
                    if ($product->campaign_price !== null) {
                        $product->campaign_price = $newPrice;
                        $changed = true;
                    }
                }
            } else {
                // markup (zam) veya discount (indirim)
                $isMarkup = ($action === 'markup');

                if ($target === 'price' || $target === 'both') {
                    $curPrice = (float) $product->price;
                    $diff = ($valType === 'percent') ? ($curPrice * ($amount / 100)) : $amount;
                    $newPrice = $isMarkup ? ($curPrice + $diff) : max(1, $curPrice - $diff);
                    $product->price = $this->applyRounding($newPrice, $rounding);
                    $changed = true;
                }

                if ($target === 'campaign_price' || $target === 'both') {
                    if ($product->campaign_price !== null && (float) $product->campaign_price > 0) {
                        $curCamp = (float) $product->campaign_price;
                        $diff = ($valType === 'percent') ? ($curCamp * ($amount / 100)) : $amount;
                        $newCamp = $isMarkup ? ($curCamp + $diff) : max(1, $curCamp - $diff);
                        $product->campaign_price = $this->applyRounding($newCamp, $rounding);
                        $changed = true;
                    }
                }
            }

            if ($changed) {
                $product->save();
                $updatedCount++;
            }
        }

        $actionMessages = [
            'markup' => 'fiyat artışı (zam)',
            'discount' => 'fiyat indirimi',
            'set_campaign' => 'kampanyalı indirim fiyatı tanımlaması',
            'clear_campaign' => 'kampanya indirimi sıfırlaması',
            'set_fixed' => 'sabit fiyat tanımlaması',
        ];

        $actionName = $actionMessages[$action] ?? 'fiyat güncellemesi';

        return redirect()->route('admin.pricing.bulk')->with(
            'success', 
            "İşlem Tamamlandı: Toplam {$updatedCount} adet ürünün {$actionName} başarıyla uygulandı!"
        );
    }

    /**
     * Fiyat Yuvarlama Kurallarını Uygula
     */
    private function applyRounding(float $price, string $rounding): float
    {
        switch ($rounding) {
            case 'integer':
                return round($price);
            case 'psychological_9':
                $intVal = round($price);
                if ($intVal < 10) return 9.0;
                return (float) (floor($intVal / 10) * 10 + 9);
            case 'psychological_90':
                return (float) (floor($price) + 0.90);
            case 'ten':
                return (float) (round($price / 10) * 10);
            case 'none':
            default:
                return round($price, 2);
        }
    }
}
