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
        Schema::table('campaigns', function (Blueprint $table) {
            // Add exclusivity_period column to the campaigns table
            $table->integer('exclusivity_period')->default(0)->after('influencer_count')->comment('Exclusivity period in days for the campaign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Drop the exclusivity_period column if it exists
            if (Schema::hasColumn('campaigns', 'exclusivity_period')) {
                $table->dropColumn('exclusivity_period');
            }
        });
    }
};
