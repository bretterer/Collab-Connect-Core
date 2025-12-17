<?php

namespace Tests\Feature\Jobs;

use App\Jobs\HandleProfilePromotionUpdates;
use App\Models\Influencer;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HandleProfilePromotionUpdatesTest extends TestCase
{
    #[Test]
    public function it_unpromotes_influencers_with_expired_promotions(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Set promotion to expire today (should be unpromoted when job runs)
        $influencer->update([
            'is_promoted' => true,
            'promoted_until' => now()->startOfDay(),
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        $influencer->refresh();
        $this->assertFalse($influencer->is_promoted);
        $this->assertNull($influencer->promoted_until);
    }

    #[Test]
    public function it_unpromotes_businesses_with_expired_promotions(): void
    {
        $user = User::factory()->business()->withProfile()->create();
        $business = $user->currentBusiness;

        // Set promotion to expire today (should be unpromoted when job runs)
        $business->update([
            'is_promoted' => true,
            'promoted_until' => now()->startOfDay(),
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        $business->refresh();
        $this->assertFalse($business->is_promoted);
        $this->assertNull($business->promoted_until);
    }

    #[Test]
    public function it_keeps_active_promotions_for_future_dates(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Set promotion to expire tomorrow (should NOT be unpromoted)
        $influencer->update([
            'is_promoted' => true,
            'promoted_until' => now()->addDay()->startOfDay(),
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        $influencer->refresh();
        $this->assertTrue($influencer->is_promoted);
        $this->assertNotNull($influencer->promoted_until);
    }

    #[Test]
    public function it_unpromotes_past_promotions(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Set promotion to have expired yesterday
        $influencer->update([
            'is_promoted' => true,
            'promoted_until' => now()->subDay(),
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        $influencer->refresh();
        $this->assertFalse($influencer->is_promoted);
        $this->assertNull($influencer->promoted_until);
    }

    #[Test]
    public function it_handles_multiple_expired_promotions(): void
    {
        // Create multiple influencers with expired promotions
        $expiredInfluencers = [];
        for ($i = 0; $i < 3; $i++) {
            $user = User::factory()->influencer()->withProfile()->create();
            $user->influencer->update([
                'is_promoted' => true,
                'promoted_until' => now()->subDays($i),
            ]);
            $expiredInfluencers[] = $user->influencer;
        }

        // Create one active promotion
        $activeUser = User::factory()->influencer()->withProfile()->create();
        $activeUser->influencer->update([
            'is_promoted' => true,
            'promoted_until' => now()->addDays(5),
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        // All expired should be unpromoted
        foreach ($expiredInfluencers as $influencer) {
            $influencer->refresh();
            $this->assertFalse($influencer->is_promoted);
            $this->assertNull($influencer->promoted_until);
        }

        // Active should still be promoted
        $activeUser->refresh();
        $this->assertTrue($activeUser->influencer->is_promoted);
        $this->assertNotNull($activeUser->influencer->promoted_until);
    }

    #[Test]
    public function it_only_affects_records_with_promoted_until_set(): void
    {
        // Create influencer with is_promoted true but no promoted_until
        $user = User::factory()->influencer()->withProfile()->create();
        $user->influencer->update([
            'is_promoted' => true,
            'promoted_until' => null,
        ]);

        $job = new HandleProfilePromotionUpdates;
        $job->handle();

        // Should not be affected since promoted_until is null
        $user->refresh();
        $this->assertTrue($user->influencer->is_promoted);
    }
}
