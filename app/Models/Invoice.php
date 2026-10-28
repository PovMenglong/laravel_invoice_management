<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'issued_at',
        'paid_at',
        'subtotal',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date'   => 'date',
            'issued_at'  => 'datetime',
            'paid_at'    => 'datetime',
            'subtotal'   => 'decimal:2',
            'total'      => 'decimal:2',
        ];
    }

    public const TRANSITIONS = [
        'draft'  => ['issued', 'void'],
        'issued' => ['paid', 'void'],
        'paid'   => [],
        'void'   => [],
    ];

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canMoveTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
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

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'issued')->whereDate('due_date', '<', today());
    }

    public function isOverdue(): bool
    {
        return $this->status === 'issued'
            && $this->due_date !== null
            && $this->due_date->lt(today());
    }
}
