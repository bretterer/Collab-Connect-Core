<?php

namespace App\Models;

use App\Enums\ReactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReaction extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'reaction_type',
    ];

    protected function casts(): array
    {
        return [
            'reaction_type' => ReactionType::class,
        ];
    }

    /**
     * Get the message this reaction belongs to.
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the user who added the reaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the emoji for this reaction.
     */
    public function getEmoji(): string
    {
        return $this->reaction_type->emoji();
    }
}
