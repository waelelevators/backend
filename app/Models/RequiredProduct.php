<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequiredProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'productable',
        'quantity',
        'tax',
        'subtotal',
        'price',
        'notes',
        'status',
    ];

    protected $with = ['product'];

    /**
     * The productable model for this required product (e.g. maintenance report or quotation).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function productable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
