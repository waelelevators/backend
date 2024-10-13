<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceUpdate extends Model
{
    use HasFactory;
    protected $fillable = [
        'maintenance_contract_id',
        'description',
        'product_id',
        'productable',
        'quantity',
        'tax',
        'subtotal',
        'price',
        'notes',
        'status',
    ];

    public function maintenanceContract()
    {
        return $this->belongsTo(MaintenanceContract::class);
    }

    public function requiredProducts()
    {
        return $this->morphMany(RequiredProduct::class, 'productable');
    }
}