<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OuterDoorDirections extends Model
{
    use HasFactory;

    // table
    protected $table = 'outer_door_directions';

    protected $hidden = [
        'created_at',
        'updated_at',

    ];
}
