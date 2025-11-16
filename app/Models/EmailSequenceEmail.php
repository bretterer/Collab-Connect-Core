<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailSequenceEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_sequence_id',
        'name',
        'subject',
        'body',
        'delay_days',
        'send_time',
        'timezone',
        'order',
        'sent_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
    ];

    protected function casts(): array
    {
        return [
            'send_time' => 'datetime:H:i:s',
        ];
    }

    public function emailSequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(EmailSequenceSend::class);
    }

    public function incrementSentCount(): void
    {
        $this->increment('sent_count');
    }

    public function incrementOpenedCount(): void
    {
        $this->increment('opened_count');
    }

    public function incrementClickedCount(): void
    {
        $this->increment('clicked_count');
    }

    public function incrementUnsubscribedCount(): void
    {
        $this->increment('unsubscribed_count');
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function getUnsubscribeRateAttribute(): float
    {
        if ($this->sent_count === 0) {
            return 0;
        }

        return round(($this->unsubscribed_count / $this->sent_count) * 100, 2);
    }
}
