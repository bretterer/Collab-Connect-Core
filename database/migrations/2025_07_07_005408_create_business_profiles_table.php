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
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('business_name');
            $table->string('industry')->nullable();
            $table->json('websites')->nullable(); // Store URLs as JSON array
            $table->string('primary_zip_code');
            $table->integer('location_count')->default(1);
            $table->boolean('is_franchise')->default(false);
            $table->boolean('is_national_brand')->default(false);
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('subscription_plan')->nullable();
            $table->json('collaboration_goals')->nullable(); // Store goals as JSON array
            $table->json('campaign_types')->nullable(); // Store campaign types as JSON array
            $table->json('team_members')->nullable(); // Store team member info as JSON array
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamps();

            // Indexes for search performance
            $table->index(['user_id']); // For user relationship queries
            $table->index(['primary_zip_code']); // For location-based searches
            $table->index(['industry']); // For industry filtering
            $table->index(['onboarding_completed']); // For filtering completed profiles
            $table->index(['primary_zip_code', 'industry']); // For combined location + industry searches
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
    }
};
