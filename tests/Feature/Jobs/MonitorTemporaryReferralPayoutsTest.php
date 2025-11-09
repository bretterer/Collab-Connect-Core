<?php

namespace Tests\Feature\Jobs;

use App\Enums\PercentageChangeType;
use App\Jobs\MonitorTemporaryReferralPayouts;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MonitorTemporaryReferralPayoutsTest extends TestCase
{
    #[Test]
    public function resets_expired_temporary_date_percentage_to_permanent_percentage()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create the initial enrollment percentage
        $enrollmentHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::ENROLLMENT,
            'reason' => 'Initial enrollment',
            'created_at' => now()->subMonths(2),
        ]);

        // Create a permanent percentage change
        $permanentHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 15,
            'change_type' => PercentageChangeType::PERMANENT,
            'reason' => 'Performance increase',
            'created_at' => now()->subMonth(),
        ]);

        // Create an expired temporary percentage change
        $temporaryHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 15,
            'new_percentage' => 25,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
            'reason' => 'Promotional boost',
            'created_at' => now()->subWeek(),
        ]);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert a new history record was created
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 25, // From the temporary percentage
            'new_percentage' => 15, // Back to the permanent percentage
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
            'changed_by_user_id' => null,
        ]);

        // Verify the temporary change was marked as processed
        $temporaryHistory->refresh();
        $this->assertNotNull($temporaryHistory->processed_at);
        $this->assertEquals(PercentageChangeType::TEMPORARY_DATE, $temporaryHistory->change_type); // change_type remains unchanged

        // Verify exactly one new record was created
        $this->assertEquals(4, ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count());
    }

    #[Test]
    public function resets_to_enrollment_percentage_when_no_permanent_changes_exist()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create only the initial enrollment percentage
        $enrollmentHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::ENROLLMENT,
            'reason' => 'Initial enrollment',
        ]);

        // Create an expired temporary percentage change (no permanent changes in between)
        $temporaryHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
            'reason' => 'Promotional boost',
        ]);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert it reverts to the enrollment percentage
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 20,
            'new_percentage' => 10, // Back to enrollment percentage
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
            'changed_by_user_id' => null,
        ]);
    }

    #[Test]
    public function resets_to_zero_when_no_permanent_or_enrollment_changes_exist()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create an expired temporary percentage change with no history
        $temporaryHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 15,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
            'reason' => 'Promotional boost',
        ]);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert it defaults to 0
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 15,
            'new_percentage' => 0,
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
            'changed_by_user_id' => null,
        ]);
    }

    #[Test]
    public function handles_multiple_expired_temporary_percentages_for_different_enrollments()
    {
        // Create two different enrollments
        $enrollment1 = ReferralEnrollment::factory()->create();
        $enrollment2 = ReferralEnrollment::factory()->create();

        // Set up enrollment 1 with permanent percentage and expired temporary
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::ENROLLMENT,
        ]);

        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
        ]);

        // Set up enrollment 2 with permanent percentage and expired temporary
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'old_percentage' => 0,
            'new_percentage' => 12,
            'change_type' => PercentageChangeType::ENROLLMENT,
        ]);

        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'old_percentage' => 12,
            'new_percentage' => 25,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
        ]);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert both were reset
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment1->id,
            'old_percentage' => 20,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
        ]);

        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment2->id,
            'old_percentage' => 25,
            'new_percentage' => 12,
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
        ]);
    }

    #[Test]
    public function does_not_affect_non_expired_temporary_percentages()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create permanent percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::PERMANENT,
        ]);

        // Create a non-expired temporary percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->addWeek(),
        ]);

        $initialCount = ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count();

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert no new records were created
        $finalCount = ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count();
        $this->assertEquals($initialCount, $finalCount);
    }

    #[Test]
    public function uses_most_recent_permanent_percentage_when_multiple_exist()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create enrollment percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::ENROLLMENT,
            'created_at' => now()->subMonths(3),
        ]);

        // Create first permanent change
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 15,
            'change_type' => PercentageChangeType::PERMANENT,
            'created_at' => now()->subMonths(2),
        ]);

        // Create second (more recent) permanent change
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 15,
            'new_percentage' => 18,
            'change_type' => PercentageChangeType::PERMANENT,
            'created_at' => now()->subMonth(),
        ]);

        // Create expired temporary percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 18,
            'new_percentage' => 25,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
        ]);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Assert it uses the most recent permanent percentage (18)
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 25,
            'new_percentage' => 18, // Most recent permanent percentage
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
        ]);
    }

    #[Test]
    public function marks_expired_temporary_changes_as_processed()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create permanent percentage
        $permanentHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::PERMANENT,
            'created_at' => now()->subMonth(),
        ]);

        // Create expired temporary percentage
        $temporaryHistory = ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
            'created_at' => now()->subWeek(),
        ]);

        // Verify initial state
        $this->assertNull($temporaryHistory->processed_at);
        $this->assertEquals(PercentageChangeType::TEMPORARY_DATE, $temporaryHistory->change_type);

        // Run the job
        (new MonitorTemporaryReferralPayouts)->handle();

        // Verify the temporary change was marked as processed
        $temporaryHistory->refresh();
        $this->assertNotNull($temporaryHistory->processed_at);
        $this->assertEquals(PercentageChangeType::TEMPORARY_DATE, $temporaryHistory->change_type); // change_type remains unchanged

        // Verify other fields weren't changed
        $this->assertEquals(10, $temporaryHistory->old_percentage);
        $this->assertEquals(20, $temporaryHistory->new_percentage);
    }

    #[Test]
    public function does_not_create_duplicate_records_when_run_multiple_times()
    {
        // Create an enrollment
        $enrollment = ReferralEnrollment::factory()->create();

        // Create permanent percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 0,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::PERMANENT,
            'created_at' => now()->subMonth(),
        ]);

        // Create expired temporary percentage
        ReferralPercentageHistory::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 10,
            'new_percentage' => 20,
            'change_type' => PercentageChangeType::TEMPORARY_DATE,
            'expires_at' => now()->subDay(),
            'created_at' => now()->subWeek(),
        ]);

        // Run the job first time
        (new MonitorTemporaryReferralPayouts)->handle();

        // Should have 3 records: original permanent, expired temporary, and new permanent
        $this->assertEquals(3, ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count());

        // Verify the expired temporary change was marked
        $this->assertDatabaseHas('referral_percentage_history', [
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => 20,
            'new_percentage' => 10,
            'change_type' => PercentageChangeType::PERMANENT->value,
            'reason' => 'Temporary percentage expired',
        ]);

        // Run the job second time
        (new MonitorTemporaryReferralPayouts)->handle();

        // Should still have only 3 records - no duplicates created
        $this->assertEquals(3, ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count());

        // Run the job third time
        (new MonitorTemporaryReferralPayouts)->handle();

        // Should still have only 3 records - no duplicates created
        $this->assertEquals(3, ReferralPercentageHistory::where('referral_enrollment_id', $enrollment->id)->count());
    }
}
