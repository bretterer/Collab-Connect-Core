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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('draft');

            // Step 1: Campaign Goal & Type
            $table->text('campaign_goal');
            $table->string('campaign_type')->nullable();
            $table->string('target_zip_code');
            $table->string('target_area')->nullable();

            // Step 2: Campaign Details
            $table->text('campaign_description');
            $table->json('social_requirements')->nullable();
            $table->json('placement_requirements')->nullable();

            // Step 3: Campaign Settings
            $table->integer('influencer_count');
            $table->date('application_deadline');
            $table->date('campaign_completion_date');
            $table->text('additional_requirements')->nullable();

            // Step 4: Publish Settings
            $table->string('publish_action')->default('publish');
            $table->date('scheduled_date')->nullable();

            // Metadata
            $table->integer('current_step')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
