<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        
        $query = \App\Models\Order::with('region')->latest();

        if ($tab === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        } elseif ($tab === 'active') {
            $query->whereNotIn('status', ['Tamamlandı', 'İptal edildi']);
        } elseif ($tab === 'completed') {
            $query->where('status', 'Tamamlandı');
        } elseif ($tab === 'cancelled') {
            $query->where('status', 'İptal edildi');
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('admin.orders.index', compact('orders', 'tab'));
    }

    public function show(\App\Models\Order $order)
    {
        $order->load('items.product', 'region');
        return view('admin.orders.show', compact('order'));
    }

    public function update(\Illuminate\Http\Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->update(['status' => $request->status]);
        return redirect()->route('admin.orders.show', $order)->with('success', 'Sipariş durumu güncellendi.');
    }

    public function print(\App\Models\Order $order)
    {
        $order->load('items.product', 'region');
        $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.orders.print', compact('order', 'settings'));
    }

    /**
     * Siparişleri Excel (CSV) Formatında Dışa Aktar
     */
    public function exportExcel(Request $request)
    {
        $orders = \App\Models\Order::with(['region', 'items.product'])->latest()->get();
        $fileName = 'Siparisler_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Sipariş No',
                'Müşteri Adı',
                'Telefon',
                'İlçe / Bölge',
                'Adres',
                'Toplam Tutar (TL)',
                'Maliyet (TL)',
                'Durum',
                'Ürünler',
                'Sipariş Notları / Kart Mesajı',
                'Sipariş Tarihi'
            ], ';');

            foreach ($orders as $order) {
                $itemNames = $order->items->map(fn($item) => ($item->product->name ?? 'Ürün') . " ({$item->quantity} Adet)")->implode(', ');
                fputcsv($handle, [
                    $order->order_number,
                    $order->customer_name,
                    $order->customer_phone,
                    $order->region->name ?? '-',
                    $order->address,
                    number_format($order->total_amount, 2, ',', ''),
                    number_format($order->total_cost ?? 0, 2, ',', ''),
                    $order->status,
                    $itemNames,
                    $order->notes,
                    $order->created_at->format('d.m.Y H:i:s'),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }
}
