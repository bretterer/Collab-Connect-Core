<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum PayoutStatus: string
{
    use HasFormOptions;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PROCESSING = 'processing';
    case PROCESSED = 'processed';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending Review',
            self::APPROVED => 'Approved',
            self::PROCESSING => 'Processing',
            self::PROCESSED => 'Processed',
            self::PAID => 'Paid',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'yellow',
            self::APPROVED => 'blue',
            self::PROCESSING => 'purple',
            self::PROCESSED => 'indigo',
            self::PAID => 'green',
            self::FAILED => 'red',
            self::CANCELLED => 'zinc',
        };
    }
}
