# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CollabConnect is a Laravel-based platform connecting local businesses with influencers for collaborative marketing campaigns. It's built using the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) with Laravel 12.

**Key Business Logic:**
- Businesses create campaigns with detailed requirements and compensation
- Influencers discover campaigns through smart matching algorithms based on location, niche, and preferences
- Match scoring uses weighted factors: Location (40%), Niche (30%), Campaign Type (20%), Compensation (10%)
- Two-sided marketplace with subscription-based revenue model (no commission structure)

## Development Commands

### Essential Commands
```bash
# Development server (via Laravel Herd - always running at https://collabconnect.test)

# Database operations
php artisan migrate
php artisan db:seed
php artisan db:seed --class=CampaignSeeder
php artisan db:seed --class=BiggbysCampaignTemplateSeeder

# Testing
php artisan test
php artisan test tests/Feature/Livewire/InfluencerCampaignsTest.php
php artisan config:clear --ansi && php artisan test

# Development tools
composer dev

# Stripe sync
php artisan collabconnect:sync-stripe  # Sync all products and prices
php artisan collabconnect:sync-stripe --products-only  # Only sync products
php artisan collabconnect:sync-stripe --prices-only  # Only sync prices
php artisan collabconnect:sync-stripe --limit=50  # Limit items synced
php artisan collabconnect:sync-stripe --active-only  # Only sync active items
php artisan collabconnect:sync-stripe --force  # Skip confirmation prompt

# Frontend
npm run dev
npm run build
```

## Architecture Overview

### Core Models & Relationships
- **User**: Central model with account_type enum (BUSINESS, INFLUENCER, ADMIN)
  - hasOne BusinessProfile, InfluencerProfile
  - hasMany Campaigns, CampaignApplications, Notifications, SocialMediaAccounts
- **Campaign**: Complex model with extensive fields for campaign management
  - belongsTo User, hasMany CampaignApplications
  - Extensive enum-based fields (status, compensation_type, campaign_type)
- **PostalCode**: Geographic data for location-based matching
- **CampaignApplication**: Junction model for influencer applications

### Livewire Architecture
- **BaseComponent**: Abstract class providing common utilities for all Livewire components
  - Authentication helpers, flash messaging, validation utilities
  - Enum handling, array manipulation, account type checks
- **Wizard Pattern**: Uses HasWizardSteps trait for multi-step processes (onboarding, campaign creation)
- **Component Organization**: Grouped by feature (Auth, Campaigns, Onboarding)

### Key Services
- **CampaignService**: Campaign CRUD operations, status management, event dispatching
- **ProfileService**: User profile management and onboarding completion checks
- **SearchService**: Campaign discovery and matching algorithm implementation
- **NotificationService**: User notification management
- **ValidationService**: Centralized validation logic
- **SubscriptionService**: Subscription plan management and billing

### Event System
Events are dispatched for campaign lifecycle changes:
- CampaignPublished, CampaignScheduled, CampaignArchived, CampaignEdited
- BusinessJoined, InfluencerJoined, ProfileUpdated
- CampaignApplicationSubmitted (creates notifications)

### Enum System
Extensive use of enums with HasFormOptions trait for form generation:
- **AccountType**: BUSINESS, INFLUENCER, ADMIN
- **CampaignStatus**: DRAFT, PUBLISHED, SCHEDULED, ARCHIVED
- **CompensationType**: MONETARY, PRODUCT, BARTER, DISCOUNT
- **CampaignType**: USER_GENERATED, PRODUCT_REVIEW, BRAND_AWARENESS, EVENT_PROMOTION
- **TargetPlatform**: INSTAGRAM, TIKTOK, YOUTUBE, FACEBOOK, X
- **DeliverableType**: INSTAGRAM_POST, INSTAGRAM_REEL, INSTAGRAM_STORY, TIKTOK_VIDEO
- **SuccessMetric**: IMPRESSIONS, ENGAGEMENT_RATE, CLICKS, CONVERSIONS

## Development Guidelines

### Livewire-First Approach
- Always use Livewire components over traditional controllers
- Extend BaseComponent for all Livewire components
- Use Livewire navigation (wire:navigate) for better UX

### Enum Usage
- Always validate enum values in forms and APIs using HasFormOptions trait
- Use toOptions() method for form dropdowns
- Leverage validationRule() method for validation

### Route Structure
```
/dashboard - Main dashboard (post-onboarding)
/onboarding/* - Account setup flow
/campaigns/* - Campaign management (CRUD)
/discover - Influencer campaign discovery
/search - General search functionality
```

### Database Patterns
- Uses SQLite for development/testing, supports MySQL/PostgreSQL for production
- Extensive use of casts for JSON fields and enums
- Factory-based testing data generation
- Array fields must be cast as 'array' in model $casts property

### Authentication & Authorization
- Standard Laravel authentication with Livewire auth components
- Onboarding middleware (EnsureOnboardingCompleted) protects main routes
- Account type-based access control throughout application

### Frontend Stack
- Tailwind CSS 4.x with Vite plugin
- Alpine.js for JavaScript interactivity
- Livewire Flux Pro for UI components
- All running through Vite with hot reload

### Testing Strategy
- Feature tests for Livewire components and business logic
- Unit tests for services, models, and enums
- Factory-based test data generation
- In-memory SQLite for test database

## Key Files to Understand

- `app/Livewire/BaseComponent.php` - Foundation for all Livewire components
- `app/Models/User.php` - Central user model with profile relationships
- `app/Models/Campaign.php` - Complex campaign model with extensive business logic
- `app/Services/SearchService.php` - Campaign matching and discovery algorithms
- `routes/web.php` - Route structure and middleware configuration
- `app/Enums/` - Business logic enums with form option generation
- `app/Livewire/Traits/HasWizardSteps.php` - Multi-step form wizard implementation
- `app/Enums/Traits/HasFormOptions.php` - Enum form option utilities

## Common Patterns

### Form Handling
- Multi-step wizards using HasWizardSteps trait
- Real-time validation with Livewire
- Extensive use of enums for dropdown options

### Data Management
- Array-based form fields with add/remove functionality
- JSON casting for complex data structures
- Event-driven architecture for state changes

### User Experience
- Smart campaign matching with visual indicators
- Real-time filtering and search
- Progressive disclosure in complex forms


## General Information
- This has not been sent to production or anywhere outside of my local computer and github, you can update existing migrations and just run `php artisan migrate:fresh`
