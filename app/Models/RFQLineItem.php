<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RFQLineItem extends Model
{
    use HasFactory;

    protected $table = 'rfq_line_items';

    protected $appends = ['prices', 'number_of_rfq_line_items'];
    protected $fillable = [
        'rfq_id',
        'product_id',
        'quantity',
    ];

    // product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // rfq
    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    // responses
    public function responses()
    {
        return $this->hasMany(RFQResponse::class, 'rfq_line_item_id');
    }


    /**
     * Get the "prices" attribute.
     *
     * @return array
     */
    public function getPricesAttribute()
    {
        return [
            [
                'price' => null,
                'note' => 'صناعه سعوديه',
                // 'note' => 'صناعه ايطاليه',
            ]
        ];
    }

    // in rfq_supplier_line_items table the rfq_line_items coulumn is the foreign key for the rfq_line_items table and rfq_line_items is array  [75,76,77,78,79,80] how can get number of this rfq_line_items in rfq_supplier_line_items
    public function getNumberOfRfqLineItemsAttribute()
    {
        return RfqSupplierLineItem::where('rfq_line_items', 'like', '%' . $this->id . '%')->count();
    }
}
