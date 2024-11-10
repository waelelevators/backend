<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'technician_info' => 'array',

    ];
    // protected $hidden = [
    //     'created_at',
    // ];
    protected $appends = [
        'email'
    ];
    public function getEmailAttribute()
    {
        return User::find($this->user_id)->email ?? '';
        //return User::where('id', $this->user_id)->first()->email ?? '';
        // return $this->user->email;
    }


    public function representatives()
    {
        return $this->morphMany(Representative::class, 'representativeable');
    }


    public function workOrders()
    {
        return $this->hasManyThrough(WorkOrder::class, TechniciansWorkOrder::class, 'employee_id', 'technician_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function techniciansWorkOrder()
    {
        return $this->hasMany(TechniciansWorkOrder::class, 'technician_id');
    }

    public function visits()
    {
        return $this->hasMany(MaintenanceVisit::class, 'technician_id');
    }
}
