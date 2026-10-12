<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $query = Product::where('is_active', true)->with('category');
        $compatibleNotice = null;

        // Arama (İsim, Kısa Açıklama, Açıklama ve Kategori Arama)
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $cleanTerm = \Illuminate\Support\Str::lower($searchTerm);

            // Çok genel tekil kelimeler hariç (örn: sadece 'filtre' veya 'su' yazılmışsa genel listelesin)
            $genericWords = ['filtre', 'filtresi', 'su', 'su arıtma', 'arıtma', 'cihaz', 'cihazı', 'pompa', 'musluk', 'tank'];

            if (!in_array($cleanTerm, $genericWords)) {
                // 1. Doğrudan Marka veya Search Query eşleşmesi ara
                $matchedBrand = \App\Models\Brand::where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('search_query', 'like', '%' . $searchTerm . '%')
                      ->orWhere('slug', 'like', '%' . \Illuminate\Support\Str::slug($searchTerm) . '%');
                })->first();

                // 2. Eğer tam bulunamadıysa kelime bazlı kontrol (örn: "ihlas filtre" yazıldıysa "ihlas" markasını bulsun)
                if (!$matchedBrand) {
                    $words = explode(' ', $searchTerm);
                    foreach ($words as $w) {
                        $w = trim($w);
                        if (mb_strlen($w) >= 2 && !in_array(mb_strtolower($w), $genericWords)) {
                            $matchedBrand = \App\Models\Brand::where(function($q) use ($w) {
                                $q->where('name', 'like', '%' . $w . '%')
                                  ->orWhere('search_query', 'like', '%' . $w . '%');
                            })->first();
                            if ($matchedBrand) break;
                        }
                    }
                }

                // 🎯 Marka bulunduysa doğrudan o markanın kendi özel sayfasına yönlendir!
                if ($matchedBrand) {
                    if ($matchedBrand->parent_id && $matchedBrand->parent) {
                        return redirect()->route('brand.show', [
                            'brand' => $matchedBrand->parent->slug,
                            'model' => $matchedBrand->slug
                        ]);
                    }
                    return redirect()->route('brand.show', $matchedBrand->slug);
                }
            }

            // Önce doğrudan eşleşen ürünleri ara
            $hasDirectMatches = Product::where('is_active', true)
                ->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('short_description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%' . $searchTerm . '%'));
                })->exists();

            if ($hasDirectMatches) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('short_description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', '%' . $searchTerm . '%'));
                });
            } else {
                // Eğer doğrudan aranan kelimede (Örn: özel bir marka) kayıt çıkmazsa,
                // müşteriyi boş sayfaya düşürme; filtre setlerini göster ve uyumluluk uyarısı ver
                $compatibleNotice = $searchTerm;
                $query->where(function($q) {
                    $q->where('name', 'like', '%filtre%')
                      ->orWhere('short_description', 'like', '%filtre%')
                      ->orWhere('description', 'like', '%filtre%');
                });
            }
        }

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Fiyat filtresi
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sıralama
        match($request->get('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(16)->withQueryString();

        return view('products', compact('products', 'categories', 'compatibleNotice'));
    }
}
