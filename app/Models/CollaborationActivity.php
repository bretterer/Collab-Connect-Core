<?php

namespace App\Models;

use App\Enums\CollaborationActivityType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollaborationActivity extends Model
{
    /** @use HasFactory<\Database\Factories\CollaborationActivityFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'collaboration_id',
        'user_id',
        'type',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => CollaborationActivityType::class,
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function collaboration(): BelongsTo
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute(): string
    {
        return $this->type->description($this->metadata);
    }

    public function getIconAttribute(): string
    {
        return $this->type->icon();
    }

    public function getColorAttribute(): string
    {
        return $this->type->color();
    }

    public function isSystemActivity(): bool
    {
        return $this->user_id === null;
    }
}
