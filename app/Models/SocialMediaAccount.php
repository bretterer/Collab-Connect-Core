<?php

namespace App\Models;

use Database\Factories\SocialMediaAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaAccount extends Model
{
    /** @use HasFactory<SocialMediaAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'username',
        'url',
        'follower_count',
        'is_primary',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'platform' => \App\Enums\SocialPlatform::class,
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the normalized URL for the social media account.
     * Extracts the username from any URL format and generates the proper platform URL.
     */
    public function getNormalizedUrlAttribute(): ?string
    {
        if (! $this->username || ! $this->platform) {
            return $this->url;
        }

        // Use the platform's generateUrl method with the username
        return $this->platform->generateUrl($this->username);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SocialMediaAccountFactory
    {
        return SocialMediaAccountFactory::new();
    }
}
