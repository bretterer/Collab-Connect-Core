<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case PENDING = 'pending';
    case OPEN = 'open';
    case CLOSED = 'closed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::OPEN => 'Open for Review',
            self::CLOSED => 'Closed',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::OPEN => 'blue',
            self::CLOSED => 'green',
            self::EXPIRED => 'amber',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Review period has not started yet',
            self::OPEN => 'Review period is open - both parties can submit reviews',
            self::CLOSED => 'Both reviews submitted - reviews are now visible',
            self::EXPIRED => 'Review period expired - no more reviews can be submitted',
        };
    }

    public function canSubmitReview(): bool
    {
        return $this === self::OPEN;
    }

    public function areReviewsVisible(): bool
    {
        return in_array($this, [self::CLOSED, self::EXPIRED]);
    }
}
