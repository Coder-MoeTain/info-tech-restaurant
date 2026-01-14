<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
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
    ];

    protected $casts = [
        'variations' => 'array',
        'modifiers' => 'array',
        'is_available' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
