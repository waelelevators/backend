<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceQuotation extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'client_id',
    //     'q_number',
    //     'location_data',
    //     'elevator_data',
    //     'status',
    //     'more_adds',
    //     'amount',
    //     'user_id',
    // ];

    protected $casts = [
        'id' => 'integer',
        'how_did_you_get_to_us' => 'integer',
        'is_convert_to_contract' => 'integer',
        'location_data' => 'array',
        'elevator_data' => 'array'
    ];



    protected $with = ['client'];

    protected $appends = [
        'city', 'region', 'elevatorType', 'stopsNumber', 'controlCard',
        'machineType', 'doorSize', 'machineSpeed', 'buildingType'
    ];

    public function representatives()
    {
        return $this->hasMany(Representative::class, 'contract_id', 'id')
            ->where('contract_type', 'main-quotation');
    }
    public function getBuildingTypeAttribute()
    {
        return BuildingType::where('id', $this->elevator_data['building_type_id'] ?? 0)->first();
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
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

    // public function  getDriveTypeAttribute()
    // {
    //     return DriveTypes::where('id', $this->elevator_data['drive_type_id'] ?? 0)->first();
    // }

    public function getCityAttribute()
    {
        return City::where('id', $this->location_data['city'] ?? 0)->first();
    }

    public function getRegionAttribute()
    {
        return Region::where('id', $this->location_data['region'] ?? 0)->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
