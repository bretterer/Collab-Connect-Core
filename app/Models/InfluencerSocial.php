<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerSocial extends Model
{
    protected $fillable = [
        'influencer_id',
        'platform',
        'username',
        'url',
        'followers',
    ];

    protected function casts(): array
    {
        return [
            'platform' => SocialPlatform::class,
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    public function getPlatformLabelAttribute(): string
    {
        return $this->platform->label();
    }

    public function getPlatformIconAttribute(): string
    {
        return $this->platform->getIcon();
    }
}
