<?php

namespace App\Models;

use backend\models\DoorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CabinManufacture extends Model
{
    use HasFactory;

    protected $with = ['status', 'weightLocation', 'doorSize', 'doorType', 'doorDirection', 'coverType'];


    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function weightLocation()
    {
        return $this->belongsTo(WeightLocation::class);
    }

    public function doorSize()
    {
        return $this->belongsTo(DoorSize::class);
    }

    public function doorType()
    {
        return $this->belongsTo(InnerDoorType::class);
    }

    public function doorDirection()
    {
        return $this->belongsTo(OuterDoorDirection::class);
    }
    public function orderResponse()
    {
        return $this->belongsTo(ManufactureResponses::class, 'id', 'm_id');
    }
    public function coverType()
    {
        return $this->belongsTo(CoverType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
