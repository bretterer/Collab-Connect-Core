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
        Schema::create('influencer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('creator_name')->nullable();
            $table->string('primary_niche')->nullable();
            $table->string('primary_zip_code');
            $table->string('media_kit_url')->nullable();
            $table->boolean('has_media_kit')->default(false);
            $table->json('collaboration_preferences')->nullable(); // Store preferences as JSON array
            $table->json('preferred_brands')->nullable(); // Store preferred brands/industries as JSON array
            $table->string('subscription_plan')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();

            // Indexes for search performance
            $table->index(['user_id']); // For user relationship queries
            $table->index(['primary_zip_code']); // For location-based searches
            $table->index(['primary_niche']); // For niche filtering
            $table->index(['onboarding_completed']); // For filtering completed profiles
            $table->index(['primary_zip_code', 'primary_niche']); // For combined location + niche searches
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_profiles');
    }
};
