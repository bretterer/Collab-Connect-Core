<?php

namespace Tests\Feature\Livewire\LinkInBio;

use App\Livewire\LinkInBio\Analytics;
use App\Models\Influencer;
use App\Models\LinkInBioSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function createInfluencerWithSettings(): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $influencer->update(['username' => 'testuser_'.uniqid()]);

        LinkInBioSettings::create([
            'influencer_id' => $influencer->id,
            'settings' => LinkInBioSettings::getDefaultSettings(),
            'is_published' => true,
        ]);

        return $influencer->fresh();
    }

    #[Test]
    public function it_redirects_if_no_link_in_bio_settings(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        Livewire::actingAs($user)
            ->test(Analytics::class)
            ->assertRedirect(route('link-in-bio.index'));
    }

    #[Test]
    public function it_renders_analytics_page_for_user_with_settings(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->assertStatus(200)
            ->assertSee('Link-in-Bio Analytics');
    }

    #[Test]
    public function it_defaults_to_30_day_date_range(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->assertSet('dateRange', '30');
    }

    #[Test]
    public function it_can_switch_date_range(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->set('dateRange', '7')
            ->assertSet('dateRange', '7')
            ->set('dateRange', '90')
            ->assertSet('dateRange', '90');
    }

    #[Test]
    public function it_clears_custom_dates_when_switching_to_preset_range(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->set('dateRange', 'custom')
            ->set('customStartDate', '2024-01-01')
            ->set('customEndDate', '2024-01-31')
            ->set('dateRange', '7')
            ->assertSet('customStartDate', null)
            ->assertSet('customEndDate', null);
    }

    #[Test]
    public function it_shows_zero_metrics_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $this->assertEquals(0, $component->get('overviewMetrics')['views']);
        $this->assertEquals(0, $component->get('overviewMetrics')['clicks']);
        $this->assertEquals(0, $component->get('overviewMetrics')['ctr']);
    }

    #[Test]
    public function it_shows_elite_feature_badge_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->assertSee('Elite Feature');
    }

    #[Test]
    public function it_returns_settings_computed_property(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $this->assertNotNull($component->get('settings'));
        $this->assertEquals($influencer->linkInBioSettings->id, $component->get('settings')->id);
    }

    #[Test]
    public function it_returns_empty_chart_data_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $this->assertEmpty($component->get('viewsChartData'));
        $this->assertEmpty($component->get('clicksChartData'));
    }

    #[Test]
    public function it_returns_zero_device_breakdown_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $breakdown = $component->get('deviceBreakdown');
        $this->assertEquals(0, $breakdown['mobile']);
        $this->assertEquals(0, $breakdown['desktop']);
        $this->assertEquals(0, $breakdown['tablet']);
    }

    #[Test]
    public function it_returns_empty_referrers_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $this->assertEmpty($component->get('topReferrers'));
    }

    #[Test]
    public function it_returns_empty_link_performance_for_non_elite_user(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class);

        $this->assertEmpty($component->get('linkPerformance'));
    }

    #[Test]
    public function it_computes_correct_start_date_for_preset_ranges(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->set('dateRange', '7');

        $startDate = $component->get('startDate');
        $this->assertEquals(now()->subDays(7)->startOfDay()->format('Y-m-d'), $startDate->format('Y-m-d'));
    }

    #[Test]
    public function it_computes_correct_dates_for_custom_range(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        $component = Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->set('dateRange', 'custom')
            ->set('customStartDate', '2024-06-01')
            ->set('customEndDate', '2024-06-30');

        $this->assertEquals('2024-06-01', $component->get('startDate')->format('Y-m-d'));
        $this->assertEquals('2024-06-30', $component->get('endDate')->format('Y-m-d'));
    }

    #[Test]
    public function it_shows_back_to_editor_link(): void
    {
        $influencer = $this->createInfluencerWithSettings();

        Livewire::actingAs($influencer->user)
            ->test(Analytics::class)
            ->assertSee('Back to Editor');
    }
}
