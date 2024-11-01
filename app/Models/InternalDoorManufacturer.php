<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalDoorManufacturer extends Model
{
    use HasFactory;

    protected $with = ['contract'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function doorCover()
    {
        return $this->belongsTo(Color::class);
    }
    public function doorSize()
    {
        return $this->belongsTo(DoorSize::class);
    }
    public function orderResponse()
    {
        return $this->belongsTo(ManufactureResponses::class, 'id', 'm_id');
    }
}
