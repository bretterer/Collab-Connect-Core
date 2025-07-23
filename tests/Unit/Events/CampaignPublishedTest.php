<?php

namespace Tests\Unit\Events;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Events\CampaignPublished;
use App\Models\Campaign;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CampaignPublishedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function campaign_published_event_is_fired_when_campaign_is_published()
    {
        Event::fake();

        $user = User::factory()->business()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'status' => CampaignStatus::DRAFT,
        ]);

        CampaignService::publishCampaign($campaign);

        Event::assertDispatched(CampaignPublished::class, function ($event) use ($user, $campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->publisher->id === $user->id;
        });
    }

    #[Test]
    public function campaign_published_event_contains_correct_data()
    {
        Event::fake();

        $user = User::factory()->business()->create();

        $campaign = Campaign::factory()->create([
            'user_id' => $user->id,
            'status' => CampaignStatus::DRAFT,
            'campaign_goal' => 'Test Campaign',
        ]);

        CampaignService::publishCampaign($campaign);

        Event::assertDispatched(CampaignPublished::class, function ($event) use ($user, $campaign) {
            return $event->campaign->id === $campaign->id &&
                   $event->publisher->id === $user->id &&
                   $event->campaign->status === CampaignStatus::PUBLISHED;
        });
    }
}