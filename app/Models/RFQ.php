<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFQ extends Model
{
    use HasFactory;

    // table
    protected $table = 'rfqs';

    public function lineItems()
    {
        return $this->hasMany(RFQLineItem::class, 'rfq_id');
    }

    public function responses()
    {
        return $this->hasMany(RFQResponse::class, 'rfq_id');
    }

    // products through line items
    public function products()
    {
        return $this->hasManyThrough(Product::class, RFQLineItem::class, 'rfq_id', 'id', 'id', 'product_id');
    }

    // has one invoice
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'rfq_id');
    }

    // belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
