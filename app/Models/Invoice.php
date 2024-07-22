<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    // append
    protected $appends = ['calculate_total', 'calculate_total_paid'];

    // fillable
    protected $fillable = ['rfq_id', 'total', 'supplier_id',];

    /**
     * Get all of the invoice_deails for the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice_details(): HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    // has many products through invoice_details
    public function products()
    {
        return $this->hasManyThrough(Product::class, InvoiceDetail::class);
    }

    /**
     * Get the invoice that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(RFQ::class, 'rfq_id');
    }

    // calculate total from invoice_details qty * price for each product
    public function getCalculateTotalAttribute(): float
    {
        return $this->invoice_details->sum(function ($invoiceDetail) {
            return $invoiceDetail->qty * $invoiceDetail->price;
        });
    }

    // المبلغ المدفوع هو مجوع المدفوعات للفاتورة من supplier_payments
    public function getcalculateTotalPaidAttribute(): float
    {

        return $this->suppliersPayments->sum('amount');
    }


    /**
     * Get all of the supplierPayments for the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliersPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
