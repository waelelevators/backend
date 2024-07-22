<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Malfunction extends Model
{
    use HasFactory;

    protected $with = ["maintenance"];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'm_id', 'id');
    }
}
