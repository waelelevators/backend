<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaultCategory extends Model
{
    use HasFactory;

    // حدد الأعمدة التي يمكن ملؤها
    protected $fillable = ['name', 'description'];

    // العلاقة مع موديل Fault
    public function faults()
    {
        return $this->hasMany(Fault::class);
    }
}