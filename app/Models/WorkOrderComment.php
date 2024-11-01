<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderComment  extends Model
{
    use HasFactory;

    protected $with = ['user'];


    protected $fillable = [
        'work_order_id',
        'comment',
        'user_id',
        'attachment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
