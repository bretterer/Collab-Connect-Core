<?php

namespace Tests\Unit\Events;

use App\Events\BusinessJoined;
use App\Models\BusinessProfile;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessJoinedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function business_joined_event_is_fired_when_business_profile_is_created()
    {
        Event::fake();

        $user = User::factory()->create();

        $data = [
            'businessName' => 'Test Business',
            'industry' => 'technology',
            'websites' => ['https://example.com'],
            'primaryZipCode' => '12345',
            'locationCount' => 1,
            'isFranchise' => false,
            'isNationalBrand' => false,
            'contactName' => 'John Doe',
            'contactEmail' => 'john@example.com',
            'collaborationGoals' => ['brand_awareness'],
            'campaignTypes' => ['social_post'],
        ];

        $businessProfile = ProfileService::createBusinessProfile($user, $data);

        Event::assertDispatched(BusinessJoined::class, function ($event) use ($user, $businessProfile) {
            return $event->user->id === $user->id &&
                   $event->businessProfile->id === $businessProfile->id;
        });
    }

    #[Test]
    public function business_joined_event_contains_correct_data()
    {
        Event::fake();

        $user = User::factory()->create();

        $data = [
            'businessName' => 'Test Business',
            'industry' => 'technology',
            'websites' => ['https://example.com'],
            'primaryZipCode' => '12345',
            'locationCount' => 1,
            'isFranchise' => false,
            'isNationalBrand' => false,
            'contactName' => 'John Doe',
            'contactEmail' => 'john@example.com',
            'collaborationGoals' => ['brand_awareness'],
            'campaignTypes' => ['social_post'],
        ];

        $businessProfile = ProfileService::createBusinessProfile($user, $data);

        Event::assertDispatched(BusinessJoined::class, function ($event) use ($user, $businessProfile) {
            return $event->user->id === $user->id &&
                   $event->businessProfile->id === $businessProfile->id &&
                   $event->businessProfile->business_name === 'Test Business';
        });
    }
}