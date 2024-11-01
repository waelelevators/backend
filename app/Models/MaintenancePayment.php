<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePayment extends Model
{
    use HasFactory;


    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'm_id', 'id');
    }
}
