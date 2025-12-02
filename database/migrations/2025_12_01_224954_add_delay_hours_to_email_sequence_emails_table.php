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
        Schema::table('email_sequence_emails', function (Blueprint $table) {
            $table->integer('delay_hours')->default(0)->after('delay_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sequence_emails', function (Blueprint $table) {
            $table->dropColumn('delay_hours');
        });
    }
};
