<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum BusinessType: string
{
    use HasFormOptions;

    case ECOMMERCE = 'ecommerce';
    case BRICK_MORTAR = 'brick-mortar';
    case DIGITAL_SERVICES = 'digital-services';
    case SAAS = 'saas';
    case MARKETPLACE = 'marketplace';
    case AGENCY = 'agency';
    case RESTAURANT = 'restaurant';
    case PROFESSIONAL_SERVICES = 'professional-services';
    case NONPROFIT = 'nonprofit';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ECOMMERCE => 'E-commerce Store',
            self::BRICK_MORTAR => 'Brick & Mortar Store',
            self::DIGITAL_SERVICES => 'Digital Services',
            self::SAAS => 'SaaS/Software',
            self::MARKETPLACE => 'Marketplace',
            self::AGENCY => 'Marketing Agency',
            self::RESTAURANT => 'Restaurant/Food Service',
            self::PROFESSIONAL_SERVICES => 'Professional Services',
            self::NONPROFIT => 'Non-profit',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ECOMMERCE => 'An online store that sells products or services.',
            self::BRICK_MORTAR => 'A physical retail store located in a building.',
            self::DIGITAL_SERVICES => 'Businesses that provide services online.',
            self::SAAS => 'Software as a Service, delivered online.',
            self::MARKETPLACE => 'A platform for multiple vendors to sell products.',
            self::AGENCY => 'A business that provides specialized services.',
            self::RESTAURANT => 'A place where meals are prepared and served.',
            self::PROFESSIONAL_SERVICES => 'Services requiring specialized knowledge.',
            self::NONPROFIT => 'An organization that operates for a charitable purpose.',
            self::OTHER => 'Any other type of business not listed.',
        };
    }
}
