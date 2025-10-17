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
        // Check if columns have already been renamed (partially applied migration)
        $columns = Schema::getColumnListing('chats');
        $alreadyRenamed = in_array('business_id', $columns) && in_array('influencer_id', $columns);

        if (! $alreadyRenamed) {
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
        } else {
            // Columns already renamed, just add campaign_id if it doesn't exist
            if (! in_array('campaign_id', $columns)) {
                Schema::table('chats', function (Blueprint $table) {
                    $table->foreignId('campaign_id')->after('id')->nullable()->constrained()->onDelete('cascade');
                });
            }
        }

        // Clean up orphaned chats where user IDs don't have corresponding profiles
        // At this point, business_id and influencer_id contain user IDs, not profile IDs
        // Note: businesses use business_users pivot table, influencers have direct user_id
        DB::statement('
            DELETE FROM chats
            WHERE business_id NOT IN (SELECT user_id FROM business_users)
               OR influencer_id NOT IN (SELECT user_id FROM influencers)
        ');

        // Add temporary columns for the new profile IDs
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('temp_business_id')->nullable()->after('business_id');
            $table->unsignedBigInteger('temp_influencer_id')->nullable()->after('influencer_id');
        });

        // Convert user IDs to profile IDs
        // For businesses: chat.business_id (user_id) -> business_users.business_id
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('
                UPDATE chats
                SET temp_business_id = (
                    SELECT business_id
                    FROM business_users
                    WHERE business_users.user_id = chats.business_id
                )
            ');

            // For influencers: chat.influencer_id (user_id) -> influencers.id
            DB::statement('
                UPDATE chats
                SET temp_influencer_id = (
                    SELECT id
                    FROM influencers
                    WHERE influencers.user_id = chats.influencer_id
                )
            ');
        } else {
            // MySQL/PostgreSQL syntax
            DB::statement('
                UPDATE chats c
                INNER JOIN business_users bu ON c.business_id = bu.user_id
                SET c.temp_business_id = bu.business_id
            ');

            DB::statement('
                UPDATE chats c
                INNER JOIN influencers i ON c.influencer_id = i.user_id
                SET c.temp_influencer_id = i.id
            ');
        }

        // Drop old columns and rename temp columns
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['business_id', 'influencer_id']);
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
