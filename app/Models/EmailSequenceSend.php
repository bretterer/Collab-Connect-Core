<?php

namespace App\Models;

use App\Enums\EmailSendStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSequenceSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_sequence_email_id',
        'subscriber_id',
        'scheduled_at',
        'sent_at',
        'opened_at',
        'clicked_at',
        'status',
        'error_message',
        'is_welcome_email',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
            'status' => EmailSendStatus::class,
            'is_welcome_email' => 'boolean',
        ];
    }

    public function email(): BelongsTo
    {
        return $this->belongsTo(EmailSequenceEmail::class, 'email_sequence_email_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(EmailSequenceSubscriber::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => EmailSendStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => EmailSendStatus::FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsOpened(): void
    {
        if ($this->opened_at === null) {
            $this->update(['opened_at' => now()]);
            $this->email->incrementOpenedCount();
        }
    }

    public function markAsClicked(): void
    {
        if ($this->clicked_at === null) {
            $this->update(['clicked_at' => now()]);
            $this->email->incrementClickedCount();
        }

        // Also mark as opened if not already
        $this->markAsOpened();
    }

    public function isPending(): bool
    {
        return $this->status === EmailSendStatus::PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === EmailSendStatus::SENT;
    }
}
