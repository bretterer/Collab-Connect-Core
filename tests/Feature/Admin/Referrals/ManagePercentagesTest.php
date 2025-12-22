<?php

namespace Tests\Feature\Admin\Referrals;

use App\Enums\AccountType;
use App\Enums\PercentageChangeType;
use App\Livewire\Admin\Referrals\ManagePercentages;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManagePercentagesTest extends TestCase
{
    protected function createEnrollmentWithUser(array $userAttributes = [], int $initialPercentage = 10): ReferralEnrollment
    {
        $user = User::factory()->influencer()->withProfile()->create($userAttributes);

        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create initial enrollment percentage history
        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => $initialPercentage,
            'change_type' => PercentageChangeType::ENROLLMENT,
            'reason' => 'Initial enrollment',
            'changed_by_user_id' => null,
        ]);

        return $enrollment;
    }

    #[Test]
    public function admin_can_view_percentage_management_page()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);

        $this->actingAs($admin);

        Livewire::test(ManagePercentages::class)
            ->assertStatus(200)
            ->assertSee('Manage Affiliate Percentages');
    }

    #[Test]
    public function displays_enrolled_referrers_with_their_percentages()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ], 15);

        Livewire::test(ManagePercentages::class)
            ->assertSee('John Doe')
            ->assertSee('john@example.com')
            ->assertSee('15%');
    }

    #[Test]
    public function can_search_for_referrers_by_name()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment1 = $this->createEnrollmentWithUser(['name' => 'John Doe']);
        $enrollment2 = $this->createEnrollmentWithUser(['name' => 'Jane Smith']);

        Livewire::test(ManagePercentages::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    #[Test]
    public function can_search_for_referrers_by_email()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment1 = $this->createEnrollmentWithUser(['email' => 'john@example.com']);
        $enrollment2 = $this->createEnrollmentWithUser(['email' => 'jane@example.com']);

        Livewire::test(ManagePercentages::class)
            ->set('search', 'jane@')
            ->assertSee('jane@example.com')
            ->assertDontSee('john@example.com');
    }

    #[Test]
    public function can_open_edit_modal()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser([], 12);

        Livewire::test(ManagePercentages::class)
            ->assertSet('editingEnrollmentId', null)
            ->call('openEditModal', $enrollment->id)
            ->assertSet('editingEnrollmentId', $enrollment->id)
            ->assertSet('newPercentage', 12);
    }

    #[Test]
    public function can_close_edit_modal()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->assertSet('editingEnrollmentId', $enrollment->id)
            ->call('closeEditModal')
            ->assertSet('editingEnrollmentId', null);
    }

    #[Test]
    public function can_update_percentage_with_permanent_change()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser([], 10);

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::PERMANENT->value)
            ->set('reason', 'Increasing percentage for top performer')
            ->call('updatePercentage');

        // Verify enrollment percentage was updated via history
        $this->assertEquals(20, $enrollment->fresh()->currentReferralPercentage());

        // Verify history was created
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Increasing percentage for top performer',
            'changed_by_user_id' => $admin->id,
        ]);
    }

    #[Test]
    public function can_update_percentage_with_temporary_date_change()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser([], 10);
        $expiresAt = now()->addMonths(3)->format('Y-m-d');

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 25)
            ->set('changeType', PercentageChangeType::TEMPORARY_DATE->value)
            ->set('expiresAt', $expiresAt)
            ->set('reason', 'Promotional period')
            ->call('updatePercentage');

        // Verify enrollment percentage was updated via history
        $this->assertEquals(25, $enrollment->fresh()->currentReferralPercentage());

        // Verify history was created with expires_at
        $history = ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)
            ->where('change_type', PercentageChangeType::TEMPORARY_DATE)
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals(25, $history->new_percentage);
        $this->assertEquals(PercentageChangeType::TEMPORARY_DATE, $history->change_type);
        $this->assertNotNull($history->expires_at);
        $this->assertEquals($expiresAt, $history->expires_at->format('Y-m-d'));
    }

    #[Test]
    public function can_update_percentage_with_temporary_months_change()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser([], 10);

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 30)
            ->set('changeType', PercentageChangeType::TEMPORARY_MONTHS->value)
            ->set('monthsRemaining', 6)
            ->set('reason', '6-month promotional boost')
            ->call('updatePercentage');

        // Verify enrollment percentage was updated via history
        $this->assertEquals(30, $enrollment->fresh()->currentReferralPercentage());

        // Verify history was created with months_remaining
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 30,
            'change_type' => PercentageChangeType::TEMPORARY_MONTHS->value,
            'months_remaining' => 6,
            'reason' => '6-month promotional boost',
        ]);
    }

    #[Test]
    public function validates_new_percentage_is_between_0_and_100()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        // Test below minimum
        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', -1)
            ->set('reason', 'Test reason')
            ->call('updatePercentage')
            ->assertHasErrors(['newPercentage' => 'min']);

        // Test above maximum
        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 101)
            ->set('reason', 'Test reason')
            ->call('updatePercentage')
            ->assertHasErrors(['newPercentage' => 'max']);
    }

    #[Test]
    public function validates_reason_is_required()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 15)
            ->set('reason', '')
            ->call('updatePercentage')
            ->assertHasErrors(['reason' => 'required']);
    }

    #[Test]
    public function validates_expires_at_is_required_for_temporary_date_change()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::TEMPORARY_DATE->value)
            ->set('expiresAt', null)
            ->set('reason', 'Test')
            ->call('updatePercentage')
            ->assertHasErrors(['expiresAt' => 'required_if']);
    }

    #[Test]
    public function validates_expires_at_is_after_today()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::TEMPORARY_DATE->value)
            ->set('expiresAt', now()->subDay()->format('Y-m-d'))
            ->set('reason', 'Test')
            ->call('updatePercentage')
            ->assertHasErrors(['expiresAt' => 'after']);
    }

    #[Test]
    public function validates_months_remaining_is_required_for_temporary_months_change()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::TEMPORARY_MONTHS->value)
            ->set('monthsRemaining', null)
            ->set('reason', 'Test')
            ->call('updatePercentage')
            ->assertHasErrors(['monthsRemaining' => 'required_if']);
    }

    #[Test]
    public function validates_months_remaining_is_between_1_and_120()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        // Test below minimum
        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::TEMPORARY_MONTHS->value)
            ->set('monthsRemaining', 0)
            ->set('reason', 'Test')
            ->call('updatePercentage')
            ->assertHasErrors(['monthsRemaining' => 'min']);

        // Test above maximum
        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 20)
            ->set('changeType', PercentageChangeType::TEMPORARY_MONTHS->value)
            ->set('monthsRemaining', 121)
            ->set('reason', 'Test')
            ->call('updatePercentage')
            ->assertHasErrors(['monthsRemaining' => 'max']);
    }

    #[Test]
    public function can_open_history_modal()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->assertSet('viewingHistoryEnrollmentId', null)
            ->call('openHistoryModal', $enrollment->id)
            ->assertSet('viewingHistoryEnrollmentId', $enrollment->id);
    }

    #[Test]
    public function can_close_history_modal()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openHistoryModal', $enrollment->id)
            ->assertSet('viewingHistoryEnrollmentId', $enrollment->id)
            ->call('closeHistoryModal')
            ->assertSet('viewingHistoryEnrollmentId', null);
    }

    #[Test]
    public function displays_percentage_change_history()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        // Create some history
        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 15,
            'change_type' => PercentageChangeType::PERMANENT,
            'reason' => 'First increase',
            'changed_by_user_id' => $admin->id,
        ]);

        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 15,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::PERMANENT,
            'reason' => 'Second increase',
            'changed_by_user_id' => $admin->id,
        ]);

        Livewire::test(ManagePercentages::class)
            ->call('openHistoryModal', $enrollment->id)
            ->assertSee('10% → 15%')
            ->assertSee('15% → 20%')
            ->assertSee('First increase')
            ->assertSee('Second increase');
    }

    #[Test]
    public function tracks_admin_who_made_percentage_change()
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
            'name' => 'Admin User',
        ]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 25)
            ->set('changeType', PercentageChangeType::PERMANENT->value)
            ->set('reason', 'Test change')
            ->call('updatePercentage');

        $history = ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)
            ->where('changed_by_user_id', $admin->id)
            ->first();
        $this->assertNotNull($history);
        $this->assertEquals($admin->id, $history->changed_by_user_id);
    }

    #[Test]
    public function form_resets_after_successful_update()
    {
        $admin = User::factory()->create(['account_type' => AccountType::ADMIN]);
        $this->actingAs($admin);

        $enrollment = $this->createEnrollmentWithUser();

        Livewire::test(ManagePercentages::class)
            ->call('openEditModal', $enrollment->id)
            ->set('newPercentage', 25)
            ->set('changeType', PercentageChangeType::PERMANENT->value)
            ->set('reason', 'Test change')
            ->call('updatePercentage')
            ->assertSet('editingEnrollmentId', null)
            ->assertSet('newPercentage', 10)
            ->assertSet('reason', '');
    }
}
