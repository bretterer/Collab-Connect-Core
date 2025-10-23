<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripePrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'metadata' => 'json',
        'recurring' => 'json',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(StripeProduct::class);
    }
}
