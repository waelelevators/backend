<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    use HasFactory;

    // supplier_payments

    protected $fillable = [
        'invoice_id',
        'supplier_id',
        'payment_amount',
        'user_id',
        'attached_file',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',

    ];

    // belongs to supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }


    // user

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // invoice
    function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
