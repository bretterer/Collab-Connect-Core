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
        Schema::table('stripe_products', function (Blueprint $table) {
            $table->string('billable_type')->nullable()->after('metadata');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'stripe_status']);
            $table->dropColumn('user_id');

            $table->foreignId('influencer_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->after('influencer_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['influencer_id']);
            $table->dropForeign(['business_id']);
            $table->dropColumn(['influencer_id', 'business_id']);

            $table->foreignId('user_id')->after('id');
            $table->index(['user_id', 'stripe_status']);
        });
        Schema::table('stripe_products', function (Blueprint $table) {
            $table->dropColumn('billable_type');
        });
    }
};
