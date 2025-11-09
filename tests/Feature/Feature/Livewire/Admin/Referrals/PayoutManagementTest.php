<?php

namespace Tests\Feature\Feature\Livewire\Admin\Referrals;

use App\Enums\AccountType;
use App\Enums\PayoutStatus;
use App\Livewire\Admin\Referrals\PayoutManagement;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use App\Models\User;
use App\Services\PayPalPayoutsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayoutManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);
    }

    #[Test]
    public function it_renders_payout_management_page(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->assertStatus(200)
            ->assertSee('Payout Management');
    }

    #[Test]
    public function it_displays_payouts_with_user_information(): void
    {
        $enrollment = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'John Doe']))
            ->create([
                'paypal_email' => 'john@example.com',
                'paypal_verified' => true,
            ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'amount' => 150.00,
            'status' => PayoutStatus::PENDING,
            'month' => 1,
            'year' => 2025,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->assertSee('John Doe')
            ->assertSee('$150.00')
            ->assertSee('Pending Review');
    }

    #[Test]
    public function it_filters_payouts_by_status(): void
    {
        $enrollment1 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'Pending User']))
            ->create();

        $enrollment2 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'Failed User']))
            ->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'status' => PayoutStatus::PENDING,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'status' => PayoutStatus::FAILED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('statusFilter', PayoutStatus::PENDING->value)
            ->assertSee('Pending User')
            ->assertDontSee('Failed User');
    }

    #[Test]
    public function it_filters_payouts_by_month(): void
    {
        $enrollment1 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'January User']))
            ->create();

        $enrollment2 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'February User']))
            ->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'month' => 1,
            'year' => 2025,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'month' => 2,
            'year' => 2025,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('monthFilter', '2025-01')
            ->assertSee('January User')
            ->assertDontSee('February User');
    }

    #[Test]
    public function it_can_select_payouts(): void
    {
        $enrollment = ReferralEnrollment::factory()->create();

        $payout1 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
        ]);

        $payout2 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::FAILED,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('selectedPayouts', [$payout1->id, $payout2->id])
            ->assertSee('2 payouts selected');
    }

    #[Test]
    public function paid_payouts_are_not_selectable(): void
    {
        $enrollment = ReferralEnrollment::factory()->create([
            'paypal_email' => 'user@example.com',
            'paypal_verified' => true,
        ]);

        $paidPayout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PAID,
        ]);

        $this->actingAs($this->admin);

        $component = Livewire::test(PayoutManagement::class)
            ->set('selectAll', true);

        $this->assertNotContains($paidPayout->id, $component->get('selectedPayouts'));
    }

    #[Test]
    public function it_can_retrigger_selected_payouts(): void
    {
        $enrollment = ReferralEnrollment::factory()->create([
            'paypal_email' => 'user@example.com',
            'paypal_verified' => true,
        ]);

        $payout1 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::FAILED,
            'amount' => 100.00,
        ]);

        $payout2 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 150.00,
        ]);

        // Mock PayPal service
        $mockPayPalService = $this->mock(PayPalPayoutsService::class);
        $mockPayPalService->shouldReceive('createBatchPayout')
            ->once()
            ->andReturn([
                'batch_header' => [
                    'payout_batch_id' => 'TEST_BATCH_123',
                ],
            ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('selectedPayouts', [$payout1->id, $payout2->id])
            ->call('retriggerPayouts')
            ->assertSuccessful();

        // Verify payouts were updated
        $this->assertDatabaseHas('referral_payouts', [
            'id' => $payout1->id,
            'status' => PayoutStatus::PROCESSING->value,
            'paypal_batch_id' => 'TEST_BATCH_123',
        ]);

        $this->assertDatabaseHas('referral_payouts', [
            'id' => $payout2->id,
            'status' => PayoutStatus::PROCESSING->value,
            'paypal_batch_id' => 'TEST_BATCH_123',
        ]);
    }

    #[Test]
    public function it_prevents_retriggering_payouts_without_paypal(): void
    {
        $enrollmentWithoutPayPal = ReferralEnrollment::factory()->create([
            'paypal_email' => null,
            'paypal_verified' => false,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollmentWithoutPayPal->id,
            'status' => PayoutStatus::PENDING,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('selectedPayouts', [$payout->id])
            ->call('retriggerPayouts')
            ->assertSuccessful();

        // Verify payout was NOT updated
        $this->assertDatabaseMissing('referral_payouts', [
            'id' => $payout->id,
            'status' => PayoutStatus::PROCESSING->value,
        ]);
    }

    #[Test]
    public function it_clears_selection_after_successful_retrigger(): void
    {
        $enrollment = ReferralEnrollment::factory()->create([
            'paypal_email' => 'user@example.com',
            'paypal_verified' => true,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::FAILED,
        ]);

        // Mock PayPal service
        $mockPayPalService = $this->mock(PayPalPayoutsService::class);
        $mockPayPalService->shouldReceive('createBatchPayout')
            ->once()
            ->andReturn([
                'batch_header' => [
                    'payout_batch_id' => 'TEST_BATCH_456',
                ],
            ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('selectedPayouts', [$payout->id])
            ->call('retriggerPayouts')
            ->assertSet('selectedPayouts', [])
            ->assertSet('selectAll', false);
    }

    #[Test]
    public function it_displays_stats_correctly(): void
    {
        $enrollment1 = ReferralEnrollment::factory()->create();
        $enrollment2 = ReferralEnrollment::factory()->create();

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'status' => PayoutStatus::FAILED,
            'amount' => 50.00,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'status' => PayoutStatus::PAID,
            'amount' => 200.00,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->assertSee('3') // Total payouts
            ->assertSee('$100.00') // Pending amount
            ->assertSee('1') // Failed count
            ->assertSee('$200.00'); // Paid amount
    }

    #[Test]
    public function it_searches_payouts_by_user_name(): void
    {
        $enrollment1 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'Alice Johnson']))
            ->create();

        $enrollment2 = ReferralEnrollment::factory()
            ->for(User::factory()->create(['name' => 'Bob Smith']))
            ->create();

        ReferralPayout::factory()->create(['referral_enrollment_id' => $enrollment1->id]);
        ReferralPayout::factory()->create(['referral_enrollment_id' => $enrollment2->id]);

        $this->actingAs($this->admin);

        Livewire::test(PayoutManagement::class)
            ->set('search', 'Alice')
            ->assertSee('Alice Johnson')
            ->assertDontSee('Bob Smith');
    }

    #[Test]
    public function non_admin_cannot_access_payout_management(): void
    {
        $regularUser = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $this->actingAs($regularUser)
            ->get(route('admin.referrals.payouts'))
            ->assertStatus(403);
    }
}
