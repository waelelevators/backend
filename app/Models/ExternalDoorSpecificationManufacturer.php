<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDoorSpecificationManufacturer extends Model
{
    use HasFactory;

    protected $with = ['floor', 'externalDoorSpecification', 'doorCover'];

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }
    public function externalDoorSpecification()
    {
        return $this->belongsTo(OuterDoorDirection::class, 'do_spec_id', 'id');
    }
    public function doorCover()
    {
        return $this->belongsTo(Color::class);
    }
}
