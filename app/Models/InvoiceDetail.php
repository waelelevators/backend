<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'qty',
        'price',
    ];


    protected $with = [
        'supplier', 'product',
    ];



    // belongs to invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // belongs to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
