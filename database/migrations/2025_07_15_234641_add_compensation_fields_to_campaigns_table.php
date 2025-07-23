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
            // Add compensation type field
            $table->string('compensation_type')->default('monetary')->after('additional_requirements');

            // Add compensation amount field (replaces budget)
            $table->integer('compensation_amount')->default(0)->after('compensation_type');

            // Add compensation description for non-monetary compensation
            $table->text('compensation_description')->nullable()->after('compensation_amount');

            // Add compensation details for specific types
            $table->json('compensation_details')->nullable()->after('compensation_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['compensation_type', 'compensation_amount', 'compensation_description', 'compensation_details']);
        });
    }
};
