<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\SocialPlatform;
use App\Livewire\Onboarding\InfluencerOnboarding;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class InfluencerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $this->actingAs($this->user);
    }

    #[Test]
    public function component_renders_successfully()
    {
        Livewire::test(InfluencerOnboarding::class)
            ->assertStatus(200)
            ->assertSee('Basic Information')
            ->assertSet('step', 1);
    }

    #[Test]
    public function step_2_requires_at_least_one_social_account()
    {
        $influencer = $this->createInfluencerAndGoToStep(2);

        Livewire::test(InfluencerOnboarding::class)
            ->set('step', 2)
            ->call('nextStep')
            ->assertHasErrors(['socialAccounts']);
    }

    #[Test]
    public function step_2_requires_follower_count_when_username_provided()
    {
        $influencer = $this->createInfluencerAndGoToStep(2);

        Livewire::test(InfluencerOnboarding::class)
            ->set('step', 2)
            ->set('socialAccounts.instagram.username', 'testuser')
            ->set('socialAccounts.instagram.followers', null)
            ->call('nextStep')
            ->assertHasErrors(['socialAccounts.instagram.followers']);
    }

    #[Test]
    public function step_2_passes_validation_with_complete_social_account()
    {
        $influencer = $this->createInfluencerAndGoToStep(2);

        Livewire::test(InfluencerOnboarding::class)
            ->set('step', 2)
            ->set('socialAccounts.instagram.username', 'testuser')
            ->set('socialAccounts.instagram.followers', 10000)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 3);
    }

    #[Test]
    public function step_2_validates_multiple_accounts_independently()
    {
        $influencer = $this->createInfluencerAndGoToStep(2);

        // Instagram has username but no followers - should fail
        // TikTok has both - should be fine
        Livewire::test(InfluencerOnboarding::class)
            ->set('step', 2)
            ->set('socialAccounts.instagram.username', 'testuser')
            ->set('socialAccounts.instagram.followers', null)
            ->set('socialAccounts.tiktok.username', 'tiktoker')
            ->set('socialAccounts.tiktok.followers', 5000)
            ->call('nextStep')
            ->assertHasErrors(['socialAccounts.instagram.followers']);
    }

    #[Test]
    public function step_2_allows_empty_social_accounts_if_at_least_one_complete()
    {
        $influencer = $this->createInfluencerAndGoToStep(2);

        // Only Instagram filled out, others empty
        Livewire::test(InfluencerOnboarding::class)
            ->set('step', 2)
            ->set('socialAccounts.instagram.username', 'testuser')
            ->set('socialAccounts.instagram.followers', 10000)
            ->set('socialAccounts.tiktok.username', '')
            ->set('socialAccounts.tiktok.followers', null)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 3);
    }

    #[Test]
    public function get_max_steps_returns_correct_count()
    {
        $component = Livewire::test(InfluencerOnboarding::class);
        $maxSteps = $component->instance()->getMaxSteps();

        $this->assertEquals(7, $maxSteps);
    }

    private function createInfluencerAndGoToStep(int $targetStep): Influencer
    {
        $influencer = Influencer::factory()->create([
            'user_id' => $this->user->id,
            'bio' => 'Test bio for testing',
        ]);

        // Initialize social accounts
        foreach (SocialPlatform::cases() as $platform) {
            $influencer->socials()->create([
                'platform' => $platform->value,
                'username' => '',
                'url' => '',
                'followers' => null,
            ]);
        }

        // Set cache to target step
        if ($targetStep > 1) {
            Cache::put('onboarding_step_influencer_'.$influencer->id, $targetStep, now()->addHours(24));
        }

        return $influencer;
    }
}
