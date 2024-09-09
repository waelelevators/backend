<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// طلبات البضاعة بالمرحلة وعدد الوقفات
class ContractProductQuantity extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price',
        'qty',
        'elevator_type_id',
        'floor',
        'stage'
    ];

    // // with product
    // protected $with = [
    //     'product'
    // ];

    /**
     * Get the product that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    // make realtion betwen this model and elevator_type
    /**
     * Get the elevator type that owns the ContractProductQuantity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function elevatorType(): BelongsTo
    {
        return $this->belongsTo(ElevatorType::class);
    }
}
