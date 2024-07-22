<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [];

    // payment

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }
    // protected $hidden = [
    //     'created_at',
    //     'updated_at',

    // ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function getAddressAttribute()
    // {
    //     return $this->User->address;
    // }

  //  protected $appends = ['address'];
}
