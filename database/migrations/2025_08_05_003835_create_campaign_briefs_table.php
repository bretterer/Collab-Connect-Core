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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_briefs');
    }
};
