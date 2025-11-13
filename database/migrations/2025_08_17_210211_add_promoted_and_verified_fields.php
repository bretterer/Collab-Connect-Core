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
        // Add promoted and verified fields to influencers table
        Schema::table('influencers', function (Blueprint $table) {
            $table->boolean('is_promoted')->default(false)->after('completed_collaborations');
            $table->timestamp('promoted_until')->nullable()->after('is_promoted');
            $table->boolean('is_verified')->default(false)->after('promoted_until');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->timestamp('verified_until')->nullable()->after('verified_at');
        });

        // is_verified field already exists in social_media_accounts table
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn([
                'is_promoted',
                'promoted_until',
                'is_verified',
                'verified_at',
            ]);
        });

        // is_verified field already exists in social_media_accounts table
    }
};
