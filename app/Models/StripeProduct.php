<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'metadata' => 'json',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(StripePrice::class);
    }
}
