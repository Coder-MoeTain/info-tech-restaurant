<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'connection',
        'ip',
        'port',
        'is_kitchen',
        'is_cashier',
    ];

    protected $casts = [
        'is_kitchen' => 'boolean',
        'is_cashier' => 'boolean',
    ];
}
