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
        Schema::table('influencers', function (Blueprint $table) {
            // Account Settings - visibility controls
            $table->boolean('is_campaign_active')->default(true)->after('onboarding_complete');
            $table->boolean('is_accepting_invitations')->default(true)->after('is_campaign_active');

            // Match Profile - About (helps businesses understand the influencer)
            $table->text('about_yourself')->nullable()->after('bio');
            $table->text('passions')->nullable()->after('about_yourself');

            // Match Profile - Preferred campaign types (helps match scoring)
            $table->json('preferred_campaign_types')->nullable()->after('passions');

            // Match Profile - Deliverables the influencer can provide
            $table->json('deliverable_types')->nullable()->after('preferred_campaign_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn([
                'is_campaign_active',
                'is_accepting_invitations',
                'about_yourself',
                'passions',
                'preferred_campaign_types',
                'deliverable_types',
            ]);
        });
    }
};
