<?php

namespace App\Http\Controllers;

use App\Models\CustomFlowerMaterial;
use Illuminate\Http\Request;

class CustomFlowerController extends Controller
{
    /**
     * 2.5D Canlı Çiçek Tasarım Stüdyosu
     */
    public function studio()
    {
        // Aktif ve günlük stoğu bulunan malzemeleri çek
        $containers = CustomFlowerMaterial::whereIn('category', ['vase', 'container'])
            ->where('is_active', true)
            ->where('daily_stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $flowers = CustomFlowerMaterial::where('category', 'flower')
            ->where('is_active', true)
            ->where('daily_stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $fillers = CustomFlowerMaterial::where('category', 'filler')
            ->where('is_active', true)
            ->where('daily_stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $wrappings = CustomFlowerMaterial::where('category', 'wrapping')
            ->where('is_active', true)
            ->where('daily_stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        $ribbons = CustomFlowerMaterial::whereIn('category', ['ribbon', 'accessory'])
            ->where('is_active', true)
            ->where('daily_stock', '>', 0)
            ->orderBy('sort_order')
            ->get();

        return view('custom_flower.studio', compact(
            'containers',
            'flowers',
            'fillers',
            'wrappings',
            'ribbons'
        ));
    }

    /**
     * Özel Tasarımı Sepete Ekle
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'container_id' => 'required|exists:custom_flower_materials,id',
            'flowers' => 'required|array',
            'fillers' => 'nullable|array',
            'wrapping_id' => 'nullable|exists:custom_flower_materials,id',
            'ribbon_id' => 'nullable|exists:custom_flower_materials,id',
            'card_note' => 'nullable|string|max:500',
            'card_sender_title' => 'nullable|string|max:100',
            'custom_title' => 'nullable|string|max:150',
            'snapshot' => 'nullable|string',
            'slots' => 'nullable|array',
            'greenery_level' => 'nullable|string',
            'arrangement_form' => 'nullable|string',
        ]);

        $container = CustomFlowerMaterial::findOrFail($validated['container_id']);
        if ($container->daily_stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => "Seçtiğiniz \"{$container->name}\" maalesef tükendi."
            ], 422);
        }

        $totalPrice = (float) $container->unit_price;
        $totalCost = (float) $container->cost_price;
        $totalStems = 0;

        $flowersData = [];
        foreach ($validated['flowers'] as $flowerId => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) continue;

            $flower = CustomFlowerMaterial::find($flowerId);
            if (!$flower || !$flower->is_active) continue;

            if ($qty > $flower->daily_stock) {
                return response()->json([
                    'success' => false,
                    'message' => "\"{$flower->name}\" için yeterli stok bulunmuyor. Kalan stok: {$flower->daily_stock}"
                ], 422);
            }

            $totalPrice += $flower->unit_price * $qty;
            $totalCost += $flower->cost_price * $qty;
            $totalStems += $qty;

            $flowersData[] = [
                'id' => $flower->id,
                'name' => $flower->name,
                'quantity' => $qty,
                'unit_price' => (float) $flower->unit_price,
                'subtotal' => (float) ($flower->unit_price * $qty),
                'color_code' => $flower->color_code,
                'image_url' => $flower->image_url,
            ];
        }

        if (empty($flowersData)) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen aranjmanınıza en az 1 dal çiçek ekleyin.'
            ], 422);
        }

        $fillersData = [];
        if (!empty($validated['fillers'])) {
            foreach ($validated['fillers'] as $fillerId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;

                $filler = CustomFlowerMaterial::find($fillerId);
                if (!$filler || !$filler->is_active) continue;

                if ($qty > $filler->daily_stock) {
                    return response()->json([
                        'success' => false,
                        'message' => "\"{$filler->name}\" için yeterli stok bulunmuyor. Kalan stok: {$filler->daily_stock}"
                    ], 422);
                }

                $totalPrice += $filler->unit_price * $qty;
                $totalCost += $filler->cost_price * $qty;
                $totalStems += $qty;

                $fillersData[] = [
                    'id' => $filler->id,
                    'name' => $filler->name,
                    'quantity' => $qty,
                    'unit_price' => (float) $filler->unit_price,
                    'subtotal' => (float) ($filler->unit_price * $qty),
                    'color_code' => $filler->color_code,
                    'image_url' => $filler->image_url,
                ];
            }
        }

        $wrappingData = null;
        if (!empty($validated['wrapping_id'])) {
            $wrapping = CustomFlowerMaterial::find($validated['wrapping_id']);
            if ($wrapping && $wrapping->is_active && $wrapping->daily_stock > 0) {
                $totalPrice += $wrapping->unit_price;
                $totalCost += $wrapping->cost_price;
                $wrappingData = [
                    'id' => $wrapping->id,
                    'name' => $wrapping->name,
                    'price' => (float) $wrapping->unit_price,
                    'color_code' => $wrapping->color_code,
                ];
            }
        }

        $ribbonData = null;
        if (!empty($validated['ribbon_id'])) {
            $ribbon = CustomFlowerMaterial::find($validated['ribbon_id']);
            if ($ribbon && $ribbon->is_active && $ribbon->daily_stock > 0) {
                $totalPrice += $ribbon->unit_price;
                $totalCost += $ribbon->cost_price;

                $position = $request->input('ribbon_position', 'neck');
                $positionLabels = [
                    'neck' => 'Vazo Boynu',
                    'waist' => 'Orta Gövde / Bel',
                    'base' => 'Taban Kısmı',
                ];

                $ribbonData = [
                    'id' => $ribbon->id,
                    'name' => $ribbon->name,
                    'price' => (float) $ribbon->unit_price,
                    'color_code' => $ribbon->color_code,
                    'position' => $position,
                    'position_label' => $positionLabels[$position] ?? 'Vazo Boynu',
                ];
            }
        }

        // Tasarım Başlığı
        $designTitle = $validated['custom_title'] ?: ($container->name . ' - Özel Çiçek Aranjmanı');

        // Sepete Ekle
        $customCartKey = 'custom_' . time() . '_' . rand(100, 999);
        $cart = session()->get('cart', []);

        $cart[$customCartKey] = [
            'id' => $customCartKey,
            'is_custom' => true,
            'name' => $designTitle,
            'price' => $totalPrice,
            'cost_price' => $totalCost,
            'quantity' => 1,
            'image' => null,
            'custom_title' => $designTitle,
            'custom_design_data' => [
                'container' => [
                    'id' => $container->id,
                    'name' => $container->name,
                    'price' => (float) $container->unit_price,
                    'stem_capacity' => $container->stem_capacity,
                    'color_code' => $container->color_code,
                ],
                'flowers' => $flowersData,
                'fillers' => $fillersData,
                'wrapping' => $wrappingData,
                'ribbon' => $ribbonData,
                'card_note' => $validated['card_note'] ?? null,
                'card_sender_title' => $validated['card_sender_title'] ?? null,
                'total_stems' => $totalStems,
                'slots' => $validated['slots'] ?? [],
                'greenery_level' => $validated['greenery_level'] ?? 'natural',
                'arrangement_form' => $validated['arrangement_form'] ?? 'compact',
            ],
            'custom_design_snapshot' => $validated['snapshot'] ?? null,
        ];

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Özel çiçek tasarımınız başarıyla sepete eklendi!',
            'redirect' => route('cart.index'),
        ]);
    }
}
