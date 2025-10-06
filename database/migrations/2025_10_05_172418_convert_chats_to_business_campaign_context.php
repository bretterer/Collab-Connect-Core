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
        Schema::table('chats', function (Blueprint $table) {
            // Drop existing foreign keys first
            $table->dropForeign(['business_user_id']);
            $table->dropForeign(['influencer_user_id']);

            // Now drop unique constraint
            $table->dropUnique(['business_user_id', 'influencer_user_id']);

            // Add campaign_id column
            $table->foreignId('campaign_id')->after('id')->nullable()->constrained()->onDelete('cascade');

            // Rename columns
            $table->renameColumn('business_user_id', 'business_id');
            $table->renameColumn('influencer_user_id', 'influencer_id');
        });

        // Now add foreign keys with correct table references
        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('influencer_id')->references('id')->on('influencers')->onDelete('cascade');

            // Add unique constraint for business-influencer-campaign combination
            $table->unique(['business_id', 'influencer_id', 'campaign_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Drop new unique constraint and foreign keys
            $table->dropUnique(['business_id', 'influencer_id', 'campaign_id']);
            $table->dropForeign(['business_id']);
            $table->dropForeign(['influencer_id']);
            $table->dropForeign(['campaign_id']);

            // Rename columns back
            $table->renameColumn('business_id', 'business_user_id');
            $table->renameColumn('influencer_id', 'influencer_user_id');

            // Drop campaign_id
            $table->dropColumn('campaign_id');
        });

        // Restore original foreign keys
        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('business_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('influencer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['business_user_id', 'influencer_user_id']);
        });
    }
};
