<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RTable extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = ['floor_id', 'name', 'capacity', 'status'];

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }
}
