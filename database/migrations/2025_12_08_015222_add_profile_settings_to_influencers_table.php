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
            // Account Settings
            $table->boolean('is_campaign_active')->default(true)->after('onboarding_complete');
            $table->boolean('is_accepting_invitations')->default(true)->after('is_campaign_active');

            // Match Profile - About
            $table->text('about_yourself')->nullable()->after('bio');
            $table->text('passions')->nullable()->after('about_yourself');

            // Match Profile - Creator Account
            $table->json('topics')->nullable()->after('passions');
            $table->json('account_niches')->nullable()->after('topics');

            // Match Profile - Brands
            $table->json('preferred_companies')->nullable()->after('account_niches');
            $table->json('interested_brand_types')->nullable()->after('preferred_companies');

            // Match Profile - Collaboration deliverables
            $table->json('collaboration_deliverables')->nullable()->after('interested_brand_types');
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
                'topics',
                'account_niches',
                'preferred_companies',
                'interested_brand_types',
                'collaboration_deliverables',
            ]);
        });
    }
};
