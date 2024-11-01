<?php

namespace App\Models;

use backend\models\NeighboorHoods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'region_id',
    ];


    // protected $with = ['neighborhood'];

    protected $hidden = [
        'created_at',
        'updated_at',

    ];
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function neighborhoods(): HasMany
    {
        return $this->hasMany(Neighborhood::class);
    }


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'region_id' => 'integer',
    ];
}
