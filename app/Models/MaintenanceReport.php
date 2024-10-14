<?php

namespace App\Models;

use Blueprint\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Fluent;

class MaintenanceReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'maintenance_contract_id',
        'status',
        'problems',
        'tax',
        'price_without_tax',
        'discount',
        'final_price',
        'notes',
        'technician_id',
    ];


    // problems hidden in the database
    protected $hidden = ['problems'];
    protected $casts = [
        'problems' => 'array',
    ];

    protected $appends = ['faults'];

    public function maintenanceContract()
    {
        return $this->belongsTo(MaintenanceContract::class);
    }



    public function technician()
    {
        return $this->belongsTo(Employee::class, 'technician_id');
    }

    // the problems is array of fault_id saved in database like that [1,2,3] I want to get the faults name and description
    public function getFaultsAttribute()
    {
        return Fault::whereIn('id', $this->problems ?? [])->get() ?? [];
    }


    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }

    public function requiredProducts()
    {
        return $this->morphMany(RequiredProduct::class, 'productable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}