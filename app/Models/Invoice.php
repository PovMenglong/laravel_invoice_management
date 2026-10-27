<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Invoice.php
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'number',
        'customer_id',
        'user_id',
        'issue_date',
        'due_date',
        'status',
        'subtotal',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date'   => 'date',
            'subtotal'   => 'decimal:2',
            'total'      => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
