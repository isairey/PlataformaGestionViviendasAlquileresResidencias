<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'move_in_date',
        'move_out_date',
    ];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'datetime',
            'move_out_date' => 'datetime',
        ];
    }

    public function outstandingBalance()
    {
        return $this->bills()
            ->where('status', 'overdue')
            ->sum('amount_due');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * Get the maintenance requests for the tenant.
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }
}
