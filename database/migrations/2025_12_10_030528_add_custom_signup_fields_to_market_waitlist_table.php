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
        Schema::table('market_waitlist', function (Blueprint $table) {
            $table->foreignId('custom_signup_page_id')->nullable()->after('postal_code')->constrained('custom_signup_pages')->nullOnDelete();
            $table->string('subscription_stripe_price_id')->nullable()->after('custom_signup_page_id');
            $table->integer('intended_trial_days')->nullable()->after('subscription_stripe_price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_waitlist', function (Blueprint $table) {
            $table->dropForeign(['custom_signup_page_id']);
            $table->dropColumn(['custom_signup_page_id', 'subscription_stripe_price_id', 'intended_trial_days']);
        });
    }
};
