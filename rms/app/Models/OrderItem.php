<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'qty',
        'unit_price',
        'line_total',
        'note',
        'is_voided',
        'sent_to_kitchen_qty',
        'discount_amount',
        'void_reason',
    ];

    protected $casts = [
        'is_voided' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }
}
