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
            $table->date('campaign_start_date')->nullable()->after('application_deadline');
            $table->timestamp('started_at')->nullable()->after('published_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('archived_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'campaign_start_date',
                'started_at',
                'completed_at',
                'archived_at',
            ]);
        });
    }
};
