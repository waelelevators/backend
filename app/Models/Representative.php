<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    use HasFactory;

    // fillable
    protected $fillable = [
        'representativeable_type',
        'representativeable_id',
        'contract_type',
        'name'
    ];

    //   protected $with = ['representativeable'];

    protected $appends = [
        'names'
    ];

    public function getNamesAttribute()
    {

        $modelName = $this->representativeable_type;

        if ($modelName) {

            $Additions = $this->representativeable_id;
            $model = $modelName::where('id', $Additions)->first();

            return $model;
        } else return $this->name;
    }
}
