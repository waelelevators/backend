<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallationClientLocation extends Model
{
    use HasFactory;

    // Define the fields that are mass assignable
    protected $fillable = [
        'client_id',
        'region_id',
        'city_id',
        'neighborhood_id',
        'elevator_trip_id',
        'height',
        'width',
        'length',
        'visit_reason_id',  // Add visit_reason_id here
        'building_type',
        'lat',
        'long',
        'location_image',
        'type',
        'description',
        'assigned_to',
        'created_by',
    ];
}
