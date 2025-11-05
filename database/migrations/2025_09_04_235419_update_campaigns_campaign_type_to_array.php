<?php

use App\Models\Campaign;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert existing single campaign_type values to arrays
        Campaign::whereNotNull('campaign_type')->chunk(100, function ($campaigns) {
            foreach ($campaigns as $campaign) {
                // Skip if already an array
                if (is_array($campaign->campaign_type)) {
                    continue;
                }

                // Convert single enum value to array
                $campaign->update([
                    'campaign_type' => [$campaign->getRawOriginal('campaign_type')],
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert arrays back to single values (take first element)
        Campaign::whereNotNull('campaign_type')->chunk(100, function ($campaigns) {
            foreach ($campaigns as $campaign) {
                if (is_array($campaign->campaign_type) && count($campaign->campaign_type) > 0) {
                    $campaign->update([
                        'campaign_type' => $campaign->campaign_type[0],
                    ]);
                }
            }
        });
    }
};
