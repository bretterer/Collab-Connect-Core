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
        Schema::table('email_sequences', function (Blueprint $table) {
            $table->string('sequence_mode')->default('after_subscription')->after('description');
            $table->timestamp('anchor_datetime')->nullable()->after('sequence_mode');
            $table->string('anchor_timezone')->default('America/New_York')->after('anchor_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sequences', function (Blueprint $table) {
            $table->dropColumn(['sequence_mode', 'anchor_datetime', 'anchor_timezone']);
        });
    }
};
