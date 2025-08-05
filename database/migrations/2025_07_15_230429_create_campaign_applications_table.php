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
        Schema::create('campaign_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message'); // Cover letter/message from influencer
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamp('submitted_at');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable(); // Notes from business owner
            $table->timestamps();

            // Prevent duplicate applications
            $table->unique(['campaign_id', 'user_id']);

            // Indexes for performance
            $table->index(['campaign_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_applications');
    }
};