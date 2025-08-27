<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSocial extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
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
