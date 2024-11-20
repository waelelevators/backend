<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Maintenance\Enums\MaintenanceUpgradeStatus;

class MaintenanceUpgrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_contract_id',
        'technician_id',
        'status',
        'city_id',
        'user_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'client_id',
        'template_id',
        'elevator_type_id',
        'building_type_id',
        'stops_count',
        'has_window',
        'has_stairs',
        'site_images',
        'total',
        'tax',
        'discount',
        'net_price',
        'speed_id',
        'attachment_contract',
        'attachment_receipt',
        'rejection_reason',
        'payment_type',
        'payment_id',
    ];

    // table maintenance_upgrades
    protected $table = 'maintenance_upgrades';

    // protected $casts = [
    //     'status' => MaintenanceUpgradeStatus::class,
    // ];


    public function stopsNumber()
    {
        return $this->belongsTo(StopNumber::class, 'stops_count');
    }
    public function maintenanceContract()
    {
        return $this->belongsTo(MaintenanceContract::class);
    }


    public function requiredProducts()
    {
        return $this->morphMany(RequiredProduct::class, 'productable');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function speed()
    {
        return $this->belongsTo(MachineSpeed::class);
    }

    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class, 'elevator_type_id');
    }

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class, 'building_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function technician()
    {
        return $this->belongsTo(Employee::class);
    }

    function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function products()
    {
        return $this->morphMany(RequiredProduct::class, 'productable');
    }
    // logs
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }
}
