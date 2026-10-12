<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
        
        $productQuery = \App\Models\Product::where('is_active', true)->latest();
        if ($request->filled('search')) {
            $productQuery->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('short_description', 'like', '%' . $request->search . '%');
        }
        $featuredProducts = $productQuery->take(12)->get();
        
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        $topDealIds = array_values(array_filter([
            $settings['top_deal_1'] ?? null,
            $settings['top_deal_2'] ?? null,
            $settings['top_deal_3'] ?? null,
            $settings['top_deal_4'] ?? null,
        ]));

        if (!empty($topDealIds)) {
            $topDealProducts = \App\Models\Product::whereIn('id', $topDealIds)
                ->where('is_active', true)
                ->get()
                ->sortBy(function ($product) use ($topDealIds) {
                    return array_search($product->id, $topDealIds);
                })
                ->values();
        } else {
            $topDealProducts = \App\Models\Product::where('is_active', true)->take(4)->get();
        }

        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
        $faqs = \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get();

        $homeCat1Id = $settings['home_cat_1'] ?? null;
        $homeCat2Id = $settings['home_cat_2'] ?? null;

        $homeCat1 = $homeCat1Id ? \App\Models\Category::find($homeCat1Id) : $categories->first();
        $homeCat2 = $homeCat2Id ? \App\Models\Category::find($homeCat2Id) : $categories->skip(1)->first();

        return view('welcome', compact('categories', 'homeCat1', 'homeCat2', 'featuredProducts', 'topDealProducts', 'banners', 'faqs'));
    }
}
