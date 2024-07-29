<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $with = ['createdBy'];

    // has many contract
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    protected $hidden = [
        'created_at',
        'updated_at',

    ];

    // cast attachments to array
    protected $casts = [
        'attachments' => 'array',
        'amount' => 'float'
    ];
    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }
}
