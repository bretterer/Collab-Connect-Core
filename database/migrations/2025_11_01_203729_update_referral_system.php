<?php

use App\Models\Referral;
use App\Models\ReferralPayout;
use App\Models\ReferralPayoutItem;
use App\Models\ReferralPercentageHistory;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_enrollments', function (Blueprint $table) {
            $table->id();
            $table->ulid('code')->unique();
            $table->foreignIdFor(User::class);
            $table->timestamp('enrolled_at')->default(now())->nullable();
            $table->timestamp('disabled_at')->nullable();

            // PayPal Payout Account Information
            $table->string('paypal_email')->nullable();
            $table->boolean('paypal_verified')->default(false);
            $table->timestamp('paypal_connected_at')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('referral_percentage_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_enrollment_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('old_percentage');
            $table->unsignedInteger('new_percentage');
            $table->string('change_type');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('months_remaining')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable(); // Tracks when an expired temporary change was processed
            $table->timestamps();

            $table->index('referral_enrollment_id');
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_user_id')->constrained('users')->onDelete('cascade');
            $table->string('referral_code_used');
            $table->string('status')->default('pending'); // pending, active, churned, cancelled
            $table->timestamp('converted_at')->nullable(); // when they became a paying customer
            $table->timestamp('churned_at')->nullable();
            $table->unsignedInteger('base_referral_percentage')->default(0);
            $table->foreignIdFor(ReferralPercentageHistory::class, 'promotional_referral_percentage_id')->nullable()->constrained('referral_percentage_history')->onDelete('set null');
            $table->timestamps();

            $table->index(['referrer_user_id', 'status']);
            $table->index('referred_user_id');
        });

        Schema::create('referral_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_enrollment_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('pending'); // pending, approved, processing, paid, failed, cancelled
            $table->unsignedInteger('month'); // 1-12
            $table->unsignedInteger('year'); // e.g., 2025
            $table->unsignedInteger('referral_count')->default(0); // how many active referrals this period

            // PayPal transaction details
            $table->string('paypal_batch_id')->nullable();
            $table->string('paypal_payout_item_id')->nullable();
            $table->string('paypal_transaction_id')->nullable();

            // Admin tracking
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();

            $table->timestamps();

            $table->index(['referral_enrollment_id', 'month', 'year']);
            $table->index('status');
        });

        Schema::create('referral_payout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReferralPayout::class, 'referral_payout_id')->nullable()->constrained()->onDelete('cascade'); // Nullable until rolled up into a payout
            $table->foreignId('referral_enrollment_id')->constrained('referral_enrollments')->onDelete('cascade'); // Direct link to enrollment for easier queries
            $table->foreignIdFor(Referral::class, 'referral_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(ReferralPercentageHistory::class, 'referral_percentage_history_id')->nullable()->constrained('referral_percentage_history')->onDelete('set null');
            $table->decimal('subscription_amount', 10, 2); // The base subscription payment amount
            $table->unsignedInteger('referral_percentage'); // The percentage used for this calculation
            $table->decimal('amount', 10, 2); // The commission amount
            $table->string('currency', 3)->default('USD');

            $table->date('scheduled_payout_date'); // When this should be paid out

            $table->string('status')->default('draft'); // draft, pending, included_in_payout, paid, cancelled
            $table->timestamp('calculated_at')->nullable(); // When the commission was calculated

            $table->timestamps();

            // Indexes
            $table->index('referral_payout_id');
            $table->index('referral_id');
            $table->index(['status', 'scheduled_payout_date']);
            $table->index(['referral_enrollment_id', 'scheduled_payout_date'], 'idx_enrollment_payout_date');

            // Unique constraint to prevent duplicate payout items for same referral on same date
            $table->unique(['referral_id', 'scheduled_payout_date'], 'unique_referral_payout_date');
        });

        Schema::create('referral_payout_item_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ReferralPayoutItem::class)->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('note');
            $table->timestamps();
        });

        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('default_percentage')->default(10);
            $table->decimal('payout_threshold', 10, 2)->default(5.00);
            $table->unsignedTinyInteger('payout_day_of_month')->default(15); // 1-28
            $table->unsignedTinyInteger('calculation_day_of_month')->default(1); // 1-28
            $table->boolean('require_subscription')->default(true);
            $table->unsignedInteger('minimum_account_age_days')->default(0);
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order, ensuring child tables (with foreign keys) are dropped first
        Schema::dropIfExists('referral_settings');
        Schema::dropIfExists('referral_payout_item_notes'); // Must drop before referral_payout_items
        Schema::dropIfExists('referral_payout_items');
        Schema::dropIfExists('referral_payouts');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('referral_percentage_history');
        Schema::dropIfExists('referral_enrollments');
    }
};
