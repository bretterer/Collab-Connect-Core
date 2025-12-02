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
            $table->boolean('send_welcome_email')->default(false)->after('anchor_timezone');
            $table->string('welcome_email_subject')->nullable()->after('send_welcome_email');
            $table->text('welcome_email_body')->nullable()->after('welcome_email_subject');
            $table->string('welcome_email_preview_text')->nullable()->after('welcome_email_body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_sequences', function (Blueprint $table) {
            $table->dropColumn([
                'send_welcome_email',
                'welcome_email_subject',
                'welcome_email_body',
                'welcome_email_preview_text',
            ]);
        });
    }
};
