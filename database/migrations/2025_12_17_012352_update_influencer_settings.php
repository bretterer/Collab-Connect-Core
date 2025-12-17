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
        Schema::table('influencers', function (Blueprint $table) {
            $table->renameColumn('is_campaign_active', 'is_searchable');
            $table->datetime('searchable_at')->nullable()->after('is_searchable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('influencers', function (Blueprint $table) {
            $table->renameColumn('is_searchable', 'is_campaign_active');
            $table->dropColumn('searchable_at');
        });
    }
};
