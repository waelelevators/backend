<?php

namespace Modules\Maintenance\Entities;

use App\Models\City;
use App\Models\ElevatorType;
use App\Models\GeneralLog;
use App\Models\Neighborhood;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';


    protected $fillable = [
        'contract_number',
        'area_id',
        'user_id',
        'contract_type',
        'total',
        'city_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'client_id',
        'elevator_type_id',
        'building_type_id',
        'stops_count',
        'has_window',
        'has_stairs',
        'site_images'
    ];

    public function contractDetail()
    {
        return $this->hasOne(MaintenanceContractDetail::class, 'installation_contract_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }


    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }

    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }
}