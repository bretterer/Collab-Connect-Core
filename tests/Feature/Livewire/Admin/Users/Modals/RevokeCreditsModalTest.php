<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Facades\SubscriptionLimits;
use App\Livewire\Admin\Users\Modals\RevokeCreditsModal;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\StripePrice;
use App\Models\User;
use App\Subscription\SubscriptionMetadataSchema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RevokeCreditsModalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);
    }

    private function createBusinessUser(): User
    {
        return User::factory()->business()->withProfile()->create();
    }

    private function createInfluencerUser(): User
    {
        return User::factory()->influencer()->withProfile()->create();
    }

    private function createSubscribedInfluencer(int $credits = 10): User
    {
        $user = $this->createInfluencerUser();
        $influencer = $user->influencer;

        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => $credits,
            ],
        ]);

        $influencer->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);

        // Initialize credits
        SubscriptionLimits::initializeCredits($influencer);

        return $user;
    }

    private function createSubscribedBusiness(int $credits = 10): User
    {
        $user = $this->createBusinessUser();
        $business = $user->businesses()->wherePivot('role', 'owner')->first();

        $price = StripePrice::factory()->create([
            'metadata' => [
                SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS => $credits,
            ],
        ]);

        $business->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_'.uniqid(),
            'stripe_status' => 'active',
            'stripe_price' => $price->stripe_id,
        ]);

        // Initialize credits
        SubscriptionLimits::initializeCredits($business);

        return $user;
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_initializes_with_default_values(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->assertSet('credits', 1)
            ->assertSet('reason', '');
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('credits', 1)
            ->assertSet('reason', '');
    }

    #[Test]
    public function it_can_open_modal_for_influencer_user(): void
    {
        $user = $this->createInfluencerUser();

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('credits', 1)
            ->assertSet('reason', '');
    }

    #[Test]
    public function it_computes_billable_for_business_user(): void
    {
        $user = $this->createBusinessUser();

        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUser();

        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_computes_current_credits_correctly(): void
    {
        $user = $this->createSubscribedInfluencer(10);

        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id);

        $this->assertEquals(10, $component->get('currentCredits'));
    }

    #[Test]
    public function it_computes_max_revokable_correctly(): void
    {
        $user = $this->createSubscribedInfluencer(7);

        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id);

        $this->assertEquals(7, $component->get('maxRevokable'));
    }

    #[Test]
    public function it_validates_credits_must_be_positive(): void
    {
        $user = $this->createSubscribedInfluencer(5);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 0)
            ->set('reason', 'Test reason')
            ->call('revokeCredits')
            ->assertHasErrors(['credits']);
    }

    #[Test]
    public function it_validates_credits_minimum(): void
    {
        $user = $this->createSubscribedInfluencer(5);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 0)
            ->set('reason', 'Test reason')
            ->call('revokeCredits')
            ->assertHasErrors(['credits']);
    }

    #[Test]
    public function it_validates_credits_cannot_exceed_current_credits(): void
    {
        $user = $this->createSubscribedInfluencer(5);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 10)
            ->set('reason', 'Test reason')
            ->call('revokeCredits')
            ->assertHasErrors(['credits']);
    }

    #[Test]
    public function it_validates_reason_required(): void
    {
        $user = $this->createSubscribedInfluencer(5);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 2)
            ->set('reason', '')
            ->call('revokeCredits')
            ->assertHasErrors(['reason' => 'required']);
    }

    #[Test]
    public function it_can_revoke_credits_from_influencer(): void
    {
        $user = $this->createSubscribedInfluencer(10);
        $influencer = $user->influencer;

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 3)
            ->set('reason', 'Policy violation')
            ->call('revokeCredits')
            ->assertDispatched('credits-updated');

        $newCredits = SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(7, $newCredits);
    }

    #[Test]
    public function it_can_revoke_credits_from_business(): void
    {
        $user = $this->createSubscribedBusiness(8);
        $business = $user->businesses()->wherePivot('role', 'owner')->first();

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', 'Account adjustment')
            ->call('revokeCredits')
            ->assertDispatched('credits-updated');

        $newCredits = SubscriptionLimits::getRemainingCredits(
            $business,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(3, $newCredits);
    }

    #[Test]
    public function it_creates_audit_log_when_revoking_credits(): void
    {
        $user = $this->createSubscribedInfluencer(15);
        $influencer = $user->influencer;

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', 'Chargeback')
            ->call('revokeCredits');

        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'credit.revoke',
            'auditable_type' => Influencer::class,
            'auditable_id' => $influencer->id,
        ]);

        $log = AuditLog::where('action', 'credit.revoke')->first();
        $this->assertEquals(['profile_promotion_credits' => 15], $log->old_values);
        $this->assertEquals(['profile_promotion_credits' => 10], $log->new_values);
        $this->assertEquals(5, $log->metadata['credits_removed']);
        $this->assertEquals('Chargeback', $log->metadata['reason']);
    }

    #[Test]
    public function it_cannot_revoke_more_credits_than_available(): void
    {
        $user = $this->createSubscribedInfluencer(3);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', 'Test')
            ->call('revokeCredits')
            ->assertHasErrors(['credits']);

        $influencer = $user->influencer;
        $currentCredits = SubscriptionLimits::getRemainingCredits(
            $influencer,
            SubscriptionMetadataSchema::PROFILE_PROMOTION_CREDITS
        );
        $this->assertEquals(3, $currentCredits);
    }

    #[Test]
    public function it_fails_when_no_billable_exists(): void
    {
        $user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 1)
            ->set('reason', 'Test')
            ->call('revokeCredits')
            ->assertNotDispatched('credits-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_returns_zero_for_current_credits_without_billable(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class);

        $this->assertEquals(0, $component->get('currentCredits'));
    }

    #[Test]
    public function it_returns_zero_for_max_revokable_without_billable(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(RevokeCreditsModal::class);

        $this->assertEquals(0, $component->get('maxRevokable'));
    }
}
