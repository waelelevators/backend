<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MalfunctionResponse extends Model
{
    use HasFactory;

    protected $casts = [
        'elevators_parts' => 'array'
    ];
}
