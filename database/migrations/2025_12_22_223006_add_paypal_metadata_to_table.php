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
        Schema::table('referral_enrollments', function (Blueprint $table) {
            $table->json('paypal_metadata')->nullable()->after('paypal_connected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_enrollments', function (Blueprint $table) {
            $table->dropColumn('paypal_metadata');
        });
    }
};
