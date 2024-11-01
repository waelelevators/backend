<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationDetectionDoorSizes extends Model
{
    use HasFactory;

    protected $with = ['floor'];

    public function floor()
    {
        return $this->belongsTo(Floor::class, 'floor_id', 'id');
    }
}
