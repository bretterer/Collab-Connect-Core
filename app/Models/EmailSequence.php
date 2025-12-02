<?php

namespace App\Models;

use App\Enums\SequenceMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailSequence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sequence_mode',
        'anchor_datetime',
        'anchor_timezone',
        'send_welcome_email',
        'welcome_email_subject',
        'welcome_email_body',
        'welcome_email_preview_text',
        'subscribe_triggers',
        'unsubscribe_triggers',
        'funnel_id',
        'total_subscribers',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'subscribe_triggers' => 'array',
            'unsubscribe_triggers' => 'array',
            'sequence_mode' => SequenceMode::class,
            'anchor_datetime' => 'datetime',
            'send_welcome_email' => 'boolean',
        ];
    }

    public function isBeforeAnchorMode(): bool
    {
        return $this->sequence_mode === SequenceMode::BEFORE_ANCHOR_DATE;
    }

    public function isAfterSubscriptionMode(): bool
    {
        return $this->sequence_mode === SequenceMode::AFTER_SUBSCRIPTION;
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(EmailSequenceEmail::class)->orderBy('order');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(EmailSequenceSubscriber::class);
    }

    public function activeSubscribers(): HasMany
    {
        return $this->hasMany(EmailSequenceSubscriber::class)->where('status', 'active');
    }

    public function updateSubscriberCount(): void
    {
        $this->update([
            'total_subscribers' => $this->subscribers()->where('status', 'active')->count(),
        ]);
    }
}
