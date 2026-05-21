<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'tenant_id',
        'bill_id',
        'amount',
        'status',
        'payment_method',
        'amount',
        'proof_photo',
        'gcash_number',
        'maya_number',
        'reference_number',
        'payment_date',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
