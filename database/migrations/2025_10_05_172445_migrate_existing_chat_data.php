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

        DB::statement('
            UPDATE chats
            INNER JOIN business_users ON chats.business_id = business_users.user_id
            INNER JOIN influencers ON chats.influencer_id = influencers.user_id
            SET
                chats.business_id = business_users.business_id,
                chats.influencer_id = influencers.id
        ');

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
