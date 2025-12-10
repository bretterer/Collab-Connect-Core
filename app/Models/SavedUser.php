<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedUser extends Model
{
    protected $fillable = [
        'user_id',
        'saved_user_id',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function savedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saved_user_id');
    }

    public function isSaved(): bool
    {
        return $this->type === 'saved';
    }

    public function isHidden(): bool
    {
        return $this->type === 'hidden';
    }
}
