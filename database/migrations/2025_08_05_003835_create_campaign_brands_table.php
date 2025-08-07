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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_brands');
    }
};
