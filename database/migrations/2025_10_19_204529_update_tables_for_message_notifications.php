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
        // add to users
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('unread_notified_up_to')->nullable()->after('remember_token');
        });

        // optional speed-up
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['chat_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove optional speed-up
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['chat_id', 'user_id']);
        });

        // remove from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unread_notified_up_to');
        });
    }
};
