<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function category($slug)
    {
        $category = \App\Models\Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $products = $category->products()->where('is_active', true)->latest()->paginate(12);

        return view('category', compact('category', 'products'));
    }

    public function product($slug)
    {
        $product = \App\Models\Product::with('category')->where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        return view('product', compact('product'));
    }

    public function trackOrder(Request $request)
    {
        $order = null;
        $error = null;

        if ($request->filled('order_number')) {
            $order = \App\Models\Order::where('order_number', $request->order_number)->first();

            if (!$order) {
                $error = 'Girdiğiniz sipariş numarasına ait bir kayıt bulunamadı.';
            }
        }

        return view('track-order', compact('order', 'error'));
    }

    public function page($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('page', compact('page'));
    }

    public function social()
    {
        return view('social');
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        \App\Models\ContactMessage::create($request->all());

        return back()->with('success', 'Mesajınız başarıyla alınmıştır. En kısa sürede size dönüş yapacağız.');
    }

    public function categories()
    {
        $categories = \App\Models\Category::where('is_active', true)->withCount(['products' => function($q) {
            $q->where('is_active', true);
        }])->orderBy('sort_order')->get();

        return view('categories', compact('categories'));
    }

    public function calculator()
    {
        $topProducts = \App\Models\Product::where('is_active', true)->take(4)->get();
        return view('calculator', compact('topProducts'));
    }

    public function wizard()
    {
        return view('wizard');
    }

    public function filterFinder(\Illuminate\Http\Request $request)
    {
        // Ana markaları ve alt modellerini yükle
        $brands = \App\Models\Brand::with(['children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $selectedBrand = null;
        $selectedSubBrand = null;

        if ($request->filled('brand')) {
            $selectedBrand = $brands->firstWhere('slug', $request->brand);
            if (!$selectedBrand) {
                // Belki doğrudan alt marka slug'ı girilmiştir
                $foundSub = \App\Models\Brand::with('parent')->whereNotNull('parent_id')->where('slug', $request->brand)->first();
                if ($foundSub) {
                    $selectedBrand = $foundSub->parent;
                    $selectedSubBrand = $foundSub;
                }
            }
        }

        if ($selectedBrand && $request->filled('sub')) {
            $selectedSubBrand = $selectedBrand->children->firstWhere('slug', $request->sub);
        }

        if ($selectedSubBrand) {
            // Spesifik alt model seçilmişse: Sadece bu modele atanmış ürünler
            $targetBrandIds = [$selectedSubBrand->id];
            $searchTerm = $selectedSubBrand->search_query ?: $selectedSubBrand->name;

            $products = \App\Models\Product::where('is_active', true)
                ->where(function($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                      ->orWhereHas('brands', fn($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (!empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
                    }
                })->latest()->get();

        } elseif ($selectedBrand) {
            // Ana marka seçilmişse: Kendisine VE bağlı tüm alt modellerine atanmış ürünler
            $targetBrandIds = array_merge([$selectedBrand->id], $selectedBrand->children->pluck('id')->toArray());
            $searchTerm = $selectedBrand->search_query ?: $selectedBrand->name;

            $products = \App\Models\Product::where('is_active', true)
                ->where(function($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                      ->orWhereHas('brands', fn($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (!empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
                    }
                })->latest()->get();

            // Eğer doğrudan eşleşen yoksa üniversal filtreleri getir
            if ($products->isEmpty()) {
                $products = \App\Models\Product::where('is_active', true)
                    ->where(function($q) {
                        $q->where('name', 'like', '%filtre%')
                          ->orWhere('short_description', 'like', '%filtre%')
                          ->orWhere('description', 'like', '%filtre%');
                    })->latest()->get();
            }
        } else {
            // Hiçbir marka seçilmemişse tüm filtre setlerini göster
            $products = \App\Models\Product::where('is_active', true)
                ->where(function($q) {
                    $q->where('name', 'like', '%filtre%')
                      ->orWhere('short_description', 'like', '%filtre%')
                      ->orWhere('description', 'like', '%filtre%');
                })->latest()->get();

            if ($products->isEmpty()) {
                $products = \App\Models\Product::where('is_active', true)->latest()->take(12)->get();
            }
        }

        // Eğer marka seçilerek geldiyse doğrudan markanın kendi özel sayfasına yönlendir
        if ($request->filled('brand')) {
            $b = \App\Models\Brand::where('slug', $request->brand)->first();
            if ($b) {
                return redirect()->route('brand.show', array_filter(['brand' => $b->slug, 'model' => $request->sub]));
            }
        }

        return view('filter-finder', compact('brands', 'selectedBrand', 'selectedSubBrand', 'products'));
    }

    /**
     * Markaya Özel Sayfa (Örn: /marka/ihlas-aura-cebilon)
     */
    public function brandShow(Request $request, \App\Models\Brand $brand)
    {
        $brand->load(['children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')]);

        $selectedSubBrand = null;
        if ($request->filled('model')) {
            $selectedSubBrand = $brand->children->firstWhere('slug', $request->model);
        }

        if ($selectedSubBrand) {
            $targetBrandIds = [$selectedSubBrand->id];
            $searchTerm = $selectedSubBrand->search_query ?: $selectedSubBrand->name;

            $products = \App\Models\Product::where('is_active', true)
                ->where(function($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                      ->orWhereHas('brands', fn($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (!empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
                    }
                })->latest()->get();
        } else {
            $targetBrandIds = array_merge([$brand->id], $brand->children->pluck('id')->toArray());
            $searchTerm = $brand->search_query ?: $brand->name;

            $products = \App\Models\Product::where('is_active', true)
                ->where(function($q) use ($targetBrandIds, $searchTerm) {
                    $q->whereIn('brand_id', $targetBrandIds)
                      ->orWhereHas('brands', fn($bq) => $bq->whereIn('brands.id', $targetBrandIds));
                    if (!empty($searchTerm)) {
                        $q->orWhere('name', 'like', '%' . $searchTerm . '%')
                          ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
                    }
                })->latest()->get();

            if ($products->isEmpty()) {
                $products = \App\Models\Product::where('is_active', true)
                    ->where(function($q) {
                        $q->where('name', 'like', '%filtre%')
                          ->orWhere('short_description', 'like', '%filtre%')
                          ->orWhere('description', 'like', '%filtre%');
                    })->latest()->take(12)->get();
            }
        }

        $otherBrands = \App\Models\Brand::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $brand->id)
            ->orderBy('sort_order')
            ->get();

        return view('brand', compact('brand', 'selectedSubBrand', 'products', 'otherBrands'));
    }
}
