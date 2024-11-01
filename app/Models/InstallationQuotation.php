<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationQuotation extends Model
{
    use HasFactory;

    protected $with = ['client', 'representatives','user'];

    // ''

    protected $appends = [
        'city',
        'region',
        'elevator_type',
        'machine_type',
        'stops_number',
        'people_load',
        'drive_type',
        'machine_load',
        'machine_warranty',
        'machine_speed',
        'control_card',
        'door_size',
        'more_additions',
        'entrances_number',
        'elevator_trip',
        'elevator_room'
    ];

    protected $casts = [
        'id' => 'integer',
        'how_did_you_get_to_us' => 'integer',
        'location_data' => 'array',
        'elevator_data' => 'array'
    ];

    public function getMoreAdditionsAttribute()
    {

        $Additions = json_decode($this->more_adds, true);

        foreach ($Additions as $value) {
            //     # code...
            $model = Addition::find($value)->get();
        }
        return $model ?? '';
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function representatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function  getElevatorTypeAttribute()
    {
        return ElevatorType::where('id', $this->elevator_data['elevator_type_id'])->first();
    }

    public function  getMachineTypeAttribute()
    {
        return MachineType::where('id', $this->elevator_data['machine_type_id'] ?? 0)->first();
    }
    public function  getStopsNumberAttribute()
    {
        return StopNumber::where('id', $this->elevator_data['stop_number_id'] ?? 0)->first();
    }

    public function  getPeopleLoadAttribute()
    {
        return PeopleLoad::where('id', $this->elevator_data['people_load_id'])->first();
    }

    public function  getDriveTypeAttribute()
    {
        return DriveTypes::where('id', $this->elevator_data['drive_type_id'])->first();
    }

    public function  getMachineLoadAttribute()
    {
        return MachineLoad::where('id', $this->elevator_data['machine_load_id'])->first();
    }

    public function  getMachineWarrantyAttribute()
    {
        return ElevatorWarranty::where('id', $this->elevator_data['machine_warranty_id'])->first();
    }

    public function  getEntrancesNumberAttribute()
    {
        return EntrancesNumber::where('id', $this->elevator_data['entrances_number_id'])->first();
    }

    public function  getDoorSizeAttribute()
    {
        return DoorSize::where('id', $this->elevator_data['door_size_id'] ?? 0)->first();
    }

    public function  getControlCardAttribute()
    {
        return ControlCard::where('id', $this->elevator_data['control_card_id'] ?? 0)->first();
    }

    public function  getElevatorTripAttribute()
    {
        return ElevatorTrip::where('id', $this->elevator_data['elevator_trip_id'] ?? 0)->first();
    }

    public function  getElevatorRoomAttribute()
    {
        return ElevatorRoom::where('id', $this->elevator_data['elevator_room_id'] ?? 0)->first();
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

    public function getInstallmentsAttribute($value)
    {
        return json_decode($value, true);
    }
}
