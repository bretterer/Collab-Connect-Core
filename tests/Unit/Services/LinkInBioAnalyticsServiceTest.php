<?php

namespace Tests\Unit\Services;

use App\Models\LinkInBioClick;
use App\Models\LinkInBioSettings;
use App\Models\LinkInBioView;
use App\Models\User;
use App\Services\LinkInBioAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkInBioAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private LinkInBioAnalyticsService $service;

    private LinkInBioSettings $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(LinkInBioAnalyticsService::class);

        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;
        $influencer->update(['username' => 'testuser_'.uniqid()]);

        $this->settings = LinkInBioSettings::create([
            'influencer_id' => $influencer->id,
            'settings' => LinkInBioSettings::getDefaultSettings(),
            'is_published' => true,
        ]);
    }

    #[Test]
    public function it_records_view_with_correct_data(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        $request->headers->set('Referer', 'https://instagram.com/testuser');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertNotNull($view);
        $this->assertEquals($this->settings->id, $view->link_in_bio_settings_id);
        $this->assertNotEmpty($view->ip_hash);
        $this->assertEquals(64, strlen($view->ip_hash)); // SHA-256 hash length
        $this->assertEquals('mobile', $view->device_type);
        $this->assertEquals('instagram.com', $view->referrer_domain);
    }

    #[Test]
    public function it_respects_dnt_header_for_views(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('DNT', '1');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertNull($view);
        $this->assertDatabaseCount('link_in_bio_views', 0);
    }

    #[Test]
    public function it_respects_dnt_header_for_clicks(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('DNT', '1');

        $click = $this->service->recordClick(
            $this->settings,
            0,
            'Instagram',
            'https://instagram.com/test',
            $request
        );

        $this->assertNull($click);
        $this->assertDatabaseCount('link_in_bio_clicks', 0);
    }

    #[Test]
    public function it_enforces_rate_limiting_on_views(): void
    {
        $request = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '192.168.1.1']);

        // First view should be recorded
        $firstView = $this->service->recordView($this->settings, $request);
        $this->assertNotNull($firstView);

        // Second view from same IP should be rate limited
        $secondView = $this->service->recordView($this->settings, $request);
        $this->assertNull($secondView);

        $this->assertDatabaseCount('link_in_bio_views', 1);
    }

    #[Test]
    public function it_allows_views_from_different_ips(): void
    {
        $request1 = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '192.168.1.1']);
        $request2 = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '192.168.1.2']);

        $firstView = $this->service->recordView($this->settings, $request1);
        $secondView = $this->service->recordView($this->settings, $request2);

        $this->assertNotNull($firstView);
        $this->assertNotNull($secondView);
        $this->assertDatabaseCount('link_in_bio_views', 2);
    }

    #[Test]
    public function it_records_click_with_correct_data(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

        $click = $this->service->recordClick(
            $this->settings,
            0,
            'My Website',
            'https://example.com',
            $request
        );

        $this->assertNotNull($click);
        $this->assertEquals($this->settings->id, $click->link_in_bio_settings_id);
        $this->assertEquals(0, $click->link_index);
        $this->assertEquals('My Website', $click->link_title);
        $this->assertEquals('https://example.com', $click->link_url);
        $this->assertEquals('desktop', $click->device_type);
    }

    #[Test]
    public function it_detects_mobile_device_correctly(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Linux; Android 11; SM-G991B)');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertEquals('mobile', $view->device_type);
    }

    #[Test]
    public function it_detects_tablet_device_correctly(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X)');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertEquals('tablet', $view->device_type);
    }

    #[Test]
    public function it_detects_desktop_device_correctly(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertEquals('desktop', $view->device_type);
    }

    #[Test]
    public function it_extracts_referrer_domain_correctly(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Referer', 'https://www.tiktok.com/@username/video/123');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertEquals('www.tiktok.com', $view->referrer_domain);
    }

    #[Test]
    public function it_handles_missing_referrer_gracefully(): void
    {
        $request = Request::create('/test', 'POST');

        $view = $this->service->recordView($this->settings, $request);

        $this->assertNull($view->referrer);
        $this->assertNull($view->referrer_domain);
    }

    #[Test]
    public function it_calculates_overview_metrics_correctly(): void
    {
        // Create some test data
        $startDate = now()->subDays(7);
        $endDate = now();

        // Create views with different IPs
        LinkInBioView::factory()->count(10)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(3),
        ]);

        // Create clicks
        LinkInBioClick::factory()->count(3)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(3),
        ]);

        $metrics = $this->service->getOverviewMetrics($this->settings, $startDate, $endDate);

        $this->assertEquals(10, $metrics['views']);
        $this->assertEquals(3, $metrics['clicks']);
        $this->assertEquals(30.0, $metrics['ctr']); // 3/10 * 100 = 30%
    }

    #[Test]
    public function it_returns_zero_ctr_when_no_views(): void
    {
        $startDate = now()->subDays(7);
        $endDate = now();

        $metrics = $this->service->getOverviewMetrics($this->settings, $startDate, $endDate);

        $this->assertEquals(0, $metrics['views']);
        $this->assertEquals(0, $metrics['clicks']);
        $this->assertEquals(0, $metrics['ctr']);
    }

    #[Test]
    public function it_filters_views_by_date_range(): void
    {
        // Create views inside the date range
        LinkInBioView::factory()->count(5)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(3),
        ]);

        // Create views outside the date range
        LinkInBioView::factory()->count(5)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(30),
        ]);

        $metrics = $this->service->getOverviewMetrics(
            $this->settings,
            now()->subDays(7),
            now()
        );

        $this->assertEquals(5, $metrics['views']);
    }

    #[Test]
    public function it_returns_device_breakdown_correctly(): void
    {
        LinkInBioView::factory()->count(5)->mobile()->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(3),
        ]);

        LinkInBioView::factory()->count(3)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'device_type' => 'desktop',
            'viewed_at' => now()->subDays(3),
        ]);

        LinkInBioView::factory()->count(2)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'device_type' => 'tablet',
            'viewed_at' => now()->subDays(3),
        ]);

        $breakdown = $this->service->getDeviceBreakdown(
            $this->settings,
            now()->subDays(7),
            now()
        );

        $this->assertEquals(5, $breakdown['mobile']);
        $this->assertEquals(3, $breakdown['desktop']);
        $this->assertEquals(2, $breakdown['tablet']);
    }

    #[Test]
    public function it_returns_top_referrers_correctly(): void
    {
        LinkInBioView::factory()->count(5)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'referrer_domain' => 'instagram.com',
            'viewed_at' => now()->subDays(3),
        ]);

        LinkInBioView::factory()->count(3)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'referrer_domain' => 'tiktok.com',
            'viewed_at' => now()->subDays(3),
        ]);

        LinkInBioView::factory()->count(2)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'referrer_domain' => null, // Direct traffic
            'viewed_at' => now()->subDays(3),
        ]);

        $referrers = $this->service->getTopReferrers(
            $this->settings,
            now()->subDays(7),
            now()
        );

        $this->assertCount(2, $referrers);
        $this->assertEquals('instagram.com', $referrers->first()->referrer_domain);
        $this->assertEquals(5, $referrers->first()->count);
    }

    #[Test]
    public function it_returns_per_link_performance(): void
    {
        // Create views for CTR calculation
        LinkInBioView::factory()->count(100)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(3),
        ]);

        // Create clicks for different links
        LinkInBioClick::factory()->count(10)->forLink(0, 'Instagram', 'https://instagram.com/test')->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(3),
        ]);

        LinkInBioClick::factory()->count(5)->forLink(1, 'TikTok', 'https://tiktok.com/@test')->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(3),
        ]);

        $performance = $this->service->getPerLinkPerformance(
            $this->settings,
            now()->subDays(7),
            now()
        );

        $this->assertCount(2, $performance);

        $instagramLink = $performance->first();
        $this->assertEquals('Instagram', $instagramLink->link_title);
        $this->assertEquals(10, $instagramLink->clicks);
        $this->assertEquals(10.0, $instagramLink->ctr); // 10/100 * 100 = 10%
    }

    #[Test]
    public function it_returns_views_over_time(): void
    {
        // Create views on different days
        LinkInBioView::factory()->count(3)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(2),
        ]);

        LinkInBioView::factory()->count(5)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(1),
        ]);

        $viewsOverTime = $this->service->getViewsOverTime(
            $this->settings,
            now()->subDays(7),
            now()
        );

        // Should return all 8 days (7 days ago to today) with gaps filled with zeros
        $this->assertCount(8, $viewsOverTime);

        // Find the entries with actual data
        $dataByDate = $viewsOverTime->keyBy('date');
        $twoDaysAgo = now()->subDays(2)->format('Y-m-d');
        $oneDayAgo = now()->subDays(1)->format('Y-m-d');

        $this->assertEquals(3, $dataByDate[$twoDaysAgo]->count);
        $this->assertEquals(5, $dataByDate[$oneDayAgo]->count);
    }

    #[Test]
    public function it_returns_clicks_over_time(): void
    {
        // Create clicks on different days
        LinkInBioClick::factory()->count(2)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(3),
        ]);

        LinkInBioClick::factory()->count(4)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(1),
        ]);

        $clicksOverTime = $this->service->getClicksOverTime(
            $this->settings,
            now()->subDays(7),
            now()
        );

        // Should return all 8 days with gaps filled with zeros
        $this->assertCount(8, $clicksOverTime);

        // Find the entries with actual data
        $dataByDate = $clicksOverTime->keyBy('date');
        $threeDaysAgo = now()->subDays(3)->format('Y-m-d');
        $oneDayAgo = now()->subDays(1)->format('Y-m-d');

        $this->assertEquals(2, $dataByDate[$threeDaysAgo]->count);
        $this->assertEquals(4, $dataByDate[$oneDayAgo]->count);
    }

    #[Test]
    public function it_cleans_up_old_data(): void
    {
        // Create old views (beyond retention)
        LinkInBioView::factory()->count(5)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(100),
        ]);

        // Create recent views
        LinkInBioView::factory()->count(3)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'viewed_at' => now()->subDays(10),
        ]);

        // Create old clicks
        LinkInBioClick::factory()->count(4)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(100),
        ]);

        // Create recent clicks
        LinkInBioClick::factory()->count(2)->create([
            'link_in_bio_settings_id' => $this->settings->id,
            'clicked_at' => now()->subDays(10),
        ]);

        $deletedCount = $this->service->cleanupOldData();

        $this->assertEquals(9, $deletedCount); // 5 old views + 4 old clicks
        $this->assertDatabaseCount('link_in_bio_views', 3); // Only recent views remain
        $this->assertDatabaseCount('link_in_bio_clicks', 2); // Only recent clicks remain
    }

    #[Test]
    public function it_marks_first_visitor_as_unique(): void
    {
        $request = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '10.0.0.1']);

        $view = $this->service->recordView($this->settings, $request);

        $this->assertTrue($view->is_unique);
    }

    #[Test]
    public function it_hashes_ip_consistently(): void
    {
        $request1 = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '192.168.1.100']);
        $view1 = $this->service->recordView($this->settings, $request1);

        // Create a new settings to avoid rate limiting
        $user2 = User::factory()->influencer()->withProfile()->create();
        $influencer2 = $user2->influencer;
        $influencer2->update(['username' => 'testuser2_'.uniqid()]);

        $settings2 = LinkInBioSettings::create([
            'influencer_id' => $influencer2->id,
            'settings' => LinkInBioSettings::getDefaultSettings(),
            'is_published' => true,
        ]);

        $request2 = Request::create('/test', 'POST', [], [], [], ['REMOTE_ADDR' => '192.168.1.100']);
        $view2 = $this->service->recordView($settings2, $request2);

        // Same IP should produce same hash
        $this->assertEquals($view1->ip_hash, $view2->ip_hash);
    }
}
