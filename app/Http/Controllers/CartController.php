<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $couponData = session()->get('coupon', null);
        
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        if ($couponData && !empty($cart)) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon');
                $couponData = null;
            }
        }

        $total = max(0, $subtotal - $discount);

        return view('cart.index', compact('cart', 'subtotal', 'discount', 'total', 'couponData'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        
        $quantity = max(1, (int) $request->input('quantity', 1));

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->effective_price,
                'original_price' => $product->price,
                'campaign_price' => $product->campaign_price,
                'quantity' => $quantity,
                'image' => $product->image_path,
                'slug' => $product->slug,
            ];
        }
        
        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Ürün sepete eklendi!');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $action = $request->input('action');

        if(isset($cart[$id])) {
            if ($action === 'increase') {
                $cart[$id]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$id]['quantity']--;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            } elseif ($request->has('quantity')) {
                $qty = (int) $request->input('quantity');
                if ($qty > 0) {
                    $cart[$id]['quantity'] = $qty;
                } else {
                    unset($cart[$id]);
                }
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Sepet güncellendi.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Ürün sepetten kaldırıldı.');
    }

    public function clear()
    {
        session()->forget('cart');
        session()->forget('coupon');
        return redirect()->route('cart.index')->with('success', 'Sepetiniz temizlendi.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Sepetiniz boşken kupon uygulayamazsınız.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Geçersiz veya bulunamayan kupon kodu.');
        }

        if (!$coupon->isValidForAmount($subtotal)) {
            if ($coupon->min_order_amount > $subtotal) {
                return back()->with('error', "Bu kuponu kullanabilmek için sepet tutarınız en az {$coupon->min_order_amount} TL olmalıdır.");
            }
            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return back()->with('error', 'Bu kuponun kullanım süresi dolmuştur.');
            }
            if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                return back()->with('error', 'Bu kupon maksimum kullanım limitine ulaşmıştır.');
            }
            return back()->with('error', 'Kupon şu anda kullanılamıyor.');
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]);

        return back()->with('success', "Tebrikler! \"{$coupon->code}\" kuponu uygulandı ve {$discount} TL indirim kazandınız.");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Kupon sepetten kaldırıldı.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) return redirect()->route('home');

        $couponData = session()->get('coupon', null);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $discount = 0;
        if ($couponData) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }
        $total = max(0, $subtotal - $discount);
        
        // Tüm İstanbul ilçelerinin listede olduğundan emin ol (sipariş kaçmasın)
        if (Region::count() < 10) {
            foreach (\App\Http\Controllers\DistrictController::$districts as $d) {
                Region::firstOrCreate(['name' => $d['name']], ['is_active' => true]);
            }
        }

        $regions = Region::where('is_active', true)->orderBy('name')->get();
        return view('cart.checkout', compact('cart', 'regions', 'subtotal', 'discount', 'total', 'couponData'));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'address' => 'required|string',
            'region_id' => 'required|exists:regions,id',
        ]);

        $cart = session()->get('cart', []);
        if(empty($cart)) return redirect()->route('home');

        // Calculate total amount and total cost
        $subtotal = 0;
        $totalCost = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            
            if (!empty($item['is_custom'])) {
                $totalCost += ($item['cost_price'] ?? 0) * $item['quantity'];
            } else {
                $product = Product::find($item['id']);
                if ($product && $product->cost_price) {
                    $totalCost += $product->cost_price * $item['quantity'];
                }
            }
        }

        // Apply coupon if exists
        $couponData = session()->get('coupon', null);
        $discount = 0;
        $couponCode = null;
        if ($couponData) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
                $coupon->increment('used_count');
            }
        }

        $totalAmount = max(0, $subtotal - $discount);

        // Handle Customer
        $customer = Customer::firstOrCreate(
            ['phone' => $request->customer_phone],
            [
                'name' => $request->customer_name,
                'email' => null,
                'address' => $request->address,
                'region_id' => $request->region_id,
            ]
        );

        $utmData = session('utm_data', []);

        $notes = $request->notes;
        if ($couponCode) {
            $notes = ($notes ? $notes . " | " : "") . "Kullanılan Kupon: {$couponCode} (-{$discount} TL İndirim)";
        }

        $recipientName = $request->input('recipient_name') ?: $request->customer_name;
        $recipientPhone = $request->input('recipient_phone') ?: $request->customer_phone;
        $senderName = $request->input('sender_name') ?: $request->customer_name;
        $senderPhone = $request->input('sender_phone') ?: $request->customer_phone;
        $deliveryDate = $request->input('delivery_date');
        $deliveryTimeSlot = $request->input('delivery_time_slot');
        $cardNote = $request->input('card_note');
        $cardSenderTitle = $request->input('card_sender_title');
        $isAnonymous = $request->boolean('is_anonymous');

        // Eğer checkout formunda not girilmemişse ama özel tasarımda kart notu varsa onu kullan
        if (empty($cardNote)) {
            foreach ($cart as $cItem) {
                if (!empty($cItem['is_custom']) && !empty($cItem['custom_design_data']['card_note'])) {
                    $cardNote = $cItem['custom_design_data']['card_note'];
                    $cardSenderTitle = $cardSenderTitle ?: ($cItem['custom_design_data']['card_sender_title'] ?? null);
                    break;
                }
            }
        }

        if ($deliveryDate || $deliveryTimeSlot) {
            $notes = ($notes ? $notes . " | " : "") . "🕒 Teslimat: " . trim("{$deliveryDate} {$deliveryTimeSlot}");
        }
        if ($cardNote) {
            $notes = ($notes ? $notes . " | " : "") . "💌 Kart Notu: \"{$cardNote}\"";
            if ($cardSenderTitle) {
                $notes .= " (Kart İmzası: {$cardSenderTitle})";
            }
        }
        if ($isAnonymous) {
            $notes = ($notes ? $notes . " | " : "") . "🤫 Gizli Gönderim (Alıcıya İsim İletilmeyecek)";
        }

        $isPayTR = $request->input('payment_method') === 'paytr';
        $orderStatus = $isPayTR ? 'Ödeme Bekliyor (PayTR)' : 'Sipariş alındı';

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
            'customer_name' => $recipientName,
            'customer_phone' => $recipientPhone,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'sender_name' => $senderName,
            'sender_phone' => $senderPhone,
            'is_anonymous' => $isAnonymous,
            'delivery_date' => $deliveryDate,
            'delivery_time_slot' => $deliveryTimeSlot,
            'card_note' => $cardNote,
            'card_sender_title' => $cardSenderTitle,
            'address' => $request->address,
            'region_id' => $request->region_id,
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'status' => $orderStatus,
            'wants_service' => $request->has('wants_service'),
            'notes' => $notes,
            'utm_source' => $utmData['utm_source'] ?? null,
            'utm_medium' => $utmData['utm_medium'] ?? null,
            'utm_campaign' => $utmData['utm_campaign'] ?? null,
            'utm_content' => $utmData['utm_content'] ?? null,
        ]);

        foreach($cart as $item) {
            $isCustom = !empty($item['is_custom']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $isCustom ? null : ($item['id'] ?? null),
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'custom_title' => $isCustom ? ($item['name'] ?? 'Özel Çiçek Tasarımı') : null,
                'custom_design_data' => $isCustom ? ($item['custom_design_data'] ?? null) : null,
                'custom_design_snapshot' => $isCustom ? ($item['custom_design_snapshot'] ?? null) : null,
            ]);
        }

        // PayTR ile Kredi Kartı Ödemesi Seçilmişse Direkt PayTR Sitesine Yönlendir
        if ($isPayTR) {
            $payTRService = app(\App\Services\PayTRService::class);
            $paytrResult = $payTRService->createPaymentUrl($order, $cart, $request->ip());

            if ($paytrResult['status'] === 'success') {
                session()->forget('cart');
                session()->forget('coupon');
                return redirect()->away($paytrResult['payment_url']);
            }

            // PayTR anahtarları girilmemiş veya hata vermişse müşteriye bildir
            return redirect()->route('paytr.fail', $order->order_number)
                ->with('error', 'PayTR Bağlantı Hatası: ' . ($paytrResult['reason'] ?? 'Ödeme başlatılamadı.'));
        }

        session()->forget('cart');
        session()->forget('coupon');
        return redirect()->route('cart.success', $order->order_number);
    }

    public function success(Order $order)
    {
        return view('cart.success', compact('order'));
    }
}
