<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceUpgradeElevator extends Model
{
    use HasFactory;
    protected $fillable = [
        'elevators_parts',
        // other fillable attributes...
    ];

   // protected $with = ['location'];

    //   protected $appends = ['elevatorsParts'];
    public function getElevatorsPartsAttribute($value)
    {
        return json_decode($value, true);
    }

    // public function location()
    // {
    //     return $this->belongsTo(MaintenanceLocationDetection::class, 'm_location_id');
    // }
}
