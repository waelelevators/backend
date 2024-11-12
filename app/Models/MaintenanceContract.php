<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';
    protected $fillable = [
        'project_name',
        'contract_number',
        'area_id',
        'region_id',
        'user_id',
        'contract_type',
        'contract_cancellation_attachment',
        'contract_cancellation_note',
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
        'control_card_id',
        'door_size_id',
        'machine_type_id',
        'machine_speed_id',
        'representative_id',
        'branch_id',
        'drive_type_id',
        'quotation_to_contract_date',
        'template_id'
    ];



    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
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

    // drive_type
    public function driveType()
    {
        return $this->belongsTo(DriveTypes::class, 'drive_type_id');
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

    // buildingType
    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class, 'building_type_id');
    }


    // active_contract_id
    public function activeContract()
    {
        return $this->belongsTo(MaintenanceContractDetail::class, 'active_contract_id');
    }

    // logs
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }

    // machineSpeed
    public function machineSpeed()
    {
        return $this->belongsTo(MachineSpeed::class, 'machine_speed_id');
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

    // template
    function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}