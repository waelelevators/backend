<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Client extends Model
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'data',
    ];

    protected $appends = ['full_name'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $with = ['employee'];


    protected $casts = [
        'id' => 'integer',
        'data' => 'array',
    ];

    protected $with = ['contract', 'ContractQuotations'];

    /**
     * Get the employee that owns the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return Employee::find(1);
        // return Employee::find($this->data['employee_id']);
    }

    public function representatives()
    {
        return $this->morphMany(Representative::class, 'representativeable');
    }

    // contract
    /**
     * Get the contract that owns the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function ContractQuotations(): BelongsTo
    {
        return $this->belongsTo(InstallationQuotation::class, 'client_id');
    }

    public function contractAssen()
    {
        return $this->hasManyThrough(Contract::class, Representative::class, 'representativeable_id', 'id', 'id', 'contract_id')
            ->where('representativeable_type', 'App\Models\Client');
    }

    public function getFullNameAttribute()
    {

        return "{$this->first_name} {$this->second_name} {$this->third_name} {$this->last_name}";
    }
}
