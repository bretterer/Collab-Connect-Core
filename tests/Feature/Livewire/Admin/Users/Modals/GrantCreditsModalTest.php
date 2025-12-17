<?php

namespace Tests\Feature\Livewire\Admin\Users\Modals;

use App\Enums\AccountType;
use App\Livewire\Admin\Users\Modals\GrantCreditsModal;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GrantCreditsModalTest extends TestCase
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

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_initializes_with_default_values(): void
    {
        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->assertSet('credits', 1)
            ->assertSet('reason', '');
    }

    #[Test]
    public function it_can_open_modal_for_business_user(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
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
            ->test(GrantCreditsModal::class)
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
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Business::class, $billable);
    }

    #[Test]
    public function it_computes_billable_for_influencer_user(): void
    {
        $user = $this->createInfluencerUser();

        $component = Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id);

        $billable = $component->get('billable');
        $this->assertInstanceOf(Influencer::class, $billable);
    }

    #[Test]
    public function it_computes_current_credits_correctly(): void
    {
        $user = $this->createInfluencerUser();
        $user->influencer->update(['promotion_credits' => 5]);

        $component = Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id);

        $this->assertEquals(5, $component->get('currentCredits'));
    }

    #[Test]
    public function it_validates_credits_must_be_positive(): void
    {
        $user = $this->createBusinessUser();

        // When setting credits to empty string, PHP coerces int to 0
        // So the min validation catches this case
        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 0)
            ->set('reason', 'Test reason')
            ->call('grantCredits')
            ->assertHasErrors(['credits' => 'min']);
    }

    #[Test]
    public function it_validates_credits_minimum(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 0)
            ->set('reason', 'Test reason')
            ->call('grantCredits')
            ->assertHasErrors(['credits' => 'min']);
    }

    #[Test]
    public function it_validates_credits_maximum(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 101)
            ->set('reason', 'Test reason')
            ->call('grantCredits')
            ->assertHasErrors(['credits' => 'max']);
    }

    #[Test]
    public function it_validates_reason_required(): void
    {
        $user = $this->createBusinessUser();

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', '')
            ->call('grantCredits')
            ->assertHasErrors(['reason' => 'required']);
    }

    #[Test]
    public function it_can_grant_credits_to_influencer(): void
    {
        $user = $this->createInfluencerUser();
        $user->influencer->update(['promotion_credits' => 0]);

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', 'Promotional bonus')
            ->call('grantCredits')
            ->assertDispatched('credits-updated');

        $this->assertEquals(5, $user->influencer->fresh()->promotion_credits);
    }

    #[Test]
    public function it_can_grant_credits_to_business(): void
    {
        $user = $this->createBusinessUser();
        $business = $user->businesses()->wherePivot('role', 'owner')->first();
        $business->update(['promotion_credits' => 2]);

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 3)
            ->set('reason', 'VIP customer bonus')
            ->call('grantCredits')
            ->assertDispatched('credits-updated');

        $this->assertEquals(5, $business->fresh()->promotion_credits);
    }

    #[Test]
    public function it_creates_audit_log_when_granting_credits(): void
    {
        $user = $this->createInfluencerUser();
        $user->influencer->update(['promotion_credits' => 0]);

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 10)
            ->set('reason', 'Contest winner')
            ->call('grantCredits');

        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'credit.grant',
            'auditable_type' => Influencer::class,
            'auditable_id' => $user->influencer->id,
        ]);

        $log = AuditLog::where('action', 'credit.grant')->first();
        $this->assertEquals(['promotion_credits' => 0], $log->old_values);
        $this->assertEquals(['promotion_credits' => 10], $log->new_values);
        $this->assertEquals(10, $log->metadata['credits_added']);
        $this->assertEquals('Contest winner', $log->metadata['reason']);
    }

    #[Test]
    public function it_fails_when_no_billable_exists(): void
    {
        $user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class)
            ->call('open', $user->id)
            ->set('credits', 5)
            ->set('reason', 'Test')
            ->call('grantCredits')
            ->assertNotDispatched('credits-updated');
    }

    #[Test]
    public function it_returns_null_for_user_without_user_id(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class);

        $this->assertNull($component->get('user'));
    }

    #[Test]
    public function it_returns_null_for_billable_without_user(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class);

        $this->assertNull($component->get('billable'));
    }

    #[Test]
    public function it_returns_zero_for_current_credits_without_billable(): void
    {
        $component = Livewire::actingAs($this->admin)
            ->test(GrantCreditsModal::class);

        $this->assertEquals(0, $component->get('currentCredits'));
    }
}
