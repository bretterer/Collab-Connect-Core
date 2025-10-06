<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing chats to use business_id and influencer_id
        // This migration maps old user-to-user chats to business-influencer chats

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite doesn't support UPDATE with JOIN, so we need to do it row by row
            $chats = DB::table('chats')->get();

            foreach ($chats as $chat) {
                // Get the business_id from business_users table
                $businessUser = DB::table('business_users')
                    ->where('user_id', $chat->business_id)
                    ->first();

                // Get the influencer_id from influencers table
                $influencer = DB::table('influencers')
                    ->where('user_id', $chat->influencer_id)
                    ->first();

                if ($businessUser && $influencer) {
                    DB::table('chats')
                        ->where('id', $chat->id)
                        ->update([
                            'business_id' => $businessUser->business_id,
                            'influencer_id' => $influencer->id,
                        ]);
                }
            }
        } else {
            // MySQL/PostgreSQL support UPDATE with JOIN
            DB::statement('
                UPDATE chats
                INNER JOIN business_users ON chats.business_id = business_users.user_id
                INNER JOIN influencers ON chats.influencer_id = influencers.user_id
                SET
                    chats.business_id = business_users.business_id,
                    chats.influencer_id = influencers.id
            ');
        }

        // Note: campaign_id will remain NULL for existing chats
        // These can be manually associated with campaigns later if needed
        // or business users can start new campaign-specific chats
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it transforms the data
        // The previous migration handles the schema rollback
    }
};
