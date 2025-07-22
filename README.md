# CollabConnect

A Laravel-based platform connecting local businesses with influencers for collaborative marketing campaigns.

## Features

### For Influencers
- **Campaign Discovery**: View open campaigns sorted by match score based on your profile
- **Smart Matching**: Campaigns are ranked by location, niche compatibility, and campaign type preferences
- **Advanced Filtering**: Filter campaigns by industry, campaign type, and search terms
- **Profile Management**: Complete onboarding and profile setup

### For Businesses
- **Campaign Creation**: Create and manage marketing campaigns
- **Influencer Search**: Find influencers based on location, niche, and follower count
- **Campaign Management**: Track campaign status and applications

## Campaign Discovery for Influencers

The platform includes an intelligent campaign discovery system that helps influencers find the most relevant opportunities:

### Match Scoring Algorithm
Campaigns are scored based on multiple factors:
- **Location Match (40%)**: Proximity to your primary zip code
- **Niche Match (30%)**: Alignment between your niche and business industry
- **Campaign Type (20%)**: Compatibility with campaign requirements
- **Compensation (10%)**: Budget and compensation type preferences

### Features
- **Real-time Filtering**: Search and filter campaigns by various criteria
- **Sort Options**: Sort by match score, newest, budget, or deadline
- **Visual Match Indicators**: Color-coded match scores (green/yellow/red)
- **Detailed Campaign Cards**: View comprehensive campaign information

### Access
Influencers can access the campaign discovery page via:
- Dashboard "Discover Campaigns" button
- Navigation menu "Discover Campaigns" link
- Direct URL: `/discover`

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Testing**: PHPUnit with Livewire testing support

## Getting Started

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy environment file: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Start the development server: `php artisan serve`

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific feature tests:
```bash
php artisan test tests/Feature/Livewire/InfluencerCampaignsTest.php
```

## Sample Data

The application includes sample campaigns for testing the discovery feature. To add more sample data:

```bash
php artisan db:seed --class=CampaignSeeder
```