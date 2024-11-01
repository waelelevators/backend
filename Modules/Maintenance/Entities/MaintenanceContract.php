<?php

namespace Modules\Maintenance\Entities;

use App\Models\City;
use App\Models\ElevatorType;
use App\Models\GeneralLog;
<<<<<<< HEAD
use App\Models\Neighborhood;
=======
use App\Models\MachineType;
use App\Models\Neighborhood;
use App\Models\StopNumber;
use App\Models\User;
>>>>>>> 1ebb111 (Maintenance Part)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';

<<<<<<< HEAD

=======
>>>>>>> 1ebb111 (Maintenance Part)
    protected $fillable = [
        'contract_number',
        'area_id',
        'user_id',
        'contract_type',
        'total',
<<<<<<< HEAD
=======
        'region_id',
>>>>>>> 1ebb111 (Maintenance Part)
        'city_id',
        'neighborhood_id',
        'latitude',
        'longitude',
        'client_id',
        'elevator_type_id',
<<<<<<< HEAD
=======
        'machine_type_id',
        'machine_speed_id',
        'door_size_id',
        'control_card_id',
        'drive_type_id',
>>>>>>> 1ebb111 (Maintenance Part)
        'building_type_id',
        'stops_count',
        'has_window',
        'has_stairs',
        'site_images',
        'active_contract_id',
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

<<<<<<< HEAD
=======
    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

>>>>>>> 1ebb111 (Maintenance Part)

    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }

<<<<<<< HEAD
=======

>>>>>>> 1ebb111 (Maintenance Part)
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 1ebb111 (Maintenance Part)
