<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CompensationType: string
{
    use HasFormOptions;

    case MONETARY = 'monetary';
    case BARTER = 'barter';
    case FREE_PRODUCT = 'free_product';
    case DISCOUNT = 'discount';
    case GIFT_CARD = 'gift_card';
    case EXPERIENCE = 'experience';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::MONETARY => 'Monetary Payment',
            self::BARTER => 'Barter/Trade',
            self::FREE_PRODUCT => 'Free Product',
            self::DISCOUNT => 'Discount/Store Credit',
            self::GIFT_CARD => 'Gift Card',
            self::EXPERIENCE => 'Experience/Event',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MONETARY => 'Direct cash payment',
            self::BARTER => 'Exchange of goods or services',
            self::FREE_PRODUCT => 'Free product or service',
            self::DISCOUNT => 'Percentage off or store credit',
            self::GIFT_CARD => 'Gift card to your business',
            self::EXPERIENCE => 'Event tickets, experiences, etc.',
            self::OTHER => 'Custom compensation arrangement',
        };
    }
}
