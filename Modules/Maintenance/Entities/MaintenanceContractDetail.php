<?php

namespace Modules\Maintenance\Entities;

use App\Models\MaintenanceVisit;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContractDetail extends Model
{
    protected $fillable = [
        'installation_contract_id',
        'maintenance_contract_id',
        'maintenance_type',
        'client_id',
        'user_id',
        'start_date',
        'end_date',
        'visits_count',
        'cost',
        'notes',
        'remaining_visits',
        'cancellation_allowance',
        'payment_status',
        'receipt_attachment',
        'contract_attachment',
        'status'
    ];

    public function contract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'installation_contract_id');
    }

    public function visits()
    {
        return $this->hasMany(MaintenanceVisit::class);
    }


    // getExpiredContracts

    public function getExpiredContracts()
    {
        // if end date is less than now and remaining visits is less than 1
        return $this->where('status',  'expired')->get();
    }
}