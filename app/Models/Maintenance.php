<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;
    protected $casts = [
        'id' => 'integer',
        'm_status_id' => 'integer'
        // 'started_date' => 'date',
        // 'ended_date' => 'date',
        // m_type_id
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',

    ];

    protected $with = [
        'mType', 'mStatus', 'mInfo'
    ];

    public function mInfo()
    {
        return $this->belongsTo(MaintenanceInfo::class, 'm_info_id', 'id');
    }

    public function mType()
    {
        return $this->belongsTo(MaintenanceType::class, 'm_type_id', 'id');
    }

    public function mStatus()
    {
        return $this->belongsTo(MaintenanceStatus::class, 'm_status_id', 'id');
    }
}
