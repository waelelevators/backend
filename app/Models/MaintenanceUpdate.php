<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_contract_id',
        'status',
        'city_id',
        'user_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'client_id',
        'elevator_type_id',
        'building_type_id',
        'stops_count',
        'has_window',
        'has_stairs',
        'site_images',
        'total',
        'tax',
        'discount',
        'net_price'
    ];

    public function maintenanceContract()
    {
        return $this->belongsTo(MaintenanceContract::class);
    }

    public function requiredProducts()
    {
        return $this->morphMany(RequiredProduct::class, 'productable');
    }

    public function customer()
    {
        return $this->belongsTo(Client::class);
    }
}
