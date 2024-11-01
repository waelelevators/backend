<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractInstallationStage  extends Model
{
    use HasFactory;

    protected $fillable = [
        'stage_id',
        'contract_id',
        'status',
        'start_at',
        'end_at'
    ];

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
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
