<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationAssignment extends Model
{
    use HasFactory;


    // [ financial Status
    //     1=>'لم يتم الدفع',
    //     2=>'تم دفع جزء',
    //     3=>'تم الدفع',
    // ]

    // [ status
    //     1=>'غير مسند للمندوب',
    //     2=>'مسند',
    //     3=>'تم الزيارة',
    // ]


    protected $with = ['contract', 'stage', 'representative'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }
    public function representative()
    {
        return $this->belongsTo(Employee::class, 'representative_by', 'id');
    }

    public function logs()
    {
        return $this->hasMany(LocationAssignmentsLog::class, 'location_assignment_id');
    }

    public function toShow()
    {
        // Customize this method to return the specific columns you need
        return [
            'id' => $this->id,
            'location_id' => $this->location_id,
            'user_id' => $this->user_id,
            'assigned_at' => $this->assigned_at,
            'logs' => $this->logs->map(function ($log) {
                return [
                    'id' => $log->id,
    
                    'logged_at' => $log->logged_at,
                    // Add other log fields as needed
                ];
            }),
        ];
    }
}
