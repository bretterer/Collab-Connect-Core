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
        Schema::dropIfExists('campaign_brands');
        Schema::dropIfExists('campaign_briefs');
        Schema::dropIfExists('campaign_compensations');
        Schema::dropIfExists('campaign_requirements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('campaign_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->text('brand_overview')->nullable();
            $table->text('brand_essence')->nullable();
            $table->json('brand_pillars')->nullable();
            $table->text('current_advertising_campaign')->nullable();
            $table->text('brand_story')->nullable();
            $table->text('brand_guidelines')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
        });

        Schema::create('campaign_briefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('project_name')->nullable();
            $table->string('main_contact')->nullable();
            $table->text('campaign_objective')->nullable();
            $table->text('key_insights')->nullable();
            $table->text('fan_motivator')->nullable();
            $table->text('creative_connection')->nullable();
            $table->text('target_audience')->nullable();
            $table->text('timing_details')->nullable();
            $table->text('additional_requirements')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
        });

        Schema::create('campaign_compensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('compensation_type');
            $table->integer('compensation_amount')->nullable();
            $table->text('compensation_description')->nullable();
            $table->json('compensation_details')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
        });

        Schema::create('campaign_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->json('social_requirements')->nullable();
            $table->json('placement_requirements')->nullable();
            $table->json('target_platforms')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('success_metrics')->nullable();
            $table->text('content_guidelines')->nullable();
            $table->text('posting_restrictions')->nullable();
            $table->text('specific_products')->nullable();
            $table->text('additional_considerations')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
        });
    }
};
