<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitReason extends Model
{
    use HasFactory;

    public function installationClientLocations()
    {
        return $this->hasMany(InstallationClientLocation::class);
    }
}
