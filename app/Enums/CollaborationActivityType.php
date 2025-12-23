<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CollaborationActivityType: string
{
    use HasFormOptions;

    case STARTED = 'started';
    case DELIVERABLE_SUBMITTED = 'deliverable_submitted';
    case DELIVERABLE_APPROVED = 'deliverable_approved';
    case REVISION_REQUESTED = 'revision_requested';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::STARTED => 'Collaboration Started',
            self::DELIVERABLE_SUBMITTED => 'Deliverable Submitted',
            self::DELIVERABLE_APPROVED => 'Deliverable Approved',
            self::REVISION_REQUESTED => 'Revision Requested',
            self::COMPLETED => 'Collaboration Completed',
            self::CANCELLED => 'Collaboration Cancelled',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::STARTED => 'play',
            self::DELIVERABLE_SUBMITTED => 'paper-airplane',
            self::DELIVERABLE_APPROVED => 'check-circle',
            self::REVISION_REQUESTED => 'arrow-path',
            self::COMPLETED => 'trophy',
            self::CANCELLED => 'x-circle',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::STARTED => 'blue',
            self::DELIVERABLE_SUBMITTED => 'amber',
            self::DELIVERABLE_APPROVED => 'green',
            self::REVISION_REQUESTED => 'red',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }

    public function description(?array $metadata = null): string
    {
        return match ($this) {
            self::STARTED => 'The collaboration has begun',
            self::DELIVERABLE_SUBMITTED => $this->formatDeliverableMessage('submitted', $metadata),
            self::DELIVERABLE_APPROVED => $this->formatDeliverableMessage('approved', $metadata),
            self::REVISION_REQUESTED => $this->formatDeliverableMessage('needs revision', $metadata),
            self::COMPLETED => 'All deliverables have been approved',
            self::CANCELLED => 'The collaboration was cancelled',
        };
    }

    private function formatDeliverableMessage(string $action, ?array $metadata): string
    {
        if ($metadata && isset($metadata['deliverable_type'])) {
            $type = DeliverableType::tryFrom($metadata['deliverable_type']);
            if ($type) {
                return "{$type->label()} was {$action}";
            }
        }

        return "A deliverable was {$action}";
    }
}
