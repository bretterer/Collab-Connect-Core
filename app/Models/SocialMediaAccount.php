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
     * Removes duplicate domain portions from URLs like:
     * https://instagram.com/https://www.instagram.com/username/
     */
    public function getNormalizedUrlAttribute(): ?string
    {
        if (! $this->url) {
            return null;
        }

        // Pattern to match duplicated URLs (e.g., https://domain.com/https://www.domain.com/path)
        $pattern = '/^(https?:\/\/[^\/]+\/)https?:\/\/[^\/]+\/(.*)$/i';

        if (preg_match($pattern, $this->url, $matches)) {
            // Return the cleaned URL: first domain + remaining path
            return $matches[1].$matches[2];
        }

        return $this->url;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SocialMediaAccountFactory
    {
        return SocialMediaAccountFactory::new();
    }
}
