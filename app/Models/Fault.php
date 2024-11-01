<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fault extends Model
{
    use HasFactory;

    // حدد الأعمدة التي يمكن ملؤها
    protected $fillable = ['fault_category_id', 'name', 'description', 'status'];

    // العلاقة مع موديل FaultCategory
    public function category()
    {
        return $this->belongsTo(FaultCategory::class, 'fault_category_id');
    }

    // العلاقة العكسية مع موديل MaintenanceReport
    public function maintenanceReports()
    {
        return $this->belongsToMany(MaintenanceReport::class);
    }
}
