<?php

namespace Modules\Maintenance\Entities;

use App\Models\MaintenanceVisit;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContractDetail extends Model
{
    protected $fillable = [
        'installation_contract_id',
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
        'contract_attachment'
    ];

    public function contract()
    {
        return $this->belongsTo(MaintenanceContract::class, 'installation_contract_id');
    }

    public function visits()
    {
        return $this->hasMany(MaintenanceVisit::class);
    }
}