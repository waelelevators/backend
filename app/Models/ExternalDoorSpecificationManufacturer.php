<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalDoorSpecificationManufacturer extends Model
{
    use HasFactory;

    protected $hidden  = [
        'ex_do_ma_id',
        'do_spec_id',
        'door_cover_id',
        'updated_at',
        'created_at',
    ];

    protected $with = ['externalDoorSpecification', 'doorCover'];

    // public function floor()
    // {
    //     return $this->belongsTo(Floor::class);
    // }
    public function externalDoorSpecification()
    {
        return $this->belongsTo(OuterDoorDirection::class, 'do_spec_id', 'id');
    }
    public function doorCover()
    {
        return $this->belongsTo(Color::class);
    }
}
