<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'region_id', 
        'customer_notes', 'lead_source', 'status', 'marketing_allowed'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
