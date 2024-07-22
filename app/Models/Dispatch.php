<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Dispatch Model
class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'work_order_id',
        'stage_id',
        'user_id',
        'employee_id'
    ];



    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }


    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
