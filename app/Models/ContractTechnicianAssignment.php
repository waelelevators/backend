<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechniciansWorkOrder extends Model
{
    use HasFactory;

    protected $table = 'technicians_work_orders';

    protected $fillable = [
        'contract_id',
        'technician_id',
        'work_order_id',
    ];

    public function work_order()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
