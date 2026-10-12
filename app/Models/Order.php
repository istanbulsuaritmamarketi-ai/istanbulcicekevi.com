<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'customer_name',
        'customer_phone',
        'recipient_name',
        'recipient_phone',
        'sender_name',
        'sender_phone',
        'is_anonymous',
        'delivery_date',
        'delivery_time_slot',
        'card_note',
        'card_sender_title',
        'address',
        'region_id',
        'total_amount',
        'total_cost',
        'status',
        'wants_service',
        'notes',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content'
    ];

    protected static function booted()
    {
        static::updated(function ($order) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Sipariş Güncellendi',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'details' => 'Sipariş durumu "' . $order->status . '" olarak güncellendi.',
                'ip_address' => request()->ip()
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
