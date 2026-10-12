<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Region;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayOrders = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::whereNotIn('status', ['Teslim Edildi', 'Tamamlandı', 'İptal Edildi'])->count();
        $totalProducts = Product::where('is_active', true)->count();
        $totalCategories = Category::where('is_active', true)->count();

        $recentOrders = Order::with(['region', 'items.product'])->latest()->take(6)->get();

        $pendingReviewsCount = \App\Models\Review::where('is_approved', false)->count();
        $unreadMessagesCount = \App\Models\ContactMessage::where('is_read', false)->count();

        // 🕒 Teslimat Saat Dilimleri Masası (Aktif Siparişler)
        $activeOrdersQuery = Order::whereNotIn('status', ['Teslim Edildi', 'Tamamlandı', 'İptal Edildi']);
        $slot10_14 = (clone $activeOrdersQuery)->where('delivery_time_slot', '10:00 - 14:00')->count();
        $slot14_18 = (clone $activeOrdersQuery)->where('delivery_time_slot', '14:00 - 18:00')->count();
        $slot18_22 = (clone $activeOrdersQuery)->where('delivery_time_slot', '18:00 - 22:00')->count();
        $slotVip   = (clone $activeOrdersQuery)->where(function($q) {
            $q->whereNotIn('delivery_time_slot', ['10:00 - 14:00', '14:00 - 18:00', '18:00 - 22:00'])
              ->orWhereNull('delivery_time_slot');
        })->count();

        // ⚠️ Kritik Azalan Çiçek Stokları (Stok <= 5)
        $lowStockProducts = Product::where('is_active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(4)
            ->get();
        $lowStockCount = Product::where('is_active', true)->where('stock', '<=', 5)->count();

        // 🌸 Çiçek Tasarım Stüdyosu Malzeme Sayısı
        $customStudioCount = \App\Models\CustomFlowerMaterial::where('is_active', true)->count();

        // 1. Sipariş Durum Dağılımı (Doughnut Chart Verisi)
        $orderStatuses = ['Yeni Sipariş', 'Hazırlanıyor', 'Kuryeye Verildi', 'Teslim Edildi', 'İptal Edildi'];
        $orderStatusCounts = [];
        foreach ($orderStatuses as $st) {
            $orderStatusCounts[] = Order::where('status', $st)->count();
        }

        // 2. İlçe Bazlı Çiçek Gönderim Dağılımı (Bar Chart Verisi)
        $regionOrders = Region::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(7)
            ->get();

        $regionLabels = $regionOrders->pluck('name')->toArray();
        $regionCounts = $regionOrders->pluck('orders_count')->toArray();

        $popularProducts = Product::where('is_active', true)->with('category')->latest()->take(5)->get();
        $totalCustomers = \App\Models\User::where('role', 'customer')->orWhere('is_admin', false)->count();

        return view('dashboard', compact(
            'todayOrders',
            'pendingOrders',
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'popularProducts',
            'recentOrders',
            'pendingReviewsCount',
            'unreadMessagesCount',
            'slot10_14',
            'slot14_18',
            'slot18_22',
            'slotVip',
            'lowStockProducts',
            'lowStockCount',
            'customStudioCount',
            'orderStatuses',
            'orderStatusCounts',
            'regionLabels',
            'regionCounts'
        ));
    }
}

