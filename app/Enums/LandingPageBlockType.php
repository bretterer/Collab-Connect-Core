<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum LandingPageBlockType: string
{
    use HasFormOptions;

    case HEADER = 'header';
    case HERO = 'hero';
    case TEXT = 'text';
    case IMAGE = 'image';
    case YOUTUBE = 'youtube';
    case FEATURES = 'features';
    case CTA = 'cta';
    case TWO_STEP_OPTIN = 'two_step_optin';
    case TESTIMONIALS = 'testimonials';
    case FAQ = 'faq';
    case FOOTER = 'footer';
    case EXIT_POPUP = 'exit_popup';
    case CUSTOM_HTML = 'custom_html';
    case FORM = 'form';
    case STRIPE_CHECKOUT = 'stripe_checkout';
    case THANK_YOU = 'thank_you';

    public function label(): string
    {
        return match ($this) {
            self::HEADER => 'Header',
            self::HERO => 'Hero Section',
            self::TEXT => 'Text Block',
            self::IMAGE => 'Image',
            self::YOUTUBE => 'YouTube Video',
            self::FEATURES => 'Features Section',
            self::CTA => 'Call to Action',
            self::TWO_STEP_OPTIN => 'Two-Step Opt-in',
            self::TESTIMONIALS => 'Testimonials',
            self::FAQ => 'FAQ Section',
            self::FOOTER => 'Footer',
            self::EXIT_POPUP => 'Exit Intent Popup',
            self::CUSTOM_HTML => 'Custom HTML',
            self::FORM => 'Form',
            self::STRIPE_CHECKOUT => 'Stripe Checkout',
            self::THANK_YOU => 'Thank You Page',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::HEADER => 'bars-3',
            self::HERO => 'photo',
            self::TEXT => 'document-text',
            self::IMAGE => 'photo',
            self::YOUTUBE => 'play-circle',
            self::FEATURES => 'squares-2x2',
            self::CTA => 'megaphone',
            self::TWO_STEP_OPTIN => 'cursor-arrow-rays',
            self::TESTIMONIALS => 'chat-bubble-left-right',
            self::FAQ => 'question-mark-circle',
            self::FOOTER => 'bars-3-bottom-left',
            self::EXIT_POPUP => 'arrow-up-on-square',
            self::CUSTOM_HTML => 'code-bracket',
            self::FORM => 'clipboard-document-list',
            self::STRIPE_CHECKOUT => 'credit-card',
            self::THANK_YOU => 'check-circle',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HEADER => 'Navigation header with logo and menu',
            self::HERO => 'Main hero section with headline and image',
            self::TEXT => 'Rich text content with formatting',
            self::IMAGE => 'Single image with caption and alt text',
            self::YOUTUBE => 'Embedded YouTube video player',
            self::FEATURES => 'Feature highlights with icons',
            self::CTA => 'Call-to-action button with text',
            self::TWO_STEP_OPTIN => 'Two-step email capture form',
            self::TESTIMONIALS => 'Customer testimonials and reviews',
            self::FAQ => 'Frequently asked questions',
            self::FOOTER => 'Footer with links and information',
            self::EXIT_POPUP => 'Exit intent popup modal',
            self::CUSTOM_HTML => 'Custom HTML/CSS/JavaScript code',
            self::FORM => 'Display a published form',
            self::STRIPE_CHECKOUT => 'Collect data and redirect to Stripe checkout',
            self::THANK_YOU => 'Success page after purchase completion',
        };
    }
}
