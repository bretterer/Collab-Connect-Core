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
        Schema::create('addon_price_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stripe_price_id')
                ->constrained('stripe_prices')
                ->cascadeOnDelete();
            $table->string('credit_key'); // e.g., 'profile_promotion_credits', 'campaign_boost_credits'
            $table->unsignedInteger('credits_granted')->default(1);
            $table->string('account_type')->default('both'); // 'business', 'influencer', or 'both'
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('display_name')->nullable();
            $table->timestamps();

            $table->unique(['stripe_price_id', 'credit_key']);
            $table->index(['credit_key', 'account_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addon_price_mappings');
    }
};
