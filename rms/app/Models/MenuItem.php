<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'cost',
        'price',
        'is_available',
        'variations',
        'modifiers',
        'image',
        'stock',
        'low_stock_threshold',
    ];

    protected $casts = [
        'variations' => 'array',
        'modifiers' => 'array',
        'is_available' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
