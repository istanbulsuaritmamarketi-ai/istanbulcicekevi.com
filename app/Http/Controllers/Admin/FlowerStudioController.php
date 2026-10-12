<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomFlowerMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageOptimizerService;

class FlowerStudioController extends Controller
{
    /**
     * 🌸 Amcanız İçin Günlük Mezat Stok & Fiyat Masası
     */
    public function dailyStock(Request $request)
    {
        $category = $request->get('category', 'all');

        $query = CustomFlowerMaterial::orderBy('category')->orderBy('sort_order');
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $materials = $query->get();

        $counts = [
            'all' => CustomFlowerMaterial::count(),
            'flower' => CustomFlowerMaterial::where('category', 'flower')->count(),
            'vase' => CustomFlowerMaterial::where('category', 'vase')->count(),
            'filler' => CustomFlowerMaterial::where('category', 'filler')->count(),
            'wrapping' => CustomFlowerMaterial::where('category', 'wrapping')->count(),
            'accessory' => CustomFlowerMaterial::where('category', 'accessory')->count(),
        ];

        return view('admin.flower_studio.daily_stock', compact('materials', 'category', 'counts'));
    }

    /**
     * ⚡ Tek Tıkla / AJAX Günlük Stok Güncelleme
     */
    public function updateStock(Request $request, CustomFlowerMaterial $material)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $material->update([
            'daily_stock' => $request->stock,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$material->name} stok adedi {$material->daily_stock} olarak güncellendi.",
                'daily_stock' => $material->daily_stock,
            ]);
        }

        return back()->with('success', "{$material->name} stok adedi güncellendi.");
    }

    /**
     * 💰 Tek Tıkla / AJAX Fiyat Güncelleme
     */
    public function updatePrice(Request $request, CustomFlowerMaterial $material)
    {
        $request->validate([
            'unit_price' => 'required|numeric|min:0',
        ]);

        $material->update([
            'unit_price' => $request->unit_price,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$material->name} birim fiyatı {$material->unit_price} TL olarak güncellendi.",
                'unit_price' => number_format($material->unit_price, 2),
            ]);
        }

        return back()->with('success', "{$material->name} fiyatı güncellendi.");
    }

    /**
     * 🟢 Durum Aç / Kapat
     */
    public function toggle(CustomFlowerMaterial $material)
    {
        $material->update(['is_active' => !$material->is_active]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $material->is_active,
                'message' => $material->is_active ? 'Malzeme satışa açıldı.' : 'Malzeme satışa kapatıldı.',
            ]);
        }

        return back()->with('success', 'Malzeme durumu güncellendi.');
    }

    /**
     * ➕ Yeni Malzeme Kaydet
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:flower,vase,filler,wrapping,accessory',
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'daily_stock' => 'required|integer|min:0',
            'stem_capacity' => 'nullable|integer|min:1|max:200',
            'color_hex' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = ImageOptimizerService::optimizeAndStore($request->file('image'), 'flower_studio', 800, 800, 85);
        }

        $maxOrder = CustomFlowerMaterial::where('category', $request->category)->max('sort_order') ?? 0;

        CustomFlowerMaterial::create([
            'category' => $request->category,
            'name' => $request->name,
            'unit_price' => $request->unit_price,
            'cost_price' => $request->cost_price,
            'daily_stock' => $request->daily_stock,
            'stem_capacity' => $request->input('stem_capacity', 25),
            'color_hex' => $request->color_hex ?: '#e11d48',
            'description' => $request->description,
            'image_path' => $imagePath,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        return back()->with('success', 'Yeni çiçek/malzeme başarıyla eklendi.');
    }

    /**
     * ✏️ Malzeme Güncelle
     */
    public function update(Request $request, CustomFlowerMaterial $material)
    {
        $request->validate([
            'category' => 'required|string|in:flower,vase,filler,wrapping,accessory',
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'daily_stock' => 'required|integer|min:0',
            'stem_capacity' => 'nullable|integer|min:1|max:200',
            'color_hex' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:10240',
        ]);

        $data = [
            'category' => $request->category,
            'name' => $request->name,
            'unit_price' => $request->unit_price,
            'cost_price' => $request->cost_price,
            'daily_stock' => $request->daily_stock,
            'stem_capacity' => $request->input('stem_capacity', $material->stem_capacity),
            'color_hex' => $request->color_hex ?: $material->color_hex,
            'description' => $request->description,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($material->image_path && Storage::disk('public')->exists($material->image_path)) {
                Storage::disk('public')->delete($material->image_path);
            }
            $data['image_path'] = ImageOptimizerService::optimizeAndStore($request->file('image'), 'flower_studio', 800, 800, 85);
        }

        $material->update($data);

        return back()->with('success', "{$material->name} başarıyla güncellendi.");
    }

    /**
     * 🗑️ Malzeme Sil
     */
    public function destroy(CustomFlowerMaterial $material)
    {
        if ($material->image_path && Storage::disk('public')->exists($material->image_path)) {
            Storage::disk('public')->delete($material->image_path);
        }
        $material->delete();

        return back()->with('success', 'Malzeme başarıyla silindi.');
    }
}
