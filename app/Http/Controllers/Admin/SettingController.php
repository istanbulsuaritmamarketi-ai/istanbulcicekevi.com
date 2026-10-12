<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $products = \App\Models\Product::where('is_active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        $coupons = \App\Models\Coupon::orderBy('code')->get();
        return view('admin.settings.index', compact('settings', 'products', 'categories', 'coupons'));
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        
        // Handle File Uploads (Logo, Favicon, OG Image - Otomatik Optimize Edilir)
        $fileKeys = ['logo', 'favicon', 'og_image'];
        foreach ($fileKeys as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $maxWidth = ($fileKey === 'favicon') ? 256 : 1200;
                $maxHeight = ($fileKey === 'favicon') ? 256 : 1200;
                $path = \App\Services\ImageOptimizerService::optimizeAndStore($request->file($fileKey), 'settings', $maxWidth, $maxHeight, 90);
                Setting::updateOrCreate(['key' => $fileKey], ['value' => $path]);
                unset($data[$fileKey]);
            }
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $fileKeys)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }
        
        return redirect()->back()->with('success', 'Ayarlar ve SEO yapılandırması başarıyla güncellendi.');
    }
}
