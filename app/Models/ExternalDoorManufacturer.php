<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExternalDoorManufacturer extends Model
{
    use HasFactory;

    // protected $with = ['externalDoorSpecification'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function doorSize()
    {
        return $this->belongsTo(DoorSize::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function externalDoorSpecification()
    {
        return $this->hasMany(ExternalDoorSpecificationManufacturer::class, 'ex_do_ma_id', 'id');
    }

    public function orderResponse()
    {
        return $this->belongsTo(ManufactureResponses::class, 'id', 'm_id');
    }
}
