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
            'is_primary' => 'boolean',
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SocialMediaAccountFactory
    {
        return SocialMediaAccountFactory::new();
    }
}
