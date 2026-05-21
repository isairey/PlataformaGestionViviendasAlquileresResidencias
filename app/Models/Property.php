<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'type',
        'name',
        'description',
        'address',
    ];

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
