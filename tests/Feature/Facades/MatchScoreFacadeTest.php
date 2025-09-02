<?php

namespace Tests\Feature\Facades;

use App\Facades\MatchScore;
use App\Models\Campaign;
use App\Models\User;
use App\Services\MatchScoreService;
use Tests\TestCase;

class MatchScoreFacadeTest extends TestCase
{
    public function test_facade_works_normally(): void
    {
        $campaign = Campaign::factory()->make();
        $campaign->setRelation('business', User::factory()->make());
        $influencerProfile = (object) [
            'postal_code' => '49503',
            'primary_industry' => null,
        ];

        $score = MatchScore::calculateMatchScore($campaign, $influencerProfile);

        $this->assertGreaterThan(0, $score);
        $this->assertLessThanOrEqual(100, $score);
    }

    public function test_can_mock_facade_to_return_specific_score(): void
    {
        // Mock the MatchScoreService
        $this->mock(MatchScoreService::class, function ($mock) {
            $mock->shouldReceive('calculateMatchScore')
                ->andReturn(95.5);
        });

        $campaign = Campaign::factory()->make();
        $influencerProfile = (object) [];

        $score = MatchScore::calculateMatchScore($campaign, $influencerProfile);

        $this->assertEquals(95.5, $score);
    }

    public function test_can_mock_facade_with_different_scores_for_different_campaigns(): void
    {
        $campaign1 = Campaign::factory()->make(['id' => 1]);
        $campaign2 = Campaign::factory()->make(['id' => 2]);
        $influencerProfile = (object) [];

        // Mock the service to return different scores
        $this->mock(MatchScoreService::class, function ($mock) {
            $mock->shouldReceive('calculateMatchScore')
                ->andReturn(85.0, 65.0); // Will return 85.0 first time, 65.0 second time
        });

        $score1 = MatchScore::calculateMatchScore($campaign1, $influencerProfile);
        $score2 = MatchScore::calculateMatchScore($campaign2, $influencerProfile);

        $this->assertEquals(85.0, $score1);
        $this->assertEquals(65.0, $score2);
    }

    public function test_can_mock_detailed_score_breakdown(): void
    {
        $campaign = Campaign::factory()->make();
        $influencerProfile = (object) [];

        $expectedBreakdown = [
            'location' => ['raw' => 100.0, 'weighted' => 35.0, 'weight' => '35%'],
            'industry' => ['raw' => 100.0, 'weighted' => 35.0, 'weight' => '35%'],
            'campaign_type' => ['raw' => 85.0, 'weighted' => 17.0, 'weight' => '20%'],
            'compensation' => ['raw' => 80.0, 'weighted' => 8.0, 'weight' => '10%'],
            'total' => 95.0,
        ];

        $this->mock(MatchScoreService::class, function ($mock) use ($expectedBreakdown) {
            $mock->shouldReceive('getDetailedScoreBreakdown')
                ->andReturn($expectedBreakdown);
        });

        $breakdown = MatchScore::getDetailedScoreBreakdown($campaign, $influencerProfile);

        $this->assertEquals($expectedBreakdown, $breakdown);
        $this->assertEquals(95.0, $breakdown['total']);
    }

    public function test_can_spy_on_facade_calls(): void
    {
        $campaign = Campaign::factory()->make();
        $influencerProfile = (object) ['postal_code' => '49503'];

        // Spy on the service to verify calls while allowing real implementation
        $spy = $this->spy(MatchScoreService::class);

        MatchScore::calculateMatchScore($campaign, $influencerProfile, 25);

        // Assert the method was called with expected parameters
        $spy->shouldHaveReceived('calculateMatchScore');
    }
}