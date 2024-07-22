<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureResponses extends Model
{
    use HasFactory;

    public function acceptedBy()
    {
        return $this->belongsTo(User::class,  'accepted_by');
    }
    public function endedBy()
    {
        return $this->belongsTo(User::class,  'ended_by');
    }
}
