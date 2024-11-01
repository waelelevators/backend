<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';
    protected $fillable = [
        'contract_number',
        'area',
        'city_id',
        'neighborhood_id',
        'latitude',
        'longitude',
    ];
<<<<<<< HEAD
=======

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

>>>>>>> 1ebb111 (Maintenance Part)
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
<<<<<<< HEAD
=======

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }
>>>>>>> 1ebb111 (Maintenance Part)
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
<<<<<<< HEAD


=======
>>>>>>> 1ebb111 (Maintenance Part)
    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }
<<<<<<< HEAD

=======
    public function machineType()
    {
        return $this->belongsTo(MachineType::class, 'machine_type_id');
    }
    public function machineSpeed()
    {
        return $this->belongsTo(MachineSpeed::class, 'machine_speed_id');
    }
    public function doorSize()
    {
        return $this->belongsTo(DoorSize::class, 'door_size_id');
    }
    public function stopCount()
    {
        return $this->belongsTo(StopNumber::class, 'stops_count');
    }

    public function controlCard()
    {
        return $this->belongsTo(ControlCard::class, 'control_card_id');
    }

    public function driveType()
    {
        return $this->belongsTo(DriveTypes::class, 'drive_type_id');
    }
>>>>>>> 1ebb111 (Maintenance Part)
    // contract details
    public function contractDetails()
    {
        return $this->hasMany(MaintenanceContractDetail::class, 'maintenance_contract_id');
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

    // client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
