<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum EmailTriggerType: string
{
    use HasFormOptions;

    case FORM_SUBMITTED = 'form_submitted';
    case OFFER_PURCHASED = 'offer_purchased';
    case LANDING_PAGE_VISITED = 'landing_page_visited';

    public function label(): string
    {
        return match ($this) {
            self::FORM_SUBMITTED => 'Form is submitted',
            self::OFFER_PURCHASED => 'Offer is purchased',
            self::LANDING_PAGE_VISITED => 'Landing page is visited',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FORM_SUBMITTED => 'Triggered when a user submits a specific form',
            self::OFFER_PURCHASED => 'Triggered when a user purchases a specific offer/product',
            self::LANDING_PAGE_VISITED => 'Triggered when a user visits a specific landing page',
        };
    }
}
