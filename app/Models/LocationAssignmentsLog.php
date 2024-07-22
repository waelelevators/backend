<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationAssignmentsLog extends Model
{
    use HasFactory;
    protected $with = ['user', 'representative'];

    /**
     * Get the location assignment that owns the log.
     */
    public function locationAssignment()
    {
        return $this->belongsTo(LocationAssignment::class, 'location_assignment_id');
    }

    /**
     * Get the user that owns the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user that owns the log.
     */
    public function representative()
    {
        return $this->belongsTo(User::class, 'representative_by');
    }
}
