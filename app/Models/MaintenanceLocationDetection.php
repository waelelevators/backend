<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLocationDetection extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'location_data' => 'array',
        'elevator_data' => 'array',
        'how_did_you_get_to_us' => 'integer'
    ];

    protected $with = ['client', 'representatives'];

    protected $appends = [
        'city', 'region', 'elevatorType', 'stopsNumber', 'controlCard',
        'machineType', 'doorSize', 'machineSpeed',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function representatives()
    {
        return $this->hasMany(Representative::class, 'contract_id', 'id')
            ->where('contract_type', 'main-locations');
    }

    public function  getElevatorTypeAttribute()
    {
        return ElevatorType::where('id', $this->elevator_data['elevator_type_id'])->first();
    }

    public function  getStopsNumberAttribute()
    {
        return StopNumber::where('id', $this->elevator_data['stop_number_id'] ?? 0)->first();
    }
    public function  getDoorSizeAttribute()
    {
        return DoorSize::where('id', $this->elevator_data['door_size_id'] ?? 0)->first();
    }

    public function  getControlCardAttribute()
    {
        return ControlCard::where('id', $this->elevator_data['control_card_id'] ?? 0)->first();
    }

    public function  getMachineTypeAttribute()
    {
        return MachineType::where('id', $this->elevator_data['machine_type_id'] ?? 0)->first();
    }

    public function  getMachineSpeedAttribute()
    {
        return MachineSpeed::where('id', $this->elevator_data['machine_speed_id'] ?? 0)->first();
    }

    public function getCityAttribute()
    {
        return City::where('id', $this->location_data['city'] ?? 0)->first();
    }

    public function getRegionAttribute()
    {
        return Region::where('id', $this->location_data['region'] ?? 0)->first();
    }

    // public function  getDetectedByAttribute()
    // {
    //     return User::where('id', $this->detection_by ?? 0)->first();
    // }
}
