<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HandOverItem extends Model
{
    use HasFactory;
    protected $casts = [
        'id' => 'integer',
        'item_data' => 'array'
    ];

    protected $appends = [
        'doorSpecifications'
    ];

    // Adding an accessor for the decoded item data
    public function getDoorSpecificationsAttribute()
    {
        return $this->item_data ? json_decode($this->item_data, true) : null;
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    protected $hidden = [
        'item_data'
    ];
}
