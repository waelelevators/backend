<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LocationStatus extends Model
{
    use HasFactory;

    protected $with = ['assignment'];
    protected $appends = ['has_work_order'];

    protected $casts = [
        'id' => 'integer',
        'location_data' => 'array',
        'user_id' => 'integer',
    ];


    public function assignment()
    {
        return $this->belongsTo(LocationAssignment::class, 'l_assignment_id', 'id');
    }

    /**
     * Get all of the workOrders for the Contract
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class, 'assignment_id');
    }
    public function getHasWorkOrderAttribute()
    {
        if ($this->workOrders()->where('assignment_id', $this->id)->exists()) {
            return true;
        } else {
            return false;
        }
    }
}
