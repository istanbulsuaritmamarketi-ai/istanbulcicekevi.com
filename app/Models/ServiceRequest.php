<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'region_id',
        'address',
        'service_type',
        'device_info',
        'issue_description',
        'image_path',
        'preferred_date',
        'preferred_time',
        'status',
        'service_price',
        'payment_method',
        'admin_notes',
        'assigned_staff',
        'next_maintenance_date'
    ];

    protected static function booted()
    {
        static::updated(function ($service) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'Servis Güncellendi',
                'model_type' => 'ServiceRequest',
                'model_id' => $service->id,
                'details' => 'Servis talebi "' . $service->status . '" olarak güncellendi.',
                'ip_address' => request()->ip()
            ]);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
