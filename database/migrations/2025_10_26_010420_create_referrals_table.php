<?php

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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('referred_at')->useCurrent();

            // Can only pay out reward if referrer and referred is subscribed.
            // This will be validated via a cron job on a nightly basis to
            // assure both referrer and referred are still subscribed.
            $table->timestamp('reward_eligible_at')->nullable();
            $table->timestamp('reward_expired_at')->nullable();

            $table->timestamps();

            $table->unique(['referred_user_id']);
        });

        Schema::create('referral_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('reward_amount_cents')->default(0);
            $table->
            $table->timestamp('paid_out_at')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->unique()->nullable()->after('account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_code');
        });

        Schema::dropIfExists('referrals');
    }
};
