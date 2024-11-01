<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OuterDoorSpecification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $appends = [
        'floor', 'door_number', 'external_door_specifications',
        'door_opening_direction', 'door_opening_direction2',
        'external_door_specifications2',
    ];
    protected $fillable = [
        'contract_id',
        'floor',
        'number_of_doors',
        'out_door_specification',
        'door_opening_direction',
        'out_door_specification_tow',
        'door_opening_direction_tow',
    ];

    protected $with = [
        'floor_name',
        'door_specification',
        'door_specification_tow',
        'opening_direction_tow',
        'opening_direction'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }


    public function getFloorAttribute()
    {
        return $this->attributes['floor'];
    }
    public function getDoorNumberAttribute()
    {
        return $this->attributes['number_of_doors'];
    }

    public function getExternalDoorSpecificationsAttribute()
    {
        return $this->attributes['out_door_specification'];
    }
    public function getDoorOpeningDirectionAttribute()
    {
        return $this->attributes['door_opening_direction'];
    }

    public function getExternalDoorSpecifications2Attribute()
    {
        return $this->attributes['out_door_specification_tow'];
    }
    public function getDoorOpeningDirection2Attribute()
    {
        return $this->attributes['door_opening_direction_tow'];
    }


    // belongs to floor
    function floor_name()
    {
        return $this->belongsTo(Floor::class, 'floor');
    }

    // belongs to floor
    function door_specification()
    {
        return $this->belongsTo(ExternalDoorSpecification::class, 'out_door_specification');
    }
    // belongs to floor
    function door_specification_tow()
    {
        return $this->belongsTo(ExternalDoorSpecification::class, 'out_door_specification_tow');
    }


    // belongs to floor
    function opening_direction_tow()
    {
        return $this->belongsTo(OuterDoorDirection::class, 'door_opening_direction_tow');
    }

    function opening_direction()
    {
        return $this->belongsTo(OuterDoorDirection::class, 'door_opening_direction');
    }
}
