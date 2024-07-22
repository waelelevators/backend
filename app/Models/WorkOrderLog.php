<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderLog  extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'work_order_id',
        'status',
        'user_id',
    ];


    protected $with = ['status', 'user', 'getStatus'];

    /**
     * Get the product that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }


    // make realtion betwen this model and elevator_type
    /**
     * Get the elevator type that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }


    // status
    /**
     * Get the status that owns the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status', 'value');
    }


    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status', 'value');
    }

    // user
    /**
     * Get the user that owns the WorkOrderLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
