<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';
    protected $fillable = [
        'installation_contract_id',
        'maintenance_contract_id',
        'maintenance_type',
        'client_id',
        'user_id',
        'start_date',
        'end_date',
        'visits_count',
        'cost',
        'paid_amount',
        'notes',
        'remaining_visits',
        'cancellation_allowance',
        'payment_status',
        'receipt_attachment',
        'contract_attachment',
        'cancellation_attachment',
        'cancellation_note',
        'status'
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
