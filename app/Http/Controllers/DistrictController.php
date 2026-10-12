<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class DistrictController extends Controller
{
    public static array $districts = [
        'kadikoy' => ['name' => 'Kadıköy', 'side' => 'Anadolu'],
        'umraniye' => ['name' => 'Ümraniye', 'side' => 'Anadolu'],
        'uskudar' => ['name' => 'Üsküdar', 'side' => 'Anadolu'],
        'atasehir' => ['name' => 'Ataşehir', 'side' => 'Anadolu'],
        'maltepe' => ['name' => 'Maltepe', 'side' => 'Anadolu'],
        'kartal' => ['name' => 'Kartal', 'side' => 'Anadolu'],
        'pendik' => ['name' => 'Pendik', 'side' => 'Anadolu'],
        'tuzla' => ['name' => 'Tuzla', 'side' => 'Anadolu'],
        'beykoz' => ['name' => 'Beykoz', 'side' => 'Anadolu'],
        'cekmekoy' => ['name' => 'Çekmeköy', 'side' => 'Anadolu'],
        'sancaktepe' => ['name' => 'Sancaktepe', 'side' => 'Anadolu'],
        'sultanbeyli' => ['name' => 'Sultanbeyli', 'side' => 'Anadolu'],
        'sile' => ['name' => 'Şile', 'side' => 'Anadolu'],
        'adalar' => ['name' => 'Adalar', 'side' => 'Anadolu'],
        'besiktas' => ['name' => 'Beşiktaş', 'side' => 'Avrupa'],
        'sisli' => ['name' => 'Şişli', 'side' => 'Avrupa'],
        'sariyer' => ['name' => 'Sarıyer', 'side' => 'Avrupa'],
        'beyoglu' => ['name' => 'Beyoğlu', 'side' => 'Avrupa'],
        'fatih' => ['name' => 'Fatih', 'side' => 'Avrupa'],
        'bakirkoy' => ['name' => 'Bakırköy', 'side' => 'Avrupa'],
        'bahcelievler' => ['name' => 'Bahçelievler', 'side' => 'Avrupa'],
        'gungoren' => ['name' => 'Güngören', 'side' => 'Avrupa'],
        'zeytinburnu' => ['name' => 'Zeytinburnu', 'side' => 'Avrupa'],
        'bayrampasa' => ['name' => 'Bayrampaşa', 'side' => 'Avrupa'],
        'gaziosmanpasa' => ['name' => 'Gaziosmanpaşa', 'side' => 'Avrupa'],
        'eyupsultan' => ['name' => 'Eyüpsultan', 'side' => 'Avrupa'],
        'kagithane' => ['name' => 'Kağıthane', 'side' => 'Avrupa'],
        'sultangazi' => ['name' => 'Sultangazi', 'side' => 'Avrupa'],
        'esenler' => ['name' => 'Esenler', 'side' => 'Avrupa'],
        'bagcilar' => ['name' => 'Bağcılar', 'side' => 'Avrupa'],
        'kucukcekmece' => ['name' => 'Küçükçekmece', 'side' => 'Avrupa'],
        'avcilar' => ['name' => 'Avcılar', 'side' => 'Avrupa'],
        'beylikduzu' => ['name' => 'Beylikdüzü', 'side' => 'Avrupa'],
        'esenyurt' => ['name' => 'Esenyurt', 'side' => 'Avrupa'],
        'basaksehir' => ['name' => 'Başakşehir', 'side' => 'Avrupa'],
        'buyukcekmece' => ['name' => 'Büyükçekmece', 'side' => 'Avrupa'],
        'catalca' => ['name' => 'Çatalca', 'side' => 'Avrupa'],
        'silivri' => ['name' => 'Silivri', 'side' => 'Avrupa'],
        'arnavutkoy' => ['name' => 'Arnavutköy', 'side' => 'Avrupa'],
    ];

    public function show($slug)
    {
        $slug = strtolower($slug);
        
        if (!isset(self::$districts[$slug])) {
            abort(404);
        }

        $district = self::$districts[$slug];
        $districtName = $district['name'];
        $districtSide = $district['side'];

        $products = Product::where('is_active', true)->latest()->take(8)->get();
        $categories = Category::where('is_active', true)->get();
        $allDistricts = self::$districts;

        return view('district', compact('district', 'districtName', 'districtSide', 'slug', 'products', 'categories', 'allDistricts'));
    }

    public function index()
    {
        $districts = self::$districts;
        return view('districts', compact('districts'));
    }
}
