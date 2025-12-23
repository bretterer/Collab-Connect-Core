<?php

namespace App\Models;

use App\Enums\CollaborationDeliverableStatus;
use App\Enums\DeliverableType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollaborationDeliverable extends Model
{
    /** @use HasFactory<\Database\Factories\CollaborationDeliverableFactory> */
    use HasFactory;

    protected $fillable = [
        'collaboration_id',
        'deliverable_type',
        'status',
        'submitted_at',
        'approved_at',
        'post_url',
        'notes',
        'revision_feedback',
    ];

    protected function casts(): array
    {
        return [
            'deliverable_type' => DeliverableType::class,
            'status' => CollaborationDeliverableStatus::class,
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function collaboration(): BelongsTo
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CollaborationDeliverableFile::class);
    }

    public function isNotStarted(): bool
    {
        return $this->status === CollaborationDeliverableStatus::NOT_STARTED;
    }

    public function isInProgress(): bool
    {
        return $this->status === CollaborationDeliverableStatus::IN_PROGRESS;
    }

    public function isSubmitted(): bool
    {
        return $this->status === CollaborationDeliverableStatus::SUBMITTED;
    }

    public function isRevisionRequested(): bool
    {
        return $this->status === CollaborationDeliverableStatus::REVISION_REQUESTED;
    }

    public function isApproved(): bool
    {
        return $this->status === CollaborationDeliverableStatus::APPROVED;
    }

    public function canSubmit(): bool
    {
        return $this->status->canSubmit();
    }

    public function canApprove(): bool
    {
        return $this->status->canApprove();
    }

    public function canRequestRevision(): bool
    {
        return $this->status->canRequestRevision();
    }

    public function scopeNotStarted($query)
    {
        return $query->where('status', CollaborationDeliverableStatus::NOT_STARTED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', CollaborationDeliverableStatus::IN_PROGRESS);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', CollaborationDeliverableStatus::SUBMITTED);
    }

    public function scopeRevisionRequested($query)
    {
        return $query->where('status', CollaborationDeliverableStatus::REVISION_REQUESTED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', CollaborationDeliverableStatus::APPROVED);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            CollaborationDeliverableStatus::NOT_STARTED,
            CollaborationDeliverableStatus::IN_PROGRESS,
            CollaborationDeliverableStatus::SUBMITTED,
            CollaborationDeliverableStatus::REVISION_REQUESTED,
        ]);
    }
}
