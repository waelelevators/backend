<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RFQResponse extends Model
{
    use HasFactory;


    protected $table = 'rfq_responses';


    protected $fillable = [
        'rfq_id',
        'rfq_line_item_id',
        'supplier_id',
        'price',
        'product_id',
        'note',
    ];
    protected $with = ['supplier'];

    // rfq
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    // rfq line item
    public function rfqLineItem()
    {
        return $this->belongsTo(RFQLineItem::class);
    }



    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    protected static function booted()
    {

        static::retrieved(function ($rfqResponse) {

            $previousPrices = static::getPreviousPrices($rfqResponse);
            $rfqResponse->setAttribute('previous_prices', $previousPrices);
        });
    }

    private static function getPreviousPrices($rfqResponse)
    {
        // ابحث عن الأسعار السابقة والتي تكون لنفس المورد ونفس المنتج
        return static::where('supplier_id', $rfqResponse->supplier_id)
            ->where('product_id', $rfqResponse->product_id)
            // ->where('id', '<>', $rfqResponse->id) // استبعاد الاستجابة الحالية
            ->where('id', '<>', $rfqResponse->id) // استبعاد الاستجابة الحالية
            ->where('rfq_id', '<>', $rfqResponse->rfq_id)
            ->pluck('price')
            ->toArray();
    }
}
