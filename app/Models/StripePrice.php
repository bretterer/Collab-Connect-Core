<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripePrice extends Model
{
    use SoftDeletes;

    protected $casts = [
        'metadata' => 'json',
        'recurring' => 'json',
    ];

    public function stripeProduct(): BelongsTo
    {
        return $this->belongsTo(StripeProduct::class);
    }
}
