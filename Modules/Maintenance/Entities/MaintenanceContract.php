<?php

namespace Modules\Maintenance\Entities;

use App\Models\Area;
use App\Models\Branch;
use App\Models\BuildingType;
use App\Models\City;
use App\Models\Client;
use App\Models\ControlCard;
use App\Models\DoorSize;
use App\Models\DriveTypes;
use App\Models\ElevatorType;
use App\Models\GeneralLog;
use App\Models\MachineSpeed;
use App\Models\MachineType;
use App\Models\Neighborhood;
use App\Models\Region;
use App\Models\Representative;
use App\Models\StopNumber;
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
        'raigon_id',
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
        'active_contract_id',
        'door_direction_id',
        'control_type_id',
        'door_size_id',
        'machine_type_id',
        'drive_type_id',
        'machine_speed_id',
        'representative_id',
        'branch_id'
    ];

    public function contractDetail()
    {
        return $this->hasOne(MaintenanceContractDetail::class, 'installation_contract_id');
    }

    public function activeContract()
    {
        return $this->belongsTo(MaintenanceContractDetail::class, 'active_contract_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }




    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }


    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }

    // machineType
    public function machineType()
    {
        return $this->belongsTo(MachineType::class);
    }
    // doorSize
    public function doorSize()
    {
        return $this->belongsTo(DoorSize::class);
    }

    // stopsNumber
    public function stopsNumber()
    {
        return $this->belongsTo(StopNumber::class, 'stops_count');
    }
    // controlCard
    public function controlCard()
    {
        return $this->belongsTo(ControlCard::class, 'control_type_id');
    }

    // Branch
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    // region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    // contract details
    public function contractDetails()
    {
        return $this->hasMany(MaintenanceContractDetail::class, 'maintenance_contract_id');
    }

    // speed
    public function machineSpeed()
    {
        return $this->belongsTo(MachineSpeed::class, 'machine_speed_id');
    }

    // drive_type
    public function driveType()
    {
        return $this->belongsTo(DriveTypes::class, 'drive_type_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }



    // buildingType
    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class, 'building_type_id');
    }



    // client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // representatives
    public function representatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }
}
