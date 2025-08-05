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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_requirements');
    }
};
