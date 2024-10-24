<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceContract extends Model
{
    use HasFactory;
    protected $table = 'maintenance_contracts';
    protected $fillable = [
        'contract_number',
        'area',
        'city_id',
        'neighborhood_id',
        'latitude',
        'longitude',
    ];
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }


    public function elevatorType()
    {
        return $this->belongsTo(ElevatorType::class);
    }

    // contract details
    public function contractDetails()
    {
        return $this->hasMany(MaintenanceContractDetail::class, 'maintenance_contract_id');
    }


    // active_contract_id
    public function activeContract()
    {
        return $this->belongsTo(MaintenanceContractDetail::class, 'active_contract_id');
    }

    // logs
    public function logs()
    {
        return $this->morphMany(GeneralLog::class, 'loggable');
    }

    // client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
