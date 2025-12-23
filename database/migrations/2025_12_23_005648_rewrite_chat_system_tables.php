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
        // Modify chats table - add status and tracking fields
        Schema::table('chats', function (Blueprint $table) {
            $table->string('status')->default('active')->after('influencer_id');
            $table->timestamp('archived_at')->nullable()->after('status');
            $table->timestamp('last_activity_at')->nullable()->after('archived_at');
        });

        // Modify messages table - add system message support, remove old read tracking
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_system_message')->default(false)->after('body');
            $table->string('system_message_type')->nullable()->after('is_system_message');
        });

        // Drop old read tracking columns in separate statement (SQLite requires index drop first)
        Schema::table('messages', function (Blueprint $table) {
            // Drop index first (SQLite requires this before dropping columns in the index)
            $table->dropIndex(['chat_id', 'read_at']);
            $table->dropForeign(['read_by_user_id']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['read_at', 'read_by_user_id']);
        });

        // Create message_reads table for per-user read tracking
        Schema::create('message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });

        // Create message_reactions table
        Schema::create('message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reaction_type'); // thumbs_up, star, heart, thumbs_down
            $table->timestamps();

            $table->unique(['message_id', 'user_id', 'reaction_type']);
            $table->index(['message_id', 'reaction_type']);
        });

        // Add index for chat status queries
        Schema::table('chats', function (Blueprint $table) {
            $table->index(['status', 'last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new tables
        Schema::dropIfExists('message_reactions');
        Schema::dropIfExists('message_reads');

        // Remove new indexes from chats
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['status', 'last_activity_at']);
        });

        // Restore messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['is_system_message', 'system_message_type']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->index(['chat_id', 'read_at']);
        });

        // Restore chats table
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn(['status', 'archived_at', 'last_activity_at']);
        });
    }
};
