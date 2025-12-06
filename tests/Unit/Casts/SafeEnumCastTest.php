<?php

namespace Tests\Unit\Casts;

use App\Casts\SafeEnumCast;
use App\Enums\CompensationType;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SafeEnumCastTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_enum_for_valid_value(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->get($model, 'compensation_type', 'monetary', []);

        $this->assertInstanceOf(CompensationType::class, $result);
        $this->assertEquals(CompensationType::MONETARY, $result);
    }

    #[Test]
    public function it_returns_null_for_invalid_value(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->get($model, 'compensation_type', 'barter', []);

        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_for_null_value(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->get($model, 'compensation_type', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function it_stores_enum_value(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->set($model, 'compensation_type', CompensationType::MONETARY, []);

        $this->assertEquals('monetary', $result);
    }

    #[Test]
    public function it_stores_string_value_if_valid(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->set($model, 'compensation_type', 'monetary', []);

        $this->assertEquals('monetary', $result);
    }

    #[Test]
    public function it_returns_null_when_storing_invalid_value(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->set($model, 'compensation_type', 'barter', []);

        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_null_when_storing_null(): void
    {
        $cast = new SafeEnumCast(CompensationType::class);
        $model = new Campaign;

        $result = $cast->set($model, 'compensation_type', null, []);

        $this->assertNull($result);
    }

    #[Test]
    public function campaign_handles_invalid_compensation_type_from_database(): void
    {
        // Create a campaign with a valid compensation type first
        $campaign = Campaign::factory()->create([
            'compensation_type' => CompensationType::MONETARY,
        ]);

        // Simulate legacy data with invalid enum value directly in database
        DB::table('campaigns')
            ->where('id', $campaign->id)
            ->update(['compensation_type' => 'barter']);

        // Refresh the model from database
        $campaign->refresh();

        // The compensation_type should be null since 'barter' is invalid
        $this->assertNull($campaign->compensation_type);
    }

    #[Test]
    public function match_score_service_handles_null_compensation_type(): void
    {
        // Create a campaign with a valid compensation type first
        $campaign = Campaign::factory()->create([
            'compensation_type' => CompensationType::MONETARY,
            'campaign_goal' => 'Test campaign goal',
        ]);

        // Simulate legacy data with invalid enum value directly in database
        DB::table('campaigns')
            ->where('id', $campaign->id)
            ->update(['compensation_type' => 'barter']);

        // Refresh the model from database
        $campaign->refresh();

        // Create an influencer profile
        $influencer = \App\Models\Influencer::factory()->create();

        // This should not throw an exception
        $matchScoreService = new \App\Services\MatchScoreService;
        $score = $matchScoreService->calculateCompensationScore($campaign, $influencer);

        // Should return a valid score (the default case handles null)
        $this->assertIsFloat($score);
        $this->assertGreaterThanOrEqual(50.0, $score);
        $this->assertLessThanOrEqual(90.0, $score);
    }
}
