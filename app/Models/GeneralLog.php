<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralLog extends Model
{
    use HasFactory;


    protected $fillable = ['loggable_type', 'loggable_id', 'user_id', 'action', 'comment', 'metadata'];

    protected $appends = ['created_at'];

    public function loggable()
    {
        return $this->morphTo();
    }

    protected $with = ['user'];

    // convert created_at to arabic date
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMetadataAttribute($value)
    {
        return json_decode($value, true);
    }

    // when add log add user login name
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($log) {
            $log->user_id = auth('sanctum')->user()->id;
        });
    }
}
