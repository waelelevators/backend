<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    protected $appends = ['amountWithTaxed'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'tax',
        'payment_stages_id'

    ];
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function getAmountWithTaxedAttribute()
    {
        return $this->attributes['tax'];
    }
}
