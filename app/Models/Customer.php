<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Customer.php
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
