<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerUser extends Model
{
    protected $fillable = [
        'influencer_id',
        'user_id',
        'role',
    ];

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
