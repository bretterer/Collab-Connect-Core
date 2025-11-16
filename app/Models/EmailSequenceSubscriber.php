<?php

namespace App\Models;

use App\Enums\SubscriberStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequenceSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_sequence_id',
        'email',
        'first_name',
        'last_name',
        'metadata',
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_reason',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
            'status' => SubscriberStatus::class,
        ];
    }

    public function emailSequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailSequenceSend::class, 'subscriber_id');
    }

    public function unsubscribe(?string $reason = null): void
    {
        $this->update([
            'status' => SubscriberStatus::UNSUBSCRIBED,
            'unsubscribed_at' => now(),
            'unsubscribe_reason' => $reason,
        ]);

        $this->emailSequence->updateSubscriberCount();
    }

    public function isActive(): bool
    {
        return $this->status === SubscriberStatus::ACTIVE;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
