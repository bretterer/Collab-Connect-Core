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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collaboration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->string('reviewer_type'); // 'business' or 'influencer'
            $table->unsignedTinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Each user can only review once per collaboration
            $table->unique(['collaboration_id', 'reviewer_id']);
        });

        // Add review period fields to collaborations
        Schema::table('collaborations', function (Blueprint $table) {
            $table->string('review_status')->default('pending')->after('cancellation_reason');
            $table->timestamp('review_period_starts_at')->nullable()->after('review_status');
            $table->timestamp('review_period_ends_at')->nullable()->after('review_period_starts_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborations', function (Blueprint $table) {
            $table->dropColumn(['review_status', 'review_period_starts_at', 'review_period_ends_at']);
        });

        Schema::dropIfExists('reviews');
    }
};
