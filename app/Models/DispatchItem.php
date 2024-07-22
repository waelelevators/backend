<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// DispatchItem Model
class DispatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_id',
        'product_id',
        'qty'
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
