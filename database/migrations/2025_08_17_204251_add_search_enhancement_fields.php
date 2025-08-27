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
        // Add engagement_rate and other metrics to social_media_accounts
        Schema::table('social_media_accounts', function (Blueprint $table) {
            $table->decimal('engagement_rate', 5, 2)->nullable()->after('follower_count');
            $table->integer('avg_likes')->nullable()->after('engagement_rate');
            $table->integer('avg_comments')->nullable()->after('avg_likes');
            $table->decimal('response_rate', 5, 2)->default(95.0)->after('avg_comments');
            $table->timestamp('last_post_at')->nullable()->after('response_rate');
        });

        // Add pricing and quality fields to influencers
        Schema::table('influencers', function (Blueprint $table) {
            $table->integer('min_rate')->nullable()->after('typical_lead_time_days');
            $table->integer('max_rate')->nullable()->after('min_rate');
            $table->decimal('content_quality_score', 3, 1)->default(7.5)->after('max_rate');
            $table->decimal('avg_response_time_hours', 5, 1)->default(24.0)->after('content_quality_score');
            $table->integer('completed_collaborations')->default(0)->after('avg_response_time_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_media_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'engagement_rate',
                'avg_likes',
                'avg_comments',
                'response_rate',
                'last_post_at',
            ]);
        });

        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn([
                'min_rate',
                'max_rate',
                'content_quality_score',
                'avg_response_time_hours',
                'completed_collaborations',
            ]);
        });
    }
};
