<?php

namespace Tests\Feature\Jobs;

use App\Enums\PayoutStatus;
use App\Jobs\ProcessReferralPayouts;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use App\Models\User;
use App\Notifications\PayoutCancelledNotification;
use App\Notifications\PayPalEmailRequiredNotification;
use App\Services\PayPalPayoutsService;
use Illuminate\Support\Facades\Notification;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProcessReferralPayoutsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    #[Test]
    public function processes_payout_successfully_when_enrollment_has_paypal_email()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        // Mock PayPal service for batch processing
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->with(Mockery::on(function ($payouts) use ($payout) {
                return count($payouts) === 1 && $payouts[0]->id === $payout->id;
            }))
            ->andReturn([
                'batch_header' => [
                    'payout_batch_id' => 'BATCH123',
                ],
            ]);

        $this->app->instance(PayPalPayoutsService::class, $mockPayPal);

        // Run the job
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert payout was marked as paid
        $payout->refresh();
        $this->assertEquals(PayoutStatus::PAID, $payout->status);
        $this->assertEquals('BATCH123', $payout->paypal_batch_id);
        $this->assertNotNull($payout->paid_at);
    }

    #[Test]
    public function sends_notification_when_paypal_email_missing_on_first_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => null,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 50.00,
        ]);

        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);

        // Run the job with attempt 1
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert notification was sent
        Notification::assertSentTo(
            $user,
            PayPalEmailRequiredNotification::class,
            function ($notification) use ($payout) {
                return $notification->payout->id === $payout->id
                    && $notification->attemptNumber === 1;
            }
        );

        // Assert payout status unchanged
        $payout->refresh();
        $this->assertEquals(PayoutStatus::PENDING, $payout->status);
    }

    #[Test]
    public function cancels_payout_when_no_paypal_email_on_third_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => null,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 75.00,
        ]);

        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);

        // Run the job with attempt 3
        (new ProcessReferralPayouts(3))->handle($mockPayPal);

        // Assert cancellation notification was sent (NOT PayPalEmailRequiredNotification)
        Notification::assertSentTo(
            $user,
            PayoutCancelledNotification::class,
            function ($notification) use ($payout) {
                return $notification->payout->id === $payout->id
                    && $notification->reason === 'No PayPal email provided after 3 attempts';
            }
        );

        // Assert payout was cancelled
        $payout->refresh();
        $this->assertEquals(PayoutStatus::CANCELLED, $payout->status);
        $this->assertEquals('No PayPal email provided after 3 attempts', $payout->failure_reason);
        $this->assertNotNull($payout->failed_at);
    }

    #[Test]
    public function cancels_payout_immediately_when_enrollment_is_disabled()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'disabled_at' => now(),
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldNotReceive('createPayout');

        // Run the job with attempt 1 - should cancel immediately
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert payout was cancelled
        $payout->refresh();
        $this->assertEquals(PayoutStatus::CANCELLED, $payout->status);
        $this->assertEquals('Enrollment is disabled', $payout->failure_reason);
        $this->assertNotNull($payout->failed_at);
    }

    #[Test]
    public function cancels_disabled_enrollment_regardless_of_attempt_number()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'disabled_at' => now(),
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldNotReceive('createPayout');

        // Run the job with attempt 3 - should still cancel immediately
        (new ProcessReferralPayouts(3))->handle($mockPayPal);

        // Assert payout was cancelled with same result
        $payout->refresh();
        $this->assertEquals(PayoutStatus::CANCELLED, $payout->status);
        $this->assertEquals('Enrollment is disabled', $payout->failure_reason);
        $this->assertNotNull($payout->failed_at);
    }

    #[Test]
    public function keeps_payout_pending_when_paypal_batch_fails_on_first_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        // Mock PayPal service to return null (failure)
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->andReturn(null);

        // Run the job with attempt 1
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert payout status remains PENDING for retry
        $payout->refresh();
        $this->assertEquals(PayoutStatus::PENDING, $payout->status);
        $this->assertNull($payout->failure_reason);
        $this->assertNull($payout->failed_at);
    }

    #[Test]
    public function cancels_payout_when_paypal_fails_on_third_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        // Mock PayPal service to return null (failure)
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->andReturn(null);

        // Run the job with attempt 3
        (new ProcessReferralPayouts(3))->handle($mockPayPal);

        // Assert payout was cancelled
        $payout->refresh();
        $this->assertEquals(PayoutStatus::CANCELLED, $payout->status);
        $this->assertStringContainsString('Final attempt', $payout->failure_reason);
        $this->assertNotNull($payout->failed_at);
    }

    #[Test]
    public function keeps_payout_pending_on_exception_during_first_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        // Mock PayPal service to throw exception
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->andThrow(new \Exception('PayPal service error'));

        // Run the job with attempt 1
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert payout remains PENDING for retry
        $payout->refresh();
        $this->assertEquals(PayoutStatus::PENDING, $payout->status);
        $this->assertNull($payout->failure_reason);
        $this->assertNull($payout->failed_at);
    }

    #[Test]
    public function cancels_payout_on_exception_during_third_attempt()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        // Mock PayPal service to throw exception
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->andThrow(new \Exception('Critical PayPal error'));

        // Run the job with attempt 3
        (new ProcessReferralPayouts(3))->handle($mockPayPal);

        // Assert payout was cancelled
        $payout->refresh();
        $this->assertEquals(PayoutStatus::CANCELLED, $payout->status);
        $this->assertStringContainsString('Exception: Critical PayPal error', $payout->failure_reason);
        $this->assertNotNull($payout->failed_at);
    }

    #[Test]
    public function processes_multiple_payouts_in_single_batch()
    {
        $user1 = User::factory()->influencer()->create();
        $enrollment1 = ReferralEnrollment::factory()->create([
            'user_id' => $user1->id,
            'paypal_email' => 'user1@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $user2 = User::factory()->influencer()->create();
        $enrollment2 = ReferralEnrollment::factory()->create([
            'user_id' => $user2->id,
            'paypal_email' => 'user2@paypal.com',
            'paypal_verified' => true,
            'disabled_at' => null,
        ]);

        $payout1 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment1->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 100.00,
        ]);

        $payout2 = ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment2->id,
            'status' => PayoutStatus::PENDING,
            'amount' => 150.00,
        ]);

        // Mock PayPal service to receive batch with both payouts
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldReceive('createBatchPayout')
            ->once()
            ->with(Mockery::on(function ($payouts) use ($payout1, $payout2) {
                return count($payouts) === 2
                    && in_array($payouts[0]->id, [$payout1->id, $payout2->id])
                    && in_array($payouts[1]->id, [$payout1->id, $payout2->id]);
            }))
            ->andReturn([
                'batch_header' => ['payout_batch_id' => 'BATCH123'],
            ]);

        // Run the job
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Assert both payouts were processed in single batch
        $payout1->refresh();
        $payout2->refresh();

        $this->assertEquals(PayoutStatus::PAID, $payout1->status);
        $this->assertEquals(PayoutStatus::PAID, $payout2->status);
        $this->assertEquals('BATCH123', $payout1->paypal_batch_id);
        $this->assertEquals('BATCH123', $payout2->paypal_batch_id);
    }

    #[Test]
    public function handles_empty_pending_payouts_gracefully()
    {
        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldNotReceive('createBatchPayout');

        // Run the job with no pending payouts
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // No assertions needed - just ensure no errors occur
        $this->assertTrue(true);
    }

    #[Test]
    public function only_processes_pending_payouts()
    {
        $user = User::factory()->influencer()->create();
        $enrollment = ReferralEnrollment::factory()->create([
            'user_id' => $user->id,
            'paypal_email' => 'test@paypal.com',
            'paypal_verified' => true,
        ]);

        // Create payouts in various states
        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::PAID,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::CANCELLED,
        ]);

        ReferralPayout::factory()->create([
            'referral_enrollment_id' => $enrollment->id,
            'status' => PayoutStatus::FAILED,
        ]);

        $mockPayPal = Mockery::mock(PayPalPayoutsService::class);
        $mockPayPal->shouldNotReceive('createBatchPayout');

        // Run the job
        (new ProcessReferralPayouts(1))->handle($mockPayPal);

        // Verify no payouts were processed
        $this->assertTrue(true);
    }
}
