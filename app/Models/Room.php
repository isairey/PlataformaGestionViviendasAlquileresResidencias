<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'code',
        'name',
        'rent_amount',
        'max_occupancy',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
