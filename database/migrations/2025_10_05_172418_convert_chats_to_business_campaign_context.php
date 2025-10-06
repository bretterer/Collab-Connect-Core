<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, clean up any orphaned chat records where users don't have profiles
        DB::statement('
            DELETE FROM chats
            WHERE business_user_id NOT IN (SELECT user_id FROM businesses)
               OR influencer_user_id NOT IN (SELECT user_id FROM influencers)
        ');

        Schema::table('chats', function (Blueprint $table) {
            // Drop existing foreign keys first
            $table->dropForeign(['business_user_id']);
            $table->dropForeign(['influencer_user_id']);

            // Now drop unique constraint
            $table->dropUnique(['business_user_id', 'influencer_user_id']);

            // Add campaign_id column
            $table->foreignId('campaign_id')->after('id')->nullable()->constrained()->onDelete('cascade');
        });

        // Add temporary columns for the new IDs
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('temp_business_id')->nullable()->after('business_user_id');
            $table->unsignedBigInteger('temp_influencer_id')->nullable()->after('influencer_user_id');
        });

        // Convert user IDs to profile IDs
        DB::statement('
            UPDATE chats c
            INNER JOIN businesses b ON c.business_user_id = b.user_id
            SET c.temp_business_id = b.id
        ');

        DB::statement('
            UPDATE chats c
            INNER JOIN influencers i ON c.influencer_user_id = i.user_id
            SET c.temp_influencer_id = i.id
        ');

        // Drop old columns and rename new ones
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['business_user_id', 'influencer_user_id']);
            $table->renameColumn('temp_business_id', 'business_id');
            $table->renameColumn('temp_influencer_id', 'influencer_id');
        });

        // Make columns non-nullable
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('business_id')->nullable(false)->change();
            $table->unsignedBigInteger('influencer_id')->nullable(false)->change();
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
