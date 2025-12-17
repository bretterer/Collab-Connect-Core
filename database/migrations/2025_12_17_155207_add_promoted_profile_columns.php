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
            $table->integer('promotion_credits')->default(null)->nullable()->after('promoted_until');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('is_promoted')->default(false)->after('onboarding_complete');
            $table->timestamp('promoted_until')->default(null)->nullable()->after('is_promoted');
            $table->integer('promotion_credits')->default(null)->nullable()->after('promoted_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('is_promoted');
            $table->dropColumn('promoted_until');
            $table->dropColumn('promotion_credits');
        });

        Schema::table('influencers', function (Blueprint $table) {
            $table->dropColumn('promotion_credits');
        });
    }
};
