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
        Schema::table('campaigns', function (Blueprint $table) {
            // Brand Information
            $table->text('brand_overview')->nullable()->after('campaign_description');
            $table->text('brand_essence')->nullable()->after('brand_overview');
            $table->json('brand_pillars')->nullable()->after('brand_essence');
            $table->text('current_advertising_campaign')->nullable()->after('brand_pillars');
            $table->text('brand_story')->nullable()->after('current_advertising_campaign');

            // Campaign Briefing
            $table->text('campaign_objective')->nullable()->after('brand_story');
            $table->text('key_insights')->nullable()->after('campaign_objective');
            $table->text('fan_motivator')->nullable()->after('key_insights');
            $table->text('creative_connection')->nullable()->after('fan_motivator');
            $table->text('specific_products')->nullable()->after('creative_connection');
            $table->text('posting_restrictions')->nullable()->after('specific_products');
            $table->text('additional_considerations')->nullable()->after('posting_restrictions');

            // Deliverables & Success Metrics
            $table->json('target_platforms')->nullable()->after('additional_considerations');
            $table->json('deliverables')->nullable()->after('target_platforms');
            $table->json('success_metrics')->nullable()->after('deliverables');
            $table->text('timing_details')->nullable()->after('success_metrics');

            // Enhanced Campaign Structure
            $table->text('target_audience')->nullable()->after('timing_details');
            $table->text('content_guidelines')->nullable()->after('target_audience');
            $table->text('brand_guidelines')->nullable()->after('content_guidelines');
            $table->text('main_contact')->nullable()->after('brand_guidelines');
            $table->string('project_name')->nullable()->after('main_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'brand_overview',
                'brand_essence',
                'brand_pillars',
                'current_advertising_campaign',
                'brand_story',
                'campaign_objective',
                'key_insights',
                'fan_motivator',
                'creative_connection',
                'specific_products',
                'posting_restrictions',
                'additional_considerations',
                'target_platforms',
                'deliverables',
                'success_metrics',
                'timing_details',
                'target_audience',
                'content_guidelines',
                'brand_guidelines',
                'main_contact',
                'project_name'
            ]);
        });
    }
};