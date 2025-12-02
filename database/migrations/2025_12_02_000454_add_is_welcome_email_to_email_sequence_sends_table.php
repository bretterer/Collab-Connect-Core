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
        Schema::table('email_sequence_sends', function (Blueprint $table) {
            $table->boolean('is_welcome_email')->default(false)->after('status');

            // Make email_sequence_email_id nullable for welcome emails
            $table->unsignedBigInteger('email_sequence_email_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sequence_sends', function (Blueprint $table) {
            $table->dropColumn('is_welcome_email');
        });
    }
};
