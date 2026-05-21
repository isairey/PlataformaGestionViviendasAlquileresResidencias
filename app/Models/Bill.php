<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'tenant_id',
        'amount_due',
        'due_date',
        'status',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
