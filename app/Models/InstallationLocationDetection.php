<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationLocationDetection extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'location_data' => 'array',
        'well_data' => 'array',
        'machine_data' => 'array',
        'door_sizes' => 'array',
        'user_id' => 'integer',
    ];
    // protected $with = ['client', 'representatives', 'detectionBy', 'user'];

    protected $appends = [
        'city', 'region', 'neighborhood',  'elevatorType',
        'doorOpingDirection', 'stopsNumber', 'elevatorTrip',
        'weightLocation', 'floor', 'contractStatus'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'location_id');
    }

    public function getContractStatusAttribute()
    {
        $contract = $this->contracts()->orderBy('created_at', 'desc')->first();
        return $contract ? $contract->contract_status : null;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function detectionBy()
    {
        return $this->belongsTo(User::class, 'detection_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function representatives()
    {
        return $this->belongsTo(Representative::class, 'representative_id');
    }

    public function  getElevatorTypeAttribute()
    {
        return ElevatorType::where('id', $this->well_data['elevator_type_id'])->first();
    }
    public function  getDoorOpingDirectionAttribute()
    {
        return InnerDoorType::where('id', $this->well_data['door_open_direction_id'])->first();
    }
    public function  getWeightLocationAttribute()
    {
        return WeightLocation::where('id', $this->well_data['elevator_weight_location_id'])->first();
    }

    public function  getStopsNumberAttribute()
    {
        return StopNumber::where('id', $this->well_data['stop_number_id'])->first();
    }

    public function  getElevatorTripAttribute()
    {
        return ElevatorTrip::where('id', $this->well_data['elevator_trips_id'])->first();
    }

    public function getRegionAttribute()
    {
        return Region::where('id', $this->location_data['region'] ?? 0)->first();
    }

    public function getCityAttribute()
    {
        return City::where('id', $this->location_data['city'] ?? 0)->first();
    }
    public function getNeighborhoodAttribute()
    {
        return Neighborhood::where('id', $this->location_data['neighborhood'] ?? 0)->first();
    }

    public function getFloorAttribute()
    {
        return Floor::get();
    }


    public function getFloorDataAttribute($value)
    {
        // Decode the JSON string into an associative array
        $decodedValue = json_decode($value, true);

        // Check if the decoded value is an array
        if (is_array($decodedValue)) {
            // Filter out elements that don't have a 'floor_id'
            $decodedValue = array_filter($decodedValue, function ($item) {
                return isset($item['floor_id']);
            });

            // Check again to ensure that we still have elements to sort
            if (!empty($decodedValue)) {
                // Sort the array by 'floor_id'
                $floorNumbers = array_column($decodedValue, 'floor_id');
                array_multisort($floorNumbers, SORT_ASC, $decodedValue);
            }

            // Return the sorted (or possibly filtered) array
            return $decodedValue;
        }

        // If not an array, return the original value (likely null or an empty string)
        return $value;
    }
}
