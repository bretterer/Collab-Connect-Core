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
        Schema::create('subscription_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('key'); // Credit type key (e.g., 'active_applications_limit', 'campaign_boost_credits')
            $table->integer('value')->default(0); // Current credit amount
            $table->timestamp('reset_at')->nullable(); // When this credit was last reset
            $table->timestamps();

            // Unique constraint to prevent duplicate credits for same subscription+key
            $table->unique(['subscription_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_credits');
    }
};
