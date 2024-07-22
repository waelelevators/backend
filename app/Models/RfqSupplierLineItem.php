<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfqSupplierLineItem extends Model
{
    use HasFactory;

    protected $table = 'rfq_supplier_line_items';

    protected $fillable = [
        'rfq_id',
        'rfq_line_items',
        'supplier_id',
    ];

    // cast
    protected $casts = [
        'rfq_line_items' => 'array',
    ];

    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
