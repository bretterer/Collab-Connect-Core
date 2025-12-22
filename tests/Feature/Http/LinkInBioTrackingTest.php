<?php

namespace Tests\Feature\Http;

use App\Models\Influencer;
use App\Models\LinkInBioSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LinkInBioTrackingTest extends TestCase
{
    use RefreshDatabase;

    private function createInfluencerWithLinkInBio(bool $published = true): Influencer
    {
        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        // Set a username for the influencer (required for link-in-bio routes)
        $influencer->update(['username' => 'testuser_'.uniqid()]);

        LinkInBioSettings::create([
            'influencer_id' => $influencer->id,
            'settings' => LinkInBioSettings::getDefaultSettings(),
            'is_published' => $published,
        ]);

        return $influencer->fresh();
    }

    #[Test]
    public function it_records_view_for_published_page(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        $response = $this->postJson(route('link-in-bio.track.view', [
            'username' => $influencer->username,
        ]));

        $response->assertOk()
            ->assertJson(['status' => 'recorded']);

        $this->assertDatabaseCount('link_in_bio_views', 1);
    }

    #[Test]
    public function it_ignores_view_for_unpublished_page(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(false);

        $response = $this->postJson(route('link-in-bio.track.view', [
            'username' => $influencer->username,
        ]));

        $response->assertOk()
            ->assertJson(['status' => 'ignored']);

        $this->assertDatabaseCount('link_in_bio_views', 0);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_user(): void
    {
        $response = $this->postJson(route('link-in-bio.track.view', [
            'username' => 'nonexistent_user',
        ]));

        $response->assertNotFound();
    }

    #[Test]
    public function it_excludes_owner_views(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        $response = $this->actingAs($influencer->user)
            ->postJson(route('link-in-bio.track.view', [
                'username' => $influencer->username,
            ]));

        $response->assertOk()
            ->assertJson(['status' => 'owner']);

        $this->assertDatabaseCount('link_in_bio_views', 0);
    }

    #[Test]
    public function it_respects_rate_limiting(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        // First view should be recorded
        $response1 = $this->postJson(route('link-in-bio.track.view', [
            'username' => $influencer->username,
        ]));

        $response1->assertOk()
            ->assertJson(['status' => 'recorded']);

        // Second view from same IP should be rate limited
        $response2 = $this->postJson(route('link-in-bio.track.view', [
            'username' => $influencer->username,
        ]));

        $response2->assertOk()
            ->assertJson(['status' => 'rate_limited']);

        $this->assertDatabaseCount('link_in_bio_views', 1);
    }

    #[Test]
    public function it_respects_dnt_header(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        $response = $this->withHeaders(['DNT' => '1'])
            ->postJson(route('link-in-bio.track.view', [
                'username' => $influencer->username,
            ]));

        $response->assertOk()
            ->assertJson(['status' => 'rate_limited']);

        $this->assertDatabaseCount('link_in_bio_views', 0);
    }

    #[Test]
    public function it_records_click_for_published_page(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        $response = $this->postJson(route('link-in-bio.track.click', [
            'username' => $influencer->username,
        ]), [
            'link_index' => 0,
            'link_title' => 'Instagram',
            'link_url' => 'https://instagram.com/test',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'recorded']);

        $this->assertDatabaseCount('link_in_bio_clicks', 1);
        $this->assertDatabaseHas('link_in_bio_clicks', [
            'link_index' => 0,
            'link_title' => 'Instagram',
            'link_url' => 'https://instagram.com/test',
        ]);
    }

    #[Test]
    public function it_validates_click_request(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(true);

        $response = $this->postJson(route('link-in-bio.track.click', [
            'username' => $influencer->username,
        ]), [
            'link_index' => 'not_a_number',
            'link_url' => 'not_a_url',
        ]);

        $response->assertUnprocessable();
    }

    #[Test]
    public function it_ignores_click_for_unpublished_page(): void
    {
        $influencer = $this->createInfluencerWithLinkInBio(false);

        $response = $this->postJson(route('link-in-bio.track.click', [
            'username' => $influencer->username,
        ]), [
            'link_index' => 0,
            'link_title' => 'Instagram',
            'link_url' => 'https://instagram.com/test',
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'ignored']);

        $this->assertDatabaseCount('link_in_bio_clicks', 0);
    }
}
