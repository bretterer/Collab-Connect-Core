<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CollaborationDeliverableStatus: string
{
    use HasFormOptions;

    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case SUBMITTED = 'submitted';
    case REVISION_REQUESTED = 'revision_requested';
    case APPROVED = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'Not Started',
            self::IN_PROGRESS => 'In Progress',
            self::SUBMITTED => 'Submitted',
            self::REVISION_REQUESTED => 'Revision Requested',
            self::APPROVED => 'Approved',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'zinc',
            self::IN_PROGRESS => 'amber',
            self::SUBMITTED => 'blue',
            self::REVISION_REQUESTED => 'red',
            self::APPROVED => 'green',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'Work has not been started on this deliverable',
            self::IN_PROGRESS => 'Influencer is actively working on this deliverable',
            self::SUBMITTED => 'Deliverable has been submitted and is awaiting review',
            self::REVISION_REQUESTED => 'Business has requested revisions to this deliverable',
            self::APPROVED => 'Business has approved this deliverable',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NOT_STARTED => 'minus-circle',
            self::IN_PROGRESS => 'clock',
            self::SUBMITTED => 'paper-airplane',
            self::REVISION_REQUESTED => 'arrow-path',
            self::APPROVED => 'check-circle',
        };
    }

    public function canSubmit(): bool
    {
        return in_array($this, [self::NOT_STARTED, self::IN_PROGRESS, self::REVISION_REQUESTED]);
    }

    public function canApprove(): bool
    {
        return $this === self::SUBMITTED;
    }

    public function canRequestRevision(): bool
    {
        return $this === self::SUBMITTED;
    }
}
