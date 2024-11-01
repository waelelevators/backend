<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContractDetail extends Model
{
    use HasFactory;

    // fillable
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

    // relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // visits
    public function visits()
    {
        return $this->hasMany(MaintenanceVisit::class);
    }

    // logs
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }
}
