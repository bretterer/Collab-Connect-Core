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
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('body');
            $table->foreignId('read_by_user_id')->nullable()->constrained('users')->onDelete('set null')->after('read_at');

            // Index for efficient querying of unread messages
            $table->index(['chat_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['chat_id', 'read_at']);
            $table->dropForeign(['read_by_user_id']);
            $table->dropColumn(['read_at', 'read_by_user_id']);
        });
    }
};
