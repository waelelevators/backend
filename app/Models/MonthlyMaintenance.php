<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyMaintenance extends Model
{
    use HasFactory;

    // protected $with = ['visitStatus', 'maintenance'];

    protected $casts = [
        'visit_data' => 'array',
        'visit_images' => 'array'
    ];

    //protected $with = ['tech','visitStatus'];
    public function tech()
    {
        return $this->belongsTo(User::class, 'tech_id', 'id');
    }

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'm_id', 'id');
    }

    public function visitStatus()
    {
        return $this->belongsTo(VisitStatus::class, 'visit_status_id', 'id');
    }
}
