<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    // table
    protected $table = 'floors';

    protected $hidden = [
        'created_at',
        'updated_at',

    ];
}
