<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // table products
    protected $table = 'products';

    // appended
    protected $appends = ['in_stock', 'not_received'];


    // المنتج لديه اكثر row فى الفواتير
    /**
     * Get all of the invoiceDetail for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceDetail(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }


    public function dispatchItems(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    // عدد المنتجات فى الفواتير stock_qty
    public function getInStockAttribute(): int
    {
        return $this->invoiceDetail()->sum('stock_qty') - $this->dispatchItems()->sum('qty');
    }

    public function getNotReceivedAttribute(): int
    {
        $totalQty = $this->invoiceDetail()->sum('qty');
        $totalStockQty = $this->invoiceDetail()->sum('stock_qty');
        return $totalQty - $totalStockQty;
    }

    /**
     * Get the stage that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thsStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage');
    }

    // dispatch_items


    /**
     * Get the elevatorType that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function elevatorType(): BelongsTo
    {
        return $this->belongsTo(ElevatorType::class, 'elevator_types_id');
    }
}
