<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationD extends Model
{
    use HasFactory;

    // table
    protected $table = 'quotation_d';

    // with
    protected $with = ['product'];
    // belongs to product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // belongs to quotation
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
