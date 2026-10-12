<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'is_active',
        'sort_order',
    ];

    public function getImageUrlAttribute(): string
    {
        if (!$this->image_path) {
            return asset('images/logo.png');
        }
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }
        $trimmed = ltrim($this->image_path, '/');
        if (str_starts_with($trimmed, 'storage/')) {
            return asset($trimmed);
        }
        if (str_starts_with($trimmed, 'images/')) {
            return asset($trimmed);
        }
        if (file_exists(public_path('images/' . $trimmed))) {
            return asset('images/' . $trimmed);
        }
        return asset('storage/' . $trimmed);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
