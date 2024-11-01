<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractComment extends Model
{

    use HasFactory;

    protected $table = 'contract_comments';

    protected $fillable = [
        'contract_id',
        'user_id',
        'stage_id',
        'comment',
        'attachment',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // stage_id
    function stage()
    {
        return $this->belongsTo(Stage::class);
    }
}
