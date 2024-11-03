<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceVisit extends Model
{
    use HasFactory;

    // fillable
    protected $fillable = [
        'maintenance_contract_id',
        'maintenance_contract_detail_id',
        'visit_start_date',
        'visit_end_date',
        'status',
        'images',
        'technician_id',
        'user_id',
        'notes',
        'test_checklist',
        'client_approval',
        'visit_date'
    ];


    // relations
    public function maintenanceContract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'maintenance_contract_id', 'id');
    }

    public function maintenanceContractDetail()
    {
        return $this->belongsTo(MaintenanceContractDetail::class);
    }

    public function technician()
    {
        return $this->belongsTo(Employee::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // cast
    protected $casts = [
        'images' => 'array',
        'test_checklist' => 'array',
    ];


    // logs
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }
}
