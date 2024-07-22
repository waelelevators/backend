<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    use HasFactory;

    protected $with = [
        'mInfo', 'currentContract', 'area'
    ];

    public function mInfo()
    {
        return $this->belongsTo(MaintenanceInfo::class, 'm_info_id', 'id');
    }

    public function currentContract()
    {
        return $this->belongsTo(Maintenance::class, 'm_id', 'id');
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
}
