<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFlowerMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'name',
        'unit_price',
        'cost_price',
        'daily_stock',
        'stem_capacity',
        'image_path',
        'color_hex',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'daily_stock' => 'integer',
        'stem_capacity' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'color_code',
        'stem_capacity',
        'type',
        'stem_image_url',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        // Admin henüz özel fotoğraf yüklememişse gerçekçi stüdyo sprite'larını varsayılan olarak döndür
        $nameLower = mb_strtolower($this->name);
        if ($this->category === 'flower') {
            if (str_contains($nameLower, 'santini') || str_contains($nameLower, 'ponpon') || str_contains($nameLower, 'krizantem')) return asset('images/studio/green_santini.png');
            if (str_contains($nameLower, 'papatya')) return asset('images/studio/daisy.png');
            if (str_contains($nameLower, 'beyaz') && str_contains($nameLower, 'gül')) return asset('images/studio/white_rose.png');
            if (str_contains($nameLower, 'pembe') || str_contains($nameLower, 'pudra')) return asset('images/studio/pink_rose.png');
            if (str_contains($nameLower, 'lale')) return asset('images/studio/yellow_tulip.png');
            if (str_contains($nameLower, 'gül')) return asset('images/studio/red_rose.png');
            return asset('images/studio/red_rose.png');
        }
        if ($this->category === 'filler') {
            if (str_contains($nameLower, 'santini') || str_contains($nameLower, 'ponpon') || str_contains($nameLower, 'krizantem')) return asset('images/studio/green_santini.png');
            if (str_contains($nameLower, 'portakal') || str_contains($nameLower, 'kurutulmuş') || str_contains($nameLower, 'tarçın')) return asset('images/studio/dried_orange.png');
            if (str_contains($nameLower, 'okalipt')) return asset('images/studio/eucalyptus.png');
            return asset('images/studio/gypsophila.png');
        }
        if ($this->category === 'vase') {
            if (str_contains($nameLower, 'kutu')) return asset('images/studio/black_box.png');
            if (str_contains($nameLower, 'buket')) return asset('images/studio/bouquet_wrap.png');
            return asset('images/studio/glass_vase.png');
        }
        if ($this->category === 'wrapping') {
            return asset('images/studio/bouquet_wrap.png');
        }

        return null;
    }

    public function getStemImageUrlAttribute()
    {
        $nameLower = mb_strtolower($this->name);
        if ($this->category === 'flower') {
            if (str_contains($nameLower, 'santini') || str_contains($nameLower, 'ponpon') || str_contains($nameLower, 'krizantem')) return asset('images/studio/green_santini_stem.png');
            if (str_contains($nameLower, 'papatya')) return asset('images/studio/daisy_stem.png');
            if (str_contains($nameLower, 'beyaz') && str_contains($nameLower, 'gül')) return asset('images/studio/white_rose_stem.png');
            if (str_contains($nameLower, 'pembe') || str_contains($nameLower, 'pudra')) return asset('images/studio/pink_rose_stem.png');
            if (str_contains($nameLower, 'lale')) return asset('images/studio/yellow_tulip_stem.png');
            if (str_contains($nameLower, 'gül')) return asset('images/studio/red_rose_stem.png');
            return asset('images/studio/red_rose_stem.png');
        }
        if ($this->category === 'filler') {
            if (str_contains($nameLower, 'santini') || str_contains($nameLower, 'ponpon') || str_contains($nameLower, 'krizantem')) return asset('images/studio/green_santini_stem.png');
            if (str_contains($nameLower, 'portakal') || str_contains($nameLower, 'kurutulmuş') || str_contains($nameLower, 'tarçın')) return asset('images/studio/dried_orange_stem.png');
            if (str_contains($nameLower, 'okalipt')) return asset('images/studio/eucalyptus_stem.png');
            return asset('images/studio/gypsophila_stem.png');
        }
        return $this->image_url;
    }

    public function getColorCodeAttribute()
    {
        return $this->color_hex ?: '#e11d48';
    }

    public function getStemCapacityAttribute()
    {
        if (!empty($this->attributes['stem_capacity'])) {
            return (int) $this->attributes['stem_capacity'];
        }
        if ($this->category === 'vase') {
            $nameLower = mb_strtolower($this->name);
            if (str_contains($nameLower, 'kutu')) return 20;
            return 25;
        }
        if ($this->category === 'wrapping') return 35;
        return 30;
    }

    public function getTypeAttribute()
    {
        if ($this->category === 'vase') {
            $nameLower = mb_strtolower($this->name);
            if (str_contains($nameLower, 'kutu')) return 'box';
            if (str_contains($nameLower, 'buket')) return 'bouquet';
            return 'vase';
        }
        return $this->category;
    }
}
