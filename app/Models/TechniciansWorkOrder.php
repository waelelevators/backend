<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechniciansWorkOrder  extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'technician_id', 'stage_id', 'work_order_id'];

    /**
     * Get the work_order that owns the TechniciansWorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    /**
     * Get the User that owns the TechniciansWorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
