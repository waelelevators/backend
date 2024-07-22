<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder  extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'assignment_id',
        'status',
        'start_at',
        'end_at'
    ];


    protected $with = ['status', 'stage', 'comments', 'managerApprovalStatus'];

    public function getContractIdAttribute()
    {
        return $this->locationStatus->assignment->contract_id;
    }

    /**
     * Get the product that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }


    // make realtion betwen this model and elevator_type
    /**
     * Get the elevator type that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function locationStatus(): BelongsTo
    {
        return $this->belongsTo(LocationStatus::class, 'assignment_id', 'id');
    }

    /**
     * Get the elevator type that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function technicians(): HasMany
    {
        return $this->hasMany(TechniciansWorkOrder::class);
        // ->where('stage_id', $this->stage_id);
    }

    /**
     * Get all of the comments for the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(WorkOrderComment::class);
    }

    // status
    /**
     * Get the status that owns the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id', 'value');
    }
    /**
     * Get the status that owns the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function managerApprovalStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'manager_approval', 'value');
    }

    /**
     * Get the user that owns the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get all of the products for the WorkOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(WorkOrdersProduct::class, 'foreign_key', 'local_key');
    }
}
